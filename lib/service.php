<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2025 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmService' ) ) {

	class WpssoOpmService {

		public function __construct() {}

		public static function get_ids( $schema_type = '' ) {

			$service_names = self::get_names( $schema_type );

			return array_keys( $service_names );
		}

		/*
		 * Return an associative array of service IDs and names.
		 */
		public static function get_names( $schema_type = '' ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			if ( ! $schema_type || ! is_string( $schema_type ) ) $schema_type = 'service';

			static $local_cache = array();

			if ( isset( $local_cache[ $schema_type ] ) ) {

				if ( $wpsso->debug->enabled ) {

					$wpsso->debug->log( 'returning cache entry for "' . $schema_type . '"' );
				}

				return $local_cache[ $schema_type ];
			}

			$local_cache[ $schema_type ] = array();

			$children = $wpsso->schema->get_schema_type_children( $schema_type );

			$service_ids = WpssoPost::get_public_ids( array( 'post_type' => WPSSOOPM_SERVICE_POST_TYPE ) );

			foreach ( $service_ids as $post_id ) {

				$service_opts = $wpsso->post->get_options( $post_id );
				$def_name     = sprintf( _x( 'Service #%d', 'option value', 'wpsso-organization-place' ), 'service-' . $post_id );
				$def_type     = $wpsso->cf[ 'opt' ][ 'service_md_defaults' ][ 'service_schema_type' ];
				$service_name = empty( $service_opts[ 'service_name' ] ) ? $def_name : $service_opts[ 'service_name' ];
				$service_type = empty( $service_opts[ 'service_schema_type' ] ) ? $def_type : $service_opts[ 'service_schema_type' ];

				if ( in_array( $service_type, $children ) ) {

					list( $type_context, $type_name, $type_path ) = $wpsso->schema->get_schema_type_url_parts_by_id( $service_type );

					$local_cache[ $schema_type ][ 'service-' . $post_id ] = sprintf( '%1$s [%2$s]', $service_name, $type_name );
				}
			}

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->log( 'saving cache entry for "' . $schema_type . '"' );
			}

			return $local_cache[ $schema_type ];
		}

		/*
		 * Get a specific service id.
		 */
		public static function get_id( $service_id, $mod = false, $opt_key = false, $id_prefix = 'service' ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->log_args( array(
					'service_id' => $service_id,
					'mod'      => $mod,
				) );
			}

			$service_opts = false;	// Return false by default.

			/*
			 * Check that the option value is not true, false, null, empty string, or 'none'.
			 */
			if ( ! SucomUtil::is_valid_option_value( $service_id ) ) {

				return false === $opt_key ? $service_opts : null;

			} elseif ( 0 === strpos( $service_id, $id_prefix . '-' ) ) {

				$post_id  = substr( $service_id, strlen( $id_prefix ) + 1 );
				$post_mod = $wpsso->post->get_mod( $post_id );

				if ( 'publish' === $post_mod[ 'post_status' ] ) {

					$service_opts = $post_mod[ 'obj' ]->get_options( $post_mod[ 'id' ] );

				} elseif ( ! empty( $post_mod[ 'post_status' ] ) ) {	// 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', or 'trash'.

					$service_page_link = get_edit_post_link( $post_mod[ 'id' ] );

					$notice_msg = sprintf( __( 'Unable to provide information for service ID #%s.', 'wpsso-organization-place' ), $service_id ) . ' ';

					$notice_msg .= $service_page_link ? '<a href="' . $service_page_link . '">' : '';

					$notice_msg .= sprintf( __( 'Please publish service ID #%s or select a different service.', 'wpsso-organization-place' ), $service_id );

					$notice_msg .= $service_page_link ? '</a>' : '';

					$wpsso->notice->err( $notice_msg );

				} else {

					$notice_msg = sprintf( __( 'Unable to provide information for service ID #%s.', 'wpsso-organization-place' ), $service_id ) . ' ';

					$notice_msg .= sprintf( __( 'Service ID #%s does not exist.', 'wpsso-organization-place' ), $service_id ) . ' ';

					$notice_msg .= __( 'Please select a different service.', 'wpsso-organization-place' );

					$wpsso->notice->err( $notice_msg );
				}
			}

			if ( ! empty( $service_opts ) ) {

				$service_opts[ 'service_id' ] = $service_id;

				$service_opts = array_merge( WpssoOpmConfig::$cf[ 'opt' ][ 'service_md_defaults' ], $service_opts );	// Complete the array.
				$service_opts = SucomUtil::preg_grep_keys( '/^service_/', $service_opts );
			}

			if ( false !== $opt_key ) {

				if ( isset( $service_opts[ $opt_key ] ) ) {

					return $service_opts[ $opt_key ];
				}

				return null;
			}

			return $service_opts;
		}
	}
}
