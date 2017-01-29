<?php

require_once(__ROOT__. 'local_config/config.php');

// English translation file for aixada 

$Text['en'] = 'English';


$Text['charset'] = "utf-8";
$Text['text_dir'] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)


/** 
 *  		Global things
 */
$Text['coop_name'] = configuration_vars::get_instance()->coop_name;
$Text['currency_sign'] = configuration_vars::get_instance()->currency_sign;
$Text['currency_desc'] = "Euros"; 
$Text['please_select'] = "Please select ...";
$Text['loading'] = "Please wait while loading...";
$Text['search'] = "Search";
$Text['id'] = "id";
$Text['uf_short'] = "HU";
$Text['uf_long'] = "Household";



/** 
 *  		misc
 */
$Text['date_from'] = 'from';
$Text['date_to'] = 'to';
$Text['mon'] = 'Monday';
$Text['tue'] = 'Tuesday';
$Text['wed'] = 'Wednesday';
$Text['thu'] = 'Thursday';
$Text['fri'] = 'Friday';
$Text['sat'] = 'Saturday';
$Text['sun'] = 'Sunday';

/**
 * 				selects
 */
$Text['sel_provider'] = "Please select a provider...";
$Text['sel_product'] = "Please select a product...";
$Text['sel_user'] = "Please select a user...";
$Text['sel_category'] = "Please select a product category...";
$Text['sel_uf'] = "Please select household...";
$Text['sel_uf_or_account'] = "Please select household or account...";
$Text['sel_account'] = "Please select an account...";
$Text['sel_none'] = "none";

/**
 * 				tabs
 */
$Text['by_provider'] = "By Provider";
$Text['by_category'] = "By Product Category";
$Text['special_offer'] = "Pre-order";
$Text['search_add'] = "Search and add another item";
$Text['validate'] = "Validate";


/**
 *  			titles for header <title></title>
 */
$Text['global_title'] = configuration_vars::get_instance()->coop_name;
$Text['head_ti_validate'] = "Validate";

$Text['head_ti_active_products'] = "Activate/Deactivate orderable products";
//$Text['head_ti_arrived_products'] = "Products that have arrived";
$Text['head_ti_active_roles'] = "Active Roles";
$Text['head_ti_account'] = "Accounts";
$Text['head_ti_manage_orders'] = "Manage Orders";
//$Text['head_ti_manage_dates'] = "Set Order Dates";
$Text['head_ti_manage'] = "Manage";
$Text['head_ti_manage_uf'] = "Households/Members";
$Text['head_ti_incidents'] = "Incidents";
$Text['head_ti_stats'] = "Statistics of the Day";
$Text['head_ti_prev_orders'] = "My previous purchases"; 
$Text['head_ti_cashbox'] = "Money control"; 





/**
 *  			titles for main pages <h1></h1>
 */
$Text['ti_mng_activate_products'] = "De-/activate products for ordering";
$Text['ti_mng_activate_roles'] = "Manage roles of user ";
$Text['ti_mng_activate_users'] = "Activate users for ";
$Text['ti_mng_activate_preorders'] = "Convert preorder to order";
$Text['ti_mng_members'] = "Manage member";
$Text['ti_mng_db'] = "Backup database"; 
$Text['ti_order'] = "Place your order for ";
$Text['ti_shop'] = "Buy stuff ";
//$Text['ti_report_report'] = "Summary of orders for "; 
$Text['ti_report_account'] = "Report accounts "; 
//$Text['ti_report_my_account'] = "Report my account "; 
$Text['ti_report_preorder'] = "Summary of preorders"; 
$Text['ti_report_incidents'] = "Today's incidents";
$Text['ti_incidents'] = "Incidents";
$Text['ti_validate'] = "Validate cart for HU";
$Text['ti_stats'] = "Overall statistics";
$Text['ti_my_account'] = "My settings";
$Text['ti_my_account_money'] = "My money";
//$Text['ti_my_prev_sales'] = "My previous purchases";
$Text['ti_all_sales'] = "Overview sales";
$Text['ti_login_news'] = "Login and news";
$Text['ti_timeline'] = "Timeline Report";
$Text['ti_report_torn'] = "Summary of today's session";
//$Text['ti_mng_cashbox'] = "Cashbox";




/**
 * 				roles
 */
$Text['Consumer'] = 'Consumer';
$Text['Checkout'] = 'Checkout';
$Text['Consumer Commission'] = 'Consumer Commission';
$Text['Econo-Legal Commission'] = 'Econo-Legal Commission';
$Text['Logistic Commission'] = 'Logistic Commission';
$Text['Hacker Commission'] = 'Hacker Commission';
$Text['Fifth Column Commission'] = 'Fifth Column Commission';
$Text['Producer'] = 'Producer';


/**
 * 				Manage Products / Roles
 */
//$Text['mo_inact_prod'] = "Products they can't order";
//$Text['mo_act_prod'] = "Products they will be able to order:";
//$Text['mo_notarr_prod'] = "Products that haven't arrived:";
//$Text['mo_arr_prod'] = "Products that have arrived:";
$Text['mo_inact_role'] = "Inactive roles";
//$Text['mo_act_role'] = "Active roles";
$Text['mo_inact_user'] = "Inactive users";
$Text['mo_act_user'] = "Active users";
//$Text['msg_no_report'] = "No providers/products to report for the given date!";


/**
 * 				uf member manage
 */
$Text['search_memberuf'] = "Search name or login"; //changed !!!!!!!!!
$Text['browse_memberuf'] = "Browse";
$Text['assign_members'] = "Assign members";
$Text['login'] = "Login";
$Text['create_uf'] = "New HU";
$Text['name_person'] = "Name";
$Text['address'] = "Address";
$Text['zip'] = "Zip";
$Text['city'] = "City";
$Text['phone1'] = "Phone1";
$Text['phone2'] = "Phone2";
$Text['email'] = "Email";
$Text['web'] = "Web";
$Text['last_logon'] = "Last seen";
$Text['created'] = "Created on";
$Text['active'] = "Active";
$Text['participant'] = "Participant";
$Text['roles'] = "Roles";
$Text['active_roles'] = "Active roles";
$Text['products_cared_for'] = "Products responsible for";
$Text['providers_cared_for'] = "Providers responsible for";
$Text['notes'] = "Notes";
$Text['edit_uf'] = "Edit HU";
//$Text['members_uf'] = "Members of household";
$Text['mentor_uf'] = "Mentor HU";
$Text['unassigned_members'] = "Unassigned";
$Text['edit_my_settings'] = "Edit my settings";

$Text['nif'] = "VAT Reg No";
$Text['bank_name'] = "Bank";
$Text['bank_account'] = "Bank account";
$Text['picture']  = "Picture";
$Text['offset_order_close'] = "Processing time";
$Text['iva_percent_id'] = "IVA type";
$Text['percent'] = "Percent";
$Text['type'] = "Type";
$Text['treasury'] = "Treasury";
$Text['service'] = "Service";
$Text['adult'] = "Adult";


/**
 *  			wiz stuff
 */
