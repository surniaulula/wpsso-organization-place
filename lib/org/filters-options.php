<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmOrgFiltersOptions' ) ) {

	class WpssoOpmOrgFiltersOptions {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/*
		 * Instantiated by WpssoOpmFiltersOptions->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'get_organization_options'        => 3,
				'get_post_defaults'               => 3,
				'get_post_options'                => 3,
				'save_post_options'               => 3,
				'option_type'                     => 2,
				'plugin_upgrade_advanced_exclude' => 1,
			) );
		}

		public function filter_get_organization_options( $org_opts, $mod, $org_id ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( false === $org_opts ) {	// First come, first served.

				if ( 0 === strpos( $org_id, 'org-' ) ) {

					$org_opts = WpssoOpmOrg::get_id( $org_id, $mod );
				}
			}

			return $org_opts;
		}

		public function filter_get_post_defaults( array $md_defs, $post_id, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$org_id = 'org-' . $mod[ 'id' ];

			$md_defs = array_merge( $md_defs, $this->p->cf[ 'opt' ][ 'org_md_defaults' ] );

			/*
			 * Check if this organization ID is in some default options.
			 */
			foreach ( $this->p->cf[ 'form' ][ 'org_is_defaults' ] as $opts_key => $opts_label ) {

				if ( isset( $this->p->options[ $opts_key ] ) && $org_id === $this->p->options[ $opts_key ] ) {

					$md_defs[ 'org_is_' . $opts_key ] = 1;

				} else $md_defs[ 'org_is_' . $opts_key ] = 0;
			}

			return $md_defs;
		}

		public function filter_get_post_options( array $md_opts, $post_id, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$org_id = isset( $md_opts[ 'schema_organization_id' ] ) ? $md_opts[ 'schema_organization_id' ] : 'none';

			$org_type = false;

			if ( 0 === strpos( $org_id, 'org-' ) ) {

				$org_type = WpssoOpmOrg::get_id( $org_id, $mod, 'org_schema_type' );
			}

			if ( $org_type ) {

				$md_opts[ 'og_type' ]          = 'website';
				$md_opts[ 'og_type:disabled' ] = true;

				$md_opts[ 'schema_type' ]          = $org_type;
				$md_opts[ 'schema_type:disabled' ] = true;

				$md_opts[ 'schema_place_id' ]          = 'none';
				$md_opts[ 'schema_place_id:disabled' ] = true;
			}

			return $md_opts;
		}

		public function filter_save_post_options( array $md_opts, $post_id, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( WPSSOOPM_ORG_POST_TYPE === $mod[ 'post_type' ] ) {

				$org_id = 'org-' . $mod[ 'id' ];

				$md_defs = $this->filter_get_post_defaults( array(), $post_id, $mod );

				$md_opts = array_merge( $md_defs, $md_opts );

				if ( empty( $md_opts[ 'org_name' ] ) ) {	// Just in case.

					$md_opts[ 'org_name' ] = sprintf( _x( 'Organization #%d', 'option value', 'wpsso-organization-place' ), $post_id );
				}

				/*
				 * Always keep the post title, slug, and content updated.
				 */
				SucomUtilWP::raw_update_post_title_content( $post_id, $md_opts[ 'org_name' ], $md_opts[ 'org_desc' ] );

				/*
				 * Check if some default options need to be updated.
				 */
				foreach ( $this->p->cf[ 'form' ][ 'org_is_defaults' ] as $opts_key => $opts_label ) {

					if ( empty( $md_opts[ 'org_is_' . $opts_key ] ) ) {	// Checkbox is unchecked.

						if ( $org_id === $this->p->options[ $opts_key ] ) {	// Maybe remove the existing organization ID.

							$this->p->options[ $opts_key ] = 'none';

							SucomUtilWP::update_options_key( WPSSO_OPTIONS_NAME, $opts_key, 'none' );	// Save changes.
						}

					} elseif ( $org_id !== $this->p->options[ $opts_key ] ) {	// Maybe change the existing organization ID.

						$this->p->options[ $opts_key ] = $org_id;

						SucomUtilWP::update_options_key( WPSSO_OPTIONS_NAME, $opts_key, $org_id );	// Save changes.
					}

					unset( $md_opts[ 'org_is_' . $opts_key ] );
				}

				$mod[ 'obj' ]->md_keys_multi_renum( $md_opts );

				WpssoOpmOrg::check_org_image_sizes( $md_opts );

			} elseif ( WPSSOOPM_PLACE_POST_TYPE === $mod[ 'post_type' ] ) {

				// Nothing to do.
			}

			return $md_opts;
		}

		/*
		 * Return the sanitation type for a given option key.
		 */
		public function filter_option_type( $type, $base_key ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( ! empty( $type ) ) {	// Return early if we already have a type.

				return $type;

			} elseif ( 0 !== strpos( $base_key, 'org_' ) ) {	// Nothing to do.

				return $type;
			}

			switch ( $base_key ) {

				case 'org_name':
				case 'org_name_alt':
				case 'org_desc':
				case 'org_award':

					return 'ok_blank';

				case 'org_place_id':
				case 'org_schema_type':

					return 'not_blank';

				case 'org_logo_url':
				case 'org_banner_url':

					return 'img_url';

				case 'org_url':
				case 'org_pub_principles_url':		// Publishing Principles URL.
				case 'org_corrections_policy_url':	// Corrections Policy URL.
				case 'org_diversity_policy_url':	// Diversity Policy URL.
				case 'org_ethics_policy_url':		// Ethics Policy URL.
				case 'org_fact_check_policy_url':	// Fact Checking Policy URL.
				case 'org_feedback_policy_url':		// Feedback Policy URL.
				case 'org_masthead_url':		// Masthead Page URL.
				case 'org_coverage_policy_url':		// Coverage Priorities Policy URL.
				case 'org_no_bylines_policy_url':	// No Bylines Policy URL.
				case 'org_sources_policy_url':		// Unnamed Sources Policy URL.
				case ( strpos( $base_key, '_url' ) && isset( $this->p->cf[ 'form' ][ 'social_accounts' ][ substr( $base_key, 4 ) ] ) ? true : false ):

					return 'url';

				case ( 0 === strpos( $base_key, 'org_is_' ) ? true : false ):

					return 'checkbox';
			}

			return $type;
		}

		public function filter_plugin_upgrade_advanced_exclude( $adv_exclude ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			foreach ( $this->p->cf[ 'form' ][ 'org_is_defaults' ] as $opts_key => $opts_label ) {

				$adv_exclude[] = $opts_key;
			}

			return $adv_exclude;
		}
	}
}
