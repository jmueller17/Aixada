<?php
/* 
 * The contents of this file are generated automatically. 
 * Do not edit it, but instead run
 * php make_canned_responses.php
 */

// Function get_col_names() if for debugging purposes only.
function get_col_names(){
return
array (
  'aixada_account' => 
  array (
    0 => 'id',
    1 => 'account_id',
    2 => 'quantity',
    3 => 'payment_method_id',
    4 => 'currency_id',
    5 => 'description',
    6 => 'operator_id',
    7 => 'ts',
    8 => 'balance',
  ),
  'aixada_account_desc' => 
  array (
    0 => 'id',
    1 => 'description',
    2 => 'account_type',
    3 => 'active',
  ),
  'aixada_cart' => 
  array (
    0 => 'id',
    1 => 'name',
    2 => 'uf_id',
    3 => 'date_for_shop',
    4 => 'operator_id',
    5 => 'ts_validated',
    6 => 'ts_last_saved',
  ),
  'aixada_currency' => 
  array (
    0 => 'id',
    1 => 'name',
    2 => 'one_euro',
  ),
  'aixada_incident' => 
  array (
    0 => 'id',
    1 => 'subject',
    2 => 'incident_type_id',
    3 => 'operator_id',
    4 => 'details',
    5 => 'priority',
    6 => 'ufs_concerned',
    7 => 'commission_concerned',
    8 => 'provider_concerned',
    9 => 'ts',
    10 => 'status',
  ),
  'aixada_incident_type' => 
  array (
    0 => 'id',
    1 => 'description',
    2 => 'definition',
  ),
  'aixada_iva_type' => 
  array (
    0 => 'id',
    1 => 'name',
    2 => 'percent',
    3 => 'description',
  ),
  'aixada_member' => 
  array (
    0 => 'id',
    1 => 'custom_member_ref',
    2 => 'uf_id',
    3 => 'name',
    4 => 'address',
    5 => 'nif',
    6 => 'zip',
    7 => 'city',
    8 => 'phone1',
    9 => 'phone2',
    10 => 'web',
    11 => 'bank_name',
    12 => 'bank_account',
    13 => 'picture',
    14 => 'notes',
    15 => 'active',
    16 => 'participant',
    17 => 'adult',
    18 => 'ts',
  ),
  'aixada_order' => 
  array (
    0 => 'id',
    1 => 'provider_id',
    2 => 'date_for_order',
    3 => 'ts_sent_off',
    4 => 'date_received',
    5 => 'date_for_shop',
    6 => 'total',
    7 => 'notes',
    8 => 'revision_status',
    9 => 'delivery_ref',
    10 => 'payment_ref',
  ),
  'aixada_order_item' => 
  array (
    0 => 'id',
    1 => 'uf_id',
    2 => 'favorite_cart_id',
    3 => 'order_id',
    4 => 'unit_price_stamp',
    5 => 'iva_percent',
    6 => 'rev_tax_percent',
    7 => 'date_for_order',
    8 => 'product_id',
    9 => 'quantity',
    10 => 'notes',
    11 => 'ts_ordered',
  ),
  'aixada_order_to_shop' => 
  array (
    0 => 'order_item_id',
    1 => 'uf_id',
    2 => 'order_id',
    3 => 'unit_price_stamp',
    4 => 'iva_percent',
    5 => 'rev_tax_percent',
    6 => 'product_id',
    7 => 'quantity',
    8 => 'arrived',
    9 => 'revised',
    10 => 'aixada_order_to_shop_ibfk_1',
    11 => 'aixada_order_to_shop_ibfk_2',
    12 => 'aixada_order_to_shop_ibfk_3',
  ),
  'aixada_orderable_type' => 
  array (
    0 => 'id',
    1 => 'description',
  ),
  'aixada_payment_method' => 
  array (
    0 => 'id',
    1 => 'description',
    2 => 'details',
  ),
  'aixada_price' => 
  array (
    0 => 'product_id',
    1 => 'ts',
    2 => 'current_price',
    3 => 'operator_id',
  ),
  'aixada_product' => 
  array (
    0 => 'id',
    1 => 'provider_id',
    2 => 'name',
    3 => 'description',
    4 => 'barcode',
    5 => 'custom_product_ref',
    6 => 'active',
    7 => 'responsible_uf_id',
    8 => 'orderable_type_id',
    9 => 'order_min_quantity',
    10 => 'category_id',
    11 => 'rev_tax_type_id',
    12 => 'iva_percent_id',
    13 => 'unit_price',
    14 => 'unit_measure_order_id',
    15 => 'unit_measure_shop_id',
    16 => 'stock_min',
    17 => 'stock_actual',
    18 => 'delta_stock',
    19 => 'description_url',
    20 => 'picture',
    21 => 'ts',
  ),
  'aixada_product_category' => 
  array (
    0 => 'id',
    1 => 'description',
  ),
  'aixada_product_orderable_for_date' => 
  array (
    0 => 'id',
    1 => 'product_id',
    2 => 'date_for_order',
    3 => 'closing_date',
  ),
  'aixada_provider' => 
  array (
    0 => 'id',
    1 => 'name',
    2 => 'contact',
    3 => 'address',
    4 => 'nif',
    5 => 'zip',
    6 => 'city',
    7 => 'phone1',
    8 => 'phone2',
    9 => 'fax',
    10 => 'email',
    11 => 'web',
    12 => 'bank_name',
    13 => 'bank_account',
    14 => 'picture',
    15 => 'notes',
    16 => 'active',
    17 => 'responsible_uf_id',
    18 => 'offset_order_close',
    19 => 'order_send_format',
    20 => 'order_send_prices',
    21 => 'ts',
  ),
  'aixada_rev_tax_type' => 
  array (
    0 => 'id',
    1 => 'name',
    2 => 'description',
    3 => 'rev_tax_percent',
  ),
  'aixada_shop_item' => 
  array (
    0 => 'id',
    1 => 'cart_id',
    2 => 'order_item_id',
    3 => 'unit_price_stamp',
    4 => 'product_id',
    5 => 'quantity',
    6 => 'iva_percent',
    7 => 'rev_tax_percent',
  ),
  'aixada_stock_movement' => 
  array (
    0 => 'id',
    1 => 'product_id',
    2 => 'operator_id',
    3 => 'movement_type_id',
    4 => 'amount_difference',
    5 => 'description',
    6 => 'resulting_amount',
    7 => 'ts',
  ),
  'aixada_stock_movement_type' => 
  array (
    0 => 'id',
    1 => 'name',
    2 => 'description',
  ),
  'aixada_torns' => 
  array (
    0 => 'id',
    1 => 'dataTorn',
    2 => 'ufTorn',
  ),
  'aixada_uf' => 
  array (
    0 => 'id',
    1 => 'name',
    2 => 'active',
    3 => 'created',
    4 => 'mentor_uf',
  ),
  'aixada_unit_measure' => 
  array (
    0 => 'id',
    1 => 'name',
    2 => 'unit',
  ),
  'aixada_user' => 
  array (
    0 => 'id',
    1 => 'login',
    2 => 'password',
    3 => 'email',
    4 => 'uf_id',
    5 => 'member_id',
    6 => 'provider_id',
    7 => 'language',
    8 => 'gui_theme',
    9 => 'last_login_attempt',
    10 => 'last_successful_login',
    11 => 'created_on',
  ),
  'aixada_user_role' => 
  array (
    0 => 'user_id',
    1 => 'role',
  ),
  'aixada_version' => 
  array (
    0 => 'id',
    1 => 'module_name',
    2 => 'version',
  ),
)
;
}
