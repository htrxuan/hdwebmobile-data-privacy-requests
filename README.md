# HDWebmobile Data Privacy Requests

Let customers request their own data be exported or erased from My Account, via WordPress's own core privacy-request system.

- **WordPress.org:** https://wordpress.org/plugins/hdwebmobile-data-privacy-requests/
- **Requires:** WordPress 6.9+, WooCommerce, PHP 7.4+
- **License:** GPLv2 or later

## Description

Adds "Request data export" and "Request account erasure" buttons to a logged-in customer's My Account dashboard. Every request is filed through WordPress's own core personal-data request system -- this plugin does not reimplement export or erasure itself.

## Why this plugin exists

Several competing GDPR/data-request plugins for WooCommerce accepted an email address directly from the request body when a logged-in customer submitted a data export or erasure request, and used that submitted email -- rather than the requester's own account email -- to create the underlying privacy request (CWE-863, Incorrect Authorization). A logged-in customer could submit someone else's email address and initiate an export or erasure request against a stranger's data.

This plugin never has an email field to submit in the first place:

* The one method that ever files a request takes a WordPress user id as its first parameter -- never an email address -- and always derives the email from that user's own account record.
* The only caller always passes the currently logged-in user's own id; there is no admin-post handler reachable by a guest, since this plugin only ever acts on the account that is already authenticated.
* Every request still goes through WordPress core's own confirmation-email flow, so even a compromised session can't silently complete an export or erasure without that email being seen.

## Features

* "Request data export" and "Request account erasure" buttons on My Account
* A list of the customer's own past requests and their status
* Reuses WordPress's own core Export/Erase Personal Data admin screens -- nothing new for the store owner to learn

## Limitations

* Logged-in customers only -- guests should use WordPress's own default admin-initiated privacy request tools
* Does not change what data WooCommerce exports/erases -- that's still governed by WooCommerce's and WordPress's own core exporters/erasers

## Installation

1. Upload to `/wp-content/plugins/hdwebmobile-data-privacy-requests`, or install through the WordPress plugins screen.
2. Activate. WooCommerce must already be installed and active.
3. Customers will see the new buttons on their My Account dashboard immediately.

## Development

```
includes/
  class-hddpr-activator.php   activation check
  class-hddpr-core.php        bootstraps admin + frontend
  class-hddpr-repository.php  the ONLY class that ever files a core privacy request -- see its docblock for the security design
  class-hddpr-admin.php       hub tab (links to WordPress's own core privacy-request screens)
  class-hddpr-frontend.php    My Account buttons + the logged-in-only request handler
  class-hddpr-hub.php         shared HDWebmobile admin hub (see the suite's other plugins)
```

## License

GPLv2 or later — https://www.gnu.org/licenses/gpl-2.0.html
