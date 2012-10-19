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

    /*******************************************************************
        Here begin the variables you must set during installation.
    ********************************************************************/

  /**
   * General variables
   */
  public $default_language = 'en';
  

  /**
   * What is the name of this cooperative?
   */
  public $coop_name = 'Aixada';

  /**
   * Configure the database connection
   */
  public $db_type = 'mysqli';
  public $db_host = 'localhost';
  public $db_name = 'aixada';
  public $db_user = 'aixada';
  public $db_password = 'aixada';

  /****************************************************************************
     You should not need to change anything past this point when installing.
  ****************************************************************************/

 
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
   * 
   * is this a local install or accessible online. If online
   * services such as sending emails will be active. 
   * @var boolean
   */
  public $internet_connection = false; 
  
  
  /**
   * the email address of the admin; used for sending out pwd changes
   * and orders
   */
  public $admin_email = "joerg@toytic.com";
  
  
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
             	   'may_edit_iva_type',
                   'may_view_all_accounts'),
             'Consumer' =>
             array('may_edit_incident',
                   'may_edit_provider'),
             
             'Consumer Commission' =>
             array('may_edit_product',
                   'may_edit_provider',
                   'may_edit_incident',
                   'may_edit_unit_measure',
             	   'may_edit_iva_type'),

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
                   'may_view_all_accounts',
             	   'may_edit_iva_type'),
             
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
  }

  public static function get_instance()
  {
    if (self::$instance === false)
        self::$instance = new configuration_vars();
    return self::$instance;
  }
};
?>
