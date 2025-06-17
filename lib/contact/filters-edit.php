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

			$contact_types           = $this->p->util->get_form_cache( 'contact_types_select', $add_none = false );	// Use strict for Google.
			$business_weekdays       = $this->p->cf[ 'form' ][ 'weekdays' ];
			$hide_postal_class       = $this->p->schema->get_children_css_class( 'postal.address', 'hide_contact_schema_type' );
			$tr_hide_contact_html    = '';
			$tr_hide_postal_html     = '<tr class="' . $hide_postal_class . '" style="display:none;">';
			$schema_type_event_names = array( 'on_focus_load_json', 'on_change_unhide_rows' );

			$table_rows[ 'contact_name' ] = $tr_hide_contact_html .
				$form->get_th_html( _x( 'Contact Point Name', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_name' ) .
				'<td>' . $form->get_input( 'contact_name', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'contact_name_alt' ] = $tr_hide_contact_html .
				$form->get_th_html( _x( 'Contact Point Alt. Name', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_name_alt' ) .
				'<td>' . $form->get_input( 'contact_name_alt', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'contact_desc' ] = $tr_hide_contact_html .
				$form->get_th_html( _x( 'Contact Point Description', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_desc' ) .
				'<td>' . $form->get_textarea( 'contact_desc' ) . '</td>';

			$table_rows[ 'contact_schema_type' ] = $tr_hide_contact_html .
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

			$table_rows[ 'contact_phone' ] = $tr_hide_contact_html .
				$form->get_th_html( _x( 'Contact Point Telephone', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_phone' ) .
				'<td>' . $form->get_input( 'contact_phone' ) . '</td>';

			$table_rows[ 'contact_fax' ] = $tr_hide_contact_html .
				$form->get_th_html( _x( 'Contact Point Fax', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_fax' ) .
				'<td>' . $form->get_input( 'contact_fax' ) . '</td>';

			$table_rows[ 'contact_email' ] = $tr_hide_contact_html .
				$form->get_th_html( _x( 'Contact Point Email', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_email' ) .
				'<td>' . $form->get_input( 'contact_email' ) . '</td>';

			/*
			 * Postal Address section.
			 */
			$table_rows[ 'subsection_place' ] = $tr_hide_postal_html .
				'<td class="subsection" colspan="2"><h5>' .
				_x( 'Postal Address Information', 'metabox title', 'wpsso-organization-place' ) .
				'</h5></td>';

			$table_rows[ 'contact_street_address' ] = $tr_hide_postal_html .
				$form->get_th_html( _x( 'Street Address', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_street_address' ) .
				'<td>' . $form->get_input( 'contact_street_address', 'wide' ) . '</td>';

			$table_rows[ 'contact_po_box_number' ] = $tr_hide_postal_html .
				$form->get_th_html( _x( 'P.O. Box Number', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_po_box_number' ) .
				'<td>' . $form->get_input( 'contact_po_box_number' ) . '</td>';

			$table_rows[ 'contact_city' ] = $tr_hide_postal_html .
				$form->get_th_html( _x( 'City / Locality', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_city' ) .
				'<td>' . $form->get_input( 'contact_city' ) . '</td>';

			$table_rows[ 'contact_region' ] = $tr_hide_postal_html .
				$form->get_th_html( _x( 'State / Province', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_region' ) .
				'<td>' . $form->get_input( 'contact_region' ) . '</td>';

			$table_rows[ 'contact_postal_code' ] = $tr_hide_postal_html .
				$form->get_th_html( _x( 'Zip / Postal Code', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_postal_code' ) .
				'<td>' . $form->get_input( 'contact_postal_code' ) . '</td>';

			$table_rows[ 'contact_country' ] = $tr_hide_postal_html .
				$form->get_th_html( _x( 'Country', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_country' ) .
				'<td>' . $form->get_select_country( 'contact_country' ) . '</td>';

			/*
			 * Opening hours section.
			 *
			 * See https://schema.org/OpeningHoursSpecification.
			 */
			$table_rows[ 'subsection_opening_hours' ] = $tr_hide_contact_html .
				'<td class="subsection" colspan="2"><h5>' .
				_x( 'Opening Hours Information', 'metabox title', 'wpsso-organization-place' ) .
				'</h5></td>';

			$open_close_html = '<table class="business_hours">';

			foreach ( $business_weekdays as $day_name => $day_label ) {

				$day_opt_pre   = 'contact_day_' . $day_name;
				$open_opt_key  = $day_opt_pre . '_open';
				$close_opt_key = $day_opt_pre . '_close';

				// translators: Please ignore - translation uses a different text domain.
				$day_label_transl = _x( $day_label, 'option value', 'wpsso' );

				$open_close_html .= '<tr>' .
					'<td class="weekday"><p>' . $day_label_transl . '</p></td>' .
					'<td align="right"><p>' . __( 'Opens at', 'wpsso-organization-place' ) . '</p></td>' .
					'<td>' . $form->get_select_time_none( $open_opt_key ) . '</td>' .
					'<td align="right"><p>' . __( 'and closes at', 'wpsso-organization-place' ) . '</p></td>' .
					'<td>' . $form->get_select_time_none( $close_opt_key ) . '</td>' .
					'</tr>';
			}

			$open_close_html .= '<tr>' .
				'<td><p>' . __( 'Every Midday', 'wpsso-organization-place' ) . '</p></td>' .
				'<td align="right"><p>' . __( 'Closes at', 'wpsso-organization-place' ) . '</p></td>' .
				'<td>' . $form->get_select_time_none( 'contact_midday_close' ) . '</td>' .
				'<td align="right"><p>' . __( 'and re-opens at', 'wpsso-organization-place' ) . '</p></td>' .
				'<td>' . $form->get_select_time_none( 'contact_midday_open' ) . '</td>' .
				'</tr>';

			$open_close_html .= '</table>';

			$table_rows[ 'contact_days' ] = $tr_hide_contact_html .
				$form->get_th_html( _x( 'Open Days / Hours', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_days' ) .
				'<td>' . $open_close_html . '</td>';

			$table_rows[ 'contact_season_dates' ] = $tr_hide_contact_html .
				$form->get_th_html( _x( 'Seasonal Dates', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-contact_season_dates' ) .
				'<td><p style="margin-bottom:0;">' .
				__( 'Open from', 'wpsso-organization-place' ) . ' ' .	// Keep it short for translations.
				$form->get_input_date( 'contact_season_from_date' ) . ' ' .
				__( 'until', 'wpsso-organization-place' ) . ' ' .	// Keep it short for translations.
				$form->get_input_date( 'contact_season_to_date' ) . ' ' .
				__( 'inclusively', 'wpsso-organization-place' ) .	// Keep it short for translations.
				'</p></td>';

			return $table_rows;
		}
	}
}
