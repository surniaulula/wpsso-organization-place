<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2025 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmServiceFiltersOptions' ) ) {

	class WpssoOpmServiceFiltersOptions {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/*
		 * Instantiated by WpssoOpmFiltersOptions->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'get_service_options'             => 3,
				'get_post_defaults'               => 3,
				'get_post_options'                => 3,
				'save_post_options'               => 3,
				'option_type'                     => 2,
				'plugin_upgrade_advanced_exclude' => 1,
			) );
		}

		public function filter_get_service_options( $service_opts, $mod, $service_id ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( false === $service_opts ) {	// First come, first served.

				if ( 0 === strpos( $service_id, 'service-' ) ) {

					$service_opts = WpssoOpmService::get_id( $service_id, $mod );
				}
			}

			return $service_opts;
		}

		public function filter_get_post_defaults( array $md_defs, $post_id, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			switch ( $mod[ 'post_type' ] ) {

				case WPSSOOPM_SERVICE_POST_TYPE:

					$md_defs = array_merge( $md_defs, $this->p->cf[ 'opt' ][ 'service_md_defaults' ] );

					$md_defs[ 'service_prov_org_id' ]    = $this->p->options[ 'schema_def_serv_prov_org_id' ];
					$md_defs[ 'service_prov_person_id' ] = $this->p->options[ 'schema_def_serv_prov_person_id' ];

					break;
			}

			return $md_defs;
		}

		public function filter_get_post_options( array $md_opts, $post_id, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			switch ( $mod[ 'post_type' ] ) {

				case WPSSOOPM_SERVICE_POST_TYPE:

					/*
					 * Check if this organization ID is in some default options.
					 */
					$service_id = 'service-' . $mod[ 'id' ];

					foreach ( array(
						'service_is' => $this->p->cf[ 'form' ][ 'service_is_defaults' ],
					) as $opt_prefix => $is_defaults ) {

						foreach ( $is_defaults as $opts_key => $opts_label ) {

							$md_key = $opt_prefix . '_' . $opts_key;

							if ( isset( $this->p->options[ $opts_key ] ) && $service_id === $this->p->options[ $opts_key ] ) {

								$md_opts[ $md_key ] = 1;

							} else $md_opts[ $md_key ] = 0;

							if ( $this->p->debug->enabled ) {

								$this->p->debug->log( 'setting ' . $md_key . ' = ' . $md_opts[ $md_key ] );
							}
						}
					}

					break;

				case WPSSOOPM_ORG_POST_TYPE:
				case WPSSOOPM_PLACE_POST_TYPE:

					break;	// Nothing to do.

				default:

					$service_id   = isset( $md_opts[ 'schema_service_id' ] ) ? $md_opts[ 'schema_service_id' ] : 'none';
					$service_type = false;

					if ( 0 === strpos( $service_id, 'service-' ) ) {

						$service_type = WpssoOpmService::get_id( $service_id, $mod, 'service_schema_type' );
					}

					if ( $service_type ) {

						$md_opts[ 'og_type' ]                         = 'article';
						$md_opts[ 'og_type:disabled' ]                = true;
						$md_opts[ 'schema_type' ]                     = $service_type;
						$md_opts[ 'schema_type:disabled' ]            = true;
						$md_opts[ 'schema_organization_id' ]          = 'none';
						$md_opts[ 'schema_organization_id:disabled' ] = true;
						$md_opts[ 'schema_place_id' ]                 = 'none';
						$md_opts[ 'schema_place_id:disabled' ]        = true;
					}

					break;
			}

			return $md_opts;
		}

		public function filter_save_post_options( array $md_opts, $post_id, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			switch ( $mod[ 'post_type' ] ) {

				case WPSSOOPM_SERVICE_POST_TYPE:

					$service_id = 'service-' . $mod[ 'id' ];
					$md_defs    = $this->filter_get_post_defaults( array(), $post_id, $mod );
					$md_opts    = array_merge( $md_defs, $md_opts );

					if ( empty( $md_opts[ 'service_name' ] ) ) {	// Just in case.

						$md_opts[ 'service_name' ] = sprintf( _x( 'Service #%d', 'option value', 'wpsso-organization-place' ), $post_id );
					}

					/*
					 * Always keep the post title, slug, and content updated.
					 */
					SucomUtilWP::raw_update_post_title_content( $post_id, $md_opts[ 'service_name' ], $md_opts[ 'service_desc' ] );

					/*
					 * Check if some default options need to be updated.
					 */
					foreach ( array(
						'service_is' => $this->p->cf[ 'form' ][ 'service_is_defaults' ],
					) as $opt_prefix => $is_defaults ) {

						foreach ( $is_defaults as $opts_key => $opts_label ) {

							$md_key = $opt_prefix . '_' . $opts_key;

							if ( empty( $md_opts[ $md_key ] ) ) {	// Checkbox is unchecked.

								if ( $service_id === $this->p->options[ $opts_key ] ) {	// Maybe remove the existing organization ID.

									$this->p->options[ $opts_key ] = 'none';

									SucomUtilWP::update_options_key( WPSSO_OPTIONS_NAME, $opts_key, 'none' );	// Save changes.
								}

							} elseif ( $service_id !== $this->p->options[ $opts_key ] ) {	// Maybe change the existing organization ID.

								$this->p->options[ $opts_key ] = $service_id;

								SucomUtilWP::update_options_key( WPSSO_OPTIONS_NAME, $opts_key, $service_id );	// Save changes.
							}

							unset( $md_opts[ $md_key ] );
						}
					}

					$mod[ 'obj' ]->md_keys_multi_renum( $md_opts );

					break;
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

			} elseif ( 0 !== strpos( $base_key, 'service_' ) ) {	// Nothing to do.

				return $type;
			}

			switch ( $base_key ) {

				case 'service_name':
				case 'service_name_alt':
				case 'service_desc':
				case 'service_offer_catalog':		// Offer Catalog Name.
				case 'service_offer_catalog_text':	// Offer Catalog Description.

					return 'ok_blank';

				case 'service_place_id':
				case 'service_schema_type':
				case 'service_prov_org_id':
				case 'service_prov_person_id':

					return 'not_blank';

				case 'service_offer_catalog_url':	// Offer Catalog URL.

					return 'url';

				case ( 0 === strpos( $base_key, 'service_is_' ) ? true : false ):

					return 'checkbox';
			}

			return $type;
		}

		public function filter_plugin_upgrade_advanced_exclude( $adv_exclude ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			foreach ( $this->p->cf[ 'form' ][ 'service_is_defaults' ] as $opts_key => $opts_label ) {

				$adv_exclude[] = $opts_key;
			}

			return $adv_exclude;
		}
	}
}
