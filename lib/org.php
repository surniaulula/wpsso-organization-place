<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmOrg' ) ) {

	class WpssoOpmOrg {

		public function __construct() {}

		public static function get_ids( $schema_type = '' ) {

			$org_names = self::get_names( $schema_type );

			return array_keys( $org_names );
		}

		/**
		 * Return an associative array of organization IDs and names.
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

			$children = $schema_type && is_string( $schema_type ) ? $wpsso->schema->get_schema_type_children( $schema_type ) : false;
			$org_ids  = $wpsso->post->get_public_ids( array( 'post_type' => WPSSOOPM_ORG_POST_TYPE ) );

			foreach ( $org_ids as $post_id ) {

				$org_opts = $wpsso->post->get_options( $post_id );
				$def_name = sprintf( _x( 'Organization #%d', 'option value', 'wpsso-organization-place' ), $post_id );
				$org_name = empty( $org_opts[ 'org_name' ] ) ? $def_name : $org_opts[ 'org_name' ];
				$org_type = empty( $org_opts[ 'org_schema_type' ] ) ? 'organization' : $org_opts[ 'org_schema_type' ];

				/**
				 * If we have $schema_type children, skip organization schema types that are not a sub-type of $schema_type.
				 */
				if ( $children && ! in_array( $org_type, $children ) ) {

					continue;
				}

				$local_cache[ $schema_type ][ 'org-' . $post_id ] = $org_name;
			}

			return $local_cache[ $schema_type ];
		}

		/**
		 * Get a specific organization id.
		 */
		public static function get_id( $org_id, $mixed = 'current', $opt_key = false ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->log_args( array( 
					'org_id' => $org_id,
					'mixed'  => $mixed,
				) );
			}

			$org_opts = false;	// Return false by defaults.

			if ( 'site' === $org_id ) {

				$org_opts = WpssoSchema::get_site_organization( $mixed );

			} elseif ( 0 === strpos( $org_id, 'org-' ) ) {

				$post_id    = substr( $org_id, 4 );
				$org_opts   = $wpsso->post->get_options( $post_id );
				$org_sameas = array();

				foreach ( SucomUtil::get_opts_begin( 'org_sameas_', $org_opts ) as $key => $url ) {	// Don't use $opt_key variable name.

					unset( $org_opts[ $key ] );

					if ( empty( $url ) ) {

						continue;

					} elseif ( $key === 'org_sameas_tc_site' ) {	// Convert Twitter username to a URL.

						$url = 'https://twitter.com/' . preg_replace( '/^@/', '', $url );
					}

					if ( false !== filter_var( $url, FILTER_VALIDATE_URL ) ) {	// Just in case.

						$org_sameas[] = $url;
					}
				}

				if ( ! empty( $org_sameas ) ) {

					$org_opts[ 'org_sameas' ] = $org_sameas;
				}
			}

			if ( ! empty( $org_opts ) ) {

				$org_opts[ 'org_id' ] = $org_id;

				/**
				 * Merging the defaults array also makes sure 'org_schema_type' is defined.
				 */
				$org_opts = array_merge( WpssoOpmConfig::$cf[ 'opt' ][ 'org_md_defaults' ], $org_opts );	// Complete the array.

				$org_opts = SucomUtil::preg_grep_keys( '/^org_/', $org_opts );
			}

			if ( false !== $opt_key ) {

				if ( isset( $org_opts[ $opt_key ] ) ) {

					return $org_opts[ $opt_key ];
				}

				return null;
			}

			return $org_opts;
		}
	}
}
