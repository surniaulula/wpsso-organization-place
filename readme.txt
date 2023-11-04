=== WPSSO Organization and Place Manager ===
Plugin Name: WPSSO Organization and Place Manager
Plugin Slug: wpsso-organization-place
Text Domain: wpsso-organization-place
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Assets URI: https://surniaulula.github.io/wpsso-organization-place/assets/
Tags: schema, organization, place, location, local seo, local business, publisher
Contributors: jsmoriss
Requires Plugins: wpsso
Requires PHP: 7.2.34
Requires At Least: 5.5
Tested Up To: 6.4.0
Stable Tag: 1.16.0

Manage Organizations (publisher, organizer, etc.) and Places for Facebook, Pinterest, and Google local business markup.

== Description ==

<!-- about -->

**Manage any number of Organizations** for Schema publisher, service provider, production company, event organizer, event performer (ie. a band), and job hiring organization properties.

**Manage any number of Places, Locations, and Venues** for Open Graph meta tags, Schema Place markup, organization location, event location, and job location properties.

<!-- /about -->

**The Organization editing page includes:**

* Organization Name
* Organization Alternate Name
* Organization Description
* Organization WebSite URL
* Organization Logo URL
* Organization Banner URL
* Organization Schema Type
* Organization Location
* Publishing Principles URL
* Corrections Policy URL
* Diversity Policy URL
* Ethics Policy URL
* Fact Checking Policy URL
* Feedback Policy URL
* News Media Organization:
	* Masthead Page URL
	* Coverage Priorities Policy URL
	* No Bylines Policy URL
	* Unnamed Sources Policy URL
* Organization Knowledge Graph:
	* Behance Business Page URL
	* Facebook Business Page URL
	* Instagram Business Page URL
	* LinkedIn Company Page URL
	* Medium Business Page URL
	* Myspace Business Page URL
	* Pinterest Company Page URL
	* Soundcloud Business Page URL
	* TikTok Business Page URL
	* Tumblr Business Page URL
	* Twitter Business @username
	* Wikipedia Organization Page URL
	* YouTube Business Channel URL

**The Place editing page includes:**

* Place Name
* Place Alternate Name
* Place Description
* Place Schema Type
* Street Address
* P.O. Box Number
* City / Locality
* State / Province
* Zip / Postal Code
* Country
* Telephone
* Place Latitude
* Place Longitude
* Place Altitude
* Place Image ID
* or Place Image URL
* Place Timezone
* Open Days / Hours
* Seasonal Dates
* Local Business:
	* Service Radius
	* Currencies Accepted
	* Payment Accepted
	* Price Range
* Food Establishment:
	* Accepts Reservations
	* Serves Cuisine
	* Food Menu URL
	* Order Action URL(s)

<h3>WPSSO Core Required</h3>

WPSSO Organization and Place Manager (WPSSO OPM) is an add-on for the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/), which provides complete structured data for WordPress to present your content at its best on social sites and in search results – no matter how URLs are shared, reshared, messaged, posted, embedded, or crawled.

== Installation ==

<h3 class="top">Install and Uninstall</h3>

