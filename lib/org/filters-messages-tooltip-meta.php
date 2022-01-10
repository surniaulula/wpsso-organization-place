<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2022 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmOrgFiltersMessagesTooltipMeta' ) ) {

	class WpssoOpmOrgFiltersMessagesTooltipMeta {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/**
		 * Instantiated by WpssoOpmFiltersMessagesTooltipMeta->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array( 
				'messages_tooltip_meta_org' => 2,
			) );
		}

		public function filter_messages_tooltip_meta_org( $text, $msg_key ) {

			switch ( $msg_key ) {

				case 'tooltip-meta-org_name':

					$text = __( 'The complete or common name for the organization.', 'wpsso-organization-place' );

					break;

				case 'tooltip-meta-org_name_alt':

					$text = __( 'An alternate name for the organization that you would like Google to consider.', 'wpsso-organization-place' );

					break;

				case 'tooltip-meta-org_desc':

					$text = __( 'A description for the organization.', 'wpsso-organization-place' );

					break;

				case 'tooltip-meta-org_url':

					$text = __( 'The website URL for the organization.', 'wpsso-organization-place' );

					break;

				case 'tooltip-meta-org_logo_url':

					$text = $this->p->msgs->get( 'tooltip-site_org_logo_url' );

					break;

				case 'tooltip-meta-org_banner_url':

					$text = $this->p->msgs->get( 'tooltip-site_org_banner_url' );

					break;

				case 'tooltip-meta-org_schema_type':

					$text = __( 'You may optionally choose a more accurate Schema type for this organization (default is Organization).', 'wpsso-organization-place' ) . ' ';

					$text .= __( 'Note that Google does not recognize most Schema Organization sub-types as valid organizations, so do not change this value unless you are certain that your selected Schema Organization sub-type will be recognized as a valid Organization by Google.', 'wpsso-organization-place' );

					break;

				case 'tooltip-meta-org_place_id':

					$text = __( 'Select an optional place (ie. location) for this organization.', 'wpsso-organization-place' );

					break;
			}

			return $text;
		}
	}
}