$Text['deposit_cashbox'] = 'Deposit money in cashbox'; 
$Text['widthdraw_cashbox'] = 'Withdraw money from cashbox'; 
$Text['current_balance'] = 'Current balance';
$Text['deposit_type'] = 'Type of deposit';
$Text['deposit_by_uf'] = 'Deposit by HU';
$Text['deposit_other'] = 'All other stuff...';
$Text['make_deposit_4HU'] = 'Deposit by';
$Text['short_desc'] = 'Short description';
$Text['withdraw_type'] = 'Type of withdrawal';
$Text['withdraw_for_provider'] = 'Make a withdrawal for provider';
$Text['withdraw_other'] = 'All other withdrawals..';
$Text['withdraw_provider'] = 'Pay provider';
$Text['btn_make_withdrawal'] = 'Withdrawal';
$Text['correct_balance'] = 'Correct balance';
$Text['set_balance'] = 'Set current balance in cashbox';
$Text['name_cash_account'] = 'Cashbox';

// See `account_operations.config.php`
//     * Translated keys of $config_account_operations with the prefix 'mon_op_'
$Text['mon_op_deposit_uf'] = 'HU deposit';
$Text['mon_op_deposit_others'] = 'Deposit others';
$Text['mon_op_debit_uf'] = 'Debit to HU';
$Text['mon_op_pay_pr'] = 'Payment to Prv.';
$Text['mon_op_refund_uf'] = 'Refund to HU.';
$Text['mon_op_withdraw_others'] = 'Withdraw others';
$Text['mon_op_invoice_pr'] = 'Invoice from Prv.';
$Text['mon_op_move'] = 'Move';
$Text['mon_op_correction'] = 'Balance correction';
$Text['mon_op_a_debit_uf'] = 'Annul HU debit';
$Text['mon_op_a_pay_pr'] = 'Annul provider payment';
$Text['mon_op_a_invoice_pr'] = 'Annul provider invoice';
// See `account_operations.config.php`
//   * Translated sub-keys values from 'default_desc' and 'auto_desc'
//     on $config_account_operations with the prefix 'mon_desc_'
$Text['mon_desc_deposit_uf'] = 'Deposit from HU';
$Text['mon_desc_deposit_from_uf'] = 'Deposit from HU #{uf_from_id} {comment}';
$Text['mon_desc_payment'] = 'Payment to provider';
$Text['mon_desc_payment_to_provider'] = 'Payment to #{provider_to_id} {comment}';
$Text['mon_desc_refund_to_uf'] = 'Refund to HU: #{uf_to_id} {comment}';
$Text['mon_desc_invoice'] = 'Provider invoice.';
$Text['mon_desc_treasury_movement'] = 'Treasury movement.';
$Text['mon_desc_move_from'] = 'Move from #{account_from_id} {comment}';
$Text['mon_desc_move_to'] = 'Move to #{account_to_id} {comment}';
$Text['mon_desc_correction'] = 'Balance correction';
$Text['mon_desc_a_payment_to_provider'] = 'ANNULAR Payment to #{provider_to_id} {comment}';
$Text['mon_desc_a_payment'] = 'ANNULAR Payment to provider';
$Text['mon_desc_a_invoice'] = 'ANNULAR Provider invoice.';
// Used in manege_money.php and controllers
$Text['mon_ops_standard'] = 'Standard';
$Text['mon_ops_corrections'] = 'Corrections';
$Text['mon_send'] = 'Send';
$Text['mon_from'] = 'From';
$Text['mon_to'] = 'To';
$Text['mon_all_active_uf'] = '* All active HU *';
$Text['mon_success'] = 'Operation has been successful, {count} annotations!';
$Text['mon_war_no_all_hu'] = "{mon_all_active_uf} Can not be used in this operation.";
$Text['mon_war_decimals'] = "Amount must not have more than two decimals!";
$Text['mon_war_gt_zero'] = "Amount needs to be larger than zero!";
$Text['mon_war_accounts_not_set'] = "Required accounts are not set.";
$Text['mon_war_description'] = "Account movement warning: should write a short comment.";
$Text['mon_dailyTreasurySummary'] = "Treasury Summary of the day";
$Text['mon_balance'] = "Balance";
$Text['mon_amount'] = "Amount";
$Text['mon_dailyBalance'] = "Balance of the day";
$Text['mon_accountBalances'] = "Balance of accounts";
$Text['mon_uf_balances'] = "HU balances";
$Text['mon_provider_balances'] = "Provider balances";
$Text['mon_result'] = "Result";
$Text['mon_lastOper'] =  "Last operation";
$Text['mon_operation_account'] = "Make operations";
$Text['mon_list_account'] = "Consult the operations of an account";

/**
 *				validate
 */
$Text['set_date'] = "Set a date";
$Text['get_cart_4_uf'] = "Get cart for household";
$Text['make_deposit'] = "Deposit for household";
$Text['success_deposit'] = "Deposit has been saved!";
$Text['amount'] = "Amount";
$Text['comment'] = "Comment";
//$Text['deposit_other_uf'] = "Make deposit for another household or account";
$Text['latest_movements'] = "Latest movements";
$Text['time'] = "Time";
$Text['account'] = "Account";
$Text['consum_account'] = "Consum Account";
$Text['operator'] = "Operator";
$Text['balance'] = "Balance";
$Text['dailyStats'] = "Daily statistics";
$Text['totalIncome'] = "Total income";
$Text['totalSpending'] = "Total spending";
$Text['negativeUfs']= "Negative households";
$Text['lastUpdate']= "Last update";
$Text['negativeStock']="Products with negative stock";
$Text['curStock'] = "Current stock";
$Text['minStock'] = "Minimal stock";
$Text['stock'] = "Stock";



/**
 *              Shop and order
 */

$Text['info'] = "Info";
$Text['quantity'] = "Quantity";
$Text['unit'] = "Unit";
$Text['price'] = "Price";
$Text['name_item'] = "Name";
$Text['revtax_abbrev'] = "RevTax";
//$Text['cur_stock'] = "Current Stock";
$Text['date_for_shop'] = 'Date shopped';
$Text['ts_validated'] = 'Validated';

/**
 * 		Logon Screen
 */ 
$Text['welcome_logon'] = "Welcome to " . configuration_vars::get_instance()->coop_name . "!";
$Text['logon'] = "User";
$Text['pwd']	= "Password";
$Text['old_pwd'] = "Old Password";
$Text['retype_pwd']	= "Retype password";
$Text['lang']	= "Language";
$Text['msg_err_incorrectLogon'] = "Incorrect login";
$Text['msg_err_noUfAssignedYet'] = "You haven't been assigned an UF yet. Please get somebody to complete registering you.";


//$Text['msg_reg_success'] = "You have been successfully registered; the full activation of your user is pending. Have all the other members of your UF register, and get someone to finish the process. ";
//$Text['register'] = "Register";
$Text['required_fields'] = " are required fields";


/**
 *			Navigation
 */
$Text['nav_home'] = "Home";
$Text['nav_wiz'] = "Wizard";
//	$Text['nav_wiz_arrived'] = "Products that haven't arrived";
	$Text['nav_wiz_validate'] = "Validate";
//	$Text['nav_wiz_open'] = "Open";
//	$Text['nav_wiz_close'] = "Close";
	$Text['nav_wiz_torn'] = "Summary info";
	$Text['nav_wiz_cashbox'] = "Cashbox";
