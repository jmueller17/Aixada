<?php

require_once(asset('app/storage/config.php'));

// English translation file for aixada 

return array(
  'en' => "English",
  'charset' => "utf-8",
  'text_dir' => "ltr", // ('ltr' for left to right, 'rtl' for right to left)


/** 
 *  		Global things
 */
  'coop_name' => configuration_vars::get_instance()->coop_name,
  'currency_sign' => configuration_vars::get_instance()->currency_sign,
  'currency_desc' => "Euros", 
  'please_select' => "Please select ...",
  'loading' => "Please wait while loading...",
  'search' => "Search",
  'id' => "id",
  'uf_short' => "HU",
  'uf_long' => "Household",



/** 
 *  		misc
 */
  'date_from' => "from",
  'date_to' => "to",
  'mon' => "Monday",
  'tue' => "Tuesday",
  'wed' => "Wednesday",
  'thu' => "Thursday",
  'fri' => "Friday",
  'sat' => "Saturday",
  'sun' => "Sunday",

/**
 * 				selects
 */
  'sel_provider' => "Please select a provider...",
  'sel_product' => "Please select a product...",
  'sel_user' => "Please select a user...",
  'sel_category' => "Please select a product category...",
  'sel_uf' => "Please select household...",
  'sel_uf_or_account' => "Please select household or account...",
  'sel_account' => "Please select an account...",
  'sel_none' => "none",

/**
 * 				tabs
 */
  'by_provider' => "By Provider",
  'by_category' => "By Product Category",
  'special_offer' => "Pre-order",
  'search_add' => "Search and add another item",
  'validate' => "Validate",


/**
 *  			titles for header <title></title>
 */
  'global_title' => configuration_vars::get_instance()->coop_name . " platform",
  'head_ti_validate' => "Validate",

  'head_ti_active_products' => "De/activate orderable products",
//  'head_ti_arrived_products' => "Products that have arrived",
  'head_ti_active_roles' => "Active Roles",
  'head_ti_account' => "Accounts",
  'head_ti_manage_orders' => "Manage Orders",
//  'head_ti_manage_dates' => "Set Order Dates",
  'head_ti_manage' => "Manage",
  'head_ti_manage_uf' => "Households/Members",
  'head_ti_incidents' => "Incidents",
  'head_ti_stats' => "Statistics of the Day",
  'head_ti_prev_orders' => "My previous purchases", 
  'head_ti_cashbox' => "Cashbox control", 





/**
 *  			titles for main pages <h1></h1>
 */
  'ti_mng_activate_products' => "De-/activate products for ordering",
  'ti_mng_activate_roles' => "Manage roles of user ",
  'ti_mng_activate_users' => "Activate users for ",
  'ti_mng_activate_preorders' => "Convert preorder to order",
  'ti_mng_members' => "Manage member",
  'ti_mng_db' => "Backup database", 
  'ti_order' => "Place your order for ",
  'ti_shop' => "Shop",
//  'ti_report_report' => "Summary of orders for ", 
  'ti_report_account' => "Report accounts ", 
//  'ti_report_my_account' => "Report my account ", 
  'ti_report_preorder' => "Summary of preorders", 
  'ti_report_incidents' => "Today's incidents",
  'ti_incidents' => "Incidents",
  'ti_validate' => "Validate cart for HU",
  'ti_stats' => "Overall statistics",
  'ti_my_account' => "My settings",
  'ti_my_account_money' => "My money",
//  'ti_my_prev_sales' => "My previous purchases",
  'ti_all_sales' => "Overview sales",
  'ti_login_news' => "Login and news",
  'ti_timeline' => "Timeline Report",
  'ti_report_torn' => "Summary of today's session",
//  'ti_mng_cashbox' => "Cashbox",




/**
 * 				roles
 */
  'Consumer' => "Consumer",
  'Checkout' => "Checkout",
  'Consumer Commission' => 'Consumer Commission',
  'Econo-Legal Commission' => 'Econo-Legal Commission',
  'Logistic Commission' => 'Logistic Commission',
  'Hacker Commission' => 'Hacker Commission',
  'Fifth Column Commission' => 'Fifth Column Commission',
  'Producer' => "Producer",


/**
 * 				Manage Products / Roles
 */
//$Text['mo_inact_prod'] = "Products they can't order";
//  'mo_act_prod' => "Products they will be able to order:",
//$Text['mo_notarr_prod'] = "Products that haven't arrived:";
//  'mo_arr_prod' => "Products that have arrived:",
  'mo_inact_role' => "Inactive roles",
//  'mo_act_role' => "Active roles",
  'mo_inact_user' => "Inactive users",
  'mo_act_user' => "Active users",
//  'msg_no_report' => "No providers/products to report for the given date!",


/**
 * 				uf member manage
 */
  'search_memberuf' => "Search name or login", //changed !!!!!!!!!
  'browse_memberuf' => "Browse",
  'assign_members' => "Assign members",
  'login' => "Login",
  'create_uf' => "New HU",
  'name_person' => "Name",
  'address' => "Address",
  'zip' => "Zip",
  'city' => "City",
  'phone1' => "Phone1",
  'phone2' => "Phone2",
  'email' => "Email",
  'web' => "Web",
  'last_logon' => "Last seen",
  'created' => "Created on",
  'active' => "Active",
  'participant' => "Participant",
  'roles' => "Roles",
  'active_roles' => "Active roles",
  'products_cared_for' => "Products responsible for",
  'providers_cared_for' => "Providers responsible for",
  'notes' => "Notes",
  'edit_uf' => "Edit HU",
//  'members_uf' => "Members of household",
  'mentor_uf' => "Mentor HU",
  'unassigned_members' => "Unassigned",
  'edit_my_settings' => "Edit my settings",

  'nif' => "VAT Reg No",
  'bank_name' => "Bank",
  'bank_account' => "Bank account",
  'picture' => "Picture",
  'offset_order_close' => "Processing time",
  'iva_percent_id' => "IVA type",
  'percent' => "Percent",
  'adult' => "Adult",


/**
 *  			wiz stuff
 */
  'deposit_cashbox' => "Deposit money in cashbox", 
  'widthdraw_cashbox' => "Withdraw money from cashbox", 
  'current_balance' => "Current balance",
  'deposit_type' => "Type of deposit",
  'deposit_by_uf' => "Deposite by HU",
  'deposit_other' => "All other stuff...",
  'make_deposit_4HU' => "Deposit by",
  'short_desc' => "Short description",
  'withdraw_type' => "Type of withdrawal",
  'withdraw_for_provider' => "Make a withdrawal for provider",
  'withdraw_other' => "All other withdrawals..",
  'withdraw_provider' => "Pay provider",
  'btn_make_withdrawal' => "Withdrawal",
  'correct_balance' => "Correct balance",
  'set_balance' => "Set current balance in cashbox",
  'name_cash_account' => "Cashbox",



/**
 *				validate
 */
  'set_date' => "Set a date",
  'get_cart_4_uf' => "Get cart for household",
  'make_deposit' => "Deposit for household",
  'success_deposit' => "Deposit has been saved!",
  'amount' => "Amount",
  'comment' => "Comment",
//  'deposit_other_uf' => "Make deposit for another household or account",
  'latest_movements' => "Latest movements",
  'time' => "Time",
  'account' => "Account",
  'consum_account' => "Consum Account",
  'operator' => "Operator",
  'balance' => "Balance",
  'dailyStats' => "Daily statistics",
  'totalIncome' => "Total income",
  'totalSpending' => "Total spending",
  'negativeUfs' => "Negative households",
  'lastUpdate' => "Last update",
  'negativeStock' => "Products with negative stock",
  'curStock' => "Current stock",
  'minStock' => "Minimal stock",
  'stock' => "Stock",



/**
 *              Shop and order
 */

  'info' => "Info",
  'quantity' => "Quantity",
  'unit' => "Unit",
  'price' => "Price",
  'name_item' => "Name",
  'revtax_abbrev' => "RevTax",
//  'cur_stock' => "Current Stock",
  'date_for_shop' => "Date shopped",
  'ts_validated' => "Validated",

/**
 * 		Logon Screen
 */ 
  'welcome_logon' => "Welcome to " . configuration_vars::get_instance()->coop_name . "!",
  'logon' => "User",
  'pwd' => "Password",
  'old_pwd' => "Old Password",
  'retype_pwd' => "Retype password",
  'lang' => "Language",
  'msg_err_incorrectLogon' => "Incorrect login",
  'msg_err_noUfAssignedYet' => "You haven't been assigned an UF yet. Please get somebody to complete registering you.",


//  'msg_reg_success' => "You have been successfully registered; the full activation of your user is pending. Have all the other members of your UF register, and get someone to finish the process. ",
//  'register' => "Register",
  'required_fields' => " are required fields",


/**
 *			Navigation
 */
  'nav_home' => "Home",
  'nav_wiz' => "Wizard",
//	$Text['nav_wiz_arrived' => "Products that haven't arrived";
	  'nav_wiz_validate' => "Validate",
//	  'nav_wiz_open' => "Open",
//	  'nav_wiz_close' => "Close",
	  'nav_wiz_torn' => "Summary info",
	  'nav_wiz_cashbox' => "Cashbox",
  'nav_shop' => "Buy stuff",
  'nav_order' => "Place order",
  'nav_mng' => "Manage",
	//  'nav_mng_uf' => "Households",
	  'nav_mng_member' => "Members",
	  'nav_mng_providers' => "Providers",
	  'nav_mng_products' => "Products",
		  'nav_mng_deactivate' => "De/Activate for orders",
		  'nav_mng_stock' => "Stock",
		  'nav_mng_units' => "Units",
	  'nav_mng_orders' => "Orders",
		//  'nav_mng_setorderable' => "Set orderable dates",
		//  'nav_mng_move' => "Move order to new date",
		//  'nav_mng_orders_overview' => "Manage orders",
		  'nav_mng_preorder' => "Convert preorder to order",
	  'nav_mng_db' => "Backup db",
	  'nav_mng_roles' => "Roles",
  'nav_report' => "Reports",
//  'nav_report_order' => "Current order",
  'nav_report_account' => "Accounts",
  'nav_report_timelines' => "Timelines",
  'nav_report_timelines_uf' => "Households",
  'nav_report_timelines_provider' => "Providers",
  'nav_report_timelines_product' => "Products",
  'nav_report_daystats' => "Statistics of the day",
  'nav_report_preorder' => "Preorders",
  'nav_report_incidents' => "Today's incidents",
  'nav_report_shop_hu' => "By Households",
  'nav_report_shop_pv' => "By Providers",


  'nav_incidents' => "Incidents",
	  'nav_browse' => "Browse / add",
  'nav_myaccount' => "My Account",
	  'nav_myaccount_settings' => "Settings",
	  'nav_myaccount_account' => "My money",
	  'nav_changepwd' => "Change password", 
	  'nav_prev_orders' => "Previous purchases",

  'nav_logout' => "Sign out",
  'nav_signedIn' => "Signed in as ",
  'nav_can_checkout' => "You may check out goods.",
  'nav_try_to_checkout' => "Be checkout",
  'nav_stop_checkout' => "Stop checkout",



/**
 *			Buttons
 */
  'btn_login' => "Login",
  'btn_submit' => "Submit",
  'btn_save' => "Save",
  'btn_reset' => "Reset",
  'btn_cancel' => "Cancel",
  'btn_activate' => "Activate",
  'btn_deactivate' => "Deactivate",
//  'btn_arrived' => "Has arrived",
//  'btn_notarrived' => "Has not arrived",
//  'btn_move' => "Move",
  'btn_ok' => "Ok",
  'btn_assign' => "Assign",
  'btn_create' => "Create",
  'btn_close' => "Close",
  'btn_make_deposit' => "Deposit!",
  'btn_new_incident' => "New Incident",
  'btn_reset_pwd' => "Reset Password", 
  'btn_view_cart' => "Cart", 
  'btn_view_cart_lng' => "View cart only",
  'btn_view_list' => "Products",
  'btn_view_list_lng' => "View product list only",
  'btn_view_both' => "Both",
  'btn_view_both_lng' => "View both, product list and cart",
  'btn_repeat' => "Ok, repeat this!",
  'btn_repeat_single' => "No, just one", 
  'btn_repeat_all' => "Ok, apply to all", 



/**
 * Incidents
 */
  'create_incident' => "Create new incident",
  'overview' => "Overview",
  'subject' => "Subject",
  'message' => "Message",
  'priority' => "Priority",
  'status' => "Status",
  'incident_type' => "Type",
  'status_open' => "Open",
  'status_closed' => "Done!",
  'ufs_concerned' => "HUs concerned",
  'provider_concerned' => "For provider",
  'comi_concerned' => "For commission",
  'created_by' => "Created by",
  'edit_incident' => "Edit incident",

/**
 *  Reports
 */
  'provider_name' => "Provider",
  'product_name' => "Product",
//  'qty' => "Quantity",
  'total_qty' => "Total Quantity",
  'total_price' => "Total Price",
  'total_amount' => "Total amount",
//  'select_order' => "List orders for the following date:",
//  'move_success' => "The listed order items are now active for: ",
//  'show_compact' => "Change view: shift / provider",
//  'show_all_providers' => "Toggle products",						//ADDED 8. JUNE
//  'show_all_print' => "Toggle printout",							//ADDED 8. JUNE
  'nr_ufs' => "Total HUs",
  'printout' => "Print",
  'summarized_orders' => "Summarized orders",
  'detailed_orders' => "Detailed orders",


/**
 * 		Error / Warning Messages
 */
  'msg_err_incorrectLogon' => "User or password didn't match! Please try again.",
  'msg_err_pwdctrl' => "The passwords did not match. Please retype both!",
  'msg_err_usershort' => "The username is too short. Should have min. of three characters",
  'msg_err_userexists' => "The username already exists. Please choose another one.",
  'msg_err_passshort' => "The password is too short. Should be between 4 and 15 characters long.",
  'msg_err_notempty' => " field cannot be left empty!", 
  'msg_err_namelength' => "The name and family name cannot be left empty or contain more than 255 characters!", 
  'msg_err_only_num' => " field only allows numbers and cannot be empty!", 
  'msg_err_email' => "The email format is not correct. Should be name@domain.com or similar.",
//  'msg_err_select_uf' => "In order to assign a new member to an HU you have to select an HU first by clicking on its name! If you need a new HU, create one first by clicking +New HU.",
//$Text['msg_err_select_non_member' => "In order to assign a new member to an HU you have to select one from the non-member listing on your right!"; 
//  'msg_err_insufficient_stock' => "Insufficient stock for ",


  'msg_edit_success' => "Everything saved succesfully!", //changed!!!!! 
//  'msg_edit_mysettings_success' => "Your new settings have been saved succesfully!",
  'msg_pwd_changed_success' => "Your password has been changed successfully!", 
  'msg_confirm_del' => "Are you sure you want to remove this member from this household?", //changed!!!!!
  'msg_enter_deposit_amount' => "Deposit amount should only contain numbers and not be empty!",
  'msg_please_set_ufid_deposit' => "The HU ID is not set. You either have to choose a cart or select an alternative HU to make a deposit!",
//  'msg_error_deposit' => "An error has occured while making the deposit. You can try again. Successful deposits should show in the account listing. <br/>The error message was: ",
  'msg_deposit_success' => "Deposit has been successful!",
  'msg_withdrawal_success' => "The withdrawal has been successful!",
  'msg_select_cart_first' => "In order to add items for validating you have to select an HU/cart first!",
//  'msg_err_move_date' => "An error has occured while moving the order to the new date. Try again. ",
  'msg_no_active_products' => "Sorry, but currently there are no products activated for ordering. Talk to the person in charge of provider(s)!",
//  'msg_no_movements' => "Sorry, no movements for given account and date!", 
  'msg_delete_incident' => "Are you sure you want to delete this incident?",
//  'msg_err_selectFirstUF' => "There is no household selected. Choose one first and then its purchases.", //ADDED JAN 2012


/**
 *  Product categories
 */
  'SET_ME' => 'SET_ME',

  'prdcat_vegies' => "Vegetables",
  'prdcat_fruit' => "Fresh fruits",
  'prdcat_mushrooms' => "Mushrooms",
  'prdcat_dairy' => "Milk and yoghurt", 			//fresh milk, joguhrt
  'prdcat_meat' => "Meat",							//fresh chicken, beef, lamp, etc.
  'prdcat_bakery' => "Bakery and flour",						//bread, pastry, flour
  'prdcat_cheese' => "Cheese",
  'prdcat_sausages' => "Sausages",					//ham and stuff
  'prdcat_infant' => "Infant nutrition",
  'prdcat_cereals_pasta' => "Cereals and pasta",	//Cereals and pasta
  'prdcat_canned' => "Canned stuff",
  'prdcat_cleaning' => "Cleaning",					//house clearning, detergents, etc.
  'prdcat_body' => "Body care",
  'prdcat_seasoning' => "Seasoning and algea",
  'prdcat_sweets' => "Honey and other sweets",		//mermelada, honey, sugar, chocolate
  'prdcat_drinks_alcohol' => "Alcoholics",			//wine, beer, etc.
  'prdcat_drinks_soft' => "Soft drinks",			//juice, vegetable drinks
  'prdcat_drinks_hot' => "Coffee and tea",
  'prdcat_driedstuff' => "Snacks and dried fruit",
  'prdcat_paper' => "Cellulose and paper",		//hankerchiefs, toilet paper, kitchen paper, 
  'prdcat_health' => "Health",		//hankerchiefs, toilet paper, kitchen paper, 
  'prdcat_misc' => "Everything else...",





/**
 *  Field names in database
 */

  'name' => "Name",
  'contact' => "Contact",
  'fax' => "Fax",
  'responsible_mem_name' => "Responsible member",
  'responsible_uf' => "Responsible household",
  'provider' => "Provider",
  'description' => "Description",
  'barcode' => "Barcode",
  'orderable_type' => "Product type",
  'category' => "Category",
  'rev_tax_type' => "Revolutionary tax",
  'unit_price' => "Unit price",
  'iva_percent' => "VAT in percent",
  'unit_measure_order' => "Ordered in which units",
  'unit_measure_shop' => "Sold in which units",
  'stock_min' => "Alert when stock below",
  'stock_actual' => "Current amount in stock",
  'delta_stock' => "Difference to minimal stock",
  'description_url' => "URL of description",


/**
 * added after 14.5
 */
  'msg_err_validate_self' => "You cannot validate your own cart!",
//  'msg_err_preorder' => "Sorry, but in order to activate this preoder you have to choose a date in the future!",
//  'msg_preorder_success' => "Preorder has been successfuly activated for the following date: ",
//  'msg_can_be_ordered' => "Items can be ordered for this date",
//  'msg_has_ordered_items' => "Items have been ordered for this day; they cannot be deleted, just moved",
//  'msg_today' => "Today",
//  'msg_default_day' => "Days without any orders yet",
//  'activate_for_date' => "Activate for ",
//  'start_date' => "Show items starting from ",


//$Text['Download zip' => 'Download zip file with all orders';
  'product_singular' => "product",
  'product_plural' => "products",
  'confirm_db_backup' => "Are you sure you want to backup the whole database? This may take a little while...",
  'show_date_field' => "Click here to show the calendar field and select a different date than today.",


//  'purchase_current' => "My purchase(s)",
//  'items_bought' => "Past purchases",
//  'purchase_future' => "My order(s)",
//  'purchase_prev' => "Previous purchase(s)",
  'icon_order' => "Place your order",
  'icon_purchase' => "Buy items now",
  'icon_incidents' => "Post an incident",
  'purchase_date' => "Date of purchase",
//  'purchase_validated' => "Date of validation",
  'ordered_for' => "Items ordered for",
  'not_validated' => "not validated",






/* definitely new stuff */

  'download_db_zipped' => "Download Zipped Database",
  'backup' => "Ok, back up the database!",
  'filter_incidents' => "Filter incidents",
  'todays' => "Today's",
  'recent_ones' => "Recent ones",
  'last_year' => "Last year",
  'details' => "Details",
  'actions' => "Actions",
  'incident_details' => "Incident details",
  'distribution_level' => "Distribution level",
  'internal_private' => "Internal (private)",
  'internal_email_private' => "Internal + email (private)",
  'internal_post' => "Internal + post to portal (public)",
  'internal_email_post' => "Internal + email + post (public)",

  'date' => "Date",
  'iva' => "VAT",
  'expected' => "Expected",
  'not_yet_sent' => "Not yet sent",
  'ordered_for' => "Ordered for",
  'my_orders' => "My Order(s)",
  'my_purchases' => "My Purchase(s)",
  'loading_status_info' => "Loading status info...",
  'previous' => "Previous",
  'next' => "Next",
  'date_of_purchase' => "Date of purchase",
  'validated' => "Validated",
  'total' => "Total",
  'ordered' => "Ordered",
  'delivered' => "Delivered",
  'price' => "Price",
  'qu' => "Qu",
  'msg_err_deactivatedUser' => "Your user account has been deactivated!",
  'order' => "Order",
  'order_pl' => "Orders",
  'msg_already_validated' => "The selected cart has already been validated. Do you want to see its products/items?",
  'validated_at' => "Validated at ", //refers to a date/hour


  'nothing_to_val' => "Nothing to validate for HU",
  'cart_id' => "Cart id",
  'msg_several_carts' => "The selected household has more than one cart pending for validation. Please select one:",
  'transfer_type' => "Type",
  'todays_carts' => "Today's carts",
  'head_ti_torn' => "Working shift overview", 
  'btn_validate' => "Validate",
  'desc_validate' => "Validate past and present carts for households. Make money deposits.",
  'nav_wiz_revise_order' => "Revise",
  'desc_revise' => "Revise individual orders; check if products have arrived and adjust quantities if necessary. Distribute the order into individual shopping carts.",
  'desc_cashbox' => "Make cash deposits and withdrawals. At the start of the first shift the balance has to be reset. The amount of this account has to reflect the real money available.",
  'desc_stock' => "Add and/or control the stock of products.",
  'desc_print_orders' => "Print and download orders for next week. Orders need to be finalized, printed and download as zip file.",
  'nav_report_status' => "Statistics",
  'desc_stats' => "Download a summary info of the currrent shift including today's incidents, negative ufs, total spending balance and products with negative stock",
  'order_closed' => "The order is closed for this provider.",
  'head_ti_sales' => "Sales listing", 
  'not_yet_val' => "not yet validated",
  'val_by' => "Validated by",
  'purchase_details' => "Purchase details of cart #",
  'filter_uf' => "Filter by household",
  'purchase_uf' => "Purchase of HU",
  'quantity_short' => "Qu",
  'incl_iva' => "incl. VAT",
  'incl_revtax' => "incl. RevTax",
  'no_news_today' => "No new is good news: no incidents have been posted for today!",
  'nav_mng_iva' => "VAT types",
  'nav_mng_money' => "Money",
  'nav_mng_admin' => "Admin",
  'nav_mng_users' => "Users",
  'nav_mng_access_rights' => "Access rights",
  'msg_sel_account' => "Choose an account first, then filter the results!",
  'msg_err_nomovements' => "Sorry, there are no movements for the selected account and date. Try to widen the consulted time period with the filter button.",
  'active_changed_uf' => "Active state changed for HU",
  'msg_err_mentoruf' => "The mentor household must be different from the HU itself!",
  'msg_err_ufexists' => "The HU name already exists. Please choose another one!",
  'msg_err_form_init' => "Seems like the form for creating a new member did not initialize correctly. Reload the page and then try again...   ",
  'ti_mng_hu_members' => "Manage households and their members", 
  'list_ufs' => "List of households",
  'search_members' => "Member search",
  'member_pl' => "Members",
  'mng_members_uf' => "Manage members of household ",
  'uf_name' => "Name",
  'btn_new_member' => "New member",
  'ti_add_member' => "Add new member to HU",
  'custom_member_ref' => "Custom ref.",
  'theme' => "Theme",
  'member_id' => "Member id",
  'ti_mng_stock' => "Manage stock",
  'msg_err_no_stock' => "This provider seems to have no stock",
  'msg_err_qu' => "Quantity needs to be numeric and bigger than 0!",
  'msg_correct_stock' => "Adjusting stock this way should be the exception! New stock should always be ADDED. Are you sure you want to correct the stock for this product?",
  'btn_yes_corret' => "Yes, make correction!",
  'search_product' => "Search a product",
  'add_stock' => "Add stock",
  'click_to_edit' => "Click table cells to edit!",
  'no_results' => "The search produced no results.",
  'for' => "for", //as in order FOR Aurora
  'date_for_order' => "Delivery date",
  'finished_loading' => "Finished loading",
  'msg_err_unrevised' => "There are still unrevised items in this order. Please make sure all ordered products have arrived!",
  'btn_dis_anyway' => "Distribute anyway",
  'btn_remaining' => "Revise remaining",
  'msg_err_edit_order' => "This order is not finalized. You can only save the notes and references once the order has been sent off.",
  'order_open' => "Order is open",
  'finalize_now' => "Finalize now",
  'msg_err_order_filter' => "No orders matching the filter criteria.",
  'msg_finalize' => "You are about to finalize an order. This means that no further modifications are possible to this order. Are you sure you want to continue?",
  'msg_finalize_open' => "This order is still open. Finalizing it now implies that you will close it before the anounced deadline. Are you sue you want to continue?",
  'msg_wait_tbl' => "The table header is still being constructed. Depending on your internet connection this might take a little while. Try again in 5 seconds. ",
  'msg_err_invalid_id' => "No valid ID for order found! This order has not been sent off to the provider!!",
  'msg_revise_revised' => "The items of this order have already been revised and placed into people\'s carts for the indicated shop date. Revising them again will override the modifications already made and potentially interfere with people\'s own corrections. <br/><br/> Are you really sure you want to proceed anyway?! <br/><br/>Pressing OK will delete the items from the existing shopping carts and start the order-revision process again.",
  'wait_reset' => "Please wait while the order is being reset...",
  'msg_err_already_val' => "Some or all order items have already been validated! Sorry, but it is not possible to make any further changes!!",
  'print_several' => "There is more than one order currently selected. Do you want to print them all in one go?",
  'btn_yes_all' => "Yes, print all",
  'btn_just_one' => "No, just one",
  'ostat_revised' => "Revised",
  'ostat_finalized' => "Finalized",
  'set_ostat_arrived' => "Arrived!",
  'set_ostat_postpone' => "Postpone!",
  'set_ostat_cancel' => "Cancel!",
  'ostat_desc_sent' => "Order has been sent to provider",
  'ostat_desc_nochanges' => "Revised and distributed without changes",
  'ostat_desc_postponed' => "Order has been postponed",
  'ostat_desc_cancel' => "Order has been canceled",
  'ostat_desc_changes' => "Revised with some modifications",
  'ostat_desc_incomp' => "Order ignored. Insufficient data previous to v2.5",
  'set_ostat_desc_arrived' => "Most or all ordered items have arrived. Proceed to revise and distribute the products to shopping carts...",
  'set_ostat_desc_postpone' => "The order did not arrive for the ordered date but probably will in the upcoming weeks.",
  'set_ostat_desc_cancel' => "Ordered items will never arrive.",
  'msg_move_to_shop' => "The items have been successfully moved to the shopping carts of the corresponding date.",
  'msg_err_noselect' => "Nothing selected!",
  'ti_revise' => "Revise order",
  'btn_revise' => "Revise order",
  'ti_order_detail' => "Order detail for",
  'ti_mng_orders' => "Manage orders",
  'btn_distribute' => "Distribute!",
  'distribute_desc' => "Place order-items into shopping carts",
  'filter_orders' => "Filter orders",
  'btn_filter' => "Filter",
  'filter_acc_todays' => "Today's movements",
  'filter_recent' => "Recent ones",
  'filter_year' => "Last year",
  'filter_all' => "All",
  'filter_expected' => "Expected today",
  'filter_next_week' => "Next week",
  'filter_future' => "All future orders",
  'filter_month' => "Last month",
  'filter_postponed' => "Postponed",
  'with_sel' => "With selected...",
  'dwn_zip' => "Download as zip",
  'closes_days' => "Closes in days",
  'sent_off' => "Sent off to provider",
  'date_for_shop' => "Shop date",
  'order_total' => "Order total",
  'nie' => "NIE",
  'total_orginal_order' => "Original order",
  'total_after_revision' => "After revision",
  'delivery_ref' => "Delivery ref.",
  'payment_ref' => "Payment ref.",
  'arrived' => "Arrived", //as in order items have arrived. this is a table heading
  'msg_cur_status' => "The current order status is",
  'msg_change_status' => "Change the order status to one of the following options",
  'msg_confirm_move' => "Are you sure you want to make this order available for shopping? All corresponding products will be placed into the shopping cart for the following date:",
  'alter_date' => "Choose an alternative date",
  'msg_err_miss_info' => "It seems that this order was created with an older version of the platform which is incompatible with the current revision functionality. Sorry, but this order cannot be revised.",


//added 29.09

  'order_closes' => "Order closes", //as in: order closes 20 SEP 2012
  'left_ordering' => " left for ordering.", //as in 4 days left for ordering
  'ostat_closed' => "Order is closed",
  'ostat_desc_fin_send' => "Order has been finalized. Ref. number is: #",
  'msg_err_past' => "This is the past! <br/> Too late to change anything here.",
  'msg_err_is_deactive_p' => "This product has been deactivated. In order to set an orderable date, you have to activate it first by clicking its 'active' checkbox.",
  'msg_err_deactivate_p' => "You are about to deactivate a product. This means that all associated 'orderable' dates will be erased as well.<br/><br/>As an alternative you can deactivate selected dates by clicking the corresponding table cells.",
  'msg_err_closing_date' => "The closing date cannot be later than the order date!",
  'msg_err_sel_col' => "The selected column/date has no orderable products! You have to make at least one product orderable in order to be able to generate a date pattern.",
  'msg_err_closing' => "In order to modify the closing date, you need to make at least one product orderable.",
  'msg_err_deactivate_sent' => "The given product cannot be de/activated because the corresponding order has already been sent to the provider. No further changes are possible!",
  'view_opt' => "View options",
  'days_display' => "Number of dates at display",
  'plus_seven' => "Show +7 days",
  'minus_seven' => "Show -7 days",
  'btn_earlier' => "Earlier", //cómo más temprano
  'btn_later' => "Later", //más tarde... futuro

//la frase entera es: "activate the selected day and products for the next  1|2|3.... month(s) every week | second week | third week | fourth week.
  'pattern_intro' => "Activate the selected day and products for the next ",
  'pattern_scale' => "month(s) every ",
  'week' => "week",
  'second' => "second week",  //2nd 
  'third' => "third week",
  'fourth' => "fourth week",
  'msg_pattern' => "NOTE: This action will re-generate all dates and products from the selected day onwards!",
  'sel_closing_date' => "Select new closing date",
  'btn_mod_date' => "Modify closing date",
  'btn_repeat' => "Repeat pattern!",
  'btn_entire_row' => "Invert selection of entire row",
  'btn_deposit' => "Deposit",
  'btn_withdraw' => "Withdrawal",
  'deposit_desc' => "Make a cash deposit",
  'withdraw_desc' => "Withdraw cash from the cashbox",
  'btn_set_balance' => "Set balance",
  'set_bal_desc' => "Reset current balance at the start of the 1st shift.",
  'maintenance_account' => "Maintenance",
  'posted_by' => "Posted by", //Posted by
  'ostat_yet_received' => "not yet received",
  'ostat_is_complete' => "is complete",
  'ostat_postponed' => "postponed",
  'ostat_canceled' => "canceled",
  'ostat_changes' => "with changes",
  'filter_todays' => "Today's",
  'bill' => "Bill",
  'member' => "Member",
  'cif_nif' => "VAT Reg No", //CIF/NIF
  'bill_product_name' => "Item", //concepte en cat... 
  'bill_total' => "Total", //Total factura 
  'phone_pl' => "Phones",
  'net_amount' => "Net amount", //importe netto 
  'gross_amount' => "Gross amount", //importe brutto
  'add_pagebreak' => "Click to ADD here a page break while printing",
  'remove_pagebreak' => "Click to REMOVE this page break",

  'show_deactivated' => "Show deactivated products", 
  'nav_report_sales' => "Sales", 
  'nav_help' => "Help", 
  'withdraw_from' => "Withdraw from ",  //account
  'withdraw_to_bank' => "Withdraw cash for bank",
  'withdraw_uf' => "Withdraw from HU account",
  'withdraw_cuota' => "Withdraw member quota",
  'msg_err_noorder' => "No orders found for the selected time period!",
  'primer_torn' => "1st Shift",
  'segon_torn' => "2nd Shift",
  'dff_qty' => "Diff. quantity",
  'dff_price' => "Diff. price",
  'ti_mgn_stock_mov' => "Stock movements",
  'stock_acc_loss_ever' => "Overall accumulated loss",
  'closed' => "closed", 
  'preorder_item' => "This product forms part of an accumulative order",
  'do_preorder' => "De-/activate as preorder",
  'do_deactivate_prod' => "De-/activate entire product",
  'msg_make_preorder_p' => "You are about to set this product as *preorderable*. It will be part of an accumulative order which does not have any fixed order date (yet). People can order these items until a certain quantity has been reached and you close it. Are you sure you want to continue?",
  'btn_ok_go' => "Ok, go ahead!",
  'msg_pwd_emailed' => "The new password has been sent to the user",
  'msg_pwd_change' => "The new password is: ",
  'msg_err_emailed' => "The email sending failed!",
  'msg_order_emailed' => "The order has been emailed succesfully!",
  'msg_err_responsible_uf' => "No responsible user found for this provider",
  'msg_err_finalize' => "There was an error finalizing the order!",
  'msg_err_cart_sync' => "Your shopping cart is out of synch with the database because shop items have been modified by someone else in the meanwhile. This usually happens when orders are revised and distributed while you shop. In order to proceed you need to reload your cart. Products that have been added since you last saved you cart will be lost.",
  'msg_err_no_deposit' => "The last household did not make any deposit???!!!",
  'btn_load_cart' => "Continue with next cart",
  'btn_deposit_now' => "Make desposit now",
  'msg_err_stock_mv' => "Sorry, no stock corrections/adds found for this product!",

  'ti_report_shop_pv' => "Purchase total by provider",
  'filter_all_sales' => "Show all sales",
  'filter_exact' => "Exact dates",
  'total_4date' => "Total for date",
  'total_4provider' => "Overall total for provider",
  'sel_sales_dates' => "Show sales for provider for the given time period:",
  'sel_sales_dates_ti' => "Select time period", 

  'instant_repeat' => "Instant repeat",
  'msg_confirm_delordereditems' => "There are ordered items for this product/date. Are you absolutely sure you want to deactivate it? This will delete the ordered items from people's order-carts!",
  'msg_confirm_instantr' => "Do you want to repeat this action for the rest of the active dates?",
  'msg_err_delorerable' => "Items have been ordered for this product and date. It cannot be deactivated!", 
  'msg_pre2Order' => "Convert this preorder to a regular order. This will assign an order date, i.e. when the expected items will arrive.",

  'msg_err_modified_order' => "Orderable products have been deactivated for the current date while you were ordering. Some products that you already had ordered are no longer available and will disappear from your cart after it has been reloaded.",
  'btn_confirm_del' => "Delete anyway!!",
  'print_new_win' => "New window",
  'print_pdf' => "Download pdf",
  'msg_incident_emailed' => "The incident has been emailed succesfully!",
  'upcoming_orders' => "Upcoming orders",

  'msg_confirm_del_mem' => "Are you sure delete this user from the database?? This cannot be undone!",
  'btn_del' => "Delete",


  'btn_new_provider' => "New provider",
  'btn_new_product' => "Add product",
  'orderable' => "Orderable", //product type
  'msg_err_providershort' => "The provider cannot be empty and should be at least 2 characters long.",
  'msg_err_productshort' => "The product name cannot be empty and should be at least 2 characters long.",
  'msg_err_select_responsibleuf' => "Who is in charge? Please select a responsible household.",
  'msg_err_product_category' => "Please select a product category.",
  'msg_err_order_unit' => "Please select a order unit measure.",
  'msg_err_shop_unit' => "Please select a shop unit measure.",
  'click_row_edit' => "Click to edit!",
  'click_to_list' => "Click to list products!",
  'head_ti_provider' => "Manage provider & products", 
  'edit' => "Edit",
  'ti_create_provider' => "Create new provider",
  'ti_add_product' => "Add product",
  'order_min' => "Min. order amount",
  'msg_confirm_del_product' => "Are you sure you want to delete this product?", 
  'msg_err_del_product' => "This product cannot be deleted since other database entries depend on it. Error thrown: ",
  'msg_err_del_member' => "This user cannot be deleted because other database entries reference it. <br/> Error thrown: ",
  'msg_confirm_del_provider' => "Are you sure you want to delete this provider?",
  'msg_err_del_provider' => "This provider cannot be deleted. Try deleting its products first!",
  'price_net' => "Price netto",

  'custom_product_ref' => "Custom ID", 
  'btn_back_products' => "Edit products",
  'copy_column' => "Copy column",
  'paste_column' => "Paste",

  'search_provider' => "Search for provider",
  'msg_err_export' => "Error in exporting data",
  'export_uf' => "Export Members",
  'btn_export' => "Export",

  'ti_visualization' => "Visualization",
  'file_name' => "File name",
  'active_ufs' => "Only active HU's",
  'export_format' => "Export format",
  'google_account' => "Google account",
  'other_options' => "Other options",
  'export_publish' => "Make export file public at:",
  'export_options' => "Export options",
  'correct_stock' => "Correct stock",
  'btn_edit_stock' => "Edit stock",
  'consult_mov_stock' => "Consult movements",
  'add_stock_frase' => "Total stock = current stock of ", //complete frase is: total stock = current stock of X units + new stock
  'correct_stock_frase' => "Current stock is not",
  'stock_but' => "but", //current stock is not x units but...
  'stock_info' => "Note: you can consult all previous stock movements (adds, corrections, losses) by clicking on the product name below!",
  'stock_info_product' => "Note: consult all previous stock movements (adds, corrections and overall loss) from the Report &gt; Stock page.",


  'msg_confirm_prov' => "Are you sure you want to export all providers?", 
  'msg_err_upload' => "An error occured during uploading the file ", 
  'msg_import_matchcol' => "Need to match up database entries with table rows! Please assign the required matching column ", //+ here then comes the name of the matching column, e.g. custom_product_ref
  'msg_import_furthercol' => "Apart from the required column which table columns do you want to import?", 
  'msg_import_success' => "Import has been successful. Do you want to import another file?", 
  'btn_import_another' => "Import another", 
  'btn_nothx' => "No, thanks!", 
  'import_allowed' => "Allowed formats", //as in allowed file formats
  'import_file' => "Import file", 
  'public_url' => "Public URL",
  'btn_load_file' => "Load file",
  'msg_uploading' => "Uploading file and generating preview, please wait...!",
  'msg_parsing' => "Reading file from server and parsing, please wait...!",
  'import_step1' => "Choose a file",
  'import_step2' => "Preview data and match columns",
  'import_reqcol' => "Required column",
  'import_auto' => "Good news: most data (columns) could be recognized and you could try to automatically import the file. As a more secure alternative, preview the content first and match the table columns by hand.",
  'import_qnew' => "What should happen with data that does not exist in the database?",
  'import_createnew' => "Create new entries",
  'import_update' => "Just update existing rows",
  'btn_imp_direct' => "Import directly",
  'btn_import' => "Import",
  'btn_preview' => "Preview first", 
  'sel_matchcol' => "Match column...", 
  'ti_import_products' => "Import or update products for ", 
  'ti_import_providers' => "Import providers", 
  'head_ti_import' => "Import wizard",

  'withdraw_desc_banc' => "Withdraw money from account or make transfer for provider payment.",
  'deposit_desc_banc' => "Register all incoming money to consum account.",
  'deposit_banc' => "Deposit to consume account",
  'withdraw_banc' => "Withdraw from consume account",
  'deposit_sales_cash' => "Deposit sales cash",
  'ti_stock_report' => "Stock report for ", 
  'netto_stock' => "Netto stock value", 
  'brutto_stock' => "Brutto stock value", 
  'total_netto_stock' => "Total netto stock value", 
  'total_brutto_stock' => "Total brutto stock value", 
  'sales_total_pv' => "Sales total for provider ",
  'dates_breakdown' => "Dates break down", 
  'price_brutto' => "Price brutto", 
  'total_brutto' => "Brutto total",
  'total_netto' => "Netto total",
  'msg_err_oldPwdWrong' => "Sorry, but you got your old password wrong. Please try again. ", 
  'msg_err_adminStuff' => "Insufficient access priviledges. Only Admin can do that!",

  'msg_err_deactivate_prdrow' => "This product cannot be deactivated because it has ordered items for certain dates. Deactivate the product for those individual dates first!",
  'msg_err_deactivate_ir' => "You cannot deactivate several dates for this product since certain dates contain already ordered items. Either turn off Instant Repeat or deactivate the ordered products/date individually.",
  'msg_err_deactivate_product' => "There are open orders for this product. Deactivating it will remove these items from the corresponding order carts. Deleting order items cannot be undone.",

  'msg_activate_prod_ok' => "The product has been activated successfuly.", 
  'msg_deactivate_prod_ok' => "The product has been deactivated successfuly.", 
  'msg_activate_prov_ok' => "The provider has been activated successfuly.", 
  'msg_deactivate_prov_ok' => "The provider has been deactivated successfuly.", 
  'no_stock' => "Out of stock!!",
  'stock_mov_type' => "Movement type",

	     );
?>