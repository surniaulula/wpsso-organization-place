<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2025 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmContact' ) ) {

	class WpssoOpmContact {

		public function __construct() {}

		public static function get_ids( $schema_type = '' ) {

			$contact_names = self::get_names( $schema_type );

			return array_keys( $contact_names );
		}

		/*
		 * Return an associative array of contact point IDs and names.
		 *
		 * See WpssoOpmContactFiltersEdit->filter_form_cache_contact_names().
		 */
		public static function get_names( $schema_type = '' ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			if ( ! $schema_type || ! is_string( $schema_type ) ) $schema_type = 'contact.point';

			static $local_cache = array();

			if ( isset( $local_cache[ $schema_type ] ) ) {

				if ( $wpsso->debug->enabled ) {

					$wpsso->debug->log( 'returning cache entry for "' . $schema_type . '"' );
				}

				return $local_cache[ $schema_type ];
			}

			$local_cache[ $schema_type ] = array();

			$children    = $wpsso->schema->get_schema_type_children( $schema_type );
			$contact_ids = WpssoPost::get_public_ids( array( 'post_type' => WPSSOOPM_CONTACT_POST_TYPE ) );

			foreach ( $contact_ids as $post_id ) {

				$contact_opts = $wpsso->post->get_options( $post_id );
				$def_name     = sprintf( _x( 'Contact point #%d', 'option value', 'wpsso-organization-place' ), 'contact-' . $post_id );
				$def_type     = $wpsso->cf[ 'opt' ][ 'contact_md_defaults' ][ 'contact_schema_type' ];
				$contact_name = empty( $contact_opts[ 'contact_name' ] ) ? $def_name : $contact_opts[ 'contact_name' ];
				$contact_type = empty( $contact_opts[ 'contact_schema_type' ] ) ? $def_type : $contact_opts[ 'contact_schema_type' ];

				if ( in_array( $contact_type, $children ) ) {

					list( $type_context, $type_name, $type_path ) = $wpsso->schema->get_schema_type_url_parts_by_id( $contact_type );

					$local_cache[ $schema_type ][ 'contact-' . $post_id ] = sprintf( '%1$s [%2$s]', $contact_name, $type_name );
				}
			}

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->log( 'saving cache entry for "' . $schema_type . '"' );
			}

			return $local_cache[ $schema_type ];
		}

		/*
		 * Get a specific contact point id.
		 */
		public static function get_id( $contact_id, $mod = false, $opt_key = false ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->log_args( array(
					'contact_id' => $contact_id,
					'mod'        => $mod,
				) );
			}

			$contact_opts = false;	// Return false by default.

			/*
			 * Check that the option value is not true, false, null, empty string, or 'none'.
			 */
			if ( ! SucomUtil::is_valid_option_value( $contact_id ) ) {

				return false === $opt_key ? $contact_opts : null;

			} elseif ( 0 === strpos( $contact_id, 'contact-' ) ) {

				$post_id  = substr( $contact_id, strlen( 'contact-' ) );
				$post_mod = $wpsso->post->get_mod( $post_id );

				if ( 'publish' === $post_mod[ 'post_status' ] ) {

					$contact_opts = $post_mod[ 'obj' ]->get_options( $post_mod[ 'id' ] );

				} elseif ( ! empty( $post_mod[ 'post_status' ] ) ) {	// 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', or 'trash'.

					$contact_page_link = get_edit_post_link( $post_mod[ 'id' ] );

					$notice_msg = sprintf( __( 'Unable to provide information for contact point ID #%s.', 'wpsso-organization-place' ), $contact_id ) . ' ';

					$notice_msg .= $contact_page_link ? '<a href="' . $contact_page_link . '">' : '';

					$notice_msg .= sprintf( __( 'Please publish contact point ID #%s or select a different contact point.', 'wpsso-organization-place' ), $contact_id );

					$notice_msg .= $contact_page_link ? '</a>' : '';

					$wpsso->notice->err( $notice_msg );

				} else {

					$notice_msg = sprintf( __( 'Unable to provide information for contact point ID #%s.', 'wpsso-organization-place' ), $contact_id ) . ' ';

					$notice_msg .= sprintf( __( 'Contact point ID #%s does not exist.', 'wpsso-organization-place' ), $contact_id ) . ' ';

					$notice_msg .= __( 'Please select a different contact point.', 'wpsso-organization-place' );

					$wpsso->notice->err( $notice_msg );
				}
			}

			if ( ! empty( $contact_opts ) ) {

				$contact_opts[ 'contact_id' ]  = $contact_id;

				$contact_opts = array_merge( WpssoOpmConfig::$cf[ 'opt' ][ 'contact_md_defaults' ], $contact_opts );	// Complete the array.
				$contact_opts = SucomUtil::preg_grep_keys( '/^contact_/', $contact_opts );
			}

			if ( false !== $opt_key ) {

				if ( isset( $contact_opts[ $opt_key ] ) ) {

					return $contact_opts[ $opt_key ];
				}

				return null;
			}

			return $contact_opts;
		}

		/*
		 * See WpssoOpmContactFiltersOptions->filter_save_post_options().
		 */
		public static function check_image_sizes( $md_opts ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			/*
			 * Skip if notices have already been shown.
			 */
			if ( ! $wpsso->notice->is_admin_pre_notices() ) {

				return;
			}

			$mt_images = $wpsso->media->get_mt_opts_images( $md_opts, $size_names = 'schema', $img_prefix = 'contact_img' );
		}
	}
}
