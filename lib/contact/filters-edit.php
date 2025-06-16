<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2025 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmContactFiltersEdit' ) ) {

	class WpssoOpmContactFiltersEdit {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/*
		 * Instantiated by WpssoOpmFiltersEdit->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'form_cache_contact_names' => 1,
				'mb_contact_rows'          => 4,
			) );
		}

		public function filter_form_cache_contact_names( $mixed ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$contact_names = WpssoOpmContact::get_names( $schema_type = '' );
			$contact_names = is_array( $mixed ) ? $mixed + $contact_names : $contact_names;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log_arr( 'contact_names', $contact_names);
			}

			return $contact_names;
		}

		public function filter_mb_contact_rows( $table_rows, $form, $head_info, $mod ) {

			$contact_types = $this->p->util->get_form_cache( 'contact_types_select', $add_none = false );	// Use strict for Google.

			$table_rows[ 'contact_name' ] = '' .
				$form->get_th_html( _x( 'Contact Point Name', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_name' ) .
				'<td>' . $form->get_input( 'contact_name', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'contact_name_alt' ] = '' .
				$form->get_th_html( _x( 'Contact Point Alt. Name', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_name_alt' ) .
				'<td>' . $form->get_input( 'contact_name_alt', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'contact_desc' ] = '' .
				$form->get_th_html( _x( 'Contact Point Description', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_desc' ) .
				'<td>' . $form->get_textarea( 'contact_desc' ) . '</td>';

			$table_rows[ 'contact_schema_type' ] = '' .
				$form->get_th_html( _x( 'Contact Point Schema Type', 'option label', 'wpsso-contactanization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_schema_type' ) .
				'<td>' . $form->get_select( 'contact_schema_type', $contact_types, $css_class = 'schema_type', $css_id = '',
					$is_assoc = true, $is_disabled = false, $selected = false, array( 'on_focus_load_json', 'on_change_unhide_rows' ),
						$event_args = array(
							'json_var' => 'schema_contact_types',
							'exp_secs'  => WPSSO_CACHE_SELECT_JSON_EXP_SECS,	// Create and read from a javascript URL.
							'is_transl' => true,					// No label translation required.
							'is_sorted' => true,					// No label sorting required.
						)
					) . '</td>';

			return $table_rows;
		}
	}
}