$Text['nav_shop'] = "Buy stuff";
$Text['nav_order'] = "Place order";
$Text['nav_mng'] = "Manage";
	//$Text['nav_mng_uf'] = "Households";
	$Text['nav_mng_member'] = "Members";
	$Text['nav_mng_providers'] = "Providers";
	$Text['nav_mng_products'] = "Products";
		$Text['nav_mng_deactivate'] = "De/Activate for orders";
		$Text['nav_mng_stock'] = "Stock";
		$Text['nav_mng_units'] = "Units";
	$Text['nav_mng_orders'] = "Orders";
		//$Text['nav_mng_setorderable'] = "Set orderable dates";
		//$Text['nav_mng_move'] = "Move order to new date";
		//$Text['nav_mng_orders_overview'] = "Manage orders";
		$Text['nav_mng_preorder'] = "Convert preorder to order";
	$Text['nav_mng_db'] = "Backup db";
	$Text['nav_mng_roles'] = "Roles";
$Text['nav_report'] = "Reports";
//$Text['nav_report_order'] = "Current order";
$Text['nav_report_account'] = "Accounts";
$Text['nav_report_timelines'] = "Timelines";
$Text['nav_report_timelines_uf'] = "Households";
$Text['nav_report_timelines_provider'] = "Providers";
$Text['nav_report_timelines_product'] = "Products";
$Text['nav_report_daystats'] = "Statistics of the day";
$Text['nav_report_preorder'] = "Preorders";
$Text['nav_report_incidents'] = "Today's incidents";
$Text['nav_report_shop_hu'] = "By Households";
$Text['nav_report_shop_pv'] = "By Providers";


$Text['nav_incidents'] = "Incidents";
	$Text['nav_browse'] = "Browse / add";
$Text['nav_myaccount'] = "My Account";
	$Text['nav_myaccount_settings'] = "Settings";
	$Text['nav_myaccount_account'] = "My money";
	$Text['nav_changepwd'] = "Change password"; 
	$Text['nav_prev_orders'] = "Previous purchases";

$Text['nav_logout'] = "Sign out";
$Text['nav_signedIn'] = "Signed in as ";
$Text['nav_can_checkout'] = "You may check out goods.";
$Text['nav_try_to_checkout'] = "Be checkout";
$Text['nav_stop_checkout'] = "Stop checkout";



/**
 *			Buttons
 */
$Text['btn_login'] = "Login";
$Text['btn_submit'] = "Submit";
$Text['btn_save'] = "Save";
$Text['btn_reset'] = "Reset";
$Text['btn_cancel'] = "Cancel";
$Text['btn_activate'] = "Activate";
$Text['btn_deactivate'] = "Deactivate";
//$Text['btn_arrived'] = "Has arrived";
//$Text['btn_notarrived'] = "Has not arrived";
//$Text['btn_move'] = "Move";
$Text['btn_ok'] = "Ok";
$Text['btn_assign'] = "Assign";
$Text['btn_create'] = "Create";
$Text['btn_close'] = "Close";
$Text['btn_make_deposit'] = "Deposit!";
$Text['btn_new_incident'] = "New Incident";
$Text['btn_reset_pwd'] = "Reset Password"; 
$Text['btn_view_cart'] = "Cart"; 
$Text['btn_view_cart_lng'] = "View cart only";
$Text['btn_view_list'] = "Products";
$Text['btn_view_list_lng'] = "View product list only";
$Text['btn_view_both'] = "Both";
$Text['btn_view_both_lng'] = "View both, product list and cart";
$Text['btn_repeat'] = "Ok, repeat this!";
$Text['btn_repeat_single'] = "No, just one"; 
$Text['btn_repeat_all'] = "Ok, apply to all"; 



/**
 * Incidents
 */
$Text['create_incident'] = "Create new incident";
$Text['overview'] = "Overview";
$Text['subject'] = "Subject";
$Text['message'] = "Message";
$Text['priority'] = "Priority";
$Text['status'] = "Status";
$Text['incident_type'] = "Type";
$Text['status_open'] = "Open";
$Text['status_closed'] = "Done!";
$Text['ufs_concerned'] = "HUs concerned";
$Text['provider_concerned'] = "For provider";
$Text['comi_concerned'] = "For commission";
$Text['created_by'] = "Created by";
$Text['edit_incident'] = "Edit incident";

/**
 *  Reports
 */
$Text['provider_name'] = "Provider";
$Text['product_name'] = "Product";
//$Text['qty'] = "Quantity";
$Text['total_qty'] = "Total Quantity";
$Text['total_price'] = "Total Price";
$Text['total_amount'] = "Total amount";
//$Text['select_order'] = "List orders for the following date:";
//$Text['move_success'] = "The listed order items are now active for: ";
//$Text['show_compact'] = "Change view: shift / provider";
//$Text['show_all_providers'] = "Toggle products";						//ADDED 8. JUNE
//$Text['show_all_print'] = "Toggle printout";							//ADDED 8. JUNE
$Text['nr_ufs'] = "Total HUs";
$Text['printout'] = "Print";
$Text['summarized_orders'] = "Summarized orders";
$Text['detailed_orders'] = "Detailed orders";


/**
 * 		Error / Warning Messages
 */
$Text['msg_err_incorrectLogon'] = "User or password didn't match! Please try again.";
$Text['msg_err_pwdctrl'] = "The passwords did not match. Please retype both!";
$Text['msg_err_usershort'] = "The username is too short. Should have min. of three characters";
$Text['msg_err_userexists'] = "The username already exists. Please choose another one.";
$Text['msg_err_passshort'] = "The password is too short. Should be between 4 and 15 characters long.";
$Text['msg_err_notempty'] = " field cannot be left empty!"; 
$Text['msg_err_namelength'] = "The name and family name cannot be left empty or contain more than 255 characters!"; 
$Text['msg_err_only_num'] = " field only allows numbers and cannot be empty!"; 
$Text['msg_err_email'] = "The email format is not correct. Should be name@domain.com or similar.";
//$Text['msg_err_select_uf'] = "In order to assign a new member to an HU you have to select an HU first by clicking on its name! If you need a new HU, create one first by clicking +New HU.";
//$Text['msg_err_select_non_member'] = "In order to assign a new member to an HU you have to select one from the non-member listing on your right!"; 
//$Text['msg_err_insufficient_stock'] = 'Insufficient stock for ';


$Text['msg_edit_success'] = "Everything saved successfully!"; //changed!!!!! 
//$Text['msg_edit_mysettings_success'] = "Your new settings have been saved successfully!";
$Text['msg_pwd_changed_success'] = "Your password has been changed successfully!"; 
$Text['msg_confirm_del'] = "Are you sure you want to remove this member from this household?"; //changed!!!!!
$Text['msg_enter_deposit_amount'] = "Deposit amount should only contain numbers and not be empty!";
$Text['msg_please_set_ufid_deposit'] = "The HU ID is not set. You either have to choose a cart or select an alternative HU to make a deposit!";
//$Text['msg_error_deposit'] = "An error has occurred while making the deposit. You can try again. Successful deposits should show in the account listing. <br/>The error message was: ";
$Text['msg_deposit_success'] = "Deposit has been successful!";
$Text['msg_withdrawal_success'] = "The withdrawal has been successful!";
$Text['msg_select_cart_first'] = "In order to add items for validating you have to select an HU/cart first!";
//$Text['msg_err_move_date'] = "An error has occurred while moving the order to the new date. Try again. ";
$Text['msg_no_active_products'] = "Sorry, but currently there are no products activated for ordering. Talk to the person in charge of provider(s)!";
//$Text['msg_no_movements'] = "Sorry, no movements for given account and date!"; 
$Text['msg_delete_incident'] = "Are you sure you want to delete this incident?";
//$Text['msg_err_selectFirstUF'] = "There is no household selected. Choose one first and then its purchases."; //ADDED JAN 2012

