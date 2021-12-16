<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmFiltersEdit' ) ) {

	class WpssoOpmFiltersEdit {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/**
		 * Instantiated by WpssoOpm->init_objects().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			require_once WPSSOOPM_PLUGINDIR . 'lib/org/filters-edit.php';

			new WpssoOpmOrgFiltersEdit( $plugin, $addon );

			require_once WPSSOOPM_PLUGINDIR . 'lib/place/filters-edit.php';

			new WpssoOpmPlaceFiltersEdit( $plugin, $addon );
		}
	}
}
