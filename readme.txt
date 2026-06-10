=== WPSSO Schema Organization and Place Manager ===
Plugin Name: WPSSO Schema Organization and Place Manager
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
Requires At Least: 6.0
Tested Up To: 7.0
Stable Tag: 5.2.0

Manage Organizations and Places (Local Businesses, Venues, etc.) for Google, Facebook, Pinterest, and Schema markup.

== Description ==

<!-- about -->

**Manage Schema Contact Points** for Schema markup, including origanization contact points - for example, a customer service department or mailing address.

**Manage Schema Organizations** for Schema markup, including publishers, service providers, production companies, event organizers, event performers, job hiring organizations, etc.

**Manage Schema Places (ie. Local Businesses, Venues, etc.)** for Facebook Open Graph meta tags and Schema markup, including organization locations, event locations, job locations, etc.

<!-- /about -->

**The SSO Contacts editing page includes:**

* Contact Name
* Contact Alternate Name
* Contact Description
* Contact Schema Type
* Contact Telephone
* Contact Fax
* Contact Email
* Contact Same-As URLs
* Postal Address:
	* Street Address
	* P.O. Box Number
	* City / Locality
	* State / Province
	* Zip / Postal Code
	* Country
* Service Area Information
	* Service Latitude
	* Service Longitude
	* Service Radius
	* Service Areas
* Opening Hours:
	* Open Days / Hours
	* Seasonal Dates

**The SSO Organizations editing page includes:**

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
* Organization Contact Points
* Organization Awards
* Offer Catalogs:
	* Offer Catalog Name
	* Offer Catalog Description
	* Offer Catalog URL
* Service Area Information
	* Service Latitude
	* Service Longitude
	* Service Radius
	* Service Areas
* News Media Organization:
	* Masthead Page URL
	* Coverage Priorities Policy URL
	* No Bylines Policy URL
	* Unnamed Sources Policy URL
* Knowledge Graph Information:
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
	* Wikipedia Org. Page URL
	* X (Twitter) Business @username
	* YouTube Business Channel URL

**The SSO Places editing page includes:**

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
* Place Image ID
* or Place Image URL
* Place Latitude
* Place Longitude
* Place Altitude
* Place Timezone
* Place Telephone
* Place Fax
* Place Same-As URLs
* Opening Hours:
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

WPSSO Schema Organization and Place Manager is an add-on for the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/), which creates extensive and complete structured data to present your content at its best for social sites and search results – no matter how URLs are shared, reshared, messaged, posted, embedded, or crawled.

== Installation ==

<h3 class="top">Install and Uninstall</h3>

* [Install the WPSSO Schema Organization and Place Manager add-on](https://wpsso.com/docs/plugins/wpsso-organization-place/installation/install-the-plugin/).
* [Uninstall the WPSSO Schema Organization and Place Manager add-on](https://wpsso.com/docs/plugins/wpsso-organization-place/installation/uninstall-the-plugin/).

== Frequently Asked Questions ==

== Screenshots ==

01. The SSO Organization editing page.
01. The SSO Place editing page.

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

**Version 6.0.0-b.1 (2026/06/10)**

* **New Features**
	* None.
* **Improvements**
	* Added a new "Service Area Information" section in the SSO Contacts and SSO Organizations editing pages:
		* Service Latitude
		* Service Longitude
		* Service Radius
		* Service Areas
	* Added a new "Contact Same-As URLs" option in the SSO Contacts editing page.
	* Added a new "Place Same-As URLs" option in the SSO Places editing page.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Added a new `WpssoOpmPlaceFiltersEdit->filter_form_cache_admin_area_names()` method.
* **Requires At Least**
	* PHP v7.4.33.
	* WordPress v6.0.
	* WPSSO Core v22.3.0-b.1.

**Version 5.2.0 (2026/05/26)**

* **New Features**
	* None.
* **Improvements**
	* Added information about open/close hours under the "Opening Hours Information" section.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.4.33.
	* WordPress v6.0.
	* WPSSO Core v22.1.3.

**Version 5.1.0 (2025/06/22)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Refactored the `WpssoOpmOrg::add_mb_org_rows()` method to optimize select arrays.
* **Requires At Least**
	* PHP v7.4.33.
	* WordPress v6.0.
	* WPSSO Core v21.0.0.

**Version 5.0.0 (2025/06/18)**

* **New Features**
	* Added a new "SSO Contacts" menu item to manage Schema Contact Points:
		* Contact Name
		* Contact Alt. Name	
		* Contact Description
		* Contact Schema Type
		* Contact Telephone
		* Contact Fax
		* Contact Email
		* Postal Address:
			* Street Address
			* P.O. Box Number
			* City / Locality
			* State / Province
			* Zip / Postal Code
			* Country
		* Opening Hours:
			* Open Days / Hours
			* Seasonal Dates
	* Added new options in the SSO Orgs &gt; Edit Organization page:
		* Organization Contact Points
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Added new constants:
		* WPSSOOPM_CONTACT_ARCHIVE_SLUG
		* WPSSOOPM_CONTACT_CATEGORY_TAXONOMY
		* WPSSOOPM_CONTACT_MENU_ORDER
		* WPSSOOPM_CONTACT_POST_TYPE
	* Added a new `WpssoOpmContact` class.
	* Added a new `WpssoOpmContactFiltersEdit` class.
	* Added a new `WpssoOpmContactFiltersOptions` class.
	* Added a new `WpssoOpmRegister::register_contact_post_type()` method.
	* Added a new `WpssoOpmRegister::register_contact_category_taxonomy()` method.
	* Refactored the `WpssoOpmIntegAdminPost->add_meta_boxes()` method.
* **Requires At Least**
	* PHP v7.4.33.
	* WordPress v5.9.
	* WPSSO Core v21.0.0.

== Upgrade Notice ==

= 6.0.0-b.1 =

(2026/06/10) Added a new options the SSO Contacts, Organizations, and Places editing pages.

= 5.2.0 =

(2026/05/26) Added information about open/close hours under the "Opening Hours Information" section.

= 5.1.0 =

(2025/06/22) Refactored the `WpssoOpmOrg::add_mb_org_rows()` method to optimize select arrays.

= 5.0.0 =

(2025/06/18) Added a new "SSO Contacts" menu item to manage Schema Contact Points.