$Text['click_to_change'] = "Click to change!";
$Text['cart_date'] = "Cart date";
$Text['create_cart'] = "Crete Cart";

/**
 *  Product categories
 */
$Text['SET_ME'] 			= 'To complete...';

$Text['prdcat_vegies']		 	= "Vegetables";
$Text['prdcat_fruit'] 			= "Fresh fruits";
$Text['prdcat_mushrooms'] 		= "Mushrooms";
$Text['prdcat_dairy'] 			= "Milk and yoghurt"; 			//fresh milk, joguhrt
$Text['prdcat_meat'] 			= "Meat";							//fresh chicken, beef, lamp, etc.
$Text['prdcat_bakery'] 			= "Bakery and flour";						//bread, pastry, flour
$Text['prdcat_cheese'] 			= "Cheese";
$Text['prdcat_sausages'] 		= "Sausages";					//ham and stuff
$Text['prdcat_infant'] 			= "Infant nutrition";
$Text['prdcat_cereals_pasta']	= "Cereals and pasta";	//Cereals and pasta
$Text['prdcat_canned'] 			= "Canned stuff";
$Text['prdcat_cleaning'] 		= "Cleaning";					//house clearning, detergents, etc.
$Text['prdcat_body'] 			= "Body care";
$Text['prdcat_seasoning'] 		= "Seasoning and algea";
$Text['prdcat_sweets'] 			= "Honey and other sweets";		//mermelada, honey, sugar, chocolate
$Text['prdcat_drinks_alcohol'] 	= "Alcoholics";			//wine, beer, etc.
$Text['prdcat_drinks_soft'] 	= "Soft drinks";			//juice, vegetable drinks
$Text['prdcat_drinks_hot'] 		= "Coffee and tea";
$Text['prdcat_driedstuff'] 		= "Snacks and dried fruit";
$Text['prdcat_paper'] 			= "Cellulose and paper";		//hankerchiefs, toilet paper, kitchen paper, 
$Text['prdcat_health'] 			= "Health";		//hankerchiefs, toilet paper, kitchen paper, 
$Text['prdcat_misc']			= "Everything else..." ;





/**
 *  Field names in database
 */

$Text['name'] = 'Name';
$Text['contact'] = 'Contact';
$Text['fax'] = 'Fax';
$Text['responsible_mem_name'] = 'Responsible member';
$Text['responsible_uf'] = 'Responsible household';
$Text['provider'] = 'Provider';
$Text['description'] = 'Description';
$Text['barcode'] = 'Barcode';
$Text['orderable_type'] = 'Product type';
$Text['category'] = 'Category';
$Text['rev_tax_type'] = 'Revolutionary tax';
$Text['unit_price'] = 'Unit price';
$Text['iva_percent'] = 'VAT in percent';
$Text['unit_measure_order'] = 'Ordered in which units';
$Text['unit_measure_shop'] = 'Sold in which units';
$Text['stock_min'] = 'Alert when stock below';
$Text['stock_actual'] = 'Current amount in stock';
$Text['delta_stock'] = 'Difference to minimal stock';
$Text['description_url'] = 'URL of description';


/**
 * added after 14.5
 */
$Text['msg_err_validate_self'] = 'You cannot validate your own cart!';
//$Text['msg_err_preorder'] = 'Sorry, but in order to activate this preorder you have to choose a date in the future!';
//$Text['msg_preorder_success'] = 'Preorder has been successfully activated for the following date: ';
//$Text['msg_can_be_ordered'] =  'Items can be ordered for this date';
//$Text['msg_has_ordered_items'] = 'Items have been ordered for this day; they cannot be deleted, just moved';
//$Text['msg_today'] = 'Today';
//$Text['msg_default_day'] = 'Days without any orders yet';
//$Text['activate_for_date'] = 'Activate for ';
//$Text['start_date'] = "Show items starting from ";


//$Text['Download zip'] = 'Download zip file with all orders';
$Text['product_singular'] = 'product';
$Text['product_plural'] = 'products';
$Text['confirm_db_backup'] = 'Are you sure you want to backup the whole database? This may take a little while...';
$Text['show_date_field'] = 'Click here to show the calendar field and select a different date than today.';


//$Text['purchase_current'] = 'My purchase(s)';
//$Text['items_bought'] = "Past purchases";
//$Text['purchase_future'] = 'My order(s)';
//$Text['purchase_prev'] = 'Previous purchase(s)';
$Text['icon_order'] = 'Place your order';
$Text['icon_purchase'] = 'Buy items now';
$Text['icon_incidents'] = 'Post an incident';
$Text['purchase_date'] = 'Date of purchase';
//$Text['purchase_validated'] = 'Date of validation';
//$Text['ordered_for'] = 'Items ordered for'; //!!DUPLICATE
$Text['not_validated'] = 'not validated';






/* definitely new stuff */

$Text['download_db_zipped'] = 'Download Zipped Database';
$Text['backup'] = 'Ok, back up the database!';
$Text['filter_incidents'] = 'Filter incidents';
$Text['todays'] = "Today's";
$Text['recent_ones'] = 'Recent ones';
$Text['last_year'] = 'Last year';
$Text['details'] = 'Details';
$Text['actions'] = 'Actions';
$Text['incident_details'] = 'Incident details';
$Text['distribution_level'] = 'Distribution level';
$Text['internal_private'] = 'Internal (private)';
$Text['internal_email_private'] = 'Internal + email (private)';
$Text['internal_post'] = 'Internal + post to portal (public)';
$Text['internal_email_post'] = 'Internal + email + post (public)';

$Text['date'] = "Date";
$Text['iva'] = "VAT";
$Text['expected'] = 'Expected';
$Text['not_yet_sent'] = 'Not yet sent';
$Text['ordered_for'] = 'Ordered for';
$Text['my_orders'] = 'My Order(s)';
$Text['my_purchases'] = 'My Purchase(s)';
$Text['loading_status_info'] = 'Loading status info...';
$Text['previous'] = 'Previous';
$Text['next'] = 'Next';
$Text['date_of_purchase'] = 'Date of purchase';
$Text['validated'] = 'Validated';
$Text['total'] = 'Total';
$Text['ordered'] = 'Ordered';
$Text['delivered'] = 'Delivered';
$Text['price'] = 'Price';
$Text['qu'] = 'Qu';
$Text['msg_err_deactivatedUser'] = "Your user account has been deactivated!";
$Text['order'] = 'Order';
$Text['order_pl'] = 'Orders';
$Text['msg_already_validated'] = 'The selected cart has already been validated. Do you want to see its products/items?';
$Text['validated_at'] = "Validated at "; //refers to a date/hour


