<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmConfig' ) ) {

	class WpssoOpmConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssoopm' => array(			// Plugin acronym.
					'version'     => '3.2.1',	// Plugin version.
					'opt_version' => '2',		// Increment when changing default option values.
					'short'       => 'WPSSO OPM',	// Short plugin name.
					'name'        => 'WPSSO Organization and Place Manager',
					'desc'        => 'Manage Organizations (publisher, organizer, etc.) and Places for Facebook, Pinterest, and Google local business markup.',
					'slug'        => 'wpsso-organization-place',
					'base'        => 'wpsso-organization-place/wpsso-organization-place.php',
					'update_auth' => '',		// No premium version.
					'text_domain' => 'wpsso-organization-place',
					'domain_path' => '/languages',

					/*
					 * Required plugin and its version.
					 */
					'req' => array(
						'wpsso' => array(
							'name'          => 'WPSSO Core',
							'home'          => 'https://wordpress.org/plugins/wpsso/',
							'plugin_class'  => 'Wpsso',
							'version_const' => 'WPSSO_VERSION',
							'min_version'   => '18.15.0',
						),
					),

					/*
					 * URLs or relative paths to plugin banners and icons.
					 */
					'assets' => array(

						/*
						 * Icon image array keys are '1x' and '2x'.
						 */
						'icons' => array(
							'1x' => 'images/icon-128x128.png',
							'2x' => 'images/icon-256x256.png',
						),
					),

					/*
					 * Library files loaded and instantiated by WPSSO.
					 */
					'lib' => array(
						'integ' => array(
							'admin' => array(
								'post' => 'Post Edit Page',
							),
						),
					),
				),
			),

			/*
			 * Additional add-on setting options.
			 */
			'opt' => array(
				'org_md_defaults' => array(
					'org_name'                   => '',
					'org_name_alt'               => '',
					'org_desc'                   => '',
					'org_url'                    => '',
					'org_logo_url'               => '',
					'org_banner_url'             => '',
					'org_place_id'               => 'none',
					'org_schema_type'            => 'organization',
					'org_pub_principles_url'     => '',		// Publishing Principles URL.
					'org_corrections_policy_url' => '',		// Corrections Policy URL.
					'org_diversity_policy_url'   => '',		// Diversity Policy URL.
					'org_ethics_policy_url'      => '',		// Ethics Policy URL.
					'org_fact_check_policy_url'  => '',		// Fact Checking Policy URL.
					'org_feedback_policy_url'    => '',		// Feedback Policy URL.
					'org_masthead_url'           => '',		// Masthead Page URL.
					'org_coverage_policy_url'    => '',		// Coverage Priorities Policy URL.
					'org_no_bylines_policy_url'  => '',		// No Bylines Policy URL.
					'org_sources_policy_url'     => '',		// Unnamed Sources Policy URL.
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
					'place_timezone'                 => 'UTC',		// Place Timezone.
					'place_img_id'                   => '',			// Place Image ID.
					'place_img_id_lib'               => 'wp',
					'place_img_url'                  => '',			// or Place Image URL.
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
			'form' => array(
				'org_is_defaults' => array(
					'schema_def_pub_org_id'             => 'Default Publisher Org.',
					'schema_def_prov_org_id'            => 'Default Provider Org.',
					'schema_def_fund_org_id'            => 'Default Funder Org.',
					'schema_def_event_performer_org_id' => 'Default Event Performer Org.',
					'schema_def_event_organizer_org_id' => 'Default Event Organizer Org.',
					'schema_def_event_fund_org_id'      => 'Default Event Funder Org.',
					'schema_def_job_hiring_org_id'      => 'Default Job Hiring Org.',
				),
				'place_is_defaults' => array(
					'schema_def_event_location_id' => 'Default Event Venue',
					'schema_def_job_location_id'   => 'Default Job Location',
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

			/*
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

			/*
			 * Define variable constants.
			 */
			self::set_variable_constants();
		}

		public static function set_variable_constants( $var_const = null ) {

			if ( ! is_array( $var_const ) ) {

				$var_const = self::get_variable_constants();
			}

			/*
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

			/*
			 * MENU_ORDER (aka menu_position):
			 *
			 *	null – below Comments
			 *	5 – below Posts
			 *	10 – below Media
			 *	15 – below Links
			 *	20 – below Pages
			 *	25 – below comments
			 *	60 – below first separator
			 *	65 – below Plugins
			 *	70 – below Users
			 *	75 – below Tools
			 *	80 – below Settings
			 *	100 – below second separator
			 */
			$var_const[ 'WPSSOOPM_ORG_MENU_ORDER' ]          = 82;
			$var_const[ 'WPSSOOPM_ORG_CATEGORY_TAXONOMY' ]   = false;
			$var_const[ 'WPSSOOPM_PLACE_MENU_ORDER' ]        = 82;
			$var_const[ 'WPSSOOPM_PLACE_CATEGORY_TAXONOMY' ] = false;

			/*
			 * Maybe override the default constant value with a pre-defined constant value.
			 */
			foreach ( $var_const as $name => $value ) {

				if ( defined( $name ) ) {

					$var_const[$name] = constant( $name );
				}
			}

			return $var_const;
		}

		/*
		 * Require library files with functions or static methods in require_libs().
		 *
		 * Require and instantiate library files with dynamic methods in init_objects().
		 */
		public static function require_libs( $plugin_file ) {

			require_once WPSSOOPM_PLUGINDIR . 'lib/filters.php';
			require_once WPSSOOPM_PLUGINDIR . 'lib/org.php';
			require_once WPSSOOPM_PLUGINDIR . 'lib/place.php';
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
