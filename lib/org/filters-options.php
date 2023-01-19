<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2023 Jean-Sebastien Morisset (https://wpsso.com/)
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
				'save_post_options'        => 3,
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

		public function filter_get_post_options( array $md_opts, $post_id, array $mod ) {

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

			if ( WPSSOOPM_ORG_POST_TYPE === $mod[ 'post_type' ] ) {

				if ( empty( $md_opts[ 'org_name' ] ) ) {	// Just in case.

					$md_opts[ 'org_name' ] = sprintf( _x( 'Organization #%d', 'option value', 'wpsso-organization-place' ), $post_id );
				}

				if ( ! isset( $md_opts[ 'org_desc' ] ) ) {	// Just in case.

					$md_opts[ 'org_desc' ] = '';
				}

				/**
				 * Always keep the post title, slug, and content updated.
				 */
				SucomUtilWP::raw_update_post_title_content( $post_id, $md_opts[ 'org_name' ], $md_opts[ 'org_desc' ] );

				$this->check_org_image_sizes( $md_opts );
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

		private function check_org_image_sizes( $md_opts ) {

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
			foreach ( array ( 'org_logo', 'org_banner' ) as $img_pre ) {

				$mt_single_image = $this->p->media->get_mt_img_pre_url( $md_opts, $img_pre );
				$first_image_url = SucomUtil::get_first_mt_media_url( $mt_single_image );

				if ( empty( $first_image_url ) ) {

					if ( 'org_logo' === $img_pre ) {

						// translators: %s is the organization name.
						$notice_msg = sprintf( __( 'The "%s" organization logo image URL is missing and required.',
							'wpsso-organization-place' ), $md_opts[ 'org_name' ] ) . ' ';

						$this->p->notice->err( $notice_msg );

					} elseif ( 'org_banner' === $img_pre ) {

						// translators: %s is the organization name.
						$notice_msg = sprintf( __( 'The "%s" organization banner image URL is missing and required.',
							'wpsso-organization-place' ), $md_opts[ 'org_name' ] ) . ' ';

						$this->p->notice->err( $notice_msg );
					}

				} else {

					$image_href   = '<a href="' . $first_image_url . '">' . $first_image_url . '</a>';
					$image_width  = $mt_single_image[ 'og:image:width' ];
					$image_height = $mt_single_image[ 'og:image:height' ];
					$image_dims   = $image_width . 'x' . $image_height . 'px';
					$notice_key   = 'invalid-image-dimensions-' . $image_dims . '-' . $first_image_url;

					if ( 'org_logo' === $img_pre ) {

						$min_width    = $this->p->cf[ 'head' ][ 'limit_min' ][ 'org_logo_width' ];
						$min_height   = $this->p->cf[ 'head' ][ 'limit_min' ][ 'org_logo_height' ];
						$minimum_dims = $min_width . 'x' . $min_height . 'px';

						if ( '-1x-1px' === $image_dims ) {

							// translators: %s is the organization name.
							$notice_msg = sprintf( __( 'The "%s" organization logo image dimensions cannot be determined.',
								'wpsso-organization-place' ), $md_opts[ 'org_name' ] ) . ' ';

							// translators: %s is the image URL.
							$notice_msg .= sprintf( __( 'Please make sure this site can access %s using the PHP getimagesize() function.',
								'wpsso-organization-place' ), $image_href );

							$this->p->notice->err( $notice_msg, null, $notice_key );

						} elseif ( $image_width < $min_width || $image_height < $min_height ) {

							// translators: %1$s is the organization name.
							$notice_msg = sprintf( __( 'The "%1$s" organization logo image dimensions are %2$s and must be greater than %3$s.',
								'wpsso-organization-place' ), $md_opts[ 'org_name' ], $image_dims, $minimum_dims ) . ' ';

							// translators: %s is the image URL.
							$notice_msg .= sprintf( __( 'Please correct the %s logo image or select a different logo image.',
								'wpsso-organization-place' ), $image_href );

							$this->p->notice->err( $notice_msg, null, $notice_key );
						}

					} elseif ( 'org_banner' === $img_pre ) {

						$min_width     = $this->p->cf[ 'head' ][ 'limit' ][ 'org_banner_width' ];
						$min_height    = $this->p->cf[ 'head' ][ 'limit' ][ 'org_banner_height' ];
						$required_dims = $min_width . 'x' . $min_height . 'px';

						if ( '-1x-1px' === $image_dims ) {

							// translators: %s is the organization name.
							$notice_msg = sprintf( __( 'The "%s" organization banner image dimensions cannot be determined.',
								'wpsso-organization-place' ), $md_opts[ 'org_name' ] ) . ' ';

							// translators: %s is the image URL.
							$notice_msg .= sprintf( __( 'Please make sure this site can access %s using the PHP getimagesize() function.',
								'wpsso-organization-place' ), $image_href );

							$this->p->notice->err( $notice_msg, null, $notice_key );

						} elseif ( $image_dims !== $required_dims ) {

							// translators: %1$s is the organization name.
							$notice_msg = sprintf( __( 'The "%1$s" organization banner image dimensions are %2$s and must be exactly %3$s.',
								'wpsso-organization-place' ), $md_opts[ 'org_name' ], $image_dims, $required_dims ) . ' ';

							// translators: %s is the image URL.
							$notice_msg .= sprintf( __( 'Please correct the %s banner image or select a different banner image.',
								'wpsso-organization-place' ), $image_href );

							$this->p->notice->err( $notice_msg, null, $notice_key );
						}
					}
				}
			}
		}
	}
}
