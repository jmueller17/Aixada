
  public $tables_modified_by = array (
  'deposit' => 
  array (
    0 => 'if',
  ),
  'get_orderable_dates' => 
  array (
    0 => 'procedure',
    1 => 'from_date_onward',
    2 => 'from_date',
  ),
  'delete_incident' => 
  array (
    0 => 'aixada_incident',
  ),
  'set_order_status' => 
  array (
    0 => '*',
  ),
  'activate_preorder_products' => 
  array (
    0 => 'aixada_order_to_shop',
    1 => '*/',
    2 => 'aixada_shop_item',
    4 => 'aixada_order_item',
  ),
  'deactivate_preorder_products' => 
  array (
    0 => 'aixada_order_item',
  ),
  'repeat_orderable_product' => 
  array (
    0 => 'aixada_product_orderable_for_date',
  ),
  'toggle_orderable_product' => 
  array (
    0 => 'aixada_product_orderable_for_date',
  ),
  'change_active_status_product' => 
  array (
    0 => 'aixada_product_orderable_for_date',
    1 => '',
  ),
  'register_special_user' => 
  array (
    0 => 'aixada_uf',
    1 => 'aixada_member',
    4 => 'aixada_user',
    5 => 'aixada_user_role',
  ),
  'remove_user_roles' => 
  array (
    0 => 'aixada_user_role',
  ),
  'add_user_role' => 
  array (
    0 => 'aixada_user_role',
  ),
  'update_user' => 
  array (
    0 => 'aixada_user',
  ),
  'assign_user_to_uf' => 
  array (
    0 => 'aixada_user',
    1 => 'aixada_member',
  ),
  'undo_validate' => 
  array (
    0 => 'aixada_shop_item',
    1 => 'aixada_account',
  ),
);