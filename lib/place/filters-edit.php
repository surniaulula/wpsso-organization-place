<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmPlaceFiltersEdit' ) ) {

	class WpssoOpmPlaceFiltersEdit {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/*
		 * Instantiated by WpssoOpmFiltersEdit->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'form_cache_place_names'        => 1,
				'form_cache_place_names_custom' => 1,
				'mb_place_rows'                 => 4,
				'mb_sso_edit_schema_rows'       => 4,
			) );
		}

		public function filter_form_cache_place_names_custom( $mixed ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$custom = array( 'custom' => _x( '[Custom Place]', 'option value', 'wpsso-organization-place' ) );

			$place_names = $this->filter_form_cache_place_names( $mixed );	// Always returns an array.

			$place_names_custom = $custom + $place_names;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log_arr( 'place_names_custom', $place_names_custom );
			}

			return $place_names_custom;
		}

		public function filter_form_cache_place_names( $mixed ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$place_names = WpssoOpmPlace::get_names( $schema_type = '' );

			$place_names = is_array( $mixed ) ? $mixed + $place_names : $place_names;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log_arr( 'place_names', $place_names);
			}

			return $place_names;
		}

		public function filter_mb_place_rows( $table_rows, $form, $head_info, $mod ) {

			return $this->get_mb_place_rows( $table_rows, $form, $head_info, $mod, $is_custom = false );
		}

		/*
		 * Document SSO metabox options.
		 */
		public function filter_mb_sso_edit_schema_rows( $table_rows, $form, $head_info, $mod ) {

			return $this->get_mb_place_rows( $table_rows, $form, $head_info, $mod, $is_custom = true );
		}

		private function get_mb_place_rows( $table_rows, $form, $head_info, $mod, $is_custom ) {

			$place_types               = $this->p->util->get_form_cache( 'place_types_select', $add_none = false );
			$business_weekdays         = $this->p->cf[ 'form' ][ 'weekdays' ];
			$hide_local_business_class = $this->p->schema->get_children_css_class( 'local.business', 'hide_place_schema_type' );
			$hide_food_establish_class = $this->p->schema->get_children_css_class( 'food.establishment', 'hide_place_schema_type' );
			$hide_org_class            = $this->p->schema->get_children_css_class( 'organization', 'hide_place_schema_type' );

			if ( $is_custom ) {

				$tr_hide_place_html            = '<tr class="hide_schema_place_id hide_schema_place_id_custom" style="display:none;">';
				$tr_hide_local_business_html   = '<tr class="hide_schema_place_id ' . $hide_local_business_class . '" style="display:none;">';
				$tr_hide_food_establish_html   = '<tr class="hide_schema_place_id ' . $hide_food_establish_class . '" style="display:none;">';
				$tr_hide_org_html              = '<tr class="hide_schema_place_id ' . $hide_org_class . '" style="display:none;">';
				$place_schema_type_event_names = array( 'on_focus_load_json', 'on_show_unhide_rows' );

			} else {

				$tr_hide_place_html            = '';
				$tr_hide_local_business_html   = '<tr class="' . $hide_local_business_class . '" style="display:none;">';
				$tr_hide_food_establish_html   = '<tr class="' . $hide_food_establish_class . '" style="display:none;">';
				$tr_hide_org_html              = '<tr class="' . $hide_org_class . '" style="display:none;">';
				$place_schema_type_event_names = array( 'on_focus_load_json', 'on_change_unhide_rows' );
			}

			if ( ! $is_custom ) {

				$table_rows[ 'place_name' ] = $tr_hide_place_html .
					$form->get_th_html( _x( 'Place Name', 'option label', 'wpsso-organization-place' ),
						$css_class = 'medium', $css_id = 'meta-place_name' ) .
					'<td>' . $form->get_input( 'place_name', $css_class = 'wide' ) . '</td>';

				$table_rows[ 'place_name_alt' ] = $tr_hide_place_html .
					$form->get_th_html( _x( 'Place Alternate Name', 'option label', 'wpsso-organization-place' ),
						$css_class = 'medium', $css_id = 'meta-place_name_alt' ) .
					'<td>' . $form->get_input( 'place_name_alt', $css_class = 'wide' ) . '</td>';

				$table_rows[ 'place_desc' ] = $tr_hide_place_html .
					$form->get_th_html( _x( 'Place Description', 'option label', 'wpsso-organization-place' ),
						$css_class = 'medium', $css_id = 'meta-place_desc' ) .
					'<td>' . $form->get_textarea( 'place_desc' ) . '</td>';
			}

			$table_rows[ 'place_schema_type' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'Place Schema Type', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_schema_type' ) .
				'<td>' . $form->get_select( 'place_schema_type', $place_types, $css_class = 'schema_type', $css_id = '',
					$is_assoc = true, $is_disabled = false, $selected = false, $place_schema_type_event_names,
						$event_args = array(
							'json_var' => 'schema_place_types',
							'exp_secs'  => WPSSO_CACHE_SELECT_JSON_EXP_SECS,	// Create and read from a javascript URL.
							'is_transl' => true,					// No label translation required.
							'is_sorted' => true,					// No label sorting required.
						)
					) . '</td>';

			if ( ! $is_custom ) {

				$table_rows[ 'place_is_default' ] = $tr_hide_place_html .
					$form->get_th_html( _x( 'Place Is Default', 'option label', 'wpsso-organization-place' ),
						$css_class = 'medium', $css_id = 'meta-place_is_default' ) .
					'<td>' . $form->get_checklist( 'place_is', $this->p->cf[ 'form' ][ 'place_is_defaults' ] ) . '</td>';
			}

			WpssoOpmOrg::add_mb_org_rows( $table_rows, $form, $head_info, $mod, $tr_hide_org_html );

			/*
			 * Place section.
			 */
			$table_rows[ 'subsection_place' ] = $tr_hide_local_business_html .
				'<td class="subsection" colspan="2"><h5>' .
				_x( 'Place Information', 'metabox title', 'wpsso-organization-place' ) .
				'</h5></td>';

			$table_rows[ 'place_street_address' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'Street Address', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_street_address' ) .
				'<td>' . $form->get_input( 'place_street_address', 'wide' ) . '</td>';

			$table_rows[ 'place_po_box_number' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'P.O. Box Number', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_po_box_number' ) .
				'<td>' . $form->get_input( 'place_po_box_number' ) . '</td>';

			$table_rows[ 'place_city' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'City / Locality', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_city' ) .
				'<td>' . $form->get_input( 'place_city' ) . '</td>';

			$table_rows[ 'place_region' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'State / Province', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_region' ) .
				'<td>' . $form->get_input( 'place_region' ) . '</td>';

			$table_rows[ 'place_postal_code' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'Zip / Postal Code', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_postal_code' ) .
				'<td>' . $form->get_input( 'place_postal_code' ) . '</td>';

			$table_rows[ 'place_country' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'Country', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_country' ) .
				'<td>' . $form->get_select_country( 'place_country' ) . '</td>';

			$table_rows[ 'place_phone' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'Telephone', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_phone' ) .
				'<td>' . $form->get_input( 'place_phone' ) . '</td>';

			if ( ! $is_custom ) {

				$table_rows[ 'place_img_id' ] = $tr_hide_place_html .
					$form->get_th_html( _x( 'Place Image ID', 'option label', 'wpsso-organization-place' ),
						$css_class = 'medium', $css_id = 'meta-place_img_id' ) .
					'<td>' . $form->get_input_image_upload( 'place_img' ) . '</td>';

				$table_rows[ 'place_img_url' ] = $tr_hide_place_html .
					$form->get_th_html( _x( 'or Place Image URL', 'option label', 'wpsso-organization-place' ),
						$css_class = 'medium', $css_id = 'meta-place_img_url' ) .
					'<td>' . $form->get_input_image_url( 'place_img' ) . '</td>';
			}

			$table_rows[ 'place_latitude' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'Place Latitude', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_latitude' ) .
				'<td>' . $form->get_input( 'place_latitude', $css_class = 'latitude' ) . ' ' .
				_x( 'decimal degrees', 'option comment', 'wpsso-organization-place' ) . '</td>';

			$table_rows[ 'place_longitude' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'Place Longitude', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_longitude' ) .
				'<td>' . $form->get_input( 'place_longitude', $css_class = 'longitude' ) . ' ' .
				_x( 'decimal degrees', 'option comment', 'wpsso-organization-place' ) . '</td>';

			$table_rows[ 'place_altitude' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'Place Altitude', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_altitude' ) .
				'<td>' . $form->get_input( 'place_altitude', $css_class = 'altitude' ) . ' ' .
				_x( 'meters above sea level', 'option comment', 'wpsso-organization-place' ) . '</td>';

			$table_rows[ 'place_timezone' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'Place Timezone', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_timezone' ) .
				'<td>' . $form->get_select_timezone( 'place_timezone' ) . '</td>';

			/*
			 * Example $business_weekdays = array(
			 *	'sunday'         => 'Sunday',
			 *	'monday'         => 'Monday',
			 *	'tuesday'        => 'Tuesday',
			 *	'wednesday'      => 'Wednesday',
			 *	'thursday'       => 'Thursday',
			 *	'friday'         => 'Friday',
			 *	'saturday'       => 'Saturday',
			 *	'publicholidays' => 'Public Holidays',
			 * );
			 */
			$open_close_html = '<table class="business_hours">';

			foreach ( $business_weekdays as $day_name => $day_label ) {

				$day_opt_pre   = 'place_day_' . $day_name;
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
				'<td>' . $form->get_select_time_none( 'place_midday_close' ) . '</td>' .
				'<td align="right"><p>' . __( 'and re-opens at', 'wpsso-organization-place' ) . '</p></td>' .
				'<td>' . $form->get_select_time_none( 'place_midday_open' ) . '</td>' .
				'</tr>';

			$open_close_html .= '</table>';

			$table_rows[ 'place_days' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'Open Days / Hours', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_days' ) .
				'<td>' . $open_close_html . '</td>';

			$table_rows[ 'place_season_dates' ] = $tr_hide_place_html .
				$form->get_th_html( _x( 'Seasonal Dates', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_season_dates' ) .
				'<td><p style="margin-bottom:0;">' .
				__( 'Open from', 'wpsso-organization-place' ) . ' ' .	// Keep it short for translations.
				$form->get_input_date( 'place_season_from_date' ) . ' ' .
				__( 'until', 'wpsso-organization-place' ) . ' ' .	// Keep it short for translations.
				$form->get_input_date( 'place_season_to_date' ) . ' ' .
				__( 'inclusively', 'wpsso-organization-place' ) .	// Keep it short for translations.
				'</p></td>';

			/*
			 * Local Business section.
			 */
			$table_rows[ 'subsection_local_business' ] = $tr_hide_local_business_html .
				'<td class="subsection" colspan="2"><h5>' .
				_x( 'Local Business Information', 'metabox title', 'wpsso-organization-place' ) .
				'</h5></td>';

			$table_rows[ 'place_service_radius' ] = $tr_hide_local_business_html .
				$form->get_th_html( _x( 'Service Radius', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_service_radius' ) .
				'<td>' . $form->get_input( 'place_service_radius', $css_class = 'short' ) . ' ' .
				_x( 'meters from location', 'option comment', 'wpsso-organization-place' ) . '</td>';

			$table_rows[ 'place_currencies_accepted' ] = $tr_hide_local_business_html .
				$form->get_th_html( _x( 'Currencies Accepted', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_currencies_accepted' ) .
				'<td>' . $form->get_input( 'place_currencies_accepted' ) . '</td>';

			$table_rows[ 'place_payment_accepted' ] = $tr_hide_local_business_html .
				$form->get_th_html( _x( 'Payment Accepted', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_payment_accepted' ) .
				'<td>' . $form->get_input( 'place_payment_accepted' ) . '</td>';

			$table_rows[ 'place_price_range' ] = $tr_hide_local_business_html .
				$form->get_th_html( _x( 'Price Range', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_price_range' ) .
				'<td>' . $form->get_input( 'place_price_range' ) . '</td>';

			/*
			 * Food Establishment section.
			 */
			$table_rows[ 'subsection_food_establishment' ] = $tr_hide_food_establish_html .
				'<td class="subsection" colspan="2"><h5>' .
				_x( 'Food Establishment Information', 'metabox title', 'wpsso-organization-place' ) .
				'</h5></td>';

			$table_rows[ 'place_accept_res' ] = $tr_hide_food_establish_html .
				$form->get_th_html( _x( 'Accepts Reservations', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_accept_res' ) .
				'<td>' . $form->get_checkbox( 'place_accept_res' ) . '</td>';

			$table_rows[ 'place_cuisine' ] = $tr_hide_food_establish_html .
				$form->get_th_html( _x( 'Serves Cuisine', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_cuisine' ) .
				'<td>' . $form->get_input( 'place_cuisine' ) . '</td>';

			$table_rows[ 'place_menu_url' ] = $tr_hide_food_establish_html .
				$form->get_th_html( _x( 'Food Menu URL', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_menu_url' ) .
				'<td>' . $form->get_input( 'place_menu_url', 'wide' ) . '</td>';

			$table_rows[ 'place_order_urls' ] = $tr_hide_food_establish_html .
				$form->get_th_html( _x( 'Order Action URL(s)', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-place_order_urls' ) .
				'<td>' . $form->get_input( 'place_order_urls', 'wide' ) . '</td>';

			return $table_rows;
		}
	}
}
