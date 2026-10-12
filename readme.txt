=== HDWebmobile Data Privacy Requests ===
Contributors: htrxuan
Donate link: https://paypal.me/htrxuan/20
Tags: woocommerce, gdpr, privacy, data export, data erasure
Requires at least: 6.9
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
Requires Plugins: woocommerce
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Let customers request their own data be exported or erased from My Account, via WordPress's own core privacy-request system.

== Description ==

HDWebmobile Data Privacy Requests adds "Request data export" and "Request account erasure" buttons to a logged-in customer's My Account dashboard. Every request is filed through WordPress's own core personal-data request system -- this plugin does not reimplement export or erasure itself.

= Security by design =
A common weakness in GDPR/data-request tools is accepting an email address directly from the request body when a logged-in customer submits a data export or erasure request, and using that submitted email -- rather than the requester's own account email -- to create the underlying privacy request (CWE-863, Incorrect Authorization). A logged-in customer could submit someone else's email address and initiate an export or erasure request against a stranger's data.

This plugin never has an email field to submit in the first place:

* The one method that ever files a request takes a WordPress user id as its first parameter -- never an email address -- and always derives the email from that user's own account record.
* The only caller always passes the currently logged-in user's own id; there is no admin-post handler reachable by a guest, since this plugin only ever acts on the account that is already authenticated.
* Every request still goes through WordPress core's own confirmation-email flow, so even a compromised session can't silently complete an export or erasure without that email being seen.

= Key Features =
* "Request data export" and "Request account erasure" buttons on My Account
* A list of the customer's own past requests and their status
* Reuses WordPress's own core Export/Erase Personal Data admin screens -- nothing new for the store owner to learn

= Limitations (please read before installing) =
* Logged-in customers only -- guests should use WordPress's own default admin-initiated privacy request tools
* Does not change what data WooCommerce exports/erases -- that's still governed by WooCommerce's and WordPress's own core exporters/erasers

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/hdwebmobile-data-privacy-requests` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress. WooCommerce must already be installed and active.
3. Customers will see the new buttons on their My Account dashboard immediately.

== How to Use ==

= 1. Customer requests their data =
From My Account, they click "Request data export" or "Request account erasure".

= 2. They confirm by email =
WordPress's own confirmation email is sent to their account address.

= 3. You action it as usual =
Review and complete the request from the standard Tools > Export Personal Data / Erase Personal Data screens.

== Screenshots ==

1. The request buttons on My Account.

== Changelog ==

= 1.0.0 =
* Initial release: My Account data export/erasure requests, always scoped to the requester's own account, filed through WordPress's own core privacy-request system.