$Text['nothing_to_val'] = "Nothing to validate for HU";
$Text['cart_id'] = "Cart id";
$Text['msg_several_carts'] = "The selected household has more than one cart pending for validation. Please select one:";
$Text['transfer_type'] = "Type";
$Text['todays_carts'] = "Today's carts";
$Text['week_carts'] = "Week carts";
$Text['head_ti_torn'] = "Working shift overview"; 
$Text['btn_validate'] = "Validate";
$Text['desc_validate'] = "Validate past and present carts for households. Make money deposits.";
$Text['nav_wiz_revise_order'] = "Revise";
$Text['desc_revise'] = "Revise individual orders; check if products have arrived and adjust quantities if necessary. Distribute the order into individual shopping carts.";
$Text['desc_cashbox'] = "Make cash deposits and withdrawals. At the start of the first shift the balance has to be reset. The amount of this account has to reflect the real money available.";
$Text['desc_stock'] = "Add and/or control the stock of products.";
$Text['desc_print_orders'] = "Print and download orders for next week. Orders need to be finalized, printed and download as zip file.";
$Text['nav_report_status'] = "Statistics";
$Text['desc_stats'] = "Download a summery info of the current shift including today's incidents, negative ufs, total spending balance and products with negative stock";
$Text['order_closed'] = "The order is closed for this provider.";
$Text['head_ti_sales'] = "Sales listing"; 
$Text['not_yet_val'] = "not yet validated";
$Text['val_by'] = "Validated by";
$Text['purchase_details'] = "Purchase details of cart #";
$Text['filter_uf'] = "Filter by household";
$Text['purchase_uf'] = "Purchase of HU";
$Text['quantity_short'] = "Qu";
$Text['incl_iva'] = "incl. VAT";
$Text['incl_revtax'] = "incl. RevTax";
$Text['no_news_today'] = "No new is good news: no incidents have been posted for today!";
$Text['nav_mng_iva'] = "VAT types";
$Text['nav_mng_revtax'] = "Rev. Tax";
$Text['nav_mng_accdec'] = "Accounts";
$Text['nav_mng_paymeth'] = "Type of deposit/payment";
$Text['nav_mng_movtype'] = "Stock type";
$Text['nav_mng_money'] = "Money";
$Text['nav_mng_admin'] = "Admin";
$Text['nav_mng_users'] = "Users";
$Text['nav_mng_access_rights'] = "Access rights";
$Text['nav_mng_aux'] = "Auxiliary Maintenance";
$Text['dataman_consult'] = "Consult \"{data}\"";
$Text['dataman_edit'] = "Maintenance of \"{data}\"";
$Text['dataman_err_related'] = "There are data related to \"{related}\"";


$Text['msg_sel_account'] = "Choose an account first, then filter the results!";
$Text['msg_err_nomovements'] = "Sorry, there are no movements for the selected account and date. Try to widen the consulted time period with the filter button.";
$Text['active_changed_uf'] = "Active state changed for HU";
$Text['msg_err_mentoruf'] = "The mentor household must be different from the HU itself!";
$Text['msg_err_ufexists'] = "The HU name already exists. Please choose another one!";
$Text['msg_err_form_init'] = "Seems like the form for creating a new member did not initialize correctly. Reload the page and then try again...   ";
$Text['ti_mng_hu_members'] = "Manage households and their members"; 
$Text['list_ufs'] = "List of households";
$Text['search_members'] = "Member search";
$Text['member_pl'] = "Members";
$Text['mng_members_uf'] = "Manage members of household ";
$Text['uf_name'] = "Name";
$Text['btn_new_member'] = "New member";
$Text['ti_add_member'] = "Add new member to HU";
$Text['custom_member_ref'] = "Custom ref.";
$Text['theme'] = "Theme";
$Text['member_id'] = "Member id";
$Text['ti_mng_stock'] = "Manage stock";
$Text['msg_err_no_stock'] = "This provider seems to have no stock";
$Text['msg_err_qu'] = "Quantity needs to be numeric and bigger than 0!";
$Text['msg_correct_stock'] = "Adjusting stock this way should be the exception! New stock should always be ADDED. Are you sure you want to correct the stock for this product?";
$Text['btn_yes_corret'] = "Yes, make correction!";
$Text['search_product'] = "Search a product";
$Text['add_stock'] = "Add stock";
$Text['click_to_edit'] = "Click cell to edit!";
$Text['no_results'] = "The search produced no results.";
$Text['for'] = "for"; //as in order FOR Aurora
$Text['orderToFor'] = "Order {id} to \"{provider}\" for {date}";
$Text['date_for_order'] = "Delivery date";
$Text['finished_loading'] = "Finished loading";
$Text['msg_err_unrevised'] = "There are still unrevised items in this order. Please make sure all ordered products have arrived!";
$Text['btn_dis_anyway'] = "Distribute anyway";
$Text['btn_remaining'] = "Revise remaining";
$Text['btn_disValitate'] = "Distribute and validate";
$Text['msg_con_disValitate'] =
    "Distribute and validate can not cancel!!:<ul>
    <li>the products will be put as purchases of UF on the date of the order</li>
    <li>and the amount of the purchases will be put as a debt in the accounts of each HU</li>
    </ul>";
$Text['msg_con_disValitate_prvInv'] =
    "Distribute and validate can not cancel!!:<ul>
    <li>the products will be put as purchases of UF on the date of the order,</li>
    <li>the amount of the purchases will be put as a debt in the accounts of each HU</li>
    <li>and the total amount will be put as invoice to the provider account.</li>
    </ul>";
$Text['msg_err_disValitate'] = "Error when distribute and validate order #";
$Text['msg_err_disVal_nonEmpyCatrs'] = 
    // Used to throw exception, multiple text lines causes a PHP warning "Header may not contain more than a single header..."
    "There validations pending for {date_for_shop}.<br>Is not possible \"Distribute and validate\" for the same date if there are outstanding validations!";
$Text['btn_disValitate_ok'] = "Understood: distributes and validates!";
$Text['btn_bakToRevise'] = "Not yet: I want to continue reviewing";
$Text['btn_disValitate_done'] = "Right!<br>Order #{order_id} has been distributed and validated.";
$Text['wait_work'] = "Please wait while the work is done...";
$Text['msg_err_edit_order'] = "This order is not finalized. You can only save the notes and references once the order has been sent off.";
$Text['order_open'] = "Order is open";
$Text['finalize_now'] = "Finalize now";
$Text['msg_err_order_filter'] = "No orders matching the filter criteria.";
$Text['msg_finalize'] = "You are about to finalize an order. This means that no further modifications are possible to this order. Are you sure you want to continue?";
$Text['msg_finalize_open'] = "This order is still open. Finalizing it now implies that you will close it before the announced deadline. Are you sue you want to continue?";
$Text['msg_wait_tbl'] = "The table header is still being constructed. Depending on your internet connection this might take a little while. Try again in 5 seconds. ";
$Text['msg_err_invalid_id'] = "No valid ID for order found! This order has not been sent off to the provider!!";
$Text['msg_revise_revised'] =
     "The items of this order have already been revised and placed into people\'s carts.<br>
     Revising again will override modifications already made and potentially interfere with people\'s own corrections.<br>
     Possible options are <b>modify</ b> the revision made or <b>delete</ b> it and start again.";
