=== {eac}Doojigger MetaPixel Extension for WordPress ===
Plugin URI:         https://eacdoojigger.earthasylum.com/eacmetapixel/
Author:             [EarthAsylum Consulting](https://www.earthasylum.com)
Stable tag:         2.0.1
Last Updated:       25-Aug-2025
Requires at least:  5.8
Tested up to:       6.8
Requires PHP:       7.4
Requires EAC:       3.0
Contributors:       kevinburkholder
Donate link:        https://github.com/sponsors/EarthAsylum
License:            GPLv3 or later
License URI:        https://www.gnu.org/licenses/gpl.html
Tags:               facebook, facebook pixel, meta pixel, facebook ads, {eac}Doojigger, facebook conversion, facebook tracking, conversion, tracking
WordPress URI:      https://wordpress.org/plugins/eacmetapixel
GitHub URI:         https://github.com/EarthAsylum/eacMetaPixel

{eac}MetaPixel installs the Facebook/Meta Pixel to enable tracking of PageView, Search, ViewContent, AddToCart, InitiateCheckout and Purchase events.

== Description ==

_{eac}MetaPixel_ is an [{eac}Doojigger](https://eacDoojigger.earthasylum.com/) extension which installs the Facebook/Meta Pixel and enables tracking of PageView, Search, ViewContent, AddToCart, InitiateCheckout, AddPaymentInfo, and Purchase events when using [WooCommerce](https://woocommerce.com/).

= What is the Meta Pixel? =

>   The Meta Pixel is a piece of code on your website that can help you better understand the effectiveness of your advertising and the actions people take on your site, like visiting a page or adding an item to their cart. You’ll also be able to see when customers took an action after seeing your ad on Facebook and Instagram, which can help you with retargeting. And when you use the Conversions API alongside the Pixel, it creates a more reliable connection that helps the delivery system decrease your costs.

*See [Meta Pixel](https://www.facebook.com/business/tools/meta-pixel)*

To retrieve your pixel id, Go to Meta [Events Manager](https://business.facebook.com/events_manager2) → Data Sources → Your Pixel → Settings → Pixel ID. Copy your pixel id and paste it into the "Facebook Pixel ID" field of this extension's settings.

= Pixel Events =

+   Page Views
    +   PageView events may occur on all pages or only pages that don't trigger another event.
+   Site Search
    +   Search result pages.
+   Content View (archives)
    +   Archive (category and tag) pages.
+   Content View (products)
    +   Product pages.
+   Content View (commerce)
    +   Product category, tag, and shop pages.
+   Content View (cart)
    +   The shopping cart page.
+   Add To Cart
    +   Buttons and links that add an item to the cart.
+   Initiate Checkout
    +   The checkout page.
+	Add Payment Info
	+ 	Billing information on checkout page.
+   Purchase Completed
    +   Purchase confirmation page. (a 'Purchase' event is registered as a 'Subscription' if the order includes a subscription).

= Server Based Conversion API =

Support for the Meta [Conversion API](https://developers.facebook.com/docs/marketing-api/conversions-api) to track  events directly from your server is included. When using the Conversion API (CAPI), additional information will be passed through the purchase api, including:

+   Customer name (hashed, non-decipherable)
+   Customer email address (hashed, non-decipherable)
+   Customer phone number (hashed, non-decipherable)
+   Customer billing address (hashed, non-decipherable)
+   Order/cart details (item, quantity, price)

To enable the server conversion api, Go to Meta [Events Manager](https://business.facebook.com/events_manager2) → Data Sources → Your Pixel → Settings. Scroll to Conversions API → Set up manually.

Click the "Generate access token" link under the "Get Started" button. Copy the access token and paste it into the "Server Access Token" field of this extension's settings.

>   Note: Server events require a Meta Business Manager.

In most cases, the CAPI event will be sent before the Pixel event. When the page is requested, the Pixel code is added to the page at the same time the CAPI event is sent, then when the page loads in the browser, the pixel fires. If the page is cached, the CAPI event may not be sent. *Typically, e-commerce pages are not cached.*

= Advantage+ Catalog Ads =

The Content View (products), Add To Cart, Initiate Checkout, and Purchase Completed events meet the requirements for [Advantage+ catalog ads](https://www.facebook.com/business/help/606577526529702?id=1205376682832142).

+   content_type : 'product'.
+   content_ids : array of product skus.

= Domain Verification =

You may, optionally, add the Facebook Brand Safety domain verification meta tag to your home page.

Go to Meta [Business Settings](https://business.facebook.com/settings/) → Brand Safety → Domains → Your Domain → Add a meta-tag, and copy just the _content=_ string.

In this example:
```
    <meta name="facebook-domain-verification" content="xyzzy1ndu84mmhaifl5gawo9ntafn8" />
```
We want only *xyzzy1ndu84mmhaifl5gawo9ntafn8* copied and pasted into the "Domain Verification" field of this extension's settings.

= Actions and Filters =

+	Add a custom event to the page.

	do_action( 'eacDoojigger_meta_pixel_add_event', $eventType, $eventData, $eventID );

+	Get the script code for a custom event (to attach to a DOM event).

	$script = apply_filters( 'eacDoojigger_meta_pixel_event_code', $eventType, $eventData, $eventID );

+	Modify the event-specific data sent with the pixel and capi.

	add_filter('eacDoojigger_meta_pixel_eventdata', function($eventData, $eventType) {
		// modify $eventData array as needed
		return $eventData;
	}

+	Modify the user data sent with the capi event.

	add_filter('eacDoojigger_meta_pixel_userdata', function($userData, $eventData, $eventType) {
		// modify $userData array as needed
		return $userData;
	}

+	Modify the event ID sent with the pixel and capi.

	add_filter('eacDoojigger_meta_pixel_eventid', function($eventID, $eventData, $eventType) {
		// modify $eventID as needed
		return $eventID;
	}

+	Enable console logging for pixel and capi.

	add_filter('eacDoojigger_meta_pixel_console', function($enabled) {
		return true;
	}

+	Enable setting the _fbc cookie when fbclid is passed.

	add_filter('eacDoojigger_meta_pixel_cookie', function($enabled) {
		return true;
	}

>	Normally, the pixel code should set the _fbc cookie when first-party cookies are enabled. If this is not getting set, this filter can be used to enable internal code to set the cookie.


== Installation ==

**{eac}Doojigger MetaPixel Extension** is an extension plugin to and requires installation and registration of [{eac}Doojigger](https://eacDoojigger.earthasylum.com/).

= Automatic Plugin Installation =

This plugin is available from the [WordPress Plugin Repository](https://wordpress.org/plugins/search/earthasylum/) and can be installed from the WordPress Dashboard » *Plugins* » *Add New* page. Search for 'EarthAsylum', click the plugin's [Install] button and, once installed, click [Activate].

See [Managing Plugins -> Automatic Plugin Installation](https://wordpress.org/support/article/managing-plugins/#automatic-plugin-installation-1)

= Upload via WordPress Dashboard =

Installation of this plugin can be managed from the WordPress Dashboard » *Plugins* » *Add New* page. Click the [Upload Plugin] button, then select the eacmetapixel.zip file from your computer.

See [Managing Plugins -> Upload via WordPress Admin](https://wordpress.org/support/article/managing-plugins/#upload-via-wordpress-admin)

= Manual Plugin Installation =

You can install the plugin manually by extracting the eacmetapixel.zip file and uploading the 'eacmetapixel' folder to the 'wp-content/plugins' folder on your WordPress server.

See [Managing Plugins -> Manual Plugin Installation](https://wordpress.org/support/article/managing-plugins/#manual-plugin-installation-1)

= Settings =

Once installed and activated options for this extension will show in the 'Tracking' tab of {eac}Doojigger settings.


== Screenshots ==

1. MetaPixel Extension
![{eac}MetaPixel Extension](https://ps.w.org/eacmetapixel/assets/screenshot-1.png)


== Other Notes ==

= Additional Information =

+   {eac}MetaPixel is an extension plugin to and requires installation and registration of [{eac}Doojigger](https://eacDoojigger.earthasylum.com/).

See: [Specifications for Facebook Pixel Standard Events](https://business.facebook.com/business/help/402791146561655)


== Copyright ==

= Copyright © 2019-2025, EarthAsylum Consulting, distributed under the terms of the GNU GPL. =

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should receive a copy of the GNU General Public License along with this program. If not, see [https://www.gnu.org/licenses/](https://www.gnu.org/licenses/).


== Changelog ==

= Version 2.0.1 – Aug 25, 2025 =

+	Use `get_order_number` rather than `order->id` when passing order id/event id.
+	Add option to only trigger events when click id (fbclid) is present.
+	Move standard pixel script to constant.

= Version 2.0.0 – Aug 18, 2025 =

+	Updated to Facebook Graph API Version 23.0.
+	All events use JavaScript Pixel AND server Conversion API (CAPI).
+	Include eventID/event_id with all events.
+	Include fbc/fbp (when available) with all events.
+	Set `_fbc` cookie when `fbclid` url parameter passed.
+	`ViewContent` events include slug/name as product_group.
+	Added actions and filters.
+	Added `AddPaymentInfo` event (on billing_city).
+	Added `console.info()` for events.
+	Remove jQuery dependency.
+	Output script on `wp_print_footer_scripts` not `wp_enqueue_scripts`.
+	Use `wp_print_inline_script_tag` not `wp_add_inline_script`.

= Version 1.0.7 – Apr 19, 2025 =

+   Compatible with WordPress 6.8.
+   Prevent `_load_textdomain_just_in_time was called incorrectly` notice from WordPress.
    +   All extensions - via eacDoojigger 3.1.
    +   Modified extension registration in constructor.

= Version 1.0.6 – Apr 23, 2024 =

+   Correctly load javascript with jQuery dependency.
+   Use $this->minifyString() on inline script;
+   WordPress Requires at least: 5.7.0

= Version 1.0.5 – Apr 10, 2024 =

+   Added notice if activated without {eac}Doojigger.

= Version 1.0.4 – Feb 9, 2024 =

+   Fixed critical coding error in purchase tracking.

= Version 1.0.3 – June 8, 2023 =

+   Removed unnecessary plugin_update_notice trait.

= Version 1.0.2 – November 15, 2022 =

+   Updated to / Requires {eac}Doojigger 2.0.
+   Uses 'options_settings_page' action to register options.
+   Added contextual help using 'options_settings_help' action.
+   Moved plugin_action_links_ hook to eacDoojigger_load_extensions filter.

= Version 1.0.1 – September 25, 2022 =

+   Fixed potential PHP notice on load (plugin_action_links_).
+   Added upgrade notice trait for plugins page.

= Version 1.0.0 – September 6, 2022 =

+   Initial release.

