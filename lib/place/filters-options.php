<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2025 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmPlaceFiltersOptions' ) ) {

	class WpssoOpmPlaceFiltersOptions {

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
				'get_place_options'               => 3,
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

				if ( 0 === strpos( $org_id, 'place-' ) ) {

					$org_opts = WpssoOpmOrg::get_id( $org_id, $mod, $opt_key = false, $post_id_prefix = 'place' );

					$org_opts[ 'org_name' ]        = WpssoOpmPlace::get_id( $org_id, $mod, $opt_key = 'place_name' );
					$org_opts[ 'org_name_alt' ]    = WpssoOpmPlace::get_id( $org_id, $mod, $opt_key = 'place_name_alt' );
					$org_opts[ 'org_desc' ]        = WpssoOpmPlace::get_id( $org_id, $mod, $opt_key = 'place_desc' );
					$org_opts[ 'org_schema_type' ] = WpssoOpmPlace::get_id( $org_id, $mod, $opt_key = 'place_schema_type' );
					$org_opts[ 'org_place_id' ]    = 'none';	// Just in case.
				}
			}

			return $org_opts;
		}

		public function filter_get_place_options( $place_opts, $mod, $place_id ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( false === $place_opts ) {	// First come, first served.

				if ( 0 === strpos( $place_id, 'place-' ) ) {

					$place_opts = WpssoOpmPlace::get_id( $place_id, $mod );
				}
			}

			return $place_opts;
		}

		public function filter_get_post_defaults( array $md_defs, $post_id, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			switch ( $mod[ 'post_type' ] ) {

				case WPSSOOPM_PLACE_POST_TYPE:

					$md_defs = array_merge( $md_defs, $this->p->cf[ 'opt' ][ 'place_md_defaults' ] );

					$md_defs[ 'place_schema_type' ] = $this->p->options[ 'schema_def_place_schema_type' ];
					$md_defs[ 'place_country' ]     = $this->p->options[ 'schema_def_place_country' ];
					$md_defs[ 'place_timezone' ]    = $this->p->options[ 'schema_def_place_timezone' ];

					break;
			}

			return $md_defs;
		}

		public function filter_get_post_options( array $md_opts, $post_id, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			switch ( $mod[ 'post_type' ] ) {

				case WPSSOOPM_PLACE_POST_TYPE:

					/*
					 * Check if this place ID is in some default options.
					 */
					$place_id = 'place-' . $mod[ 'id' ];

					/*
					 * If the default place schema type is an organization, check the organization default options as well.
					 */
					$is_org_type = empty( $md_opts[ 'place_schema_type' ] ) ?	// Just in case
						$this->p->schema->is_schema_type_child( $this->p->options[ 'schema_def_place_schema_type' ], 'organization' ) :
						$this->p->schema->is_schema_type_child( $md_opts[ 'place_schema_type' ], 'organization' );

					foreach ( array(
						'org_is'   => $is_org_type ? $this->p->cf[ 'form' ][ 'org_is_defaults' ] : array(),
						'place_is' => $this->p->cf[ 'form' ][ 'place_is_defaults' ],
					) as $opt_prefix => $is_defaults ) {

						foreach ( $is_defaults as $opts_key => $opts_label ) {

							$md_key = $opt_prefix . '_' . $opts_key;

							if ( isset( $this->p->options[ $opts_key ] ) && $place_id === $this->p->options[ $opts_key ] ) {

								$md_opts[ $md_key ] = 1;

							} else $md_opts[ $md_key ] = 0;

							if ( $this->p->debug->enabled ) {

								$this->p->debug->log( 'setting ' . $md_key . ' = ' . $md_opts[ $md_key ] );
							}
						}
					}

					break;

				case WPSSOOPM_CONTACT_POST_TYPE:
				case WPSSOOPM_ORG_POST_TYPE:

					break;	// Nothing to do.

				default:

					$place_id   = isset( $md_opts[ 'schema_place_id' ] ) ? $md_opts[ 'schema_place_id' ] : 'none';
					$place_type = false;

					if ( 'custom' === $place_id ) {

						$place_type = empty( $md_opts[ 'place_schema_type' ] ) ?	// Just in case.
							$this->p->options[ 'schema_def_place_schema_type' ] :
							$md_opts[ 'place_schema_type' ];

					} elseif ( 0 === strpos( $place_id, 'place-' ) ) {

						$place_type = WpssoOpmPlace::get_id( $place_id, $mod, 'place_schema_type' );
					}

					if ( $place_type ) {

						$md_opts[ 'og_type' ]                         = 'place';
						$md_opts[ 'og_type:disabled' ]                = true;
						$md_opts[ 'schema_contact_id' ]               = 'none';
						$md_opts[ 'schema_contact_id:disabled' ]      = true;
						$md_opts[ 'schema_organization_id' ]          = 'none';
						$md_opts[ 'schema_organization_id:disabled' ] = true;
						$md_opts[ 'schema_type' ]                     = $place_type;
						$md_opts[ 'schema_type:disabled' ]            = true;
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

				case WPSSOOPM_PLACE_POST_TYPE:

					$place_id = 'place-' . $mod[ 'id' ];
					$md_defs  = $this->filter_get_post_defaults( array(), $post_id, $mod );
					$md_opts  = array_merge( $md_defs, $md_opts );

					if ( empty( $md_opts[ 'place_name' ] ) ) {	// Just in case.

						$md_opts[ 'place_name' ] = sprintf( _x( 'Place #%d', 'option value', 'wpsso-organization-place' ), $post_id );
					}

					/*
					 * Always keep the post title, slug, and content updated.
					 */
					SucomUtilWP::raw_update_post_title_content( $post_id, $md_opts[ 'place_name' ], $md_opts[ 'place_desc' ] );

					/*
					 * Check if some default options need to be updated.
					 *
					 * If the default place schema type is an organization, check the organization default options as well.
					 */
					$place_id    = 'place-' . $mod[ 'id' ];
					$is_org_type = $this->p->schema->is_schema_type_child( $md_opts[ 'place_schema_type' ], 'organization' );

					foreach ( array(
						'org_is'   => $is_org_type ? $this->p->cf[ 'form' ][ 'org_is_defaults' ] : array(),
						'place_is' => $this->p->cf[ 'form' ][ 'place_is_defaults' ],
					) as $opt_prefix => $is_defaults ) {

						foreach ( $is_defaults as $opts_key => $opts_label ) {

							$md_key = $opt_prefix . '_' . $opts_key;

							if ( empty( $md_opts[ $md_key ] ) ) {	// Checkbox is unchecked.

								if ( $place_id === $this->p->options[ $opts_key ] ) {	// Maybe remove the existing place ID.

									$this->p->options[ $opts_key ] = 'none';

									SucomUtilWP::update_options_key( WPSSO_OPTIONS_NAME, $opts_key, 'none' );	// Save changes.
								}

							} elseif ( $place_id !== $this->p->options[ $opts_key ] ) {	// Maybe change the existing place ID.

								$this->p->options[ $opts_key ] = $place_id;

								SucomUtilWP::update_options_key( WPSSO_OPTIONS_NAME, $opts_key, $place_id );	// Save changes.
							}

							unset( $md_opts[ $md_key ] );
						}
					}

					if ( $is_org_type ) {

						$mod[ 'obj' ]->md_keys_multi_renum( $md_opts );

						WpssoOpmOrg::check_image_sizes( $md_opts, $name_key = 'place_name' );
					}

					WpssoOpmPlace::check_image_sizes( $md_opts );

					break;

				case WPSSOOPM_CONTACT_POST_TYPE:
				case WPSSOOPM_ORG_POST_TYPE:

					break;	// Nothing to do.

				default:

					$place_id = isset( $md_opts[ 'schema_place_id' ] ) ? $md_opts[ 'schema_place_id' ] : 'none';

					if ( 'custom' !== $place_id ) {

						$md_opts = SucomUtil::preg_grep_keys( '/^(org|place)_/', $md_opts, $invert = true );
					}

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

			} elseif ( 0 !== strpos( $base_key, 'place_' ) ) {	// Nothing to do.

				return $type;
			}

			switch ( $base_key ) {

				case 'place_name':
				case 'place_name_alt':
				case 'place_desc':
				case 'place_phone':
				case 'place_fax':
				case 'place_email':
				case 'place_street_address':
				case 'place_city':
				case 'place_state':
				case 'place_postal_code':
				case 'place_zipcode':
				case 'place_price_range':
				case 'place_cuisine':

					return 'ok_blank';

				case 'place_country':
				case 'place_schema_type':

					return 'not_blank';

				case 'place_latitude':
				case 'place_longitude':
				case 'place_altitude':
				case 'place_service_radius':
				case 'place_po_box_number':

					return 'blank_num';

				case 'place_currencies_accepted':
				case 'place_payment_accepted':

					return 'csv_blank';

				case ( preg_match( '/^place_(day_[a-z]+|midday)_(open|close)$/', $base_key ) ? true : false ):

					return 'time';	// Empty or 'none' string, or time as hh:mm or hh:mm:ss.

				case ( preg_match( '/^place_season_(from|to)_date$/', $base_key ) ? true : false ):

					return 'date';	// Empty or 'none' string, or date as yyyy-mm-dd.

				case 'place_menu_url':

					return 'url';

				case 'place_order_urls':

					return 'csv_urls';

				case 'place_accept_res':
				case ( 0 === strpos( $base_key, 'place_is_' ) ? true : false ):

					return 'checkbox';
			}

			return $type;
		}

		public function filter_plugin_upgrade_advanced_exclude( $opts ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			foreach ( $this->p->cf[ 'form' ][ 'place_is_defaults' ] as $opts_key => $opts_label ) {

				$opts[] = $opts_key;
			}

			return $opts;
		}
	}
}
