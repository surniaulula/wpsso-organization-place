<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2025 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

/*
 * WpssoOpmIntegAdminPost extends the WpssoPost class, which extends the WpssoAbstractWpMeta class.
 */
if ( ! class_exists( 'WpssoOpmIntegAdminPost' ) && class_exists( 'WpssoPost' ) ) {

	class WpssoOpmIntegAdminPost extends WpssoPost {

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			/*
			 * Do not add the Document SSO metabox to the organization or place post types.
			 */
			$this->p->options[ 'plugin_add_to_' . WPSSOOPM_CONTACT_POST_TYPE ]               = 0;
			$this->p->options[ 'plugin_add_to_' . WPSSOOPM_CONTACT_POST_TYPE . ':disabled' ] = true;
			$this->p->options[ 'plugin_add_to_' . WPSSOOPM_ORG_POST_TYPE ]                   = 0;
			$this->p->options[ 'plugin_add_to_' . WPSSOOPM_ORG_POST_TYPE . ':disabled' ]     = true;
			$this->p->options[ 'plugin_add_to_' . WPSSOOPM_PLACE_POST_TYPE ]                 = 0;
			$this->p->options[ 'plugin_add_to_' . WPSSOOPM_PLACE_POST_TYPE . ':disabled' ]   = true;

			/*
			 * This hook is fired once WordPress, plugins, and the theme are fully loaded and instantiated.
			 */
			add_action( 'wp_loaded', array( $this, 'add_wp_callbacks' ) );
		}

		/*
		 * Add WordPress action and filters callbacks.
		 */
		public function add_wp_callbacks() {

			if ( ! is_admin() ) return;	// Just in case.

			if ( ! empty( $_GET ) || 'post-new' === basename( $_SERVER[ 'PHP_SELF' ], '.php' ) ) {

				add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
			}
		}

		/*
		 * Use $obj = false to extend WpssoAbstractWpMeta->add_meta_boxes().
		 */
		public function add_meta_boxes( $post_type, $obj = false ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$post_id    = empty( $obj->ID ) ? 0 : $obj->ID;
			$capability = 'page' === $post_type ? 'edit_page' : 'edit_post';

			if ( ! current_user_can( $capability, $post_id ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'exiting early: cannot ' . $capability . ' for post id ' . $post_id );
				}

				return;
			}

			$metabox_screen  = $post_type;
			$metabox_context = 'normal';
			$metabox_prio    = 'default';

			if ( WPSSOOPM_CONTACT_POST_TYPE === $post_type ) {

				$metabox_id    = 'contact';
				$metabox_title = _x( 'Contact Point', 'metabox title', 'wpsso-organization-place' );
				$callback_args = array(	// Second argument passed to the callback function / method.
					'metabox_id'                         => $metabox_id,
					'metabox_title'                      => $metabox_title,
					'__block_editor_compatible_meta_box' => true,
				);

				add_meta_box( 'wpsso_' . $metabox_id, $metabox_title, array( $this, 'show_metabox' ),
					$metabox_screen, $metabox_context, $metabox_prio, $callback_args );

			} elseif ( WPSSOOPM_ORG_POST_TYPE === $post_type ) {

				$metabox_id    = 'org';
				$metabox_title = _x( 'Organization', 'metabox title', 'wpsso-organization-place' );
				$callback_args = array(	// Second argument passed to the callback function / method.
					'metabox_id'                         => $metabox_id,
					'metabox_title'                      => $metabox_title,
					'__block_editor_compatible_meta_box' => true,
				);

				add_meta_box( 'wpsso_' . $metabox_id, $metabox_title, array( $this, 'show_metabox' ),
					$metabox_screen, $metabox_context, $metabox_prio, $callback_args );

			} elseif ( WPSSOOPM_PLACE_POST_TYPE === $post_type ) {

				$metabox_id    = 'place';
				$metabox_title = _x( 'Place', 'metabox title', 'wpsso-organization-place' );
				$callback_args = array(	// Second argument passed to the callback function / method.
					'metabox_id'                         => $metabox_id,
					'metabox_title'                      => $metabox_title,
					'__block_editor_compatible_meta_box' => true,
				);

				add_meta_box( 'wpsso_' . $metabox_id, $metabox_title, array( $this, 'show_metabox' ),
					$metabox_screen, $metabox_context, $metabox_prio, $callback_args );
			}
		}

		public function show_metabox( $obj, $mb ) {

			echo '<style>#post-body-content { margin-bottom:0; }</style>';

			echo $this->get_metabox( $obj, $mb );
		}

		public function get_metabox( $obj, $mb ) {

			$args       = isset( $mb[ 'args' ] ) ? $mb[ 'args' ] : array();
			$metabox_id = isset( $args[ 'metabox_id' ] ) ? $args[ 'metabox_id' ] : '';
			$mod        = $this->p->page->get_mod( $use_post = false, $mod = false, $obj );
			$opts       = $this->get_options( $obj->ID );
			$def_opts   = $this->get_defaults( $obj->ID );

			/*
			 * $this->form property is inherited from the WpssoPost and WpssoAbstractWpMeta classes.
			 */
			$this->form = new SucomForm( $this->p, WPSSO_META_NAME, $opts, $def_opts, $this->p->id );

			wp_nonce_field( WpssoAdmin::get_nonce_action(), WPSSO_NONCE_NAME );

			$container_id = 'wpsso_mb_' . $metabox_id . '_inside';
			$filter_name  = 'wpsso_mb_' . $metabox_id . '_rows';

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'applying filters "' . $filter_name . '"' );
			}

			$table_rows = apply_filters( $filter_name, array(), $this->form, array(), $mod );

			$metabox_html = "\n" . '<div id="' . $container_id . '">';
			$metabox_html .= $this->p->util->metabox->get_table( $table_rows, 'wpsso-' . $metabox_id );
			$metabox_html .= '</div><!-- #'. $container_id . ' -->' . "\n";

			return $metabox_html;
		}
	}
}
