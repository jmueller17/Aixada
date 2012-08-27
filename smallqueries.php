<?php

//require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
//ob_start(); // Starts FirePHP output buffering

require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities.php");
require_once("utilities_report.php");

if (!isset($_SESSION)) {
    session_start();
 }

DBWrap::get_instance()->debug = true;

try{
    $date = isset($_REQUEST['date']) ? $_REQUEST['date'] : 0;
    $available = isset($_REQUEST['available']) ? $_REQUEST['available'] : true;
    $the_type = (isset($_REQUEST['typeSelect']) ? $_REQUEST['typeSelect'] : 1);
    $the_priority = (isset($_REQUEST['prioritySelect']) ? $_REQUEST['prioritySelect'] : 1);
    $the_comm = ((isset($_REQUEST['commissionSelect']) and $_REQUEST['commissionSelect'] != -1) ? $_REQUEST['commissionSelect'] : '');
    $the_prov = ((isset($_REQUEST['providerSelect']) and $_REQUEST['providerSelect'] != -1) ? $_REQUEST['providerSelect'] : '');
    $the_status = (isset($_REQUEST['statusSelect']) ? $_REQUEST['statusSelect'] : 1);

    /*
    $firephp->log($_REQUEST, 'request');
    $firephp->log($date, 'date');
    $firephp->log($the_type, 'type');
    $firephp->log($the_priority, 'priority');
    $firephp->log($the_comm, 'commission');
    $firephp->log($the_prov, 'provider');
    */

    switch ($_REQUEST['oper']) {

    case 'configMenu':
        printXML(get_config_menu($_REQUEST['user_role']));
        exit;
        
    case 'getFieldOptions':
        echo get_field_options_live($_REQUEST['table'], $_REQUEST['field1'], $_REQUEST['field2']);
        exit;


    case 'getAllAccounts':
        printXML(get_all_accounts());
        exit;

    case 'getAllUFs':
        printXML(stored_query_XML_fields('get_all_ufs'));
        exit;

    case 'getActiveUFs':
        printXML(stored_query_XML('get_active_ufs', 'ufs', 'name'));
        exit;

    case 'getMembersOfUF':
        printXML(stored_query_XML_fields('get_members_of_uf', $_REQUEST['uf_id']));
        exit;

    case 'getMemberInfo':
        printXML(stored_query_XML_fields('get_member_info', get_param('member_id') ));
        exit;

    case 'getProductsBelowMinStock':
        printXML(stored_query_XML_fields('products_below_min_stock'));
        exit;


    case 'getIncidentTypes':
        printXML(stored_query_XML_fields('get_incident_types'));
        exit;        

    case 'newIncident':
        $uc = '';
        foreach($_REQUEST['ufs_concerned'] as $uf)
            $uc .= $uf . ',';
        $uc = rtrim($uc, ',');
        do_stored_query('new_incident', 
                        $the_type, $the_priority, $_REQUEST['subject'], $_SESSION['userdata']['user_id'], $_REQUEST['incidents_text'], $uc, $the_comm, $the_prov, $the_status ); 
        exit;

    case 'editIncident':
        $uc = '';
        foreach($_REQUEST['ufs_concerned'] as $uf)
            $uc .= $uf . ',';
        $uc = rtrim($uc, ',');
        $iid = $_REQUEST['incident_id'];
        if ($iid > 1)
            do_stored_query('edit_incident', $iid, 
                            $the_type, $the_priority, $_REQUEST['subject'], $_SESSION['userdata']['user_id'], $_REQUEST['incidents_text'], $uc, $the_comm, $the_prov, $the_status ); 
        else
            do_stored_query('new_incident', 
                            $the_type, $the_priority, $_REQUEST['subject'], $_SESSION['userdata']['user_id'], $_REQUEST['incidents_text'], $uc, $the_comm, $the_prov, $the_status ); 
        exit;

    case 'delIncident':
        do_stored_query('delete_incident', $_REQUEST['incident_id']);
        echo 1;
        exit;
        
    case 'latestIncidents':
        printXML(stored_query_XML_fields('latest_incidents'));
        exit;

    case 'todaysIncidents':
        printXML(stored_query_XML_fields('todays_incidents'));
        exit;

    case 'addStock':
        printXML(stored_query_XML_fields('add_stock', $_REQUEST['product_id'], $_REQUEST['delta_amount'], $_REQUEST['operator_id'], $_REQUEST['description']));
        exit;

    case 'stockMovements':
        printXML(stored_query_XML_fields('stock_movements', $_REQUEST['product_id'], $date, $_REQUEST['num_rows']));
        exit;

    case 'getExistingLanguages':
        printXML(existing_languages_XML());
        exit;

    case 'getUsersWithoutUF':
        printXML(stored_query_XML_fields('users_without_uf'));
        exit;

    case 'getUsersWithoutMember':
        printXML(stored_query_XML_fields('users_without_member'));
        exit;

    case 'assignUsersToUF':
        foreach ($_REQUEST['user_id'] as $id) {
            do_stored_query('assign_user_to_uf', $id, $_REQUEST['uf_id']);
        }
        exit;

    case 'assignUserToMember':
        printXML(stored_query_XML_fields('assign_user_to_member', $_REQUEST['user_id']));
        exit;

    case 'getRoles':
        printXML(get_roles());
        exit;

    case 'getCommissions':
        printXML(get_commissions());
        exit;

    default:
        throw new Exception('smallqueries.php: Operation ' . $_REQUEST['oper'] . ' not supported.');
    }
} 

catch(Exception $e) {
    header('HTTP/1.0 401 ' . $e->getMessage());
    die($e->getMessage());
}  
?>