$Text['btn_modify'] = "Modify";
$Text['btn_delete'] = "Delete";
$Text['wait_reset'] = "Please wait while the order is being reset...";
$Text['msg_done'] = "Done!";
$Text['msg_err_already_val'] = "Some or all order items have already been validated! Sorry, but it is not possible to make any further changes!!";
$Text['print_several'] = "There is more than one order currently selected. Do you want to print them all in one go?";
$Text['btn_yes_all'] = "Yes, print all";
$Text['btn_just_one'] = "No, just one";
$Text['ostat_revised'] = "Revised";
$Text['ostat_finalized'] = "Finalized";
$Text['set_ostat_arrived'] = "Arrived!";
$Text['set_ostat_postpone'] = "Postpone!";
$Text['set_ostat_cancel'] = "Cancel!";
$Text['ostat_desc_sent'] = "Order has been sent to provider";
$Text['ostat_desc_nochanges'] = "Revised and distributed without changes";
$Text['ostat_desc_postponed'] = "Order has been postponed";
$Text['ostat_desc_cancel'] = "Order has been cancelled";
$Text['ostat_desc_changes'] = "Revised with some modifications";
$Text['ostat_desc_incomp'] = "Order ignored. Insufficient data previous to v2.5";
$Text['set_ostat_desc_arrived'] = "Most or all ordered items have arrived. Proceed to revise and distribute the products to shopping carts...";
$Text['set_ostat_desc_postpone'] = "The order did not arrive for the ordered date but probably will in the upcoming weeks.";
$Text['set_ostat_desc_cancel'] = "Ordered items will never arrive.";
$Text['msg_move_to_shop'] = "The items have been successfully moved to the shopping carts of the corresponding date.";
$Text['msg_err_noselect'] = "Nothing selected!";
$Text['ti_revise'] = "Revise order";
$Text['btn_revise'] = "Revise order";
$Text['ti_order_detail'] = "Order detail for";
$Text['ti_mng_orders'] = "Manage orders";
$Text['btn_distribute'] = "Distribute!";
$Text['distribute_desc'] = "Place order-items into shopping carts";
$Text['filter_orders'] = "Filter orders";
$Text['btn_filter'] = "Filter";
$Text['filter_acc_todays'] = "Today's movements";
$Text['filter_recent'] = "Recent ones";
$Text['filter_year'] = "Last year";
$Text['filter_all'] = "All";
$Text['filter_expected'] = "Expected today";
$Text['filter_next_week'] = "Next week";
$Text['filter_future'] = "All future orders";
$Text['filter_month'] = "Last month";
$Text['filter_postponed'] = "Postponed";
$Text['with_sel'] = "With selected...";
$Text['dwn_zip'] = "Download as zip";
$Text['closes_days'] = "Closes in days";
$Text['sent_off'] = "Sent off to provider";
$Text['date_for_shop'] = "Shop date";
$Text['order_total'] = "Order total";
$Text['nie'] = "NIE";
$Text['total_orginal_order'] = "Original order";
$Text['total_after_revision'] = "After revision";
$Text['delivery_ref'] = "Delivery ref.";
$Text['payment_ref'] = "Payment ref.";
$Text['arrived'] = "Arrived"; //as in order items have arrived. this is a table heading
$Text['tit_set_orStatus'] = "Set Order Status";
$Text['tit_set_shpDate'] = "Set shopping date";
$Text['msg_cur_status'] = "The current order status is";
$Text['msg_change_status'] = "Change the order status to";
$Text['msg_confirm_move'] = "Are you sure you want to make this order available for shopping? All corresponding products will be placed into the shopping cart for the following date:";
$Text['alter_date'] = "Choose an alternative date";
$Text['msg_err_miss_info'] = "It seems that this order was created with an older version of the platform which is incompatible with the current revision functionality. Sorry, but this order cannot be revised.";
$Text['title_addToOrder'] = "Add item to the Order";
$Text['btn_addToOrder'] = "Add item";


//added 29.09

$Text['order_closes'] = "Order closes"; //as in: order closes 20 SEP 2012
$Text['left_ordering'] = " left for ordering."; //as in 4 days left for ordering
$Text['ostat_closed'] = "Order is closed";
$Text['ostat_desc_fin_send'] = "Order has been finalized. Ref. number is: #";
$Text['msg_err_past'] = "This is the past! <br/> Too late to change anything here.";
$Text['msg_err_is_deactive_p'] = "This product has been deactivated. In order to set an orderable date, you have to activate it first by clicking its 'active' checkbox.";
$Text['msg_err_deactivate_p'] = "You are about to deactivate a product. This means that all associated 'orderable' dates will be erased as well.<br/><br/>As an alternative you can deactivate selected dates by clicking the corresponding table cells.";
$Text['msg_err_closing_date'] = "The closing date cannot be later than the order date!";
$Text['msg_err_sel_col'] = "The selected column/date has no orderable products! You have to make at least one product orderable in order to be able to generate a date pattern.";
$Text['msg_err_closing'] = "In order to modify the closing date, you need to make at least one product orderable.";
$Text['msg_err_deactivate_sent'] = "The given product cannot be de/activated because the corresponding order has already been sent to the provider. No further changes are possible!";
$Text['view_opt'] = "View options";
$Text['days_display'] = "Number of dates at display";
$Text['plus_seven'] = "Show +7 days";
$Text['minus_seven'] = "Show -7 days";
$Text['btn_earlier'] = "Earlier"; //cómo más temprano
$Text['btn_later'] = "Later"; //más tarde... futuro

//la frase entera es: "activate the selected day and products for the next  1|2|3.... month(s) every week | second week | third week | fourth week.
$Text['pattern_intro'] = "Activate the selected day and products for the next ";
$Text['pattern_scale'] = "month(s) every ";
$Text['week'] = "week";
$Text['second'] = "second week";  //2nd 
$Text['third'] = "third week";
$Text['fourth'] = "fourth week";
$Text['msg_pattern'] = "NOTE: This action will re-generate all dates and products from the selected day onwards!";
$Text['sel_closing_date'] = "Select new closing date";
$Text['btn_mod_date'] = "Modify closing date";
$Text['btn_repeat'] = "Repeat pattern!";
$Text['btn_entire_row'] = "Invert selection of entire column";
$Text['btn_deposit'] = "Deposit";
$Text['btn_withdraw'] = "Withdrawal";
$Text['deposit_desc'] = "Make a cash deposit";
$Text['withdraw_desc'] = "Withdraw cash from the cashbox";
$Text['btn_set_balance'] = "Set balance";
$Text['set_bal_desc'] = "Reset current balance at the start of the 1st shift.";
$Text['maintenance_account'] = "Maintenance";
$Text['posted_by'] = "Posted by"; //Posted by
$Text['ostat_yet_received'] = "not yet received";
$Text['ostat_is_complete'] = "is complete";
$Text['ostat_postponed'] = "postponed";
$Text['ostat_canceled'] = "cancelled";
$Text['ostat_changes'] = "with changes";
$Text['filter_todays'] = "Today's";
$Text['bill'] = "Bill";
$Text['member'] = "Member";
$Text['cif_nif'] = "VAT Reg No"; //CIF/NIF
$Text['bill_product_name'] = "Item"; //concepte en cat... 
$Text['bill_total'] = "Total"; //Total factura 
$Text['phone_pl'] = "Phones";
$Text['net_amount'] = "Net amount"; //importe netto 
$Text['gross_amount'] = "Gross amount"; //importe brutto

$Text['cost_amount'] = "Cost"; //importe sin impuestos 
$Text['final_amount'] = "HU Final amount"; //importe final a las UF
$Text['cost_amount_desc'] = "\"Cost\" = Amount according to provider price before taxes.";
$Text['final_amount_desc'] = "\"HU Final amount\" = Amount with VAT and Rev. taxes.";

