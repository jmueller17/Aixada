<?php

/** 
 * @package Aixada
 */ 


/**
 * The Singleton class for global configuration variables.
 * We make a Singleton object so as not to pollute the global namespace.
 * @package Aixada
 */

class configuration_vars {

  /**
   * General variables
   */
    //  public $caixaIP = "192.168.1.100,192.168.1.101"; //checkout can only be performed from here;
  public $n_checkout_enabled_users = 2;
  public $n_seconds_enabled_for_checkout = 1200; // 20 minutes until checkout right gets revoked
  public $default_language = 'en';
  

  /**
   * Configure the database connection
   */
  public $db_type = 'mysqli';
  public $db_host = 'localhost';
  public $db_name = 'aixadac';
  public $db_user = 'aixada';
  public $db_password = 'aixada';


 
  /**
   * 
   * Sets the max. time scale for activating orderable dates into the future. E.g. 48 means that max. the orderable dates 
   * for the next 4 years can be generated in advance. 
   * @var num
   */
  public $max_month_orderable_dates = 24;
  
  /**
   * 
   * the default jquery-ui theme. these are located in css/ui-themes
   * @var string
   */
  public $default_theme = "start"; // start | ui-lightness | smoothness | redmond
  
  
  /**
   * Sets the global development variable for debugging etc. 
   * 
   */
  public $development = true; 
  
  
  /**
   * The default template for generating the print layout of the orders
   */
  public $print_order_template = 'report_order1.php';
  
  
  /**
   * 
   * Default template for printing personal orders of household ...
   * @var string
   */
  public $print_my_orders_template = 'order_model1.php';
  
  
  
  /**
   * 
   * Default template for printing bills of cooperative to members
   * @var unknown_type
   */
  public $print_bill_template = 'bill_model1.php';
  
  
  /**
   * 
   * diplays the language select on every page or not
   */
  public $show_menu_language_select = false; 
  
  
  
  /**
   * Code optimizations
   */

  /**
   * @var bool In case the database is parsed, this variable controls if the table_manager objects are stored in $_SESSION or not. Setting this variable to true cuts down considerably on execution time.
   */
  public $use_session_cache = true;

  /**
   * @var bool If true, this variable says to not parse the database every time a page is loaded, but to read the pre-compiled responses from the file canned_responses.php.  Setting this variable to true cuts down considerably on execution time.
   */
  public $use_canned_responses = true;

  /* 
   The next variable sets the directory to be used for caching database responses.
  */
  public $cache_dir;


  /* 
   This array says which tables are read by each query. Whenever a write 
   operation on one of these tables occurs, the corresponding caches are
   deleted.
  */
  /* for comparison, some hand-made fields.  Notice that
     "balance_of_account", get_decativated_products and
     products-for_order_by_date are not handled correctly.

  public $queries_reading
      = array("aixada_account" => array("account_extract", 
                                        "latest_movements",
                                        "income_spending_balance",
                                        "balance_of_account"),
              "aixada_product_orderable_for_date" => array("activated_products",
                                                           "providers_with_active_products_for_order",
                                                           "products_for_order_by_date",
                                                           "get_activated_products",
                                                           "get_deactivated_products")
              );
  */

  // Menu control

  private $default_menus = array(
                      'navHome'      => 'enable',
                      'navWizard'    => 'disable',
                      'navShop'      => 'disable',
                      'navOrder'     => 'disable',
                      'navManage'    => 'enable',
                      'navReport'    => 'enable',
                      'navIncidents' => 'enable'                                  
                                 );

