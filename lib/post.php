<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2023 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoPost' ) ) {

	if ( defined( 'WPSSO_PLUGINDIR' ) ) {

		require_once WPSSO_PLUGINDIR . 'lib/post.php';
	}
}

if ( ! class_exists( 'WpssoOpmPost' ) && class_exists( 'WpssoPost' ) ) {

	class WpssoOpmPost extends WpssoPost {

		/**
		 * Instantiated by WpssoOpm->init_objects().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$this->p->options[ 'plugin_add_to_' . WPSSOOPM_ORG_POST_TYPE ]                 = 0;
			$this->p->options[ 'plugin_add_to_' . WPSSOOPM_ORG_POST_TYPE . ':disabled' ]   = true;
			$this->p->options[ 'plugin_add_to_' . WPSSOOPM_PLACE_POST_TYPE ]               = 0;
			$this->p->options[ 'plugin_add_to_' . WPSSOOPM_PLACE_POST_TYPE . ':disabled' ] = true;

			/**
			 * This hook is fired once WP, all plugins, and the theme are fully loaded and instantiated.
			 */
			add_action( 'wp_loaded', array( $this, 'add_wp_hooks' ) );
		}

		/**
		 * Add WordPress action and filters hooks.
		 */
		public function add_wp_hooks() {

			$is_admin = is_admin();	// Only check once.

			if ( ! empty( $_GET ) || basename( $_SERVER[ 'PHP_SELF' ] ) === 'post-new.php' ) {

				add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
			}
		}

		/**
		 * Use $post_obj = false to extend WpssoAbstractWpMeta->add_meta_boxes().
		 */
		public function add_meta_boxes( $post_type, $post_obj = false ) {

			$post_id = empty( $post_obj->ID ) ? 0 : $post_obj->ID;

			if ( ( 'page' === $post_type && ! current_user_can( 'edit_page', $post_id ) ) || ! current_user_can( 'edit_post', $post_id ) ) {

				return;
			}

			$metabox_screen  = $post_type;
			$metabox_context = 'normal';
			$metabox_prio    = 'default';
			$callback_args   = array(	// Second argument passed to the callback function / method.
				'__block_editor_compatible_meta_box' => true,
			);

			if ( WPSSOOPM_ORG_POST_TYPE === $post_type ) {

				$metabox_id    = 'org';
				$metabox_title = _x( 'Organization', 'metabox title', 'wpsso-organization-place' );

				add_meta_box( 'wpsso_' . $metabox_id, $metabox_title,
					array( $this, 'show_metabox_' . $metabox_id . '_meta' ), $metabox_screen,
						$metabox_context, $metabox_prio, $callback_args );

			} elseif ( WPSSOOPM_PLACE_POST_TYPE === $post_type ) {

				$metabox_id    = 'place';
				$metabox_title = _x( 'Place', 'metabox title', 'wpsso-organization-place' );

				add_meta_box( 'wpsso_' . $metabox_id, $metabox_title,
					array( $this, 'show_metabox_' . $metabox_id . '_meta' ), $metabox_screen,
						$metabox_context, $metabox_prio, $callback_args );
			}
		}

		public function show_metabox_org_meta( $post_obj ) {

			$this->show_metabox_for_metabox_id( $post_obj, $metabox_id = 'org' );
		}

		public function get_metabox_org_meta( $post_obj ) {

			return $this->get_metabox_for_metabox_id( $post_obj, $metabox_id = 'org' );
		}

		public function show_metabox_place_meta( $post_obj ) {

			$this->show_metabox_for_metabox_id( $post_obj, $metabox_id = 'place' );
		}

		public function get_metabox_place_meta( $post_obj ) {

			return $this->get_metabox_for_metabox_id( $post_obj, $metabox_id = 'place' );
		}

		private function show_metabox_for_metabox_id( $post_obj, $metabox_id ) {

			echo '<style>#post-body-content { margin-bottom:0; }</style>';

			echo $this->get_metabox_for_metabox_id( $post_obj, $metabox_id );
		}

		public function get_metabox_for_metabox_id( $post_obj, $metabox_id ) {

			$container_id = 'wpsso_metabox_' . $metabox_id . '_inside';
			$mod          = $this->get_mod( $post_obj->ID );
			$opts         = $this->get_options( $post_obj->ID );
			$def_opts     = $this->get_defaults( $post_obj->ID );

			$this->form = new SucomForm( $this->p, WPSSO_META_NAME, $opts, $def_opts, $this->p->id );

			wp_nonce_field( WpssoAdmin::get_nonce_action(), WPSSO_NONCE_NAME );

			$filter_name = 'wpsso_metabox_' . $metabox_id . '_meta_rows';

			$table_rows = (array) apply_filters( $filter_name, array(), $this->form, array(), $mod );

			$metabox_html = "\n" . '<div id="' . $container_id . '">';
			$metabox_html .= $this->p->util->metabox->get_table( $table_rows, 'wpsso-' . $metabox_id );
			$metabox_html .= '</div><!-- #'. $container_id . ' -->' . "\n";

			return $metabox_html;
		}
	}
}
