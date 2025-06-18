<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2025 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmContactFiltersOptions' ) ) {

	class WpssoOpmContactFiltersOptions {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/*
		 * Instantiated by WpssoOpmFiltersOptions->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'get_contact_options'             => 3,
				'get_post_defaults'               => 3,
				'get_post_options'                => 3,
				'save_post_options'               => 3,
				'option_type'                     => 2,
				'plugin_upgrade_advanced_exclude' => 1,
			) );
		}

		public function filter_get_contact_options( $contact_opts, $mod, $contact_id ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( false === $contact_opts ) {	// First come, first served.

				if ( 0 === strpos( $contact_id, 'contact-' ) ) {

					$contact_opts = WpssoOpmContact::get_id( $contact_id, $mod );
				}
			}

			return $contact_opts;
		}

		public function filter_get_post_defaults( array $md_defs, $post_id, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			switch ( $mod[ 'post_type' ] ) {

				case WPSSOOPM_CONTACT_POST_TYPE:

					$md_defs = array_merge( $md_defs, $this->p->cf[ 'opt' ][ 'contact_md_defaults' ] );

					break;
			}

			return $md_defs;
		}

		public function filter_get_post_options( array $md_opts, $post_id, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			switch ( $mod[ 'post_type' ] ) {

				case WPSSOOPM_CONTACT_POST_TYPE:

					/*
					 * Check if this contact ID is in some default options.
					 */
					$contact_id = 'contact-' . $mod[ 'id' ];

					foreach ( array(
						'contact_is' => $this->p->cf[ 'form' ][ 'contact_is_defaults' ],
					) as $opt_prefix => $is_defaults ) {

						foreach ( $is_defaults as $opts_key => $opts_label ) {

							$md_key = $opt_prefix . '_' . $opts_key;

							if ( isset( $this->p->options[ $opts_key ] ) && $contact_id === $this->p->options[ $opts_key ] ) {

								$md_opts[ $md_key ] = 1;

							} else $md_opts[ $md_key ] = 0;

							if ( $this->p->debug->enabled ) {

								$this->p->debug->log( 'setting ' . $md_key . ' = ' . $md_opts[ $md_key ] );
							}
						}
					}

				case WPSSOOPM_ORG_POST_TYPE:
				case WPSSOOPM_PLACE_POST_TYPE:

					break;	// Nothing to do.

				default:

					$contact_id   = isset( $md_opts[ 'schema_contact_id' ] ) ? $md_opts[ 'schema_contact_id' ] : 'none';
					$contact_type = false;

					if ( 0 === strpos( $contact_id, 'contact-' ) ) {

						$contact_type = WpssoOpmContact::get_id( $contact_id, $mod, 'contact_schema_type' );
					}

					if ( $contact_type ) {

						$md_opts[ 'og_type' ]                         = 'place';
						$md_opts[ 'og_type:disabled' ]                = true;
						$md_opts[ 'schema_organization_id' ]          = 'none';
						$md_opts[ 'schema_organization_id:disabled' ] = true;
						$md_opts[ 'schema_place_id' ]                 = 'none';
						$md_opts[ 'schema_place_id:disabled' ]        = true;
						$md_opts[ 'schema_type' ]                     = $contact_type;
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

				case WPSSOOPM_CONTACT_POST_TYPE:

					$contact_id  = 'contact-' . $mod[ 'id' ];
					$md_defs = $this->filter_get_post_defaults( array(), $post_id, $mod );
					$md_opts = array_merge( $md_defs, $md_opts );

					if ( empty( $md_opts[ 'contact_name' ] ) ) {	// Just in case.

						$md_opts[ 'contact_name' ] = sprintf( _x( 'Contact Point #%d', 'option value', 'wpsso-organization-place' ), $post_id );
					}

					/*
					 * Always keep the post title, slug, and content updated.
					 */
					SucomUtilWP::raw_update_post_title_content( $post_id, $md_opts[ 'contact_name' ], $md_opts[ 'contact_desc' ] );

					/*
					 * Check if some default options need to be updated.
					 */
					foreach ( array(
						'contact_is' => $this->p->cf[ 'form' ][ 'contact_is_defaults' ],
					) as $opt_prefix => $is_defaults ) {

						foreach ( $is_defaults as $opts_key => $opts_label ) {

							$md_key = $opt_prefix . '_' . $opts_key;

							if ( empty( $md_opts[ $md_key ] ) ) {	// Checkbox is unchecked.

								if ( $contact_id === $this->p->options[ $opts_key ] ) {	// Maybe remove the existing contact ID.

									$this->p->options[ $opts_key ] = 'none';

									SucomUtilWP::update_options_key( WPSSO_OPTIONS_NAME, $opts_key, 'none' );	// Save changes.
								}

							} elseif ( $contact_id !== $this->p->options[ $opts_key ] ) {	// Maybe change the existing contact ID.

								$this->p->options[ $opts_key ] = $contact_id;

								SucomUtilWP::update_options_key( WPSSO_OPTIONS_NAME, $opts_key, $contact_id );	// Save changes.
							}

							unset( $md_opts[ $md_key ] );
						}
					}

					$mod[ 'obj' ]->md_keys_multi_renum( $md_opts );

					WpssoOpmContact::check_image_sizes( $md_opts );

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

			} elseif ( 0 !== strpos( $base_key, 'contact_' ) ) {	// Nothing to do.

				return $type;
			}

			switch ( $base_key ) {

				case 'contact_name':
				case 'contact_name_alt':
				case 'contact_desc':
				case 'contact_phone':
				case 'contact_fax':
				case 'contact_email':
				case 'contact_street_address':
				case 'contact_city':
				case 'contact_state':
				case 'contact_postal_code':
				case 'contact_zipcode':
				case 'contact_price_range':

					return 'ok_blank';

				case 'contact_country':
				case 'contact_schema_type':

					return 'not_blank';

				case 'contact_po_box_number':

					return 'blank_num';

				case ( preg_match( '/^contact_(day_[a-z]+|midday)_(open|close)$/', $base_key ) ? true : false ):

					return 'time';	// Empty or 'none' string, or time as hh:mm or hh:mm:ss.

				case ( preg_match( '/^contact_season_(from|to)_date$/', $base_key ) ? true : false ):

					return 'date';	// Empty or 'none' string, or date as yyyy-mm-dd.

				case ( 0 === strpos( $base_key, 'contact_is_' ) ? true : false ):

					return 'checkbox';
			}

			return $type;
		}

		public function filter_plugin_upgrade_advanced_exclude( $opts ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			foreach ( $this->p->cf[ 'form' ][ 'contact_is_defaults' ] as $opts_key => $opts_label ) {

				$opts[] = $opts_key;
			}

			return $opts;
		}
	}
}