  public $menu_config 
      = array( 'Consumer' => 
               array ( 
                      'navHome'      => 'enable',
                      'navWizard'    => 'disable',
                      'navShop'      => 'enable',
                      'navOrder'     => 'enable',
                      'navManage'    => 'disable',
                      'navReport'    => 'enable',
                      'navIncidents' => 'enable' 
                       ),
               'Hacker Commission' =>
               array (
                      'navHome'      => 'enable',
                      'navWizard'    => 'enable',
                      'navShop'      => 'enable',
                      'navOrder'     => 'enable',
                      'navManage'    => 'enable',
                      'navReport'    => 'enable',
                      'navIncidents' => 'enable' 
                      ),
               'Checkout' =>
               array ( 
                      'navHome'      => 'enable',
                      'navWizard'    => 'enable',
                      'navShop'      => 'disable',
                      'navOrder'     => 'disable',
                      'navManage'    => 'enable',
                      'navReport'    => 'enable',
                      'navIncidents' => 'enable' 
                       ),               
               'Consumer Commission' => 
               array(
                     'navHome'      => 'enable',
                     'navWizard'    => 'disable',
                     'navShop'      => 'disable',
                     'navOrder'     => 'disable',
                     'navManage'    => 'enable',
                     'navReport'    => 'enable',
                     'navIncidents' => 'enable'                                  
                     ),
               'Econo-Legal Commission' => 
               array(
                     'navHome'      => 'enable',
                     'navWizard'    => 'disable',
                     'navShop'      => 'disable',
                     'navOrder'     => 'disable',
                     'navManage'    => 'enable',
                     'navReport'    => 'enable',
                     'navIncidents' => 'enable'                                  
                     ),
               'Logistic Commission' => 
               array(
                     'navHome'      => 'enable',
                     'navWizard'    => 'disable',
                     'navShop'      => 'disable',
                     'navOrder'     => 'disable',
                     'navManage'    => 'enable',
                     'navReport'    => 'enable',
                     'navIncidents' => 'enable'                                  
                     ),
               'Fifth Column Commission' => 
               array(
                     'navHome'      => 'enable',
                     'navWizard'    => 'disable',
                     'navShop'      => 'disable',
                     'navOrder'     => 'disable',
                     'navManage'    => 'enable',
                     'navReport'    => 'enable',
                     'navIncidents' => 'enable'                                  
                     ),
               'Producer' => 
               array(
                     'navHome'      => 'enable',
                     'navWizard'    => 'disable',
                     'navShop'      => 'disable',
                     'navOrder'     => 'disable',
                     'navManage'    => 'enable',
                     'navReport'    => 'enable',
                     'navIncidents' => 'enable'                                  
                     )
               );

  // Forbidden pages
  public $forbidden_pages = 
      array(
            'Consumer' =>
            array(
                  'validate.php',
                  'manage_table.php',
                  'activate_products.php',
                  'activate_roles.php',
                  'activate_all_roles.php',
                  //                  'report_order.php',
                  //        'report_account.php',
                  'manage_preorders.php',
                  'manage_user.php',
                  'all_prevorders.php'
                  ),
            
            'Checkout' =>
            array(
                  'activate_roles.php',
                  'activate_all_roles.php',
                  'shop_and_order.php'
                  ),

            'Consumer Commission' =>
            array(
                  'activate_all_roles.php',
                  'validate.php',
                  'all_prevorders.php'
                  ),

            'Econo-Legal Commission' =>
            array(
                  'activate_all_roles.php',
                  'validate.php',
                  'all_prevorders.php'
                  ),

            'Logistic Commission' =>
            array(
                  'activate_all_roles.php',
                  'validate.php',
                  'all_prevorders.php'
                  ),

            'Hacker Commission' =>
            array(),

            'Fifth Column Commission' =>
            array(
                  'activate_all_roles.php',
                  'validate.php',
                  'all_prevorders.php'
                  ),

            'Producer' =>
            array(
                  'validate.php',
                  'manage_table.php',
                  'activate_products.php',
                  'activate_roles.php',
                  'activate_all_roles.php',
                  'report_order.php',
                  'report_account.php',
                  'manage_preorders.php',
                  'manage_user.php',
                  'all_prevorders.php'
                  )
            );
                  
  
  // Roles and their privileges
  public $rights_of = 
      array (
             'Checkout' =>
             array('may_edit_user', 
                   'may_edit_uf', 
                   'may_edit_member',
                   'may_edit_provider',
                   'may_edit_product',
                   'may_edit_incident',
                   'may_edit_account',
                   'may_edit_unit_measure',
                   'may_view_all_accounts'),
             'Consumer' =>
             array('may_edit_incident',
                   'may_edit_provider'),
             
             'Consumer Commission' =>
             array('may_edit_product',
                   'may_edit_provider',
                   'may_edit_incident',
                   'may_edit_unit_measure'),

             'Econo-Legal Commission' =>
             array('may_edit_incident',
                   'may_view_all_accounts'),
             
             'Logistic Commission' =>
             array('may_edit_incident'),
             
             'Hacker Commission' =>
             array('may_edit_user', 
                   'may_edit_uf', 
                   'may_edit_member',
                   'may_edit_provider',
                   'may_edit_product',
                   'may_edit_incident',
                   'may_edit_account',
                   'may_edit_unit_measure',
                   'may_view_all_accounts'),
             
             'Fifth Commission' =>
             array('may_edit_incident'),

             'Producer' =>
             array('may_edit_incident')
             );
  
