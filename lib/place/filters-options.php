<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
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
				'get_place_options'               => 3,
				'get_post_defaults'               => 3,
				'get_post_options'                => 3,
				'save_post_options'               => 3,
				'option_type'                     => 2,
				'plugin_upgrade_advanced_exclude' => 1,
			) );
		}

		public function filter_get_place_options( $place_opts, $mod, $place_id ) {

			if ( false === $place_opts ) {	// First come, first served.

				if ( 0 === strpos( $place_id, 'place-' ) ) {

					$place_opts = WpssoOpmPlace::get_id( $place_id, $mod );
				}
			}

			return $place_opts;
		}

		public function filter_get_post_defaults( array $md_defs, $post_id, array $mod ) {

			$place_id = 'place-' . $mod[ 'id' ];

			$md_defs = array_merge( $md_defs, $this->p->cf[ 'opt' ][ 'place_md_defaults' ] );

			/*
			 * Since WPSSO OPM v1.11.0.
			 */
			$md_defs[ 'place_schema_type' ] = $this->p->options[ 'schema_def_place_schema_type' ];
			$md_defs[ 'place_country' ]     = $this->p->options[ 'schema_def_place_country' ];
			$md_defs[ 'place_timezone' ]    = $this->p->options[ 'schema_def_place_timezone' ];

			/*
			 * Check if this place ID is in some default options.
			 */
			foreach ( $this->p->cf[ 'form' ][ 'place_is_defaults' ] as $opts_key => $opts_label ) {

				$md_defs[ 'place_is_' . $opts_key ] = $place_id === $this->p->options[ $opts_key ] ? 1 : 0;
			}

			return $md_defs;
		}

		public function filter_get_post_options( array $md_opts, $post_id, array $mod ) {

			$place_id = isset( $md_opts[ 'schema_place_id' ] ) ? $md_opts[ 'schema_place_id' ] : 'none';

			$place_type = false;

			if ( 'custom' === $place_id ) {

				$def_type = $this->p->cf[ 'opt' ][ 'place_md_defaults' ][ 'place_schema_type' ];

				$place_type = empty( $md_opts[ 'place_schema_type' ] ) ? $def_type : $md_opts[ 'place_schema_type' ];

			} elseif ( 0 === strpos( $place_id, 'place-' ) ) {

				$place_type = WpssoOpmPlace::get_id( $place_id, $mod, 'place_schema_type' );
			}

			if ( $place_type ) {

				$md_opts[ 'og_type' ]          = 'place';
				$md_opts[ 'og_type:disabled' ] = true;

				$md_opts[ 'schema_type' ]          = $place_type;
				$md_opts[ 'schema_type:disabled' ] = true;

				$md_opts[ 'schema_organization_id' ]          = 'none';
				$md_opts[ 'schema_organization_id:disabled' ] = true;
			}

			return $md_opts;
		}

		public function filter_save_post_options( array $md_opts, $post_id, array $mod ) {

			if ( WPSSOOPM_PLACE_POST_TYPE === $mod[ 'post_type' ] ) {

				$place_id = 'place-' . $mod[ 'id' ];

				if ( empty( $md_opts[ 'place_name' ] ) ) {	// Just in case.

					$md_opts[ 'place_name' ] = sprintf( _x( 'Place #%d', 'option value', 'wpsso-organization-place' ), $post_id );
				}

				if ( ! isset( $md_opts[ 'place_desc' ] ) ) {	// Just in case.

					$md_opts[ 'place_desc' ] = '';
				}

				/*
				 * Always keep the post title, slug, and content updated.
				 */
				SucomUtilWP::raw_update_post_title_content( $post_id, $md_opts[ 'place_name' ], $md_opts[ 'place_desc' ] );

				/*
				 * Check if some default options need to be updated.
				 */
				foreach ( $this->p->cf[ 'form' ][ 'place_is_defaults' ] as $opts_key => $opts_label ) {

					if ( empty( $md_opts[ 'place_is_' . $opts_key ] ) ) {	// Checkbox is unchecked.

						if ( $place_id === $this->p->options[ $opts_key ] ) {	// Maybe remove the existing place ID.

							SucomUtilWP::update_options_key( WPSSO_OPTIONS_NAME, $opts_key, 'none' );
						}

					} elseif ( $place_id !== $this->p->options[ $opts_key ] ) {	// Maybe change the existing place ID.

						SucomUtilWP::update_options_key( WPSSO_OPTIONS_NAME, $opts_key, $place_id );
					}

					unset( $md_opts[ 'place_is_' . $opts_key ] );
				}

				$this->check_place_image_sizes( $md_opts );

			} else {

				$place_id = isset( $md_opts[ 'schema_place_id' ] ) ? $md_opts[ 'schema_place_id' ] : 'none';

				if ( 'custom' !== $place_id ) {

					$md_opts = SucomUtil::preg_grep_keys( '/^place_/', $md_opts, $invert = true );
				}
			}

			return $md_opts;
		}

		public function filter_option_type( $type, $base_key ) {

			if ( ! empty( $type ) ) {	// Return early if we already have a type.

				return $type;

			} elseif ( 0 !== strpos( $base_key, 'place_' ) ) {	// Nothing to do.

				return $type;
			}

			switch ( $base_key ) {

				case ( preg_match( '/^place_(name|name_alt|desc|phone|street_address|city|state|postal_code|zipcode)$/', $base_key ) ? true : false ):
				case ( preg_match( '/^place_(phone|price_range|cuisine)$/', $base_key ) ? true : false ):

					return 'ok_blank';

				case ( preg_match( '/^place_(country|schema_type)$/', $base_key ) ? true : false ):

					return 'not_blank';

				case ( preg_match( '/^place_(latitude|longitude|altitude|service_radius|po_box_number)$/', $base_key ) ? true : false ):

					return 'blank_num';

				case ( preg_match( '/^place_(currencies_accepted|payment_accepted)$/', $base_key ) ? true : false ):

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

		public function filter_plugin_upgrade_advanced_exclude( $adv_exclude ) {

			foreach ( $this->p->cf[ 'form' ][ 'place_is_defaults' ] as $opts_key => $opts_label ) {

				$adv_exclude[] = $opts_key;
			}

			return $adv_exclude;
		}

		private function check_place_image_sizes( $md_opts ) {

			/*
			 * Skip if notices have already been shown.
			 */
			if ( ! $this->p->notice->is_admin_pre_notices() ) {

				return;
			}

			$mt_images = $this->p->media->get_mt_opts_images( $md_opts, $size_names = 'schema', $img_pre = 'place_img' );
		}
	}
}
