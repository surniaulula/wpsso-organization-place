<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2025 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmServiceFiltersEdit' ) ) {

	class WpssoOpmServiceFiltersEdit {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/*
		 * Instantiated by WpssoOpmFiltersEdit->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'form_cache_service_names' => 1,
				'mb_service_rows'          => 4,
			) );
		}

		public function filter_form_cache_service_names( $mixed ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$service_names = WpssoOpmService::get_names( $schema_type = '' );
			$service_names = is_array( $mixed ) ? $mixed + $service_names : $service_names;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log_arr( 'service_names', $service_names);
			}

			return $service_names;
		}

		public function filter_mb_service_rows( $table_rows, $form, $head_info, $mod ) {

			$org_names          = $this->p->util->get_form_cache( 'org_names', $add_none = true );
			$person_names       = $this->p->util->get_form_cache( 'person_names', $add_none = true );
			$service_types      = $this->p->util->get_form_cache( 'service_types_select', $add_none = false );
			$offer_catalogs_max = SucomUtil::get_const( 'WPSSO_SCHEMA_OFFER_CATALOGS_MAX', 20 );

			$table_rows[ 'service_name' ] = '' .
				$form->get_th_html( _x( 'Service Name', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-service_name' ) .
				'<td>' . $form->get_input( 'service_name', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'service_name_alt' ] = '' .
				$form->get_th_html( _x( 'Service Alternate Name', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-service_name_alt' ) .
				'<td>' . $form->get_input( 'service_name_alt', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'service_desc' ] = '' .
				$form->get_th_html( _x( 'Service Description', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-service_desc' ) .
				'<td>' . $form->get_textarea( 'service_desc' ) . '</td>';

			$table_rows[ 'service_schema_type' ] = '' .
				$form->get_th_html( _x( 'Service Schema Type', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-service_schema_type' ) .
				'<td>' . $form->get_select( 'service_schema_type', $service_types, $css_class = 'schema_type', $css_id = '',
					$is_assoc = true, $is_disabled = false, $selected = false, $event_names = array( 'on_focus_load_json', 'on_change_unhide_rows' ),
						$event_args = array(
							'json_var' => 'schema_service_types',
							'exp_secs'  => WPSSO_CACHE_SELECT_JSON_EXP_SECS,	// Create and read from a javascript URL.
							'is_transl' => true,					// No label translation required.
							'is_sorted' => true,					// No label sorting required.
						)
					) . '</td>';

			$table_rows[ 'service_prov_org_id' ] = '' .
				$form->get_th_html( _x( 'Service Provider Org.', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-service_prov_org_id' ) .
				'<td>' . $form->get_select( 'service_prov_org_id', $org_names, $css_class = 'wide', $css_id = '',
					$is_assoc = true, $is_disabled = false, $selected = false, $event_names = array( 'on_focus_load_json' ),
						$event_args = array( 'json_var' => 'org_names' ) ) . '</td>';

			$table_rows[ 'service_prov_person_id' ] = '' .
				$form->get_th_html( _x( 'Service Provider Person', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-service_prov_person_id' ) .
				'<td>' . $form->get_select( 'service_prov_person_id', $person_names, $css_class = 'wide', $css_id = '',
					$is_assoc = true, $is_disabled = false, $selected = false, $event_names = array( 'on_focus_load_json' ),
						$event_args = array( 'json_var' => 'person_names' ) ) . '</td>';

			$table_rows[ 'service_latitude' ] = '' .
				$form->get_th_html( _x( 'Service Latitude', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-service_latitude' ) .
				'<td>' . $form->get_input( 'service_latitude', $css_class = 'latitude' ) . ' ' .
					_x( 'decimal degrees', 'option comment', 'wpsso-organization-place' ) . '</td>';

			$table_rows[ 'service_longitude' ] = '' .
				$form->get_th_html( _x( 'Service Longitude', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-service_longitude' ) .
				'<td>' . $form->get_input( 'service_longitude', $css_class = 'longitude' ) . ' ' .
					_x( 'decimal degrees', 'option comment', 'wpsso-organization-place' ) . '</td>';

			$table_rows[ 'service_radius' ] = '' .
				$form->get_th_html( _x( 'Service Radius', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-service_radius' ) .
				'<td>' . $form->get_input( 'service_radius', $css_class = 'short' ) . ' ' .
					_x( 'meters from coordinates', 'option comment', 'wpsso-organization-place' ) . '</td>';

			$table_rows[ 'service_offer_catalogs' ] = '' .
				$form->get_th_html( _x( 'Offer Catalogs', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-service_offer_catalogs' ) .
				'<td>' . $form->get_mixed_multi( array(
					'service_offer_catalog' => array(
						'input_label' => _x( 'Catalog Name', 'option label', 'wpsso-organization-place' ),
						'input_type'  => 'text',
						'input_class' => 'wide offer_catalog_name',
					),
					'service_offer_catalog_text' => array(
						'input_label' => _x( 'Catalog Description', 'option label', 'wpsso-organization-place' ),
						'input_type'  => 'textarea',
						'input_class' => 'wide offer_catalog_text',
					),
					'service_offer_catalog_url' => array(
						'input_label' => _x( 'Catalog URL', 'option label', 'wpsso-organization-place' ),
						'input_type'  => 'text',
						'input_class' => 'wide offer_catalog_url',
					),
				), $css_class = '', $css_id = 'service_offer_catalogs', $offer_catalogs_max, $show_first = 1 ) . '</td>';

			return $table_rows;
		}
	}
}