$Text['prvOrd_default'] = "(depending on configuration)";
$Text['prvOrdF_formatDesc'] = "Send Orders";
$Text['prvOrdF_prod'] = "List of products";
$Text['prvOrdF_matrix'] = "Grid products-HU";
$Text['prvOrdF_prod_matrix'] = "List of products + Grid";
$Text['prvOrdF_prodUf'] = "List of products with HU detail";
$Text['prvOrdF_prod_prodUf'] = "List of products + List with HU detail";
$Text['prvOrdF_ufProd'] = "List of HU with detail products";
$Text['prvOrdF_none'] = "Do not send";
$Text['prvOrdF_GroupByUf'] = "Group orders by UF";
$Text['prvOrdP_pricesDesc'] = "Order amounts to send";
$Text['prvOrdP_cost_amount'] = "Cost Amount (without tax)";
$Text['prvOrdP_cost_price'] = "Price and cost amount (without tax)";
$Text['prvOrdP_final_amount'] = "Final amount UF (VAT + imp.Rev.)";
$Text['prvOrdP_final_price'] = "Price and final amount UF (VAT + imp.Rev.)";
$Text['prvOrdP_no_amount'] = "No amounts";
$Text['order_printOpt_dialog'] = "Options to print orders";
$Text['order_printOpt_header'] = "Print head";
$Text['order_printOpt_format'] = "Format";
$Text['order_printOpt_prices'] = 'Amount';
$Text['order_printOpt_default'] = "(depending on the provider)";

$Text['add_pagebreak'] = "Click to ADD here a page break while printing";
$Text['remove_pagebreak'] = "Click to REMOVE this page break";

$Text['show_deactivated'] = "Show deactivated products"; 
$Text['nav_report_sales'] = "Sales"; 
$Text['nav_help'] = "Help"; 
$Text['withdraw_from'] = "Withdraw from ";  //account
$Text['withdraw_to_bank'] = "Withdraw cash for bank";
$Text['withdraw_uf'] = "Withdraw from HU account";
$Text['withdraw_cuota'] = "Withdraw member quota";
$Text['msg_err_noorder'] = "No orders found for the selected time period!";
$Text['primer_torn'] = "1st Shift";
$Text['segon_torn'] = "2nd Shift";
$Text['dff_qty'] = "Diff. quantity";
$Text['dff_price'] = "Diff. price";
$Text['ti_mgn_stock_mov'] = "Stock movements";
$Text['stock_acc_loss_ever'] = "Overall accumulated loss";
$Text['closed'] = "closed"; 
$Text['preorder_item'] = "This product forms part of an accumulative order";
$Text['do_preorder'] = "De-/activate as preorder";
$Text['do_deactivate_prod'] = "De-/activate entire product";
$Text['msg_make_preorder_p'] = "You are about to set this product as *preorderable*. It will be part of an accumulative order which does not have any fixed order date (yet). People can order these items until a certain quantity has been reached and you close it. Are you sure you want to continue?";
$Text['btn_ok_go'] = "Ok, go ahead!";
$Text['msg_pwd_emailed'] = "The new password has been sent to the user";
$Text['msg_pwd_email_reset'] = 'Your password has been reset.';
$Text['msg_pwd_email_logon'] = 'Please login as {user} with the new password.';
$Text['msg_pwd_email_change'] = 'Under: {menu} you can change your password.';
$Text['msg_pwd_change'] = "The new password is: ";
$Text['msg_err_emailed'] = "The email sending failed!";
$Text['msg_order_emailed'] = "The order has been emailed successfully!";
$Text['msg_err_responsible_uf'] = "No responsible user found for this provider";
$Text['msg_err_finalize'] = "There was an error finalizing the order!";
$Text['msg_err_cart_sync'] = "Your shopping cart is out of synch with the database because shop items have been modified by someone else in the meanwhile. This usually happens when orders are revised and distributed while you shop. In order to proceed you need to reload your cart. Products that have been added since you last saved you cart will be lost.";
$Text['msg_err_no_deposit'] = "The last household did not make any deposit???!!!";
$Text['btn_load_cart'] = "Continue with next cart";
$Text['btn_deposit_now'] = "Make deposit now";
$Text['msg_err_stock_mv'] = "Sorry, no stock corrections/adds found for this product!";

$Text['ti_report_shop_pv'] = "Purchase total by provider";
$Text['filter_all_sales'] = "Show all sales";
$Text['filter_exact'] = "Exact dates";
$Text['total_4date'] = "Total for date";
$Text['total_4provider'] = "Overall total for provider";
$Text['sel_sales_dates'] = "Show sales for provider for the given time period:";
$Text['sel_sales_dates_ti'] = "Select time period"; 

$Text['instant_repeat'] = "Instant repeat";
$Text['msg_confirm_delordereditems'] = "There are ordered items for this product/date. Are you absolutely sure you want to deactivate it? This will delete the ordered items from people's order-carts!";
$Text['msg_confirm_instantr'] = "Do you want to repeat this action for the rest of the active dates?";
$Text['msg_err_delorerable'] = "Items have been ordered for this product and date. It cannot be deactivated!"; 
$Text['msg_pre2Order'] = "Convert this preorder to a regular order. This will assign an order date, i.e. when the expected items will arrive.";

$Text['msg_err_modified_order'] = "Orderable products have been deactivated for the current date while you were ordering. Some products that you already had ordered are no longer available and will disappear from your cart after it has been reloaded.";
$Text['msg_err_modif_order_closed'] = "Attempt to modify an order closed.";
$Text['msg_err_cart_reloaded'] = "Your cart will be reloaded.";
$Text['btn_confirm_del'] = "Delete anyway!!";
$Text['print_new_win'] = "New window";
$Text['print_pdf'] = "Download pdf";
$Text['msg_incident_emailed'] = "The incident has been emailed successfully!";
$Text['upcoming_orders'] = "Upcoming orders";

$Text['msg_confirm_del_mem'] = "Are you sure delete this user from the database?? This cannot be undone!";
$Text['btn_del'] = "Delete";


$Text['btn_new_provider'] = "New provider";
$Text['btn_new_product'] = "Add product";
$Text['orderable'] = "Orderable"; //product type
$Text['order_notes'] = "(order comments)"; //order comments
$Text['msg_err_providershort']  = "The provider cannot be empty and should be at least 2 characters long.";
$Text['msg_err_productshort']  = "The product name cannot be empty and should be at least 2 characters long.";
$Text['msg_err_select_responsibleuf'] = "Who is in charge? Please select a responsible household.";
$Text['msg_err_product_category'] = "Please select a product category.";
$Text['msg_err_order_unit'] = "Please select a order unit measure.";
$Text['msg_err_shop_unit'] = "Please select a shop unit measure.";
$Text['click_row_edit'] = "Click to edit!";
$Text['click_to_list'] = "Click to list products!";
$Text['head_ti_provider'] = "Manage provider & products"; 
$Text['edit'] = "Edit";
$Text['ti_create_provider'] = "Create new provider";
$Text['ti_add_product'] = "Add product";
$Text['order_min'] = "Min. order amount";
$Text['msg_confirm_del_product'] = "Are you sure you want to delete this product?"; 
$Text['msg_err_del_product'] = "This product cannot be deleted since other database entries depend on it. Error thrown: ";
$Text['msg_err_del_member'] = "This user cannot be deleted because other database entries reference it. <br/> Error thrown: ";
$Text['msg_confirm_del_provider'] = "Are you sure you want to delete this provider?";
$Text['msg_err_del_provider'] = "This provider cannot be deleted. Try deleting its products first!";
$Text['price_net'] = "Price netto";

