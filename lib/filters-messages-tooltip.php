<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2022 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmFiltersMessagesTooltip' ) ) {

	class WpssoOpmFiltersMessagesTooltip {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/**
		 * Instantiated by WpssoOpmFiltersMessages->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			require_once WPSSOOPM_PLUGINDIR . 'lib/filters-messages-tooltip-meta.php';

			new WpssoOpmFiltersMessagesTooltipMeta( $plugin, $addon );
		}
	}
}