  // Placing orders
  public $use_sparklines = true;


  // from here on follow internals of the configuration_vars class.
  private static $instance = false;

  public function __construct()
  {
      $this->cache_dir = sys_get_temp_dir() . '/';
  }

  public static function get_instance()
  {
    if (self::$instance === false)
        self::$instance = new configuration_vars();
    return self::$instance;
  }


  // This and the following lines are automatically generated 
  // (see the Makefile in the current directory).
  // Please do not edit the file from this point on.


  public $queries_reading = array (
  'aixada_account' => 
  array (
    0 => 'account_extract',
    1 => 'latest_movements',
    2 => 'deposit_for_uf',
    4 => 'uf_weekly_balance',
    5 => 'initialize_caixa',
    7 => 'income_spending_balance',
    8 => 'ufs_with_negative_balance',
    9 => 'aixada_account_list_all_query',
  ),
  'aixada_currency' => 
  array (
    0 => 'account_extract',
    1 => 'latest_movements',
    2 => 'aixada_account_list_all_query',
    3 => 'aixada_currency_list_all_query',
  ),
  'aixada_payment_method' => 
  array (
    0 => 'account_extract',
    1 => 'latest_movements',
    2 => 'aixada_account_list_all_query',
    3 => 'aixada_payment_method_list_all_query',
  ),
  'aixada_user' => 
  array (
    0 => 'account_extract',
    1 => 'latest_movements',
    2 => 'check_credentials',
    3 => 'check_password',
    4 => 'get_members_of_uf',
    5 => 'find_user_by_login_or_email',
    6 => 'register_user',
    7 => 'update_user',
    8 => 'get_member_info',
    9 => 'member_id_of_user_id',
    10 => 'users_without_ufs',
    12 => 'latest_incidents',
    13 => 'todays_incidents',
    14 => 'users_without_uf',
    15 => 'users_without_member',
    16 => 'get_users',
    17 => 'get_active_roles',
    18 => 'aixada_user_list_all_query',
  ),
  'aixada_member' => 
  array (
    0 => 'account_extract',
    1 => 'latest_movements',
    2 => 'get_members_of_uf',
    3 => 'create_member',
    4 => 'get_member_info',
    6 => 'member_id_of_user_id',
    7 => 'latest_incidents',
    8 => 'todays_incidents',
    9 => 'get_users',
    10 => 'get_active_users_for_role',
    11 => 'get_inactive_users_for_role',
    12 => 'summarized_orders_for_date',
    13 => 'aixada_member_list_all_query',
    14 => 'aixada_user_list_all_query',
  ),
  'aixada_account_balance' => 
  array (
    0 => 'negative_accounts',
    1 => 'deduct_stock_and_pay',
    2 => 'undo_validate',
  ),
  'aixada_uf' => 
  array (
    0 => 'negative_accounts',
    1 => 'find_uf_by_name',
    2 => 'create_uf',
    4 => 'update_uf',
    5 => 'get_all_ufs',
    6 => 'users_without_ufs',
    7 => 'get_users',
    8 => 'summarized_orders_for_date',
    9 => 'detailed_orders_for_provider_and_date',
    10 => 'detailed_preorders_for_provider_and_date',
    11 => 'active_times',
    12 => 'get_ufs_for_validation',
    13 => 'get_active_ufs',
    14 => 'aixada_distributor_list_all_query',
    15 => 'aixada_favorite_order_cart_list_all_query',
    16 => 'aixada_favorite_order_item_list_all_query',
    17 => 'aixada_member_list_all_query',
    18 => 'aixada_order_item_list_all_query',
    19 => 'aixada_product_list_all_query',
    20 => 'aixada_provider_list_all_query',
    21 => 'aixada_shop_item_list_all_query',
    22 => 'aixada_uf_list_all_query',
    23 => 'aixada_user_list_all_query',
  ),
  'aixada_product_orderable_for_date' => 
  array (
    0 => 'get_next_equal_shop_date',
    1 => 'providers_with_active_products_for_order',
    2 => 'product_categories_for_order',
    3 => 'products_for_order_by_provider',
    4 => 'products_for_order_by_category',
    5 => 'products_for_order_like',
    6 => 'get_activated_products',
    7 => 'providers_with_active_products_for_shop',
    8 => 'product_categories_for_shop',
    9 => 'products_for_shop_by_provider',
    10 => 'products_for_shop_by_category',
    11 => 'products_for_shop_like',
    12 => 'get_ufs_for_validation',
    13 => 'products_for_validate_like',
    14 => 'aixada_product_orderable_for_date_list_all_query',
  ),
  'aixada_shopping_dates' => 
  array (
    0 => 'shopping_dates',
    1 => 'aixada_shopping_dates_list_all_query',
  ),
  'aixada_user_role' => 
  array (
    0 => 'get_members_of_uf',
    1 => 'get_member_info',
    2 => 'get_active_roles',
    3 => 'get_active_users_for_role',
    4 => 'get_inactive_users_for_role',
    6 => 'aixada_user_role_list_all_query',
  ),
  'aixada_provider' => 
  array (
    0 => 'get_member_info',
    1 => 'latest_incidents',
    2 => 'todays_incidents',
    3 => 'providers_with_active_products_for_order',
    4 => 'product_categories_for_order',
    5 => 'products_for_order_by_provider',
    6 => 'products_for_order_by_category',
    7 => 'products_for_order_like',
    8 => 'products_for_order_by_date',
    9 => 'products_for_favorite_order',
    10 => 'list_preorder_providers',
    11 => 'preorderable_products',
    12 => 'list_all_providers_short',
    13 => 'list_all_ordered_providers_short',
    14 => 'total_orders_for_date_and_provider',
    15 => 'providers_with_orders_for_date',
    16 => 'summarized_orders_for_date',
    17 => 'summarized_preorders',
    18 => 'detailed_total_orders_for_date',
    19 => 'spending_per_provider',
    20 => 'shopped_items_by_id',
    21 => 'shop_for_uf_and_time',
    22 => 'providers_with_active_products_for_shop',
    23 => 'product_categories_for_shop',
    24 => 'products_for_shop_by_provider',
    25 => 'products_for_shop_by_category',
    26 => 'products_for_shop_like',
    27 => 'products_for_shopping',
    28 => 'provider_weekly_orders',
    29 => 'products_for_validating',
    30 => 'products_for_validate_like',
    31 => 'products_below_min_stock',
    32 => 'aixada_product_list_all_query',
    33 => 'aixada_provider_list_all_query',
    34 => 'aixada_providers_of_distributor_list_all_query',
    35 => 'aixada_user_list_all_query',
  ),
  'aixada_product' => 
  array (
    0 => 'get_member_info',
    1 => 'providers_with_active_products_for_order',
    2 => 'product_categories_for_order',
    3 => 'products_for_order_by_provider',
    4 => 'products_for_order_by_category',
    5 => 'products_for_order_like',
    6 => 'products_for_order_by_date',
    7 => 'products_for_favorite_order',
    8 => 'list_preorder_providers',
    9 => 'list_preorder_products',
    10 => 'preorderable_products',
    11 => 'list_all_providers_short',
    12 => 'list_all_ordered_providers_short',
    13 => 'get_activated_products',
    14 => 'get_deactivated_products',
    15 => 'get_arrived_products',
    16 => 'get_not_arrived_products',
    17 => 'add_stock',
    18 => 'total_orders_for_date_and_provider',
    19 => 'providers_with_orders_for_date',
    20 => 'summarized_orders_for_provider_and_date',
    21 => 'detailed_orders_for_provider_and_date',
    22 => 'detailed_preorders_for_provider_and_date',
    23 => 'detailed_total_orders_for_date',
    24 => 'spending_per_provider',
    25 => 'shopped_items_by_id',
    26 => 'shop_for_uf_and_time',
    27 => 'providers_with_active_products_for_shop',
    28 => 'product_categories_for_shop',
    29 => 'products_for_shop_by_provider',
    30 => 'products_for_shop_by_category',
    31 => 'products_for_shop_like',
    32 => 'products_for_shopping',
    33 => 'most_bought_products',
    34 => 'least_bought_products',
    35 => 'uf_weekly_orders',
    36 => 'provider_weekly_orders',
    37 => 'product_weekly_orders',
    38 => 'products_for_validating',
    39 => 'products_for_validate_like',
    40 => 'products_below_min_stock',
    41 => 'aixada_favorite_order_item_list_all_query',
    42 => 'aixada_order_item_list_all_query',
    43 => 'aixada_product_list_all_query',
    44 => 'aixada_product_orderable_for_date_list_all_query',
    45 => 'aixada_shop_item_list_all_query',
    46 => 'aixada_stock_movement_list_all_query',
  ),
  'aixada_incident' => 
  array (
    0 => 'latest_incidents',
    1 => 'todays_incidents',
    2 => 'aixada_incident_list_all_query',
  ),
  'aixada_incident_type' => 
  array (
    0 => 'latest_incidents',
    1 => 'todays_incidents',
    2 => 'get_incident_types',
    3 => 'aixada_incident_list_all_query',
    4 => 'aixada_incident_type_list_all_query',
  ),
  'aixada_product_category' => 
  array (
    0 => 'product_categories_for_order',
    1 => 'product_categories_for_shop',
    2 => 'aixada_product_list_all_query',
    3 => 'aixada_product_category_list_all_query',
  ),
  'aixada_rev_tax_type' => 
  array (
    0 => 'products_for_order_by_provider',
    1 => 'products_for_order_by_category',
    2 => 'products_for_order_like',
    3 => 'products_for_order_by_date',
    4 => 'products_for_favorite_order',
    5 => 'preorderable_products',
    6 => 'shopped_items_by_id',
    7 => 'shop_for_uf_and_time',
    8 => 'products_for_shop_by_provider',
    9 => 'products_for_shop_by_category',
    10 => 'products_for_shop_like',
    11 => 'products_for_shopping',
    12 => 'products_for_validating',
    13 => 'products_for_validate_like',
    14 => 'aixada_product_list_all_query',
    15 => 'aixada_rev_tax_type_list_all_query',
  ),
  'aixada_unit_measure' => 
  array (
    0 => 'products_for_order_by_provider',
    1 => 'products_for_order_by_category',
    2 => 'products_for_order_like',
    3 => 'products_for_order_by_date',
    4 => 'preorderable_products',
    5 => 'summarized_orders_for_provider_and_date',
    6 => 'detailed_orders_for_provider_and_date',
    7 => 'detailed_preorders_for_provider_and_date',
    8 => 'detailed_total_orders_for_date',
    9 => 'shopped_items_by_id',
    10 => 'shop_for_uf_and_time',
    11 => 'products_for_shop_by_provider',
    12 => 'products_for_shop_by_category',
    13 => 'products_for_shop_like',
    14 => 'products_for_shopping',
    15 => 'products_for_validating',
    16 => 'products_for_validate_like',
    17 => 'aixada_product_list_all_query',
    19 => 'aixada_unit_measure_list_all_query',
  ),
  'aixada_order_item' => 
  array (
    0 => 'products_for_order_by_date',
    1 => 'make_favorite_order_cart',
    2 => 'move_all_orders',
    3 => 'list_preorder_providers',
    4 => 'list_preorder_products',
    5 => 'list_all_ordered_providers_short',
    6 => 'get_not_arrived_products',
    7 => 'orders_for_date_and_provider',
    8 => 'total_orders_for_date_and_provider',
    9 => 'providers_with_orders_for_date',
    10 => 'summarized_orders_for_date',
    11 => 'summarized_orders_for_provider_and_date',
    12 => 'summarized_preorders',
    13 => 'detailed_orders_for_provider_and_date',
    14 => 'detailed_preorders_for_provider_and_date',
    15 => 'detailed_total_orders_for_date',
    16 => 'spending_per_provider',
    17 => 'convert_order_to_shop',
    18 => 'most_bought_products',
    19 => 'least_bought_products',
    20 => 'uf_weekly_orders',
    21 => 'provider_weekly_orders',
    22 => 'product_weekly_orders',
    23 => 'aixada_order_item_list_all_query',
  ),
  'aixada_favorite_order_item' => 
  array (
    0 => 'products_for_favorite_order',
    1 => 'aixada_favorite_order_item_list_all_query',
  ),
  'aixada_shop_item' => 
  array (
    0 => 'move_all_orders',
    3 => 'get_arrived_products',
    4 => 'get_not_arrived_products',
    5 => 'last_shop_times_for_uf',
    6 => 'nonvalidated_shop_times_for_uf',
    7 => 'future_shop_times_for_uf',
    8 => 'past_validated_shop_times_for_uf',
    9 => 'shopped_items_by_id',
    10 => 'shop_for_uf_and_time',
    11 => 'products_for_shopping',
    12 => 'get_ufs_for_validation',
    13 => 'products_for_validating',
    14 => 'dates_with_unvalidated_shop_carts',
    15 => 'validated_shop_carts',
    16 => 'undo_validate',
    18 => 'aixada_shop_item_list_all_query',
  ),
  'aixada_stock_movement' => 
  array (
    0 => 'stock_movements',
    1 => 'aixada_stock_movement_list_all_query',
  ),
  'aixada_distributor' => 
  array (
    0 => 'aixada_distributor_list_all_query',
    1 => 'aixada_providers_of_distributor_list_all_query',
  ),
  'aixada_favorite_order_cart' => 
  array (
    0 => 'aixada_favorite_order_cart_list_all_query',
    1 => 'aixada_favorite_order_item_list_all_query',
  ),
  'aixada_orderable_type' => 
  array (
    0 => 'aixada_orderable_type_list_all_query',
    1 => 'aixada_product_list_all_query',
  ),
  'aixada_providers_of_distributor' => 
  array (
    0 => 'aixada_providers_of_distributor_list_all_query',
  ),
);
  public $tables_modified_by = array (
  'deposit_for_uf' => 
  array (
    0 => 'aixada_account',
    1 => 'aixada_account_balance',
  ),
  'check_credentials' => 
  array (
    0 => 'aixada_user',
  ),
  'create_uf' => 
  array (
    0 => 'aixada_uf',
    1 => 'aixada_account_balance',
  ),
  'update_uf' => 
  array (
    0 => 'aixada_uf',
  ),
  'register_user' => 
  array (
    0 => 'aixada_member',
    1 => 'aixada_user',
    2 => 'aixada_user_role',
  ),
  'register_special_user' => 
  array (
    0 => 'aixada_uf',
    1 => 'aixada_member',
    4 => 'aixada_user',
    5 => 'aixada_user_role',
  ),
  'update_user_email_language_login' => 
  array (
    0 => 'aixada_user',
  ),
  'update_password' => 
  array (
    0 => 'aixada_user',
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
  'create_member' => 
  array (
    0 => 'aixada_member',
    1 => 'aixada_user',
  ),
  'update_member' => 
  array (
    0 => 'aixada_member',
  ),
  'deactivate_member' => 
  array (
    0 => 'aixada_member',
  ),
  'new_incident' => 
  array (
    0 => 'aixada_incident',
  ),
  'edit_incident' => 
  array (
    0 => 'aixada_incident',
  ),
  'delete_incident' => 
  array (
    0 => 'aixada_incident',
  ),
  'assign_user_to_uf' => 
  array (
    0 => 'aixada_user',
    1 => 'aixada_member',
  ),
  'make_favorite_order_cart' => 
  array (
    0 => 'aixada_favorite_order_cart',
    1 => 'aixada_favorite_order_item',
  ),
  'delete_favorite_order_cart' => 
  array (
    0 => 'aixada_favorite_order_item',
    2 => 'aixada_favorite_order_cart',
  ),
  'move_all_orders' => 
  array (
    0 => 'aixada_order_item',
    3 => 'date_for_order',
    4 => 'aixada_shop_item',
    7 => 'date_for_shop',
    8 => 'aixada_product_orderable_for_date',
  ),
  'activate_preorder_products' => 
  array (
    0 => 'aixada_order_item',
  ),
  'deactivate_preorder_products' => 
  array (
    0 => 'aixada_order_item',
  ),
  'add_stock' => 
  array (
    0 => 'aixada_product',
    1 => 'aixada_stock_movement',
  ),
  'convert_order_to_shop' => 
  array (
    0 => 'aixada_shop_item',
  ),
  'initialize_caixa' => 
  array (
    0 => 'aixada_account',
  ),
  'validate_shop_items' => 
  array (
    0 => 'aixada_shop_item',
  ),
  'deduct_stock_and_pay' => 
  array (
    0 => 'aixada_shop_item',
    1 => 'aixada_product',
    2 => 'aixada_account',
    3 => 'aixada_account_balance',
  ),
  'undo_validate' => 
  array (
    0 => 'aixada_shop_item',
    1 => 'aixada_account',
  ),
  'aixada_account_balance_list_all_query' => 
  array (
    0 => 'aixada_account_balance',
  ),
);}
?>
