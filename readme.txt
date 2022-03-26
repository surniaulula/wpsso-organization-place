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
Requires PHP: 7.2
Requires At Least: 5.2
Tested Up To: 5.9.2
Stable Tag: 1.5.3

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
* Google's Knowledge Graph:
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

WPSSO Organization and Place Manager (WPSSO OPM) is an add-on for the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/).

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

<p><strong>WPSSO Core Premium customers have access to development, alpha, beta, and release candidate version updates:</strong></p>

<p>Under the SSO &gt; Update Manager settings page, select the "Development and Up" (for example) version filter for the WPSSO Core plugin and/or its add-ons. Save the plugin settings and click the "Check for Plugin Updates" button to fetch the latest version information. When new development versions are available, they will automatically appear under your WordPress Dashboard &gt; Updates page. You can always reselect the "Stable / Production" version filter at any time to reinstall the latest stable version.</p>

<h3>Changelog / Release Notes</h3>

**Version 1.5.3 (2022/03/26)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Removed `$check_dupes` from all methods arguments.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v11.8.0.

**Version 1.5.2 (2022/03/23)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Replaced call to `$wpsso->post->get_public_ids()` by `WpssoPost::get_public_ids()`.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v11.7.2.

**Version 1.5.1 (2022/03/07)**

Maintenance release.

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v11.5.0.

**Version 1.5.0 (2022/02/17)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* Updated the `WpssoAbstractWpMeta` class for WPSSO Core v11.0.0.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v11.0.0.

**Version 1.4.1 (2022/02/10)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* Fixed the 'wpsso_get_post_options' argument count.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v10.3.0.

**Version 1.4.0 (2022/02/02)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Refactored the `WpssoOpmOrgFiltersOptions->check_org_image_sizes()` method to check both logo and banner images.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v10.0.0.

**Version 1.3.0 (2022/01/19)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* Fixed showing Local Business and Food Establishment options when editing a place.
* **Developer Notes**
	* Renamed the lib/abstracts/ folder to lib/abstract/.
	* Renamed the `SucomAddOn` class to `SucomAbstractAddOn`.
	* Renamed the `WpssoAddOn` class to `WpssoAbstractAddOn`.
	* Renamed the `WpssoWpMeta` class to `WpssoAbstractWpMeta`.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v9.14.0.

**Version 1.2.1 (2022/01/13)**

* **New Features**
	* None.
* **Improvements**
	* Updated "Organization Schema Type" option help text.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v9.13.1.

**Version 1.2.0 (2021/12/23)**

* **New Features**
	* None.
* **Improvements**
	* Added notice messages for selected but deleted or unpublished organization and place IDs.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v9.12.3.

**Version 1.1.1 (2021/12/21)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* Fixed an incorrect text domain for some migrated help messages.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v9.12.3.

**Version 1.1.0 (2021/12/17)**

* **New Features**
	* None.
* **Improvements**
	* Added updating of the post object content, along with the title, when saving an organization / place.
* **Bugfixes**
	* Fixed an options version check for cases where the WPSSO ORG and WPSSO PLM add-ons were updated first.
* **Developer Notes**
	* Added a call to `WpssoOptions->set_version()` after upgrading the options, to make sure the upgrade is only run once.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v9.12.2.

**Version 1.0.0 (2021/12/16)**

* **New Features**
	* First release.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v9.12.0.

== Upgrade Notice ==

= 1.5.3 =

(2022/03/26) Removed `$check_dupes` from all methods arguments.

= 1.5.2 =

(2022/03/23) Replaced call to `$wpsso->post->get_public_ids()` by `WpssoPost::get_public_ids()`.

= 1.5.1 =

(2022/03/07) Maintenance release.

= 1.5.0 =

(2022/02/17) Updated the `WpssoAbstractWpMeta` class for WPSSO Core v11.0.0.

= 1.4.1 =

(2022/02/10) Fixed the 'wpsso_get_post_options' argument count.

= 1.4.0 =

(2022/02/02) Refactored the `WpssoOpmOrgFiltersOptions->check_org_image_sizes()` method to check both logo and banner images.

= 1.3.0 =

(2022/01/19) Fixed showing Local Business and Food Establishment options when editing a place. Renamed the lib/abstracts/ folder and its classes.

= 1.2.1 =

(2022/01/13) Updated "Organization Schema Type" option help text.

= 1.2.0 =

(2021/12/23) Added notice messages for selected but deleted or unpublished organization and place IDs.

= 1.1.1 =

(2021/12/21) Fixed an incorrect text domain for some migrated help messages.

= 1.1.0 =

(2021/12/17) Fixed an options version check for cases where the WPSSO ORG and WPSSO PLM add-ons were updated first.

= 1.0.0 =

(2021/12/16) First release.

