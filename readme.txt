=== {eac}Doojigger MetaPixel Extension for WordPress ===
Plugin URI:         https://eacdoojigger.earthasylum.com/eacmetapixel/
Author:             [EarthAsylum Consulting](https://www.earthasylum.com)
Stable tag:         1.0.4
Last Updated:       9-Feb-2023
Requires at least:  5.5.0
Tested up to:       6.4
Requires PHP:       7.2
Requires EAC:       2.0
Contributors:       kevinburkholder
License:            GPLv3 or later
License URI:        https://www.gnu.org/licenses/gpl.html
Tags:               facebook, facebook pixel, meta pixel, facebook ads, {eac}Doojigger, facebook conversion, facebook tracking, conversion, tracking
WordPress URI:      https://wordpress.org/plugins/eacmetapixel
GitHub URI:         https://github.com/EarthAsylum/eacMetaPixel

{eac}MetaPixel installs the Facebook/Meta Pixel to enable tracking of PageView, Search, ViewContent, AddToCart, InitiateCheckout and Purchase events.

== Description ==

_{eac}MetaPixel_ is an [{eac}Doojigger](https://eacDoojigger.earthasylum.com/) extension which installs the Facebook/Meta Pixel and enables tracking of PageView, Search, ViewContent, AddToCart, InitiateCheckout and Purchase events when using [WooCommerce](https://woocommerce.com/).

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
+   Purchase Completed
    +   Purchase confirmation page. (a 'Purchase' event is registered as a 'Subscription' if the order includes a subscription, or as a 'StartTrial' if any subscription has a trial start date.)

= Server Based Conversion API =

Support for the Meta [Conversion API](https://developers.facebook.com/docs/marketing-api/conversions-api) to track purchase events directly from your server is included. When using the Conversion API, additional information will be passed through the api, including:

+   Customer name (hashed, non-decipherable)
+   Customer email address (hashed, non-decipherable)
+   Customer phone number (hashed, non-decipherable)
+   Customer billing address (hashed, non-decipherable)
+   Order/cart details (item, quantity, price)

Since Facebook gives priority to the browser pixel, the browser pixel is suppressed in favor of the conversion api so the conversion api may provide more information.

To enable the server conversion api, Go to Meta [Events Manager](https://business.facebook.com/events_manager2) → Data Sources → Your Pixel → Settings. Scroll to Conversions API → Set up manually.

Click the "Generate access token" link under the "Get Started" button. Copy the access token and paste it into the "Server Access Token" field of this extension's settings.

>   Note: Server events require a Meta Business Manager.

= Advantage+ Catalog Ads =

The Content View (products), Add To Cart, Initiate Checkout, and Purchase Completed events meet the requirements for [Advantage+ catalog ads](https://www.facebook.com/business/help/606577526529702?id=1205376682832142).

+   content_type : 'product'.
+   content_ids : array of product ids (WooCommerce ID) e.g. [1174,1175].

The server Conversion API for purchases also includes:

+   contents : array of product details containing id (product sku), quantity, and item_price.

= Domain Verification =

You may, optionally, add the Facebook Brand Safety domain verification meta tag to your home page.

Go to Meta [Business Settings](https://business.facebook.com/settings/) → Brand Safety → Domains → Your Domain → Add a meta-tag, and copy just the _content=_ string.

In this example:
```
    <meta name="facebook-domain-verification" content="xyzzy1ndu84mmhaifl5gawo9ntafn8" />
```
We want only *xyzzy1ndu84mmhaifl5gawo9ntafn8* copied and pasted into the "Domain Verification" field of this extension's settings.


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

= Copyright © 2019-2023, EarthAsylum Consulting, distributed under the terms of the GNU GPL. =

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should receive a copy of the GNU General Public License along with this program. If not, see [https://www.gnu.org/licenses/](https://www.gnu.org/licenses/).


== Changelog ==

= Version 1.0.4 – Feb 9, 2023 =

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


== Upgrade Notice ==

= 1.0.2 =

Requires {eac}Doojigger version 2.0+
