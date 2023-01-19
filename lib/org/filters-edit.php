<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2023 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmOrgFiltersEdit' ) ) {

	class WpssoOpmOrgFiltersEdit {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/**
		 * Instantiated by WpssoOpmFiltersEdit->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'form_cache_org_names'  => 1,
				'metabox_org_meta_rows' => 4,
			) );
		}

		public function filter_form_cache_org_names( $mixed ) {

			$org_names = WpssoOpmOrg::get_names( $schema_type = '' );

			return is_array( $mixed ) ? $mixed + $org_names : $org_names;
		}

		public function filter_metabox_org_meta_rows( $table_rows, $form, $head_info, $mod ) {

			$org_types_select = $this->p->util->get_form_cache( 'org_types_select' );
			$place_names      = $this->p->util->get_form_cache( 'place_names', $add_none = true );

			$table_rows[ 'org_name' ] = '' .
				$form->get_th_html( _x( 'Organization Name', 'option label', 'wpsso-organization-place' ),
					$css_class = '', $css_id = 'meta-org_name' ) .
				'<td>' . $form->get_input( 'org_name', $css_class = 'long_name is_required' ) . '</td>';

			$table_rows[ 'org_name_alt' ] = '' .
				$form->get_th_html( _x( 'Organization Alternate Name', 'option label', 'wpsso-organization-place' ),
					$css_class = '', $css_id = 'meta-org_name_alt' ) .
				'<td>' . $form->get_input( 'org_name_alt', $css_class = 'long_name' ) . '</td>';

			$table_rows[ 'org_desc' ] = '' .
				$form->get_th_html( _x( 'Organization Description', 'option label', 'wpsso-organization-place' ),
					$css_class = '', $css_id = 'meta-org_desc' ) .
				'<td>' . $form->get_textarea( 'org_desc' ) . '</td>';

			$table_rows[ 'org_url' ] = '' .
				$form->get_th_html( _x( 'Organization WebSite URL', 'option label', 'wpsso-organization-place' ),
					$css_class = '', $css_id = 'meta-org_url' ) .
				'<td>' . $form->get_input( 'org_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_logo_url' ] = '' .
				$form->get_th_html( '<a href="https://developers.google.com/search/docs/advanced/structured-data/logo">' .
				_x( 'Organization Logo URL', 'option label', 'wpsso-organization-place' ) . '</a>',
					$css_class = '', $css_id = 'meta-org_logo_url' ) .
				'<td>' . $form->get_input( 'org_logo_url', $css_class = 'wide is_required' ) . '</td>';

			$table_rows[ 'org_banner_url' ] = '' .
				$form->get_th_html( '<a href="https://developers.google.com/search/docs/data-types/article#logo-guidelines">' .
				_x( 'Organization Banner URL', 'option label', 'wpsso-organization-place' ) . '</a>',
					$css_class = '', $css_id = 'meta-org_banner_url' ) .
				'<td>' . $form->get_input( 'org_banner_url', $css_class = 'wide is_required' ) . '</td>';

			$table_rows[ 'org_schema_type' ] = '' .
				$form->get_th_html( _x( 'Organization Schema Type', 'option label', 'wpsso-organization-place' ),
					$css_class = '', $css_id = 'meta-org_schema_type' ) .
				'<td>' . $form->get_select( 'org_schema_type', $org_types_select, $css_class = 'schema_type', $css_id = '',
					$is_assoc = true, $is_disabled = false, $selected = false, $event_names = array( 'on_focus_load_json' ),
						$event_args = array( 'json_var' => 'schema_org_types' ) ) . '</td>';

			$table_rows[ 'org_place_id' ] = '' .
				$form->get_th_html( _x( 'Organization Location', 'option label', 'wpsso-organization-place' ),
					$css_class = '', $css_id = 'meta-org_place_id' ) .
				'<td>' . $form->get_select( 'org_place_id', $place_names, $css_class = 'long_name', $css_id = '', $is_assoc = true ) . '</td>';

			$table_rows[ 'subsection_google_knowledgegraph' ] = '<td colspan="2" class="subsection"><h4>' .
				_x( 'Google\'s Knowledge Graph', 'metabox title', 'wpsso-organization-place' ) . '</h4></td>';

			foreach ( WpssoConfig::get_social_accounts() as $key => $label ) {

				$opt_key = 'org_sameas_' . $key;

				$table_rows[ $opt_key ] = '' .
					$form->get_th_html( _x( $label, 'option value', 'wpsso-organization-place' ),
						$css_class = 'nowrap', $opt_key ) .
					'<td>' . $form->get_input( $opt_key, strpos( $opt_key, '_url' ) ? 'wide' : '' ) . '</td>';
			}

			return $table_rows;
		}
	}
}
