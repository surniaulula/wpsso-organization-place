<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmFiltersUpgrade' ) ) {

	class WpssoOpmFiltersUpgrade {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		private $org_last_version = 10;
		private $plm_last_version = 50;

		/*
		 * Instantiated by WpssoOpmFilters->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'upgraded_options'    => 2,
				'upgraded_md_options' => 1,
			) );
		}

		public function filter_upgraded_options( array $opts, $defs ) {

			if ( ! is_admin() ) {

				return $opts;
			}

			/*
			 * Get the current options version number for checks to follow.
			 */
			$prev_version = $this->p->opt->get_version( $opts, 'wpssoorg' );	// Returns 'opt_version'.

			/*
			 * WPSSO ORG may have been uninstalled and its 'opt_version' value may be missing from the options array,
			 * so check for any previous version, including 0 (ie. no 'opt_version').
			 */
			if ( $prev_version <= $this->org_last_version ) {

				$opts = $this->convert_multi_opts_to_post( $opts, $opt_prefix = 'org', $md_prefix = 'org', WPSSOOPM_ORG_POST_TYPE );
				$opts = $this->convert_numeric_org_ids( $opts );

				/*
				 * Set the WPSSO ORG 'opt_version' - whether the add-on is/was active or not - so we don't try to
				 * convert its options again.
				 */
				$this->p->opt->set_version( $opts, 'wpssoorg', $this->org_last_version + 1 );
			}

			/*
			 * Get the current options version number for checks to follow.
			 */
			$prev_version = $this->p->opt->get_version( $opts, 'wpssoplm' );	// Returns 'opt_version'.

			/*
			 * WPSSO PLM may have been uninstalled and its 'opt_version' value may be missing from the options array,
			 * so check for any previous version, including 0 (ie. no 'opt_version').
			 */
			if ( $prev_version <= $this->plm_last_version ) {

				$opts = $this->convert_multi_opts_to_post( $opts, $opt_prefix = 'plm_place', $md_prefix = 'place', WPSSOOPM_PLACE_POST_TYPE );
				$opts = $this->convert_numeric_place_ids( $opts );

				/*
				 * Set the WPSSO PLM 'opt_version' - whether the add-on is/was active or not - so we don't try to
				 * convert its options again.
				 */
				$this->p->opt->set_version( $opts, 'wpssoplm', $this->plm_last_version + 1 );
			}

			return $opts;
		}

		public function filter_upgraded_md_options( array $md_opts ) {

			/*
			 * Get the current options version number for checks to follow.
			 */
			$prev_version = $this->p->opt->get_version( $md_opts, 'wpssoorg' );	// Returns 'opt_version'.

			/*
			 * WPSSO ORG may have been uninstalled and its 'opt_version' value may be missing from the options array,
			 * so check for any previous version, including 0 (ie. no 'opt_version').
			 */
			if ( $prev_version > 0 && $prev_version <= $this->org_last_version ) {

				$md_opts = $this->convert_numeric_org_ids( $md_opts );

				/*
				 * Set the WPSSO ORG 'opt_version' - whether the add-on is/was active or not - so we don't try to
				 * convert its options again.
				 */
				$this->p->opt->set_version( $md_opts, 'wpssoorg', $this->org_last_version + 1 );
			}

			/*
			 * Get the current options version number for checks to follow.
			 */
			$prev_version = $this->p->opt->get_version( $md_opts, 'wpssoplm' );	// Returns 'opt_version'.

			/*
			 * WPSSO PLM may have been uninstalled and its 'opt_version' value may be missing from the options array,
			 * so check for any previous version, including 0 (ie. no 'opt_version').
			 */
			if ( $prev_version > 0 && $prev_version <= $this->plm_last_version ) {

				foreach ( SucomUtilOptions::get_opts_begin( $md_opts, 'plm_place_' ) as $md_key => $val ) {

					$converted_key = preg_replace( '/^plm_place_/', 'place_', $md_key );

					$md_opts[ $converted_key ] = $val;

					unset( $md_opts[ $md_key ] );
				}

				$md_opts = $this->convert_numeric_place_ids( $md_opts );

				/*
				 * Set the WPSSO PLM 'opt_version' - whether the add-on is/was active or not - so we don't try to
				 * convert its options again.
				 */
				$this->p->opt->set_version( $md_opts, 'wpssoplm', $this->plm_last_version + 1 );
			}

			return $md_opts;
		}

		private function convert_multi_opts_to_post( $opts, $opt_prefix, $md_prefix, $post_type ) {

			$opt_prefix_names = SucomUtilOptions::get_key_values_multi( $opt_prefix . '_name', $opts, $add_none = false );

			foreach ( $opt_prefix_names as $id => $name ) {

				$deprecated_rexp = '/^' . $opt_prefix . '_(.*)_' . $id . '([#:].*)?$/';
				$deprecated_opts = SucomUtil::preg_grep_keys( $deprecated_rexp, $opts, $invert = false, $replace = '$1' );
				$converted_opts  = array();

				foreach ( $deprecated_opts as $key_part => $val ) {

					$opt_key = $opt_prefix . '_' . $key_part . '_' . $id;
					$md_key  = $md_prefix . '_' . $key_part;

					$converted_opts[ $md_key ] = SucomUtilOptions::get_key_value( $opt_key, $opts );
				}

				if ( empty( $converted_opts[ $md_prefix . '_name' ] ) ) {	// Just in case.

					continue;
				}

				$post_id   = 0;
				$post_name = $converted_opts[ $md_prefix . '_name' ];
				$post_desc = isset( $converted_opts[ $md_prefix . '_desc' ] ) ? $converted_opts[ $md_prefix . '_desc' ] : '';

				/*
				 * Just in case, check if this organization / place has already been converted, and if so,
				 * then update the existing post ID instead of creating a new one.
				 */
				if ( ! empty( $opts[ 'opm_' . $md_prefix . '_' . $id . '_post_id' ] ) ) {

					$post_id = $opts[ 'opm_' . $md_prefix . '_' . $id . '_post_id' ];
				}

				$post_id = wp_insert_post( array(	// Returns a post ID on success.
					'ID'           => $post_id,	// 0 or existing post ID.
					'post_title'   => $post_name,
					'post_content' => $post_desc,
					'post_type'    => $post_type,
					'post_status'  => 'publish',
					'meta_input'   => array( WPSSO_META_NAME => $converted_opts ),
				) );

				/*
				 * If successful, save the post ID and issue a notice about the update.
				 */
				if ( is_numeric( $post_id ) ) {

					$opts[ 'opm_' . $md_prefix . '_' . $id . '_post_id' ] = $post_id;

					/*
					 * Remove the deprecated options using the same regular expression used to find them.
					 */
					$opts = SucomUtil::preg_grep_keys( $deprecated_rexp, $opts, $invert = true );

					/*
					 * Just in case - save the settings now to prevent competing conversions.
					 */
					update_option( WPSSO_OPTIONS_NAME, $opts );

					$post_type_obj  = get_post_type_object( $post_type );
					$post_type_name = $post_type_obj->labels->singular_name;

					$notice_msg = sprintf( __( '%1$s "%2$s" ID #%3$s from the plugin settings has been converted to %4$s post type ID #%5$s.',
						'wpsso-organization-place' ), $post_type_name, $post_name, $id, $post_type, $post_id );

					$this->p->notice->upd( $notice_msg );
				}
			}

			return $opts;
		}

		private function convert_numeric_org_ids( $opts ) {

			foreach ( array(
				'schema_pub_org_id',		// Publisher Org.
				'schema_prov_org_id',		// Provider Org.
				'schema_movie_prodco_org_id',
				'schema_event_organizer_org_id',
				'schema_event_performer_org_id',
				'schema_job_hiring_org_id',
				'schema_organization_id',
			) as $opt_key ) {

				if ( isset( $opts[ $opt_key ] ) && is_numeric( $opts[ $opt_key ] ) ) {

					$org_id = $opts[ $opt_key ];

					if ( isset( $this->p->options[ 'opm_org_' . $org_id . '_post_id' ] ) ) {

						$post_id = $this->p->options[ 'opm_org_' . $org_id . '_post_id' ];

						/*
						 * Convert the numberic ID to an organization post type ID.
						 */
						$opts[ $opt_key ] = 'org-' . $post_id;
					}
				}
			}

			return $opts;
		}

		private function convert_numeric_place_ids( $opts ) {

			foreach ( array(
				'org_place_id',
				'site_org_place_id',
				'schema_event_location_id',
				'schema_job_location_id',
				'schema_place_id',
			) as $opt_key ) {

				if ( isset( $opts[ $opt_key ] ) && is_numeric( $opts[ $opt_key ] ) ) {

					$place_id = $opts[ $opt_key ];

					if ( isset( $this->p->options[ 'opm_place_' . $place_id . '_post_id' ] ) ) {

						$post_id = $this->p->options[ 'opm_place_' . $place_id . '_post_id' ];

						/*
						 * Convert the numberic ID to a place post type ID.
						 */
						$opts[ $opt_key ] = 'place-' . $post_id;
					}
				}
			}

			return $opts;
		}
	}
}
