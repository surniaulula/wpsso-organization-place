<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmRegister' ) ) {

	class WpssoOpmRegister {

		public function __construct() {

			register_activation_hook( WPSSOOPM_FILEPATH, array( $this, 'network_activate' ) );

			register_deactivation_hook( WPSSOOPM_FILEPATH, array( $this, 'network_deactivate' ) );

			if ( is_multisite() ) {

				add_action( 'wpmu_new_blog', array( $this, 'wpmu_new_blog' ), 10, 6 );

				add_action( 'wpmu_activate_blog', array( $this, 'wpmu_activate_blog' ), 10, 5 );
			}

			add_action( 'wpsso_init_options', array( __CLASS__, 'register_org_post_type' ), WPSSOOPM_ORG_MENU_ORDER, 0 );

			add_action( 'wpsso_init_options', array( __CLASS__, 'register_org_category_taxonomy' ), WPSSOOPM_ORG_MENU_ORDER, 0 );

			add_action( 'wpsso_init_options', array( __CLASS__, 'register_place_post_type' ), WPSSOOPM_PLACE_MENU_ORDER, 0 );

			add_action( 'wpsso_init_options', array( __CLASS__, 'register_place_category_taxonomy' ), WPSSOOPM_PLACE_MENU_ORDER, 0 );
		}

		/*
		 * Fires immediately after a new site is created.
		 */
		public function wpmu_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

			switch_to_blog( $blog_id );

			$this->activate_plugin();

			restore_current_blog();
		}

		/*
		 * Fires immediately after a site is activated (not called when users and sites are created by a Super Admin).
		 */
		public function wpmu_activate_blog( $blog_id, $user_id, $password, $signup_title, $meta ) {

			switch_to_blog( $blog_id );

			$this->activate_plugin();

			restore_current_blog();
		}

		public function network_activate( $sitewide ) {

			self::do_multisite( $sitewide, array( $this, 'activate_plugin' ) );
		}

		public function network_deactivate( $sitewide ) {

			self::do_multisite( $sitewide, array( $this, 'deactivate_plugin' ) );
		}

		/*
		 * uninstall.php defines constants before calling network_uninstall().
		 */
		public static function network_uninstall() {

			$sitewide = true;

			/*
			 * Uninstall from the individual blogs first.
			 */
			self::do_multisite( $sitewide, array( __CLASS__, 'uninstall_plugin' ) );
		}

		private static function do_multisite( $sitewide, $method, $args = array() ) {

			if ( is_multisite() && $sitewide ) {

				global $wpdb;

				$db_query = 'SELECT blog_id FROM '.$wpdb->blogs;

				$blog_ids = $wpdb->get_col( $db_query );

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );

					call_user_func_array( $method, array( $args ) );
				}

				restore_current_blog();

			} else {

				call_user_func_array( $method, array( $args ) );
			}
		}

		private function activate_plugin() {

			/*
			 * Register plugin install, activation, update times.
			 */
			if ( class_exists( 'WpssoUtilReg' ) ) {

				$version = WpssoOpmConfig::get_version();

				WpssoUtilReg::update_ext_version( 'wpssoopm', $version );
			}

			self::register_org_post_type();

			self::register_org_category_taxonomy();

			self::register_place_post_type();

			self::register_place_category_taxonomy();

			flush_rewrite_rules( $hard = false );	// Update only the 'rewrite_rules' option, not the .htaccess file.
		}

		private function deactivate_plugin() {

			unregister_post_type( WPSSOOPM_ORG_POST_TYPE );

			if ( defined( 'WPSSOOPM_ORG_CATEGORY_TAXONOMY' ) && WPSSOOPM_ORG_CATEGORY_TAXONOMY ) {

				unregister_taxonomy( WPSSOOPM_ORG_CATEGORY_TAXONOMY );
			}

			unregister_post_type( WPSSOOPM_PLACE_POST_TYPE );

			if ( defined( 'WPSSOOPM_PLACE_CATEGORY_TAXONOMY' ) && WPSSOOPM_PLACE_CATEGORY_TAXONOMY ) {

				unregister_taxonomy( WPSSOOPM_PLACE_CATEGORY_TAXONOMY );
			}

			flush_rewrite_rules( $hard = false );	// Update only the 'rewrite_rules' option, not the .htaccess file.
		}

		private static function uninstall_plugin() {}

		public static function register_org_post_type() {

			$is_public = false;

			$labels = array(
				'name'                     => _x( 'Organizations', 'post type general name', 'wpsso-organization-place' ),
				'singular_name'            => _x( 'Organization', 'post type singular name', 'wpsso-organization-place' ),
				'add_new'                  => __( 'Add Organization', 'wpsso-organization-place' ),
				'add_new_item'             => __( 'Add Organization', 'wpsso-organization-place' ),
				'edit_item'                => __( 'Edit Organization', 'wpsso-organization-place' ),
				'new_item'                 => __( 'New Organization', 'wpsso-organization-place' ),
				'view_item'                => __( 'View Organization', 'wpsso-organization-place' ),
				'view_items'               => __( 'View Organizations', 'wpsso-organization-place' ),
				'search_items'             => __( 'Search Organizations', 'wpsso-organization-place' ),
				'not_found'                => __( 'No organizations found', 'wpsso-organization-place' ),
				'not_found_in_trash'       => __( 'No organizations found in Trash', 'wpsso-organization-place' ),
				'parent_item_colon'        => __( 'Parent Organization:', 'wpsso-organization-place' ),
				'all_items'                => __( 'All Organizations', 'wpsso-organization-place' ),
				'archives'                 => __( 'Organization Archives', 'wpsso-organization-place' ),
				'attributes'               => __( 'Organization Attributes', 'wpsso-organization-place' ),
				'insert_into_item'         => __( 'Insert into organization', 'wpsso-organization-place' ),
				'uploaded_to_this_item'    => __( 'Uploaded to this organization', 'wpsso-organization-place' ),
				'featured_image'           => __( 'Organization Image', 'wpsso-organization-place' ),
				'set_featured_image'       => __( 'Set organization image', 'wpsso-organization-place' ),
				'remove_featured_image'    => __( 'Remove organization image', 'wpsso-organization-place' ),
				'use_featured_image'       => __( 'Use as organization image', 'wpsso-organization-place' ),
				'menu_name'                => _x( 'SSO Orgs', 'admin menu name', 'wpsso-organization-place' ),
				'filter_items_list'        => __( 'Filter organizations', 'wpsso-organization-place' ),
				'items_list_navigation'    => __( 'Organizations list navigation', 'wpsso-organization-place' ),
				'items_list'               => __( 'Organizations list', 'wpsso-organization-place' ),
				'name_admin_bar'           => _x( 'Organization', 'admin bar name', 'wpsso-organization-place' ),
				'item_published'	   => __( 'Organization published.', 'wpsso-organization-place' ),
				'item_published_privately' => __( 'Organization published privately.', 'wpsso-organization-place' ),
				'item_reverted_to_draft'   => __( 'Organization reverted to draft.', 'wpsso-organization-place' ),
				'item_scheduled'           => __( 'Organization scheduled.', 'wpsso-organization-place' ),
				'item_updated'             => __( 'Organization updated.', 'wpsso-organization-place' ),
			);

			$supports = false;

			if ( defined( 'WPSSOOPM_ORG_CATEGORY_TAXONOMY' ) && WPSSOOPM_ORG_CATEGORY_TAXONOMY ) {

				$taxonomies = array( WPSSOOPM_ORG_CATEGORY_TAXONOMY );

			} else {

				$taxonomies = array();
			}

			$args = array(
				'label'               => _x( 'Organization', 'post type label', 'wpsso-organization-place' ),
				'labels'              => $labels,
				'description'         => _x( 'Organization', 'post type description', 'wpsso-organization-place' ),
				'exclude_from_search' => false,	// Must be false for get_posts() queries.
				'public'              => $is_public,
				'publicly_queryable'  => $is_public,
				'show_ui'             => true,
				'show_in_nav_menus'   => true,
				'show_in_menu'        => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => WPSSOOPM_ORG_MENU_ORDER,
				'menu_icon'           => 'dashicons-groups',
				'capability_type'     => 'page',
				'hierarchical'        => false,
				'supports'            => $supports,
				'taxonomies'          => $taxonomies,
				'has_archive'         => 'orgs',
				'can_export'          => true,
				'show_in_rest'        => true,
			);

			register_post_type( WPSSOOPM_ORG_POST_TYPE, $args );
		}

		public static function register_org_category_taxonomy() {

			if ( ! defined( 'WPSSOOPM_ORG_CATEGORY_TAXONOMY' ) || ! WPSSOOPM_ORG_CATEGORY_TAXONOMY ) {

				return;
			}

			$is_public = false;

			$labels = array(
				'name'                       => __( 'Categories', 'wpsso-organization-place' ),
				'singular_name'              => __( 'Category', 'wpsso-organization-place' ),
				'menu_name'                  => _x( 'Categories', 'admin menu name', 'wpsso-organization-place' ),
				'all_items'                  => __( 'All Categories', 'wpsso-organization-place' ),
				'edit_item'                  => __( 'Edit Category', 'wpsso-organization-place' ),
				'view_item'                  => __( 'View Category', 'wpsso-organization-place' ),
				'update_item'                => __( 'Update Category', 'wpsso-organization-place' ),
				'add_new_item'               => __( 'Add New Category', 'wpsso-organization-place' ),
				'new_item_name'              => __( 'New Category Name', 'wpsso-organization-place' ),
				'parent_item'                => __( 'Parent Category', 'wpsso-organization-place' ),
				'parent_item_colon'          => __( 'Parent Category:', 'wpsso-organization-place' ),
				'search_items'               => __( 'Search Categories', 'wpsso-organization-place' ),
				'popular_items'              => __( 'Popular Categories', 'wpsso-organization-place' ),
				'separate_items_with_commas' => __( 'Separate categories with commas', 'wpsso-organization-place' ),
				'add_or_remove_items'        => __( 'Add or remove categories', 'wpsso-organization-place' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'wpsso-organization-place' ),
				'not_found'                  => __( 'No categories found.', 'wpsso-organization-place' ),
				'back_to_items'              => __( '← Back to categories', 'wpsso-organization-place' ),
			);

			$args = array(
				'label'              => _x( 'Categories', 'taxonomy label', 'wpsso-organization-place' ),
				'labels'             => $labels,
				'public'             => $is_public,
				'publicly_queryable' => $is_public,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'show_in_rest'       => true,
				'show_tagcloud'      => false,
				'show_in_quick_edit' => true,
				'show_admin_column'  => true,
				'description'        => _x( 'Categories for Organizations', 'taxonomy description', 'wpsso-organization-place' ),
				'hierarchical'       => true,
			);

			register_taxonomy( WPSSOOPM_ORG_CATEGORY_TAXONOMY, array( WPSSOOPM_ORG_POST_TYPE ), $args );
		}

		public static function register_place_post_type() {

			$is_public = false;

			$labels = array(
				'name'                     => _x( 'Places', 'post type general name', 'wpsso-organization-place' ),
				'singular_name'            => _x( 'Place', 'post type singular name', 'wpsso-organization-place' ),
				'add_new'                  => __( 'Add Place', 'wpsso-organization-place' ),
				'add_new_item'             => __( 'Add Place', 'wpsso-organization-place' ),
				'edit_item'                => __( 'Edit Place', 'wpsso-organization-place' ),
				'new_item'                 => __( 'New Place', 'wpsso-organization-place' ),
				'view_item'                => __( 'View Place', 'wpsso-organization-place' ),
				'view_items'               => __( 'View Places', 'wpsso-organization-place' ),
				'search_items'             => __( 'Search Places', 'wpsso-organization-place' ),
				'not_found'                => __( 'No places found', 'wpsso-organization-place' ),
				'not_found_in_trash'       => __( 'No places found in Trash', 'wpsso-organization-place' ),
				'parent_item_colon'        => __( 'Parent Place:', 'wpsso-organization-place' ),
				'all_items'                => __( 'All Places', 'wpsso-organization-place' ),
				'archives'                 => __( 'Place Archives', 'wpsso-organization-place' ),
				'attributes'               => __( 'Place Attributes', 'wpsso-organization-place' ),
				'insert_into_item'         => __( 'Insert into place', 'wpsso-organization-place' ),
				'uploaded_to_this_item'    => __( 'Uploaded to this place', 'wpsso-organization-place' ),
				'featured_image'           => __( 'Place Image', 'wpsso-organization-place' ),
				'set_featured_image'       => __( 'Set place image', 'wpsso-organization-place' ),
				'remove_featured_image'    => __( 'Remove place image', 'wpsso-organization-place' ),
				'use_featured_image'       => __( 'Use as place image', 'wpsso-organization-place' ),
				'menu_name'                => _x( 'SSO Places', 'admin menu name', 'wpsso-organization-place' ),
				'filter_items_list'        => __( 'Filter places', 'wpsso-organization-place' ),
				'items_list_navigation'    => __( 'Places list navigation', 'wpsso-organization-place' ),
				'items_list'               => __( 'Places list', 'wpsso-organization-place' ),
				'name_admin_bar'           => _x( 'Place', 'admin bar name', 'wpsso-organization-place' ),
				'item_published'	   => __( 'Place published.', 'wpsso-organization-place' ),
				'item_published_privately' => __( 'Place published privately.', 'wpsso-organization-place' ),
				'item_reverted_to_draft'   => __( 'Place reverted to draft.', 'wpsso-organization-place' ),
				'item_scheduled'           => __( 'Place scheduled.', 'wpsso-organization-place' ),
				'item_updated'             => __( 'Place updated.', 'wpsso-organization-place' ),
			);

			$supports = false;

			if ( defined( 'WPSSOOPM_PLACE_CATEGORY_TAXONOMY' ) && WPSSOOPM_PLACE_CATEGORY_TAXONOMY ) {

				$taxonomies = array( WPSSOOPM_PLACE_CATEGORY_TAXONOMY );

			} else {

				$taxonomies = array();
			}

			$args = array(
				'label'               => _x( 'Place', 'post type label', 'wpsso-organization-place' ),
				'labels'              => $labels,
				'description'         => _x( 'Location, place, or venue', 'post type description', 'wpsso-organization-place' ),
				'exclude_from_search' => false,	// Must be false for get_posts() queries.
				'public'              => $is_public,
				'publicly_queryable'  => $is_public,
				'show_ui'             => true,
				'show_in_nav_menus'   => true,
				'show_in_menu'        => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => WPSSOOPM_PLACE_MENU_ORDER,
				'menu_icon'           => 'dashicons-location',
				'capability_type'     => 'page',
				'hierarchical'        => false,
				'supports'            => $supports,
				'taxonomies'          => $taxonomies,
				'has_archive'         => 'places',
				'can_export'          => true,
				'show_in_rest'        => true,
			);

			register_post_type( WPSSOOPM_PLACE_POST_TYPE, $args );
		}

		public static function register_place_category_taxonomy() {

			if ( ! defined( 'WPSSOOPM_PLACE_CATEGORY_TAXONOMY' ) || ! WPSSOOPM_PLACE_CATEGORY_TAXONOMY ) {

				return;
			}

			$is_public = false;

			$labels = array(
				'name'                       => __( 'Categories', 'wpsso-organization-place' ),
				'singular_name'              => __( 'Category', 'wpsso-organization-place' ),
				'menu_name'                  => _x( 'Categories', 'admin menu name', 'wpsso-organization-place' ),
				'all_items'                  => __( 'All Categories', 'wpsso-organization-place' ),
				'edit_item'                  => __( 'Edit Category', 'wpsso-organization-place' ),
				'view_item'                  => __( 'View Category', 'wpsso-organization-place' ),
				'update_item'                => __( 'Update Category', 'wpsso-organization-place' ),
				'add_new_item'               => __( 'Add New Category', 'wpsso-organization-place' ),
				'new_item_name'              => __( 'New Category Name', 'wpsso-organization-place' ),
				'parent_item'                => __( 'Parent Category', 'wpsso-organization-place' ),
				'parent_item_colon'          => __( 'Parent Category:', 'wpsso-organization-place' ),
				'search_items'               => __( 'Search Categories', 'wpsso-organization-place' ),
				'popular_items'              => __( 'Popular Categories', 'wpsso-organization-place' ),
				'separate_items_with_commas' => __( 'Separate categories with commas', 'wpsso-organization-place' ),
				'add_or_remove_items'        => __( 'Add or remove categories', 'wpsso-organization-place' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'wpsso-organization-place' ),
				'not_found'                  => __( 'No categories found.', 'wpsso-organization-place' ),
				'back_to_items'              => __( '← Back to categories', 'wpsso-organization-place' ),
			);

			$args = array(
				'label'              => _x( 'Categories', 'taxonomy label', 'wpsso-organization-place' ),
				'labels'             => $labels,
				'public'             => $is_public,
				'publicly_queryable' => $is_public,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'show_in_rest'       => true,
				'show_tagcloud'      => false,
				'show_in_quick_edit' => true,
				'show_admin_column'  => true,
				'description'        => _x( 'Categories for Places', 'taxonomy description', 'wpsso-organization-place' ),
				'hierarchical'       => true,
			);

			register_taxonomy( WPSSOOPM_PLACE_CATEGORY_TAXONOMY, array( WPSSOOPM_PLACE_POST_TYPE ), $args );
		}
	}
}
