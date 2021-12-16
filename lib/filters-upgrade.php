<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmFiltersUpgrade' ) ) {

	class WpssoOpmFiltersUpgrade {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		private $org_last_version = 9;
		private $plm_last_version = 49;

		/**
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

		public function filter_upgraded_options( $opts, $defs ) {

			/**
			 * Get the current options version number for checks to follow.
			 */
			$prev_version = $this->p->opt->get_version( $opts, 'wpssoorg' );	// Returns 'opt_version'.

			if ( $prev_version > 0 && $prev_version <= $this->org_last_version ) {

				$opts = $this->convert_multi_opts_to_post( $opts, $opt_prefix = 'org', $md_prefix = 'org', WPSSOOPM_ORG_POST_TYPE );

				$opts = $this->convert_numeric_org_ids( $opts );
			}

			/**
			 * Get the current options version number for checks to follow.
			 */
			$prev_version = $this->p->opt->get_version( $opts, 'wpssoplm' );	// Returns 'opt_version'.

			if ( $prev_version > 0 && $prev_version <= $this->plm_last_version ) {

				$opts = $this->convert_multi_opts_to_post( $opts, $opt_prefix = 'plm_place', $md_prefix = 'place', WPSSOOPM_PLACE_POST_TYPE );

				$opts = $this->convert_numeric_place_ids( $opts );
			}

			return $opts;
		}

		public function filter_upgraded_md_options( $md_opts ) {

			/**
			 * Get the current options version number for checks to follow.
			 */
			$prev_version = $this->p->opt->get_version( $md_opts, 'wpssoorg' );	// Returns 'opt_version'.

			if ( $prev_version > 0 && $prev_version <= $this->org_last_version ) {

				$md_opts = $this->convert_numeric_org_ids( $md_opts );
			}

			/**
			 * Get the current options version number for checks to follow.
			 */
			$prev_version = $this->p->opt->get_version( $md_opts, 'wpssoplm' );	// Returns 'opt_version'.

			if ( $prev_version > 0 && $prev_version <= $this->plm_last_version ) {

				foreach ( SucomUtil::get_opts_begin( 'plm_place_', $md_opts ) as $md_key => $val ) {

					$converted_key = preg_replace( '/^plm_place_/', 'place_', $md_key );

					$md_opts[ $converted_key ] = $val;

					unset( $md_opts[ $md_key ] );
				}

				$md_opts = $this->convert_numeric_place_ids( $md_opts );
			}

			return $md_opts;
		}

		private function convert_multi_opts_to_post( $opts, $opt_prefix, $md_prefix, $post_type ) {

			$names = SucomUtil::get_multi_key_locale( $opt_prefix . '_name', $opts, $add_none = false );

			foreach ( $names as $id => $name ) {

				$deprecated_opts = SucomUtil::preg_grep_keys( '/^' . $opt_prefix . '_(.*)_' . $id . '([#:].*)?$/', $opts, $invert = false, $replace = '$1' );
				$converted_opts  = array();

				foreach ( $deprecated_opts as $key_part => $val ) {

					$md_key  = $md_prefix . '_' . $key_part;
					$opt_key = $opt_prefix . '_' . $key_part . '_' . $id;

					$converted_opts[ $md_key ] = SucomUtil::get_key_value( $opt_key, $opts );
				}

				if ( ! empty( $converted_opts[ $md_prefix . '_name' ] ) ) {	// Just in case.

					/**
					 * Just in case, check if this organization / place has already been converted, and if so,
					 * then simply strip its options.
					 */
					if ( ! empty( $opts[ 'opm_' . $md_prefix . '_' . $id . '_post_id' ] ) ) {

						$opts = SucomUtil::preg_grep_keys( '/^' . $opt_prefix . '_.*_' . $id . '([#:].*)?$/', $opts, $invert = true );

						continue;	// Get the next $id => $name pair.
					}

					$post_name = $converted_opts[ $md_prefix . '_name' ];

					$post_id = wp_insert_post( array(	// Returns a post ID on success.
						'post_title'  => $post_name,
						'post_type'   => $post_type,
						'post_status' => 'publish',
						'meta_input'  => array( WPSSO_META_NAME => $converted_opts ),
					) );

					/**
					 * If successful, save the post ID and issue a notice about the update.
					 */
					if ( is_numeric( $post_id ) ) {

						$opts[ 'opm_' . $md_prefix . '_' . $id . '_post_id' ] = $post_id;

						$opts = SucomUtil::preg_grep_keys( '/^' . $opt_prefix . '_.*_' . $id . '([#:].*)?$/', $opts, $invert = true );

						$post_type_obj  = get_post_type_object( $post_type );
						$post_type_name = $post_type_obj->labels->singular_name;

						$notice_msg = sprintf( __( '%1$s "%2$s" ID %3$d from the plugin settings has been converted to post type %4$s ID %5$d.',
							'wpsso-organization-place' ), $post_type_name, $post_name, $id, $post_type, $post_id );

						$this->p->notice->upd( $notice_msg );
					}
				}
			}

			return $opts;
		}

		private function convert_numeric_org_ids( $opts ) {

			foreach ( array(
				'org_place_id',
				'schema_pub_org_id',
				'schema_prov_org_id',
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

						$opts[ $opt_key ] = 'org-' . $post_id;
					}
				}
			}

			return $opts;
		}

		private function convert_numeric_place_ids( $opts ) {

			foreach ( array(
				'site_org_place_id',
				'schema_event_location_id',
				'schema_job_location_id',
				'schema_place_id',
			) as $opt_key ) {

				if ( isset( $opts[ $opt_key ] ) && is_numeric( $opts[ $opt_key ] ) ) {

					$place_id = $opts[ $opt_key ];

					if ( isset( $this->p->options[ 'opm_place_' . $place_id . '_post_id' ] ) ) {

						$post_id = $this->p->options[ 'opm_place_' . $place_id . '_post_id' ];

						$opts[ $opt_key ] = 'place-' . $post_id;
					}
				}
			}

			return $opts;
		}
	}
}
