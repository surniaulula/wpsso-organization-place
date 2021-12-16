<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmConfig' ) ) {

	class WpssoOpmConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssoopm' => array(			// Plugin acronym.
					'version'     => '1.0.0-dev.6',	// Plugin version.
					'opt_version' => '2',		// Increment when changing default option values.
					'short'       => 'WPSSO OPM',	// Short plugin name.
					'name'        => 'WPSSO Organization and Place Manager',
					'desc'        => 'Manage organizations and places.',
					'slug'        => 'wpsso-organization-place',
					'base'        => 'wpsso-organization-place/wpsso-organization-place.php',
					'update_auth' => '',		// No premium version.
					'text_domain' => 'wpsso-organization-place',
					'domain_path' => '/languages',

					/**
					 * Required plugin and its version.
					 */
					'req' => array(
						'wpsso' => array(
							'name'          => 'WPSSO Core',
							'home'          => 'https://wordpress.org/plugins/wpsso/',
							'plugin_class'  => 'Wpsso',
							'version_const' => 'WPSSO_VERSION',
							'min_version'   => '9.12.0-dev.6',
						),
					),

					/**
					 * URLs or relative paths to plugin banners and icons.
					 */
					'assets' => array(

						/**
						 * Icon image array keys are '1x' and '2x'.
						 */
						'icons' => array(
							'1x' => 'images/icon-128x128.png',
							'2x' => 'images/icon-256x256.png',
						),
					),

					/**
					 * Library files loaded and instantiated by WPSSO.
					 */
					'lib' => array(
					),
				),
			),

			/**
			 * Additional add-on setting options.
			 */
			'opt' => array(
				'org_md_defaults' => array(
					'org_name'            => '',
					'org_name_alt'        => '',
					'org_desc'            => '',
					'org_url'             => '',
					'org_logo_url'        => '',
					'site_org_banner_url' => '',
					'org_schema_type'     => 'organization',
					'org_place_id'        => 'none',
				),
				'place_md_defaults' => array(
					'place_schema_type'              => 'local.business',	// Place Schema Type.
					'place_name'                     => '',			// Place Name.
					'place_name_alt'                 => '',			// Place Altername Name.
					'place_desc'                     => '',			// Place Description.
					'place_street_address'           => '',			// Street Address.
					'place_po_box_number'            => '',			// P.O. Box Number.
					'place_city'                     => '',			// City.
					'place_region'                   => '',			// State / Province.
					'place_postal_code'              => '',			// Zip / Postal Code.
					'place_country'                  => 'none',		// Country.
					'place_phone'                    => '',			// Telephone.
					'place_latitude'                 => '',			// Place Latitude.
					'place_longitude'                => '',			// Place Longitude.
					'place_altitude'                 => '',			// Place Altitude.
					'place_img_id'                   => '',			// Place Image ID.
					'place_img_id_lib'               => 'wp',
					'place_img_url'                  => '',			// or Place Image URL.
					'place_timezone'                 => 'UTC',		// Place Timezone.
					'place_day_sunday_open'          => 'none',
					'place_day_sunday_close'         => 'none',
					'place_day_monday_open'          => 'none',
					'place_day_monday_close'         => 'none',
					'place_day_tuesday_open'         => 'none',
					'place_day_tuesday_close'        => 'none',
					'place_day_wednesday_open'       => 'none',
					'place_day_wednesday_close'      => 'none',
					'place_day_thursday_open'        => 'none',
					'place_day_thursday_close'       => 'none',
					'place_day_friday_open'          => 'none',
					'place_day_friday_close'         => 'none',
					'place_day_saturday_open'        => 'none',
					'place_day_saturday_close'       => 'none',
					'place_day_publicholidays_open'  => 'none',
					'place_day_publicholidays_close' => 'none',
					'place_midday_close'             => 'none',
					'place_midday_open'              => 'none',
					'place_season_from_date'         => '',
					'place_season_to_date'           => '',
					'place_service_radius'           => '',			// Service Radius.
					'place_currencies_accepted'      => '',			// Currencies Accepted.
					'place_payment_accepted'         => '',			// Payment Accepted.
					'place_price_range'              => '',			// Price Range.
					'place_accept_res'               => 0,			// Accepts Reservations.
					'place_cuisine'                  => '',			// Serves Cuisine.
					'place_menu_url'                 => '',			// Food Menu URL.
					'place_order_urls'               => '',			// Order Action URL(s).
				),
			),
		);

		public static function get_version( $add_slug = false ) {

			$info =& self::$cf[ 'plugin' ][ 'wpssoopm' ];

			return $add_slug ? $info[ 'slug' ] . '-' . $info[ 'version' ] : $info[ 'version' ];
		}

		public static function set_constants( $plugin_file ) {

			if ( defined( 'WPSSOOPM_VERSION' ) ) {	// Define constants only once.

				return;
			}

			$info =& self::$cf[ 'plugin' ][ 'wpssoopm' ];

			/**
			 * Define fixed constants.
			 */
			define( 'WPSSOOPM_FILEPATH', $plugin_file );
			define( 'WPSSOOPM_PLUGINBASE', $info[ 'base' ] );	// Example: wpsso-organization-place/wpsso-organization-place.php
			define( 'WPSSOOPM_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_file ) ) ) );
			define( 'WPSSOOPM_PLUGINSLUG', $info[ 'slug' ] );	// Example: wpsso-organization-place
			define( 'WPSSOOPM_URLPATH', trailingslashit( plugins_url( '', $plugin_file ) ) );
			define( 'WPSSOOPM_VERSION', $info[ 'version' ] );

			define( 'WPSSOOPM_ORG_POST_TYPE', 'organization' );
			define( 'WPSSOOPM_PLACE_POST_TYPE', 'place' );

			/**
			 * Define variable constants.
			 */
			self::set_variable_constants();
		}

		public static function set_variable_constants( $var_const = null ) {

			if ( ! is_array( $var_const ) ) {

				$var_const = (array) self::get_variable_constants();
			}

			/**
			 * Define the variable constants, if not already defined.
			 */
			foreach ( $var_const as $name => $value ) {

				if ( ! defined( $name ) ) {

					define( $name, $value );
				}
			}
		}

		public static function get_variable_constants() {

			$var_const = array();

			$var_const[ 'WPSSOOPM_ORG_CATEGORY_TAXONOMY' ]   = false;
			$var_const[ 'WPSSOOPM_PLACE_CATEGORY_TAXONOMY' ] = false;

			/**
			 * Maybe override the default constant value with a pre-defined constant value.
			 */
			foreach ( $var_const as $name => $value ) {

				if ( defined( $name ) ) {

					$var_const[$name] = constant( $name );
				}
			}

			return $var_const;
		}

		public static function require_libs( $plugin_file ) {

			require_once WPSSOOPM_PLUGINDIR . 'lib/filters.php';
			require_once WPSSOOPM_PLUGINDIR . 'lib/org.php';
			require_once WPSSOOPM_PLUGINDIR . 'lib/place.php';
			require_once WPSSOOPM_PLUGINDIR . 'lib/post.php';
			require_once WPSSOOPM_PLUGINDIR . 'lib/register.php';

			add_filter( 'wpssoopm_load_lib', array( __CLASS__, 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $success = false, $filespec = '', $classname = '' ) {

			if ( false !== $success ) {

				return $success;
			}

			if ( ! empty( $classname ) ) {

				if ( class_exists( $classname ) ) {

					return $classname;
				}
			}

			if ( ! empty( $filespec ) ) {

				$file_path = WPSSOOPM_PLUGINDIR . 'lib/' . $filespec . '.php';

				if ( file_exists( $file_path ) ) {

					require_once $file_path;

					if ( empty( $classname ) ) {

						return SucomUtil::sanitize_classname( 'wpssoopm' . $filespec, $allow_underscore = false );
					}

					return $classname;
				}
			}

			return $success;
		}
	}
}
