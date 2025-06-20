<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2025 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmOrgFiltersEdit' ) ) {

	class WpssoOpmOrgFiltersEdit {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/*
		 * Instantiated by WpssoOpmFiltersEdit->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'form_cache_org_names' => 1,
				'mb_org_rows'          => 4,
			) );
		}

		public function filter_form_cache_org_names( $mixed ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$org_names = WpssoOpmOrg::get_names( $schema_type = '' );
			$org_names = is_array( $mixed ) ? $mixed + $org_names : $org_names;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log_arr( 'org_names', $org_names);
			}

			return $org_names;
		}

		public function filter_mb_org_rows( $table_rows, $form, $head_info, $mod ) {

			$args = array(
				'select' => array(
					'contact'     => $this->p->util->get_form_cache( 'contact_names', $add_none = true ),
					'org_types'   => $this->p->util->get_form_cache( 'strict_org_types_select', $add_none = false ),
					'place'       => $this->p->util->get_form_cache( 'place_names', $add_none = false ),
					'place_types' => $this->p->util->get_form_cache( 'place_types_select', $add_none = false ),
				),
				'tr_class_org' => '',
				'tr_class_org_news_media' => $this->p->schema->get_children_css_class( 'news.media.organization', 'hide_org_schema_type' ),
			);

			$table_rows[ 'org_name' ] = '' .
				$form->get_th_html( _x( 'Organization Name', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-org_name' ) .
				'<td>' . $form->get_input( 'org_name', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_name_alt' ] = '' .
				$form->get_th_html( _x( 'Organization Alternate Name', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-org_name_alt' ) .
				'<td>' . $form->get_input( 'org_name_alt', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_desc' ] = '' .
				$form->get_th_html( _x( 'Organization Description', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-org_desc' ) .
				'<td>' . $form->get_textarea( 'org_desc' ) . '</td>';

			$table_rows[ 'org_place_id' ] = '' .
				$form->get_th_html( _x( 'Organization Location', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-org_place_id' ) .
				'<td>' . $form->get_select( 'org_place_id', $args[ 'select' ][ 'place' ], $css_class = 'wide', $css_id = '', $is_assoc = true ) . '</td>';

			$table_rows[ 'org_schema_type' ] = '' .
				$form->get_th_html( _x( 'Organization Schema Type', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-org_schema_type' ) .
				'<td>' . $form->get_select( 'org_schema_type', $args[ 'select' ][ 'org_types' ], $css_class = 'schema_type', $css_id = '',
					$is_assoc = true, $is_disabled = false, $selected = false, array( 'on_focus_load_json', 'on_change_unhide_rows' ),
						$event_args = array(
							'json_var' => 'schema_org_types',
							'exp_secs'  => WPSSO_CACHE_SELECT_JSON_EXP_SECS,	// Create and read from a javascript URL.
							'is_transl' => true,					// No label translation required.
							'is_sorted' => true,					// No label sorting required.
						)
					) . $this->p->msgs->get( 'info-meta-org-schema-type' ) . '</td>';

			WpssoOpmOrg::add_mb_org_rows( $table_rows, $form, $head_info, $mod, $args );

			return $table_rows;
		}
	}
}
