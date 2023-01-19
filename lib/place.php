<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2023 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmPlace' ) ) {

	class WpssoOpmPlace {

		public function __construct() {}

		public static function get_ids( $schema_type = '' ) {

			$place_names = self::get_names( $schema_type );

			return array_keys( $place_names );
		}

		/**
		 * Return an associative array of place IDs and names.
		 */
		public static function get_names( $schema_type = '' ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			static $local_cache = array();

			if ( isset( $local_cache[ $schema_type ] ) ) {

				return $local_cache[ $schema_type ];
			}

			$local_cache[ $schema_type ] = array();

			$children  = $schema_type && is_string( $schema_type ) ? $wpsso->schema->get_schema_type_children( $schema_type ) : false;
			$place_ids = WpssoPost::get_public_ids( array( 'post_type' => WPSSOOPM_PLACE_POST_TYPE ) );

			foreach ( $place_ids as $post_id ) {

				$place_opts = $wpsso->post->get_options( $post_id );
				$def_name   = sprintf( _x( 'Place #%d', 'option value', 'wpsso-organization-place' ), $post_id );
				$def_type   = $wpsso->cf[ 'opt' ][ 'place_md_defaults' ][ 'place_schema_type' ];
				$place_name = empty( $place_opts[ 'place_name' ] ) ? $def_name : $place_opts[ 'place_name' ];
				$place_type = empty( $place_opts[ 'place_schema_type' ] ) ? $def_type : $place_opts[ 'place_schema_type' ];

				/**
				 * If we have $schema_type children, skip place schema types that are not a sub-type of $schema_type.
				 */
				if ( $children && ! in_array( $place_type, $children ) ) {

					continue;
				}

				$local_cache[ $schema_type ][ 'place-' . $post_id ] = $place_name;
			}

			return $local_cache[ $schema_type ];
		}

		/**
		 * Get a specific place id.
		 */
		public static function get_id( $place_id, $mod = false, $opt_key = false ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->log_args( array(
					'place_id' => $place_id,
					'mod'      => $mod,
				) );
			}

			$place_opts = false;	// Return false by defaults.

			if ( 'custom' === $place_id ) {	// Read place options from the post, term, or user object.

				if ( ! empty( $mod[ 'obj' ] ) && ! empty( $mod[ 'id' ] ) ) {

					$place_opts = $mod[ 'obj' ]->get_options( $mod[ 'id' ] );

					unset(
						$place_opts[ 'place_name' ],
						$place_opts[ 'place_name_alt' ],
						$place_opts[ 'place_desc' ],
						$place_opts[ 'place_img_id' ],
						$place_opts[ 'place_img_id_lib' ],
						$place_opts[ 'place_img_url' ]
					);
				}

			} elseif ( 0 === strpos( $place_id, 'place-' ) ) {

				$post_id  = substr( $place_id, 6 );
				$post_mod = $wpsso->post->get_mod( $post_id );

				if ( 'publish' === $post_mod[ 'post_status' ] ) {

					$place_opts = $post_mod[ 'obj' ]->get_options( $post_mod[ 'id' ] );

				} elseif ( ! empty( $post_mod[ 'post_status' ] ) ) {	// 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', or 'trash'.

					$place_page_link = get_edit_post_link( $post_id );

					$notice_msg = sprintf( __( 'Unable to provide information for place ID #%s.', 'wpsso-organization-place' ), $post_id );
					$notice_msg .= ' ';
					$notice_msg .= $place_page_link ? '<a href="' . $place_page_link . '">' : '';
					$notice_msg .= sprintf( __( 'Please publish place ID #%s or select a different place.', 'wpsso-organization-place' ), $post_id );
					$notice_msg .= $place_page_link ? '</a>' : '';

					$wpsso->notice->err( $notice_msg );

				} else {

					$notice_msg = sprintf( __( 'Unable to provide information for place ID #%s.', 'wpsso-organization-place' ), $post_id );
					$notice_msg .= ' ';
					$notice_msg .= sprintf( __( 'Place ID #%s does not exist.', 'wpsso-organization-place' ), $post_id );
					$notice_msg .= ' ';
					$notice_msg .= __( 'Please select a different place.', 'wpsso-organization-place' );

					$wpsso->notice->err( $notice_msg );
				}
			}

			if ( ! empty( $place_opts ) ) {

				$place_opts[ 'place_id' ] = $place_id;

				/**
				 * Merging the defaults array also makes sure 'place_schema_type' is defined.
				 */
				$place_opts = array_merge( WpssoOpmConfig::$cf[ 'opt' ][ 'place_md_defaults' ], $place_opts );
				$place_opts = SucomUtil::preg_grep_keys( '/^place_/', $place_opts );

				/**
				 * If not a local business or food establishment, remove local business or food establishment options.
				 */
				if ( $wpsso->schema->is_schema_type_child( $place_opts[ 'place_schema_type' ], 'local.business' ) ) {

					if ( ! $wpsso->schema->is_schema_type_child( $place_opts[ 'place_schema_type' ], 'food.establishment' ) ) {

						unset(
							$place_opts[ 'place_accept_res' ],	// Accepts Reservations.
							$place_opts[ 'place_cuisine' ],	// Serves Cuisine.
							$place_opts[ 'place_menu_url' ],	// Food Menu URL.
							$place_opts[ 'place_order_urls' ]	// Order Action URL(s).
						);
					}

				} else {

					unset(
						$place_opts[ 'place_service_radius' ],	// Service Radius.
						$place_opts[ 'place_currencies_accepted' ],	// Currencies Accepted.
						$place_opts[ 'place_payment_accepted' ],	// Payment Accepted.
						$place_opts[ 'place_price_range' ],		// Price Range.
						$place_opts[ 'place_accept_res' ],		// Accepts Reservations.
						$place_opts[ 'place_cuisine' ],		// Serves Cuisine.
						$place_opts[ 'place_menu_url' ],		// Food Menu URL.
						$place_opts[ 'place_order_urls' ]		// Order Action URL(s).
					);
				}
			}

			if ( false !== $opt_key ) {

				if ( isset( $place_opts[ $opt_key ] ) ) {

					return $place_opts[ $opt_key ];
				}

				return null;
			}

			return $place_opts;
		}

		/**
		 * Return a text a value for the https://schema.org/address property.
		 */
		public static function get_address( array $place_opts ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			$address = '';

			foreach ( array(
				'place_street_address',
				'place_po_box_number',
				'place_city',
				'place_region',
				'place_postal_code',
				'place_country',
			) as $opt_key ) {

				if ( isset( $place_opts[ $opt_key ] ) && $place_opts[ $opt_key ] !== '' && $place_opts[ $opt_key ] !== 'none' ) {

					switch ( $opt_key ) {

						case 'place_name':

							$place_opts[ $opt_key ] = preg_replace( '/\s*,\s*/', ' ', $place_opts[ $opt_key ] );	// Just in case.

							break;

						case 'place_po_box_number':

							$address = rtrim( $address, ', ' ) . ' #';	// Continue street address.

							break;
					}

					$address .= $place_opts[ $opt_key ] . ', ';
				}
			}

			return rtrim( $address, ', ' );
		}

		public static function has_md_place( array $mod ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			if ( ! isset( $mod[ 'obj' ] ) || ! is_object( $mod[ 'obj' ] ) ) {	// Just in case.

				if ( $wpsso->debug->enabled ) {

					$wpsso->debug->log( 'exiting early: no module object defined' );
				}

				return false;
			}

			$place_id = $mod[ 'obj' ]->get_options( $mod[ 'id' ], 'schema_place_id' );

			if ( 'custom' === $place_id ) {	// Read place options from the post, term, or user object.

				return self::get_id( $place_id, $mod );

			} elseif ( 0 === strpos( $place_id, 'place-' ) ) {

				return self::get_id( $place_id );
			}

			return false;
		}
	}
}
