<p align="center"><a href="https://yithemes.com/"><img src="https://docs.yithemes.com/wp-content/uploads/2018/02/logo-1.png" alt="yithemes.com"></a></p>

<p align="center">
<img src="https://img.shields.io/github/v/release/yithemes/yith-woocommerce-wishlist?label=stable" alt="Latest release">
<img src="https://img.shields.io/github/license/yithemes/yith-woocommerce-wishlist" alt="License">
<img src="https://img.shields.io/github/last-commit/yithemes/yith-woocommerce-wishlist" alt="Last commit">
<img src="https://img.shields.io/github/languages/code-size/yithemes/yith-woocommerce-wishlist" alt="Code size">
</p>

Welcome to the YITH WooCommerce Wishlist repository on GitHub. Here you can browse the source, look at open issues and keep track of the development.

If you are not a developer, please, use the [YITH WooCommerce Wishlist plugin page](https://wordpress.org/plugins/yith-woocommerce-wishlist/) on WordPress.org.

## About plugin

What can really make the difference in conversions and amount of sales is without a doubt the freedom to share your own wishlist, even on social networks, and increase indirect sales: can you imagine the sales volume you can generate during holidays or birthdays, when relatives and friends will be looking for the wishlist of your clients to buy a gift?

OOffer to your visitors a chance to add the products of your WooCommerce store to a wishlist page. With YITH WooCommerce Wishlist you can add a link on each product detail page
 to add the products to the wishlist page. The plugin will create the specific page for you and the products will be added on this page. Afterwards, you will be able to add them to the cart or remove them.

## Getting started

* [Installation Guide](#quick-guide)
* [Languages](#available-languages)
* [Documentation](#documentation)
* [Changelog](#changelog)
* [Support](#support)
* [Reporting Security Issue](#reporting-security-issues)

## Installation guide

Clone the plugin directly into `wp-content/plugins/` directory of your WordPress site.

Otherwise, you can 

1. Download the repository .zip file.
2. Unzip the downloaded package.
3. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.

Finally, you'll need to activate `YITH WooCommerce Wishlist` from Plugins page.

## Available Languages

* Chinese - CHINA
* Chinese - TAIWAN
* Croatian - CROATIA
* Danish - DENMARK
* Dutch - NETHERLANDS
* English - UNITED KINGDOM (Default)
* French - FRANCE
* German - GERMANY
* Hebrew - ISRAEL
* Italian - ITALY
* Korean - KOREA
* Persian - IRAN, ISLAMIC REPUBLIC OF
* Polish - POLAND
* Portuguese - BRAZIL
* Portuguese - PORTUGAL
* Russian - RUSSIAN FEDERATION
* Spanish - ARGENTINA
* Spanish - SPAIN
* Spanish - MEXICO
* Swedish - SWEDEN
* Turkish - TURKEY
* Ukrainian - UKRAINE

## Documentation

You can find the official documentation of the plugin [here](https://docs.yithemes.com/yith-woocommerce-wishlist/)

We're also working hard to release a developer guide; please, follow our [social channels](http://twitter.com/yithemes) to be informed about any update.

## Changelog

### 3.0.6 ??? Released on 04 February 2020

* Tweak: avoid redirect for guest users if wishlist page is set to my-account
* Tweak: minor improvements to localization
* Tweak: update wrong text domains
* Tweak: changed default value for ATW icons
* Tweak: set wishlist session cookie JIT
* Tweak: use secure cookie for sessions, when possible (thanks to Ahmed)
* Tweak: improved cache handling for get_default_wishlist method
* Tweak: even if system cannot set session cookie, calculate session_id and use it for the entire execution
* Update: Italian language
* Update: plugin framework
* Fix: prevent error if list doesn't exists
* Fix: issue with wishlist_id query param
* Fix: items query now search for product in original language
* Fix: returning correct wishlist and user id to yith_wcwl_added_to_wishlist and yith_wcwl_removed_from_wishlist actions (thanks to danielbitzer)
* Fix: issue with default value for yith_wcwl_positions option
* Fix: added key name to avoid DB error during install or update procedure
* Dev: added yith_wcwl_shortcode_share_link_url filter

## Support

This repository should be considered as a development tool.
Please, post any support request about this plugin on [wp.org support forum](https://wordpress.org/support/plugin/yith-woocommerce-wishlist/)

If you have purchased the premium version and need support, please, refer to our [support desk](https://yithemes.com/my-account/support/dashboard/)

## Reporting Security Issues
To disclose a security issue to our team, please, contact us from our [contact form](https://yithemes.com/contact-form/).