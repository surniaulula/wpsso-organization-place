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
 * Description: Manage Organizations (publisher, organizer, etc.) and Places for Facebook, Pinterest, and Google local business markup.
 * Requires Plugins: wpsso
 * Requires PHP: 7.2
 * Requires At Least: 5.2
 * Tested Up To: 6.1.1
 * Version: 1.7.0-b.1
 *
 * Version Numbering: {major}.{minor}.{bugfix}[-{stage}.{level}]
 *
 *      {major}         Major structural code changes and/or incompatible API changes (ie. breaking changes).
 *      {minor}         New functionality was added or improved in a backwards-compatible manner.
 *      {bugfix}        Backwards-compatible bug fixes or small improvements.
 *      {stage}.{level} Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).
 *
 * Copyright 2014-2022 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoAbstractAddOn' ) ) {

	require_once dirname( __FILE__ ) . '/lib/abstract/add-on.php';
}

if ( ! class_exists( 'WpssoOpm' ) ) {

	class WpssoOpm extends WpssoAbstractAddOn {

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
