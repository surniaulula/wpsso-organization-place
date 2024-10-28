=== WPSSO Organization and Place Manager ===
Plugin Name: WPSSO Organization and Place Manager
Plugin Slug: wpsso-organization-place
Text Domain: wpsso-organization-place
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Assets URI: https://surniaulula.github.io/wpsso-organization-place/assets/
Tags: organization, place, location, local seo, local business
Contributors: jsmoriss
Requires Plugins: wpsso
Requires PHP: 7.4.33
Requires At Least: 5.9
Tested Up To: 6.7.0
Stable Tag: 3.2.1

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
	* Wikipedia Organization Page URL
	* X (Twitter) Business @username
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

WPSSO Organization and Place Manager (WPSSO OPM) is an add-on for the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/), which creates extensive and complete structured data to present your content at its best for social sites and search results – no matter how URLs are shared, reshared, messaged, posted, embedded, or crawled.

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

**Version 3.2.1 (2024/10/23)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* Fixed an incorrect check for `is_admin()` in `WpssoOpmIntegAdminPost->add_wp_callbacks()`.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.4.33.
	* WordPress v5.9.
	* WPSSO Core v18.15.0.

**Version 3.2.0 (2024/09/07)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Renamed and moved the `WpssoOpmPost` class to `WpssoOpmIntegAdminPost`.
* **Requires At Least**
	* PHP v7.4.33.
	* WordPress v5.9.
	* WPSSO Core v18.10.0.

== Upgrade Notice ==

= 3.2.1 =

(2024/10/23) Fixed an incorrect check for `is_admin()` in `WpssoOpmIntegAdminPost->add_wp_callbacks()`.

= 3.2.0 =

(2024/09/07) Renamed and moved the `WpssoOpmPost` class to `WpssoOpmIntegAdminPost`.