* [Install the WPSSO Organization and Place Manager add-on](https://wpsso.com/docs/plugins/wpsso-organization-place/installation/install-the-plugin/).
* [Uninstall the WPSSO Organization and Place Manager add-on](https://wpsso.com/docs/plugins/wpsso-organization-place/installation/uninstall-the-plugin/).

== Frequently Asked Questions ==

== Screenshots ==

01. The Organization editing page.
01. The Place editing page.

== Changelog ==

<h3 class="top">Version Numbering</h3>

Version components: `{major}.{minor}.{bugfix}[-{stage}.{level}]`

* {major} = Major structural code changes and/or incompatible API changes (ie. breaking changes).
* {minor} = New functionality was added or improved in a backwards-compatible manner.
* {bugfix} = Backwards-compatible bug fixes or small improvements.
* {stage}.{level} = Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).

<h3>Standard Edition Repositories</h3>

* [GitHub](https://surniaulula.github.io/wpsso-organization-place/)
* [WordPress.org](https://plugins.trac.wordpress.org/browser/wpsso-organization-place/)

<h3>Development Version Updates</h3>

<p><strong>WPSSO Core Premium edition customers have access to development, alpha, beta, and release candidate version updates:</strong></p>

<p>Under the SSO &gt; Update Manager settings page, select the "Development and Up" (for example) version filter for the WPSSO Core plugin and/or its add-ons. When new development versions are available, they will automatically appear under your WordPress Dashboard &gt; Updates page. You can reselect the "Stable / Production" version filter at any time to reinstall the latest stable version.</p>

<p><strong>WPSSO Core Standard edition users (ie. the plugin hosted on WordPress.org) have access to <a href="https://wordpress.org/plugins/organization-place/advanced/">the latest development version under the Advanced Options section</a>.</strong></p>

<h3>Changelog / Release Notes</h3>

**Version 1.17.0-dev.10 (2021/11/04)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Refactored the settings page load process.
* **Requires At Least**
	* PHP v7.2.34.
	* WordPress v5.5.
	* WPSSO Core v16.7.0-dev.10.

**Version 1.16.0 (2023/10/28)**

* **New Features**
	* None.
* **Improvements**
	* Update for deprecated method.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Changed deprecated `WpssoUtilReg::update_options_key()` call for `WpssoUtilWP::update_options_key()`.
* **Requires At Least**
	* PHP v7.2.34.
	* WordPress v5.5.
	* WPSSO Core v16.5.0.

**Version 1.15.0 (2023/08/27)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Updated the 'wpsso_get_post_defaults' filter arguments for WPSSO Core v15.21.0.
* **Requires At Least**
	* PHP v7.2.34.
	* WordPress v5.5.
	* WPSSO Core v15.21.0.

**Version 1.14.0 (2023/07/13)**

* **New Features**
	* Added new Organization options:
		* Publishing Principles URL
		* Corrections Policy URL
		* Diversity Policy URL
		* Ethics Policy URL
		* Fact Checking Policy URL
		* Feedback Policy URL
	* Added new NewsMediaOrganization options
		* Masthead Page URL
		* Coverage Priorities Policy URL
		* No Bylines Policy URL
		* Unnamed Sources Policy URL
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.34.
	* WordPress v5.5.
	* WPSSO Core v15.16.0.

**Version 1.13.0 (2023/06/06)**

* **New Features**
	* None.
* **Improvements**
	* Added options to the Organization editing page:
		* Default Publisher Org.
		* Default Service Provider Org.
		* Default Event Organizer Org.
		* Default Event Performer Org.
		* Default Job Hiring Org.
	* Added options to the Place editing page:
		* Default Event Venue
		* Default Job Location
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.34.
	* WordPress v5.5.
	* WPSSO Core v15.13.1.

**Version 1.12.0 (2023/05/26)**

* **New Features**
	* None.
* **Improvements**
	* Renamed the "Organizations" menu item to "SSO Orgs".
	* Renamed the "Places" menu item to "SSO Places".
	* Moved menu items below the "SSO" settings menu item.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.5.
	* WPSSO Core v15.12.0.

== Upgrade Notice ==

= 1.17.0-dev.10 =

(2021/11/04) Refactored the settings page load process.

= 1.16.0 =

(2023/10/28) Update for deprecated method.

= 1.15.0 =

(2023/08/27) Updated the 'wpsso_get_post_defaults' filter arguments for WPSSO Core v15.21.0.

= 1.14.0 =

(2023/07/13) Added new Organization and NewsMediaOrganization options.

= 1.13.0 =

(2023/06/06) Added options to the Organization and Place editing pages.

= 1.12.0 =

(2023/05/26) Renamed and moved the "Organizations" and "Places" menu items.

