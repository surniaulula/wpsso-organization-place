<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmOrgFiltersOptions' ) ) {

	class WpssoOpmOrgFiltersOptions {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/**
		 * Instantiated by WpssoOpmFiltersOptions->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array( 
				'get_organization_options' => 3,
				'get_post_defaults'        => 4,
				'get_post_options'         => 3,
				'save_post_options'        => 4,
				'option_type'              => 2,
			) );
		}

		public function filter_get_organization_options( $org_opts, $mod, $org_id ) {

			if ( false === $org_opts ) {	// First come, first served.
			
				if ( 0 === strpos( $org_id, 'org-' ) ) {

					$org_opts = WpssoOpmOrg::get_id( $org_id, $mod );
				}
			}

			return $org_opts;
		}

		public function filter_get_post_defaults( $md_defs, $post_id, $rel, $mod ) {

			$md_defs = array_merge( $md_defs, $this->p->cf[ 'opt' ][ 'org_md_defaults' ] );

			return $md_defs;
		}

		public function filter_get_post_options( $md_opts, $mod ) {

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

		public function filter_save_post_options( $md_opts, $post_id, $rel, $mod ) {

			if ( WPSSOOPM_ORG_POST_TYPE === $mod[ 'post_type' ] ) {

				if ( empty( $md_opts[ 'org_name' ] ) ) {	// Just in case.

					$md_opts[ 'org_name' ] = sprintf( _x( 'Organization #%d', 'option value', 'wpsso-organization-place' ), $post_id );
				}

				/**
				 * Always keep the post title and slug updated.
				 */
				SucomUtilWP::raw_update_post_title( $post_id, $md_opts[ 'org_name' ] );

				$this->check_banner_image_size( $md_opts );
			}

			return $md_opts;
		}

		public function filter_option_type( $type, $base_key ) {

			if ( ! empty( $type ) ) {	// Return early if we already have a type.

				return $type;

			} elseif ( 0 !== strpos( $base_key, 'org_' ) ) {	// Nothing to do.

				return $type;
			}

			switch ( $base_key ) {

				case 'org_desc':
				case 'org_name':
				case 'org_name_alt':

					return 'ok_blank';

				case 'org_banner_url':
				case 'org_logo_url':

					return 'img_url';

				case 'org_place_id':
				case 'org_schema_type':

					return 'not_blank';

				case 'org_url':

					return 'url';

				case ( strpos( $base_key, '_url' ) && isset( $this->p->cf[ 'form' ][ 'social_accounts' ][ substr( $base_key, 4 ) ] ) ? true : false ):

					return 'url';
			}

			return $type;
		}

		private function check_banner_image_size( $md_opts ) {

			/**
			 * Skip if notices have already been shown.
			 */
			if ( ! $this->p->notice->is_admin_pre_notices() ) {

				return;
			}

			/**
			 * Returns an image array:
			 *
			 * array(
			 *	'og:image:url'       => null,
			 *	'og:image:width'     => null,
			 *	'og:image:height'    => null,
			 *	'og:image:cropped'   => null,
			 *	'og:image:id'        => null,
			 *	'og:image:alt'       => null,
			 *	'og:image:size_name' => null,
			 * );
			 */
			$mt_single_image = $this->p->media->get_mt_img_pre_url( $md_opts, $img_pre = 'org_banner' );
			$first_image_url = SucomUtil::get_first_mt_media_url( $mt_single_image );

			if ( ! empty( $first_image_url ) ) {

				$image_href    = '<a href="' . $first_image_url . '">' . $first_image_url . '</a>';
				$image_dims    = $mt_single_image[ 'og:image:width' ] . 'x' . $mt_single_image[ 'og:image:height' ] . 'px';
				$required_dims = '600x60px';

				if ( $image_dims !== $required_dims ) {

					if ( '-1x-1px' === $image_dims ) {

						$error_msg = sprintf( __( 'The "%s" organization banner URL image dimensions cannot be determined.',
							'wpsso-organization-place' ), $md_opts[ 'org_name' ] ) . ' ';

						$error_msg .= sprintf( __( 'Please make sure this site can access %s using the PHP getimagesize() function.',
							'wpsso-organization-place' ), $image_href );

					} else {

						$error_msg = sprintf( __( 'The "%1$s" organization banner URL image dimensions are %2$s and must be exactly %3$s.',
							'wpsso-organization-place' ), $md_opts[ 'org_name' ], $image_dims, $required_dims ) . ' ';

						$error_msg .= sprintf( __( 'Please correct the %s banner image.', 'wpsso-organization-place' ), $image_href );
					}

					$this->p->notice->err( $error_msg );
				}
			}
		}
	}
}