$Text['custom_product_ref'] = "Custom ID"; 
$Text['btn_back_products'] = "Edit products";
$Text['copy_column'] = "Copy column";
$Text['paste_column'] = "Paste";

$Text['search_provider'] = "Search for provider";
$Text['msg_err_export'] = "Error in exporting data";
$Text['export_uf'] = "Export Members";
$Text['btn_export'] = "Export";

$Text['ti_visualization'] = "Visualization";
$Text['file_name'] = "File name";
$Text['active_ufs'] = "Only active HU's";
$Text['export_format'] = "Export format";
$Text['google_account'] = "Google account";
$Text['other_options'] = "Other options";
$Text['export_publish'] = "Make export file public at:";
$Text['export_options'] = "Export options";
$Text['correct_stock'] = "Correct stock";
$Text['btn_edit_stock'] = "Edit stock";
$Text['consult_mov_stock'] = "Consult movements";
$Text['add_stock_frase'] = "Total stock = current stock of "; //complete frase is: total stock = current stock of X units + new stock
$Text['correct_stock_frase'] = "Current stock is not";
$Text['stock_but'] = "but"; //current stock is not x units but...
$Text['stock_info'] = "Note: you can consult all previous stock movements (adds, corrections, losses) by clicking on the product name below!";
$Text['stock_info_product'] = "Note: consult all previous stock movements (adds, corrections and overall loss) from the Report &gt; Stock page.";


$Text['msg_success'] = "Completed successfully";
$Text['msg_confirm'] = "Confirm";
$Text['msg_warning'] = "Warning";
$Text['msg_confirm_prov'] = "Are you sure you want to export all providers?"; 
$Text['msg_err_upload'] = "An error occurred during uploading the file "; 
$Text['msg_import_matchcol'] = "Need to match up database entries with table rows! Please assign the required matching column "; //+ here then comes the name of the matching column, e.g. custom_product_ref
$Text['msg_import_furthercol'] = "Apart from the required column which table columns do you want to import?"; 
$Text['msg_import_done'] = '{$rows} rows have been imported.'; 
$Text['msg_import_another'] = "Do you want to import another file?"; 
$Text['btn_import_another'] = "Import another"; 
$Text['btn_nothx'] = "No, thanks!"; 
$Text['direct_import_template'] = "Direct import template";
$Text['import_allowed'] = "Allowed formats"; //as in allowed file formats
$Text['import_file'] = "Import file"; 
$Text['public_url'] = "Public URL";
$Text['btn_load_file'] = "Load file";
$Text['msg_uploading'] = "Uploading file and generating preview, please wait...!";
$Text['msg_parsing'] = "Reading file from server and parsing, please wait...!";
$Text['import_step1'] = "Choose a file";
$Text['import_step2'] = "Preview data and match columns";
$Text['import_reqcol'] = "Required column";
$Text['import_ignore_rows'] = '(rows without "{$match_field}" are ignored)';
$Text['import_ignore_value'] = '(value of column "{$match_field}" is ignored)';
$Text['import_auto'] = "Good news: most data (columns) could be recognized and you could try to automatically import the file. As a more secure alternative, preview the content first and match the table columns by hand.";
$Text['import_qnew'] = "What should happen with data that does not exist in the database?";
$Text['import_create_update'] = "Create new entries and update existing rows";
$Text['import_createnew'] = "Create new entries";
$Text['import_update'] = "Just update existing rows";
$Text['btn_imp_direct'] = "Import directly";
$Text['btn_import'] = "Import";
$Text['btn_preview'] = "Preview first"; 
$Text['sel_matchcol'] = "Match column..."; 
$Text['ti_import_products'] = "Import or update products for "; 
$Text['ti_import_providers'] = "Import providers"; 
$Text['head_ti_import'] = "Import wizard";

$Text['withdraw_desc_banc'] = "Withdraw money from account or make transfer for provider payment.";
$Text['deposit_desc_banc'] = "Register all incoming money to consum account.";
$Text['deposit_banc'] = "Deposit to consume account";
$Text['withdraw_banc'] = "Withdraw from consume account";
$Text['deposit_sales_cash'] = "Deposit sales cash";
$Text['ti_stock_report'] = "Stock report for "; 
$Text['netto_stock'] = "Netto stock value"; 
$Text['brutto_stock'] = "Brutto stock value"; 
$Text['total_netto_stock'] = "Total netto stock value"; 
$Text['total_brutto_stock'] = "Total brutto stock value"; 
$Text['sales_total_pv'] = "Sales total for provider ";
$Text['dates_breakdown'] = "Dates break down"; 
$Text['price_brutto'] = "Price brutto"; 
$Text['total_brutto'] = "Brutto total";
$Text['total_netto'] = "Netto total";
$Text['msg_err_oldPwdWrong'] = "Sorry, but you got your old password wrong. Please try again. "; 
$Text['msg_err_adminStuff'] = "Insufficient access privileges. Only Admin can do that!";
$Text['set_c_balance'] = "Set balance for consume account";

$Text['msg_err_deactivate_prdrow'] = "This product cannot be deactivated because it has ordered items for certain dates. Deactivate the product for those individual dates first!";
$Text['msg_err_deactivate_ir'] = "You cannot deactivate several dates for this product since certain dates contain already ordered items. Either turn off Instant Repeat or deactivate the ordered products/date individually.";
$Text['msg_err_deactivate_product'] = "There are open orders for this product. Deactivating it will remove these items from the corresponding order carts. Deleting order items cannot be undone.";

$Text['msg_activate_prod_ok'] = "The product has been activated successfully."; 
$Text['msg_deactivate_prod_ok'] = "The product has been deactivated successfully."; 
$Text['msg_activate_prov_ok'] = "The provider has been activated successfully."; 
$Text['msg_deactivate_prov_ok'] = "The provider has been deactivated successfully."; 
$Text['no_stock'] = "Out of stock!!";
$Text['stock_mov_type'] = "Movement type";

// Orders
$Text['or_prv_prices'] = 'Prices provider (without rev.tax)';
$Text['or_gross_price'] = 'Price';
$Text['or_suma'] = 'Sum';
$Text['or_gross_total'] = 'Amo.Prov.';
$Text['or_net_price'] = 'Price+VAT';
$Text['or_net_total'] = 'Amount+VAT';
$Text['or_click_to_edit_total'] = 'Click to adjust total quantities';
$Text['or_click_to_edit_gprice'] = 'Click to adjust price';
$Text['or_saving'] = 'Saving';
$Text['or_ostat_desc_validated'] = 'Items of this order have been validated';
$Text['or_cancel_order_a'] = "Cancel order";
$Text['or_cancel_order'] =
     "Are you sure you want to cancel this order?<hr><br>
     The order will be finalized as canceled,<br>
     and NOT be sent by mail to the provider.<br><br>
     Later, if you want, you can change the order status.";
$Text['or_cancel_order_open'] =
     "Are you sure you want to cancel this order?<hr><br>
     The order will be finalized as canceled <b>before its deadline</b>,<br>
     and NOT be sent by mail to the provider.<br><br>
     Later, if you want, you can change the order status.";
$Text['os_reopen_order_a'] = "Reopen";
$Text['os_reopen_order'] =
    "Are you sure to reopen this order?<hr><br>
    NOTE:<br>
    The order may have been mailed.<br>
    If reopens order
    <b>must talk with the provider</b>
    to tell him that the order has been cancelled!";


//$Text[''] = ""; 



?>
