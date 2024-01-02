<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmPlaceFiltersSchema' ) ) {

	class WpssoOpmPlaceFiltersSchema {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/*
		 * Instantiated by WpssoOpmFilters->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'json_array_schema_type_ids'                 => 2,
				'json_prop_https_schema_org_potentialaction' => 5,
			) );
		}

		public function filter_json_array_schema_type_ids( $type_ids, $mod ) {

			$place_opts = WpssoOpmPlace::has_md_place( $mod );	// Returns false or place array.

			if ( empty( $place_opts ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'exiting early: no place options found' );
				}

				return $type_ids;	// Stop here.
			}

			$type_ids[ $place_opts[ 'place_schema_type' ] ] = true;

			return $type_ids;
		}

		public function filter_json_prop_https_schema_org_potentialaction( $action_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $is_main ) {

				if ( ! empty( $mt_og[ 'place:business:order_url' ] ) ) {

					$action_data[] = array(
						'@context' => 'https://schema.org',
						'@type'    => 'OrderAction',
						'target'   => $mt_og[ 'place:business:order_url' ],
					);
				}
			}

			return $action_data;
		}
	}
}
