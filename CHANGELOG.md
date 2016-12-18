Changelog
=========

v1.0
----
 * Done by Gilad Buzi back in 2005

v2.0
----
* relaunch: major changes in database structure and interface which is now jquey based

v2.5
----
* new page: manage orders for revising individual quantities 
* de-/activate dates for orders redone; has now table format and clickable cells
* logic of turnos has own page which shortcuts for most common tasks
* incidents have now distribution level and can be posted on portal entry
* member management completely redone
* orders have now closing dates
* orders can be send directly to provider if platform has internet connection
* added personal overview of ordered items and past purchases for each household
* bill template; printable
* order item and shop item linked for better control 
* vat / iva groups to easy modify tax for product groups

v2.6
----
* direct PDF printing of incidents and bills
* emailing of incidents
* new page: purchase total ordered by provider
* unlocking orderable products/dates with ordered items is now possible
* de-/activating products for dates can be automatically repeated for all remaining dates
* warning message when cart has been validated without money desposit.
* overview of upcoming orders added to Home

**Bugfixes**
* creating preorders
* deactivating orderable products failed when other product for the same date had ordered items
* sum of revised orders is now updated when editing uf quantities
* mentor household did not show when creating new HU

v2.7
----
Released 2 April 2013
* provider new/edit form rewrite for better required form-fields checking
* product new/edit rewrite for better required form-fields checking
* new field in aixada_product: min order (requires to run dbUpgradeAixada2.6To2.7.sql
* config.sample.php added; copy this file and rename it to config.php to configure your local install
* provider/product listing sortable
* export architecture; works for providers, products, members to csv, xml, google drive.
* import architecture; works for providers and products (formats: csv, ods, xls, xlsx, xml) 
* change of licence to GPL  

**Bugfixes**
* delete members now possible for unassigned members
* member validation issues
* delete order cart completely bug 
* iva and revolutionary tax price calculation fix
* table manager checks fields against database before updating 
* correct warning message when creating new cart on validated
* #41 illegal collation fix
* #36 install language issue (thanks marc0s!) fix
* #37 filter session variables for table manager fix
* ...and otherss

v2.8 (to publish soon)
----

*This document is out of date for version 2.8, see "CHANGELOG-es.md"*

**New Features**

**Changes**
* report_stock page displays total value of stock products and stock adds/corrections
* provider/product page now is searchable for products
* rudimentary export order to csv 
* order_to_shop revisions won't be deleted anymore from aixada_order_to_shop. Needed to keep track of total of revised order and validated income
* currency symbol is not in config.php and currency description in lang files. 
* deactivate current_stock = 0 is set in config.php; prevents buying items.  

**Bugfixes**
* product edit form, calculation and display of brutto price fixed
* provider - responsible HU fix
* #51 pwd create/logon now works with proper salt and is backward compatible 
* #52 entire product row deactivation
* #78, #79 

