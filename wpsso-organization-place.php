<?php
/**
 * Plugin Name: WPSSO Organization and Place Manager
 * Plugin Slug: wpsso-organization-place
 * Text Domain: wpsso-organization-place
 * Domain Path: /languages
 * Plugin URI: https://wpsso.com/extend/plugins/wpsso-organization-place/
 * Assets URI: https://surniaulula.github.io/wpsso-organization-place/assets/
 * Author: JS Morisset
 * Author URI: https://wpsso.com/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Description: Manage organizations and places.
 * Requires PHP: 7.2
 * Requires At Least: 5.2
 * Tested Up To: 5.8.2
 * Version: 1.0.0-dev.6
 * 
 * Version Numbering: {major}.{minor}.{bugfix}[-{stage}.{level}]
 *
 *      {major}         Major structural code changes / re-writes or incompatible API changes.
 *      {minor}         New functionality was added or improved in a backwards-compatible manner.
 *      {bugfix}        Backwards-compatible bug fixes or small improvements.
 *      {stage}.{level} Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).
 * 
 * Copyright 2014-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoAddOn' ) ) {

	require_once dirname( __FILE__ ) . '/lib/abstracts/add-on.php';	// WpssoAddOn class.
}

if ( ! class_exists( 'WpssoOpm' ) ) {

	class WpssoOpm extends WpssoAddOn {

		protected $p;		// Wpsso class object.

		private static $instance = null;	// WpssoOpm class object.

		public function __construct() {

			parent::__construct( __FILE__, __CLASS__ );
		}

		public static function &get_instance() {

			if ( null === self::$instance ) {

				self::$instance = new self;
			}

			return self::$instance;
		}

		public function init_textdomain() {

			load_plugin_textdomain( 'wpsso-organization-place', false, 'wpsso-organization-place/languages/' );
		}

		public function init_objects() {

			$this->p =& Wpsso::get_instance();

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( $this->get_missing_requirements() ) {	// Returns false or an array of missing requirements.

				return;	// Stop here.
			}

			new WpssoOpmFilters( $this->p, $this );

			if ( class_exists( 'WpssoOpmPost' ) ) {		// Extends on WpssoPost, which may be missing if WPSSO Core is deactivated.

				new WpssoOpmPost( $this->p, $this );
			}
		}
	}

	WpssoOpm::get_instance();
}
