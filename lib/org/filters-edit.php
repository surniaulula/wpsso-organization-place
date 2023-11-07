<?php
/*
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

			$org_names = WpssoOpmOrg::get_names( $schema_type = '' );

			return is_array( $mixed ) ? $mixed + $org_names : $org_names;
		}

		public function filter_mb_org_rows( $table_rows, $form, $head_info, $mod ) {

			$org_types_select        = $this->p->util->get_form_cache( 'org_types_select' );
			$place_names             = $this->p->util->get_form_cache( 'place_names', $add_none = true );
			$hide_news_media_class   = $this->p->schema->get_children_css_class( 'news.media.organization', 'hide_org_schema_type' );
			$tr_hide_news_media_html = '<tr class="' . $hide_news_media_class . '" style="display:none;">';
			$awards_max              = SucomUtil::get_const( 'WPSSO_SCHEMA_AWARDS_MAX', 5 );

			$table_rows[ 'org_name' ] = '' .
				$form->get_th_html( _x( 'Organization Name', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-org_name' ) .
				'<td>' . $form->get_input( 'org_name', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_is_defaults' ] = '' .
				$form->get_th_html( '', $css_class = 'medium' ) .
				'<td>' . $form->get_checklist( 'org_is', $this->p->cf[ 'form' ][ 'org_is_defaults' ] ) . '</td>';

			$table_rows[ 'org_name_alt' ] = '' .
				$form->get_th_html( _x( 'Organization Alternate Name', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-org_name_alt' ) .
				'<td>' . $form->get_input( 'org_name_alt', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_desc' ] = '' .
				$form->get_th_html( _x( 'Organization Description', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-org_desc' ) .
				'<td>' . $form->get_textarea( 'org_desc' ) . '</td>';

			$table_rows[ 'org_url' ] = '' .
				$form->get_th_html( _x( 'Organization WebSite URL', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-org_url' ) .
				'<td>' . $form->get_input( 'org_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_logo_url' ] = '' .
				$form->get_th_html( '<a href="https://developers.google.com/search/docs/advanced/structured-data/logo">' .
				_x( 'Organization Logo URL', 'option label', 'wpsso-organization-place' ) . '</a>',
					$css_class = 'medium', $css_id = 'meta-org_logo_url' ) .
				'<td>' . $form->get_input( 'org_logo_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_banner_url' ] = '' .
				$form->get_th_html( '<a href="https://developers.google.com/search/docs/data-types/article#logo-guidelines">' .
				_x( 'Organization Banner URL', 'option label', 'wpsso-organization-place' ) . '</a>',
					$css_class = 'medium', $css_id = 'meta-org_banner_url' ) .
				'<td>' . $form->get_input( 'org_banner_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_place_id' ] = '' .
				$form->get_th_html( _x( 'Organization Location', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-org_place_id' ) .
				'<td>' . $form->get_select( 'org_place_id', $place_names,
					$css_class = 'wide', $css_id = '', $is_assoc = true ) . '</td>';

			$table_rows[ 'org_schema_type' ] = '' .
				$form->get_th_html( _x( 'Organization Schema Type', 'option label', 'wpsso-organization-place' ),
					$css_class = 'medium', $css_id = 'meta-org_schema_type' ) .
				'<td>' . $form->get_select( 'org_schema_type', $org_types_select, $css_class = 'schema_type', $css_id = '',
					$is_assoc = true, $is_disabled = false, $selected = false, array( 'on_focus_load_json', 'on_change_unhide_rows' ),
						$event_args = array(
							'json_var' => 'schema_org_types',
							'exp_secs'  => WPSSO_CACHE_SELECT_JSON_EXP_SECS,	// Create and read from a javascript URL.
							'is_transl' => true,					// No label translation required.
							'is_sorted' => true,					// No label sorting required.
						)
					) . '</td>';

			$table_rows[ 'org_pub_principles_url' ] = '' .
				$form->get_th_html( _x( 'Publishing Principles URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_pub_principles_url' ) .
				'<td>' . $form->get_input( 'org_pub_principles_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_corrections_policy_url' ] = '' .
				$form->get_th_html( _x( 'Corrections Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_corrections_policy_url' ) .
				'<td>' . $form->get_input( 'org_corrections_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_diversity_policy_url' ] = '' .
				$form->get_th_html( _x( 'Diversity Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_diversity_policy_url' ) .
				'<td>' . $form->get_input( 'org_diversity_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_ethics_policy_url' ] = '' .
				$form->get_th_html( _x( 'Ethics Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_ethics_policy_url' ) .
				'<td>' . $form->get_input( 'org_ethics_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_fact_check_policy_url' ] = '' .
				$form->get_th_html( _x( 'Fact Checking Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_fact_check_policy_url' ) .
				'<td>' . $form->get_input( 'org_fact_check_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_feedback_policy_url' ] = '' .
				$form->get_th_html( _x( 'Feedback Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_feedback_policy_url' ) .
				'<td>' . $form->get_input( 'org_feedback_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_award' ] = ''.
				$form->get_th_html( _x( 'Organization Awards', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_award' ) .
				'<td>' . $form->get_input_multi( 'org_award', $css_class = 'wide', $css_id = '',
					$awards_max, $show_first = 1 ) . '</td>';

			/*
			 * News Media Organization section.
			 */
			$table_rows[ 'subsection_org_news_media_urls' ] = $tr_hide_news_media_html .
				'<td class="subsection" colspan="2"><h5>' .
				_x( 'News Media Organization Information', 'metabox title', 'wpsso' ) .
				'</h5></td>';

			$table_rows[ 'org_masthead_url' ] = $tr_hide_news_media_html .
				$form->get_th_html( _x( 'Masthead Page URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_masthead_url' ) .
				'<td>' . $form->get_input( 'org_masthead_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_coverage_policy_url' ] = $tr_hide_news_media_html .
				$form->get_th_html( _x( 'Coverage Priorities Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium nowrap', $css_id = 'meta-org_coverage_policy_url' ) .
				'<td>' . $form->get_input( 'org_coverage_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_no_bylines_policy_url' ] = $tr_hide_news_media_html .
				$form->get_th_html( _x( 'No Bylines Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_no_bylines_policy_url' ) .
				'<td>' . $form->get_input( 'org_no_bylines_policy_url', $css_class = 'wide' ) . '</td>';

			$table_rows[ 'org_sources_policy_url' ] = $tr_hide_news_media_html .
				$form->get_th_html( _x( 'Unnamed Sources Policy URL', 'option label', 'wpsso' ),
					$css_class = 'medium', $css_id = 'meta-org_sources_policy_url' ) .
				'<td>' . $form->get_input( 'org_sources_policy_url', $css_class = 'wide' ) . '</td>';

			/*
			 * Organization Knowledge Graph section.
			 */
			$table_rows[ 'subsection_org_knowledgegraph' ] = '<td colspan="2" class="subsection"><h4>' .
				_x( 'Organization Knowledge Graph', 'metabox title', 'wpsso-organization-place' ) . '</h4></td>';

			foreach ( WpssoConfig::get_social_accounts() as $key => $label ) {

				$opt_key = 'org_sameas_' . $key;

				$table_rows[ $opt_key ] = '' .
					$form->get_th_html( _x( $label, 'option value', 'wpsso-organization-place' ),
						$css_class = 'medium nowrap', $opt_key ) .
					'<td>' . $form->get_input( $opt_key, strpos( $opt_key, '_url' ) ? 'wide' : '' ) . '</td>';
			}

			return $table_rows;
		}
	}
}
