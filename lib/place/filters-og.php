<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoOpmPlaceFiltersOg' ) ) {

	class WpssoOpmPlaceFiltersOg {

		private $p;	// Wpsso class object.
		private $a;	// WpssoOpm class object.

		/*
		 * Instantiated by WpssoOpmFilters->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'og_type' => 3,
				'og_seed' => 2,
			) );
		}

		public function filter_og_type( $og_type, $mod, $is_custom ) {

			if ( $is_custom ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'exiting early: is custom og type' );
				}

				return $og_type;
			}

			if ( 'place' === $og_type ) {	// Just in case.

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'exiting early: og type already place' );
				}

				return $og_type;
			}

			$place_opts = WpssoOpmPlace::has_md_place( $mod );	// Returns false or place array.

			if ( empty( $place_opts ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'exiting early: no place options found' );
				}

				return $og_type;	// Stop here.
			}

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'returning og type place' );
			}

			return 'place';
		}

		public function filter_og_seed( array $mt_og, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$place_opts = WpssoOpmPlace::has_md_place( $mod );

			if ( empty( $place_opts ) ) {	// No custom place options.

				if ( $mod[ 'is_home' ] ) {	// Home page (static or blog archive).

					if ( $mod[ 'obj' ] ) {	// Just in case.

						$og_type = $mod[ 'obj' ]->get_options( $mod[ 'id' ], 'og_type' );

						if ( 'place' === $og_type ) {

							$org_opts = WpssoSchema::get_site_organization( $mod );

							if ( isset( $org_opts[ 'org_place_id' ] ) ) {

								$place_id = $org_opts[ 'org_place_id' ];

								if ( $this->p->debug->enabled ) {

									$this->p->debug->log( 'getting site organization place id ' . $place_id );
								}

								$place_opts = WpssoOpmPlace::get_id( $place_id, $mod );
							}
						}
					}
				}
			}

			if ( empty( $place_opts ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'exiting early: no place options found' );
				}

				return $mt_og;	// Stop here.
			}

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'creating meta tags for place' );
			}

			/*
			 * og:type
			 */
			$mt_og[ 'og:type' ] = 'place';	// Pre-define to optimize.

			/*
			 * place:name
			 * place:name_alt
			 * place:description
			 * place:street_address
			 * place:po_box_number
			 * place:locality
			 * place:region
			 * place:postal_code
			 * place:country_name
			 * place:telephone
			 */
			foreach ( array(
				'place_name'           => 'place:name',
				'place_name_alt'       => 'place:name_alt',
				'place_desc'           => 'place:description',
				'place_street_address' => 'place:street_address',
				'place_po_box_number'  => 'place:po_box_number',
				'place_city'           => 'place:locality',
				'place_region'         => 'place:region',
				'place_postal_code'    => 'place:postal_code',
				'place_country'        => 'place:country_name',
				'place_phone'          => 'place:telephone',
			) as $key => $mt_name ) {

				$mt_og[ $mt_name ] = isset( $place_opts[ $key ] ) && 'none' !== $place_opts[ $key ] ? $place_opts[ $key ] : '';
			}

			/*
			 * place:location:latitude
			 * place:location:longitude
			 * place:location:altitude
			 * og:latitude
			 * og:longitude
			 * og:altitude
			 */
			if ( isset( $place_opts[ 'place_latitude' ] ) && '' !== $place_opts[ 'place_latitude' ] &&
				isset( $place_opts[ 'place_longitude' ] ) && '' !== $place_opts[ 'place_longitude' ] ) {

				foreach( array( 'place:location', 'og' ) as $mt_pre ) {

					$mt_og[ $mt_pre . ':latitude' ]  = $place_opts[ 'place_latitude' ];
					$mt_og[ $mt_pre . ':longitude' ] = $place_opts[ 'place_longitude' ];

					if ( isset( $place_opts[ 'altitude' ] ) && '' !== $place_opts[ 'altitude' ] ) {

						$mt_og[ $mt_pre . ':altitude' ] = $place_opts[ 'place_altitude' ];
					}
				}
			}

			/*
			 * Internal meta tags.
			 */
			$business_weekdays = $this->p->cf[ 'form' ][ 'weekdays' ];

			foreach ( $business_weekdays as $day_name => $day_label ) {

				foreach ( array(
					'place_day_' . $day_name . '_open'  => 'place:opening_hours:day:' . $day_name . ':open',
					'place_day_' . $day_name . '_close' => 'place:opening_hours:day:' . $day_name . ':close',
				) as $opt_key => $mt_name ) {

					if ( ! empty( $place_opts[ $opt_key ] ) && 'none' !== $place_opts[ $opt_key ] ) {

						$mt_og[ $mt_name ] = $place_opts[ $opt_key ];
					}
				}
			}

			foreach ( array(
				'place_id'                  => 'place:opening_hours:id',
				'place_timezone'            => 'place:opening_hours:timezone',
				'place_midday_close'        => 'place:opening_hours:midday:close',
				'place_midday_open'         => 'place:opening_hours:midday:open',
				'place_season_from_date'    => 'place:opening_hours:season:from_date',
				'place_season_to_date'      => 'place:opening_hours:season:to_date',
				'place_service_radius'      => 'place:business:service_radius',
				'place_currencies_accepted' => 'place:business:currencies_accepted',
				'place_payment_accepted'    => 'place:business:payment_accepted',
				'place_price_range'         => 'place:business:price_range',
				'place_accept_res'          => 'place:business:accepts_reservations',
				'place_menu_url'            => 'place:business:menu_url',
				'place_order_urls'          => 'place:business:order_url',
			) as $opt_key => $mt_name ) {

				if ( isset( $place_opts[ $opt_key ] ) ) {

					if ( 'place_accept_res' === $opt_key ) {

						$mt_og[ $mt_name ] = empty( $place_opts[ $opt_key ] ) ? false : true;

					} elseif ( 'place_order_urls' === $opt_key ) {

						$mt_og[ $mt_name ] = SucomUtil::explode_csv( $place_opts[ $opt_key ] );

					} else {

						$mt_og[ $mt_name ] = $place_opts[ $opt_key ];
					}

				} else {

					$mt_og[ $mt_name ] = '';
				}
			}

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'returning meta tags for place' );
			}

			return $mt_og;
		}
	}
}
