<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmFilters' ) ) {

	class WpssoOpmFilters {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/*
		 * Instantiated by WpssoOpm->init_objects().
		 */
		public function __construct( &$plugin, &$addon ) {

			static $do_once = null;

			if ( $do_once ) return;	// Stop here.

			$do_once = true;

			$this->p =& $plugin;
			$this->a =& $addon;

			require_once WPSSOOPM_PLUGINDIR . 'lib/filters-options.php';

			new WpssoOpmFiltersOptions( $plugin, $addon );

			require_once WPSSOOPM_PLUGINDIR . 'lib/filters-upgrade.php';

			new WpssoOpmFiltersUpgrade( $plugin, $addon );

			if ( is_admin() ) {

				require_once WPSSOOPM_PLUGINDIR . 'lib/filters-edit.php';

				new WpssoOpmFiltersEdit( $plugin, $addon );
			}

			require_once WPSSOOPM_PLUGINDIR . 'lib/place/filters-og.php';

			new WpssoOpmPlaceFiltersOg( $plugin, $addon );

			require_once WPSSOOPM_PLUGINDIR . 'lib/place/filters-schema.php';

			new WpssoOpmPlaceFiltersSchema( $plugin, $addon );
		}
	}
}
