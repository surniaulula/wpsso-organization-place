<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
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

		/*
		 * Return an associative array of organization IDs and names.
		 */
		public static function get_names( $schema_type = '' ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			if ( ! $schema_type || ! is_string( $schema_type ) ) $schema_type = 'organization';

			static $local_cache = array();

			if ( isset( $local_cache[ $schema_type ] ) ) {

				if ( $wpsso->debug->enabled ) {

					$wpsso->debug->log( 'returning cache entry for "' . $schema_type . '"' );
				}

				return $local_cache[ $schema_type ];
			}

			$local_cache[ $schema_type ] = array();

			$children = $wpsso->schema->get_schema_type_children( $schema_type );

			$org_ids = WpssoPost::get_public_ids( array( 'post_type' => WPSSOOPM_ORG_POST_TYPE ) );

			foreach ( $org_ids as $post_id ) {

				$org_opts = $wpsso->post->get_options( $post_id );
				$def_name = sprintf( _x( 'Organization #%d', 'option value', 'wpsso-organization-place' ), 'org-' . $post_id );
				$org_name = empty( $org_opts[ 'org_name' ] ) ? $def_name : $org_opts[ 'org_name' ];
				$org_type = empty( $org_opts[ 'org_schema_type' ] ) ? 'organization' : $org_opts[ 'org_schema_type' ];

				if ( in_array( $org_type, $children ) ) {

					list( $type_context, $type_name, $type_path ) = $wpsso->schema->get_schema_type_url_parts_by_id( $org_type );

					$local_cache[ $schema_type ][ 'org-' . $post_id ] = sprintf( '%1$s [%2$s]', $org_name, $type_name );
				}
			}

			/*
			 * Add places that are also sub-types of organization (or the requested schema type).
			 */
			$local_cache[ $schema_type ] = array_merge( $local_cache[ $schema_type ], WpssoOpmPlace::get_names( $schema_type ) );

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->log( 'saving cache entry for "' . $schema_type . '"' );
			}

			return $local_cache[ $schema_type ];
		}

		/*
		 * Get a specific organization id.
		 */
		public static function get_id( $org_id, $mixed = 'current', $opt_key = false, $id_prefix = 'org' ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->log_args( array(
					'org_id' => $org_id,
					'mixed'  => $mixed,
				) );
			}

			$org_opts = false;	// Return false by default.

			/*
			 * Check that the option value is not true, false, null, empty string, or 'none'.
			 */
			if ( ! SucomUtil::is_valid_option_value( $org_id ) ) {

				return false === $opt_key ? $org_opts : null;

			} elseif ( 'site' === $org_id ) {

				$org_opts = WpssoSchema::get_site_organization( $mixed );

			} elseif ( 0 === strpos( $org_id, $id_prefix . '-' ) ) {

				$post_id = substr( $org_id, strlen( $id_prefix ) + 1 );

				$post_mod = $wpsso->post->get_mod( $post_id );

				if ( 'publish' === $post_mod[ 'post_status' ] ) {

					$org_opts = $post_mod[ 'obj' ]->get_options( $post_mod[ 'id' ] );

					$org_sameas = array();

					foreach ( SucomUtilOptions::get_opts_begin( $org_opts, 'org_sameas_' ) as $sameas_key => $sameas_url ) {

						unset( $org_opts[ $sameas_key ] );

						if ( empty( $sameas_url ) ) {

							continue;

						} elseif ( 'org_sameas_tc_site' === $sameas_key ) {	// Convert X (Twitter) username to a URL.

							$sameas_url = 'https://twitter.com/' . preg_replace( '/^@/', '', $sameas_url );
						}

						if ( false === filter_var( $sameas_url, FILTER_VALIDATE_URL ) ) {	// Just in case.

							if ( $wpsso->debug->enabled ) {

								$wpsso->debug->log( 'skipping ' . $sameas_key . ': url "' . $sameas_url . '" is invalid' );
							}

						} else $org_sameas[] = $sameas_url;
					}

					if ( ! empty( $org_sameas ) ) {

						$org_opts[ 'org_sameas' ] = $org_sameas;
					}

					$post_mod[ 'obj' ]->md_keys_multi_array( $org_opts, 'org_award', 'org_awards' );

				} elseif ( ! empty( $post_mod[ 'post_status' ] ) ) {	// 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', or 'trash'.

					$org_page_link = get_edit_post_link( $post_mod[ 'id' ] );

					$notice_msg = sprintf( __( 'Unable to provide information for organization ID #%s.', 'wpsso-organization-place' ), $org_id ) . ' ';

					$notice_msg .= $org_page_link ? '<a href="' . $org_page_link . '">' : '';

					$notice_msg .= sprintf( __( 'Please publish organization ID #%s or select a different organization.',
						'wpsso-organization-place' ), $org_id );

					$notice_msg .= $org_page_link ? '</a>' : '';

					$wpsso->notice->err( $notice_msg );

				} else {

					$notice_msg = sprintf( __( 'Unable to provide information for organization ID #%s.', 'wpsso-organization-place' ), $org_id ) . ' ';

					$notice_msg .= sprintf( __( 'Organization ID #%s does not exist.', 'wpsso-organization-place' ), $org_id ) . ' ';

					$notice_msg .= __( 'Please select a different organization.', 'wpsso-organization-place' );

					$wpsso->notice->err( $notice_msg );
				}
			}

			if ( ! empty( $org_opts ) ) {

				$org_opts[ 'org_id' ] = $org_id;

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

		public static function add_mb_org_rows( &$table_rows, $form, $head_info, $mod, $tr_hide_html = '' ) {	// Pass by reference is OK.

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			$hide_news_media_class   = $wpsso->schema->get_children_css_class( 'news.media.organization', 'hide_org_schema_type' );
			$tr_hide_news_media_html = '<tr class="' . $hide_news_media_class . '" style="display:none;">';
			$awards_max              = SucomUtil::get_const( 'WPSSO_SCHEMA_AWARDS_MAX', 5 );
			$is_defaults             = array_diff_key( $wpsso->cf[ 'form' ][ 'org_is_defaults' ], $wpsso->cf[ 'form' ][ 'place_is_defaults' ] );

			$table_rows[ 'org_is_default' ] = $tr_hide_html .
				$form->get_th_html( _x( 'Organization Is Default', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-org_is_default' ) .
				'<td>' . $form->get_checklist( 'org_is', $is_defaults ) . '</td>';

			/*
			 * Organization section.
			 */
			$table_rows[ 'subsection_org' ] = $tr_hide_html .
				'<td class="subsection" colspan="2"><h5>' .
				_x( 'Organization Information', 'metabox title', 'wpsso' ) .
				'</h5></td>';

			$table_rows[ 'org_url' ] = $tr_hide_html .
				$form->get_th_html( _x( 'Organization WebSite URL', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-org_url' ) .
				'<td>' . $form->get_input( 'org_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_logo_url' ] = $tr_hide_html .
				$form->get_th_html( '<a href="https://developers.google.com/search/docs/advanced/structured-data/logo">' .
				_x( 'Organization Logo URL', 'option label', 'wpsso-organization-place' ) . '</a>',
					$css_class = 'medium', $css_id = 'meta-org_logo_url' ) .
				'<td>' . $form->get_input( 'org_logo_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_banner_url' ] = $tr_hide_html .
				$form->get_th_html( '<a href="https://developers.google.com/search/docs/data-types/article#logo-guidelines">' .
				_x( 'Organization Banner URL', 'option label', 'wpsso-organization-place' ) . '</a>',
					$css_class = 'medium', $css_id = 'meta-org_banner_url' ) .
				'<td>' . $form->get_input( 'org_banner_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_pub_principles_url' ] = $tr_hide_html .
				$form->get_th_html( _x( 'Publishing Principles URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_pub_principles_url' ) .
				'<td>' . $form->get_input( 'org_pub_principles_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_corrections_policy_url' ] = $tr_hide_html .
				$form->get_th_html( _x( 'Corrections Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_corrections_policy_url' ) .
				'<td>' . $form->get_input( 'org_corrections_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_diversity_policy_url' ] = $tr_hide_html .
				$form->get_th_html( _x( 'Diversity Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_diversity_policy_url' ) .
				'<td>' . $form->get_input( 'org_diversity_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_ethics_policy_url' ] = $tr_hide_html .
				$form->get_th_html( _x( 'Ethics Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_ethics_policy_url' ) .
				'<td>' . $form->get_input( 'org_ethics_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_fact_check_policy_url' ] = $tr_hide_html .
				$form->get_th_html( _x( 'Fact Checking Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_fact_check_policy_url' ) .
				'<td>' . $form->get_input( 'org_fact_check_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_feedback_policy_url' ] = $tr_hide_html .
				$form->get_th_html( _x( 'Feedback Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_feedback_policy_url' ) .
				'<td>' . $form->get_input( 'org_feedback_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_award' ] = $tr_hide_html .
				$form->get_th_html( _x( 'Organization Awards', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_award' ) .
				'<td>' . $form->get_input_multi( 'org_award', $css_class = 'wide', $css_id = '',
					$awards_max, $show_first = 1 ) . '</td>';

			/*
			 * News Media Organization section.
			 */
			$table_rows[ 'subsection_org_news_media' ] = $tr_hide_news_media_html .
				'<td class="subsection" colspan="2"><h5>' .
				_x( 'News Media Organization Information', 'metabox title', 'wpsso' ) .
				'</h5></td>';

			$table_rows[ 'org_masthead_url' ] = $tr_hide_news_media_html .
				$form->get_th_html( _x( 'Masthead Page URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_masthead_url' ) .
				'<td>' . $form->get_input( 'org_masthead_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_coverage_policy_url' ] = $tr_hide_news_media_html .
				$form->get_th_html( _x( 'Coverage Priorities Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium nowrap', $css_id = 'meta-org_coverage_policy_url' ) .
				'<td>' . $form->get_input( 'org_coverage_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_no_bylines_policy_url' ] = $tr_hide_news_media_html .
				$form->get_th_html( _x( 'No Bylines Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_no_bylines_policy_url' ) .
				'<td>' . $form->get_input( 'org_no_bylines_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_sources_policy_url' ] = $tr_hide_news_media_html .
				$form->get_th_html( _x( 'Unnamed Sources Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_sources_policy_url' ) .
				'<td>' . $form->get_input( 'org_sources_policy_url', $css_class = 'wide' ) . '</td>';

			/*
			 * Organization Knowledge Graph section.
			 */
			$table_rows[ 'subsection_org_knowledgegraph' ] = $tr_hide_html .
				'<td colspan="2" class="subsection"><h5>' .
				_x( 'Organization Knowledge Graph', 'metabox title', 'wpsso-organization-place' ) .
				'</h5></td>';

			foreach ( WpssoConfig::get_social_accounts() as $key => $label ) {

				$opt_key = 'org_sameas_' . $key;

				$table_rows[ $opt_key ] = $tr_hide_html .
					$form->get_th_html( _x( $label, 'option value', 'wpsso-organization-place' ),
						$css_class = 'medium nowrap', $opt_key ) .
					'<td>' . $form->get_input( $opt_key, strpos( $opt_key, '_url' ) ? 'wide' : '' ) . '</td>';
			}
		}

		public static function check_org_image_sizes( $md_opts, $name_key = 'org_name' ) {

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

			/*
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
			foreach ( array ( 'org_logo', 'org_banner' ) as $img_prefix ) {

				$mt_single_image = $wpsso->media->get_mt_img_pre_url( $md_opts, $img_prefix );

				$first_image_url = SucomUtil::get_first_mt_media_url( $mt_single_image );

				if ( empty( $first_image_url ) ) {

					if ( 'org_logo' === $img_prefix ) {

						// translators: %s is the organization name.
						$notice_msg = sprintf( __( 'The "%s" organization logo image URL is missing and required.',
							'wpsso-organization-place' ), $md_opts[ $name_key ] ) . ' ';

						$wpsso->notice->err( $notice_msg );

					} elseif ( 'org_banner' === $img_prefix ) {

						// translators: %s is the organization name.
						$notice_msg = sprintf( __( 'The "%s" organization banner image URL is missing and required.',
							'wpsso-organization-place' ), $md_opts[ $name_key ] ) . ' ';

						$wpsso->notice->err( $notice_msg );
					}

				} else {

					$image_href   = '<a href="' . $first_image_url . '">' . $first_image_url . '</a>';
					$image_width  = $mt_single_image[ 'og:image:width' ];
					$image_height = $mt_single_image[ 'og:image:height' ];
					$image_dims   = $image_width . 'x' . $image_height . 'px';
					$notice_key   = 'invalid-image-dimensions-' . $image_dims . '-' . $first_image_url;

					if ( 'org_logo' === $img_prefix ) {

						$min_width    = $wpsso->cf[ 'head' ][ 'limit_min' ][ 'org_logo_width' ];
						$min_height   = $wpsso->cf[ 'head' ][ 'limit_min' ][ 'org_logo_height' ];
						$minimum_dims = $min_width . 'x' . $min_height . 'px';

						if ( '-1x-1px' === $image_dims ) {

							// translators: %s is the organization name.
							$notice_msg = sprintf( __( 'The "%s" organization logo image dimensions cannot be determined.',
								'wpsso-organization-place' ), $md_opts[ $name_key ] ) . ' ';

							// translators: %s is the image URL.
							$notice_msg .= sprintf( __( 'Please make sure this site can access %s using the PHP getimagesize() function.',
								'wpsso-organization-place' ), $image_href );

							$wpsso->notice->err( $notice_msg, null, $notice_key );

						} elseif ( $image_width < $min_width || $image_height < $min_height ) {

							// translators: %1$s is the organization name.
							$notice_msg = sprintf( __( 'The "%1$s" organization logo image dimensions are %2$s and must be greater than %3$s.',
								'wpsso-organization-place' ), $md_opts[ $name_key ], $image_dims, $minimum_dims ) . ' ';

							// translators: %s is the image URL.
							$notice_msg .= sprintf( __( 'Please correct the %s logo image or select a different logo image.',
								'wpsso-organization-place' ), $image_href );

							$wpsso->notice->err( $notice_msg, null, $notice_key );
						}

					} elseif ( 'org_banner' === $img_prefix ) {

						$min_width     = $wpsso->cf[ 'head' ][ 'limit' ][ 'org_banner_width' ];
						$min_height    = $wpsso->cf[ 'head' ][ 'limit' ][ 'org_banner_height' ];
						$required_dims = $min_width . 'x' . $min_height . 'px';

						if ( '-1x-1px' === $image_dims ) {

							// translators: %s is the organization name.
							$notice_msg = sprintf( __( 'The "%s" organization banner image dimensions cannot be determined.',
								'wpsso-organization-place' ), $md_opts[ $name_key ] ) . ' ';

							// translators: %s is the image URL.
							$notice_msg .= sprintf( __( 'Please make sure this site can access %s using the PHP getimagesize() function.',
								'wpsso-organization-place' ), $image_href );

							$wpsso->notice->err( $notice_msg, null, $notice_key );

						} elseif ( $image_dims !== $required_dims ) {

							// translators: %1$s is the organization name.
							$notice_msg = sprintf( __( 'The "%1$s" organization banner image dimensions are %2$s and must be exactly %3$s.',
								'wpsso-organization-place' ), $md_opts[ $name_key ], $image_dims, $required_dims ) . ' ';

							// translators: %s is the image URL.
							$notice_msg .= sprintf( __( 'Please correct the %s banner image or select a different banner image.',
								'wpsso-organization-place' ), $image_href );

							$wpsso->notice->err( $notice_msg, null, $notice_key );
						}
					}
				}
			}
		}
	}
}
