<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmPlaceFiltersMessagesTooltipMeta' ) ) {

	class WpssoOpmPlaceFiltersMessagesTooltipMeta {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/**
		 * Instantiated by WpssoOpmFiltersMessagesTooltipMeta->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array( 
				'messages_tooltip_meta_place' => 2,
			) );
		}

		public function filter_messages_tooltip_meta_place( $text, $msg_key ) {

			switch ( $msg_key ) {

				case 'tooltip-meta-place_name':

					$text = __( 'A name for this place (required).', 'wpsso-plm' ) . ' ';

					$text .= __( 'The place name may appear in forms and in the Schema Place "name" property.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_name_alt':

					$text = __( 'An alternate name for this place.', 'wpsso-plm' ) . ' ';

					$text .= __( 'The place alternate name may appear in the Schema Place "alternateName" property.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_desc':

					$text = __( 'A description for this place.', 'wpsso-plm' ) . ' ';

					$text .= __( 'The place description may appear in the Schema Place "description" property.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_schema_type':	// Place Schema Type.

					$text = __( 'You may optionally choose a different Schema type for this place (default is LocalBusiness).', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_street_address':

					$text = __( 'An optional street address for Pinterest Rich Pin / Schema Place meta tags and related markup.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_po_box_number':

					$text = __( 'An optional post office box number for the Pinterest Rich Pin / Schema Place meta tags and related markup.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_city':

					$text = __( 'An optional city name for the Pinterest Rich Pin / Schema Place meta tags and related markup.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_region':

					$text = __( 'An optional state or province name for the Pinterest Rich Pin / Schema Place meta tags and related markup.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_postal_code':

					$text = __( 'An optional postal or zip code for the Pinterest Rich Pin / Schema Place meta tags and related markup.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_country':

					$text = __( 'An optional country for the Pinterest Rich Pin / Schema Place meta tags and related markup.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_phone':

					$text = __( 'An optional telephone number for this place.', 'wpsso-plm' );

					break;
				case 'tooltip-meta-place_latitude':

					$text = __( 'The numeric decimal degrees latitude for this place (required).', 'wpsso-plm' ) . ' ';

					$text .= __( 'You may use a service like <a href="http://www.gps-coordinates.net/">Google Maps GPS Coordinates</a> (as an example), to find the approximate GPS coordinates of a street address.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_longitude':

					$text = __( 'The numeric decimal degrees longitude for this place (required).', 'wpsso-plm' ) . ' ';

					$text .= __( 'You may use a service like <a href="http://www.gps-coordinates.net/">Google Maps GPS Coordinates</a> (as an example), to find the approximate GPS coordinates of a street address.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_altitude':

					$text = __( 'An optional numeric altitude (in meters above sea level) for this place.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_img_id':	// Place Image ID.

					$text = __( 'An image of this place (ie. an image of the business storefront or location).', 'wpsso-plm' ) . ' ';

					$text .= '<em>' . __( 'This option is disabled if a place image URL is entered.', 'wpsso-plm' ) . '</em>';

					break;

				case 'tooltip-meta-place_img_url':	// or Place Image URL.

					$text = __( 'You can enter a place image URL (including the http:// prefix) instead of selecting an image ID.', 'wpsso-plm' ) . ' ';

					$text .= __( 'The image URL option allows you to specify an image URL outside of the WordPress Media Library and/or a smaller logo style image.', 'wpsso-plm' ) . ' ';

					$text .= '<em>' . __( 'This option is disabled if a place image ID is selected.', 'wpsso' ) . '</em>';

					break;

				case 'tooltip-meta-place_timezone':	// Place Timezone.

					$text = __( 'A timezone for the place open and close hours.', 'wpsso-plm' ) . ' ';

					$text .= __( 'The default timezone is provided by WordPress.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_days':		// Open Days / Hours.

					$text = __( 'Select the days and hours this place is open.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_season_dates':	// Seasonal Dates.

					$text = __( 'If this place is open seasonally, select the open and close dates of the season.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_service_radius':

					$text = __( 'The geographic area where a service is provided, in meters around the location.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_currencies_accepted':

					$text = sprintf( __( 'A comma-delimited list of <a href="%1$s">ISO 4217 currency codes</a> accepted by the local business (example: %2$s).', 'wpsso-plm' ), 'https://en.wikipedia.org/wiki/ISO_4217', 'USD, CAD' );

					break;

				case 'tooltip-meta-place_payment_accepted':

					$text = __( 'A comma-delimited list of payment options accepted by the local business (example: Cash, Credit Card).', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_price_range':

					$text = __( 'The relative price of goods or services provided by the local business (example: $, $$, $$$, or $$$$).', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_accept_res':

					$text = __( 'This food establishment accepts reservations.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_menu_url':

					$text = __( 'The menu URL for this food establishment.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_cuisine':

					$text = __( 'The cuisine served by this food establishment.', 'wpsso-plm' );

					break;

				case 'tooltip-meta-place_order_urls':

					$text = __( 'A comma-delimited list of website and mobile app URLs to order products.', 'wpsso-plm' ) . ' ';

					$text .= __( 'These order action URL(s) will be used in the Schema potentialAction property.', 'wpsso-plm' );

					break;
			}

			return $text;
		}
	}
}
