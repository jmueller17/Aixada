<?php

//require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
//ob_start(); // Starts FirePHP output buffering

require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities.php");
require_once("utilities_useruf.php");


if (!isset($_SESSION)) {
    session_start();
 }


try{
   

  switch ($_REQUEST['oper']) {
  	    
  		
  		case 'getUfListing':
        	printXML(stored_query_XML_fields('get_uf_listing', get_param('all',0)));
        	exit;
        	
        case 'createUF':
		   	printXML(stored_query_XML_fields('create_uf', get_param('name'), get_param('mentor_uf',0)));
		   	exit;
 		
	   	case 'editUF':
      		echo do_stored_query('update_uf', get_param('uf_id'), get_param('name'), get_param('is_active'), get_param('mentor_uf',0));
      		exit;
		   
        case 'getUsersWithoutUF':
        	printXML(stored_query_XML_fields('users_without_uf'));
        	exit;
        	
        case 'checkFormField':
        	echo validate_field(get_param('table'), get_param('field'), get_param('value'));
        	exit;

        //listing of all/only active members. 	
        case 'getMemberListing':
        	printXML(stored_query_XML_fields('get_member_listing', get_param('all',0)));
        	exit;
		        	
		//detailed info about member, if member_id is given, otherwise for all members of uf. 
	    case 'getMemberInfo':
	        printXML(stored_query_XML_fields('get_member_info', get_param('member_id',0), get_param('uf_id',0) ));
	        exit;
	        
	    case 'updateMember':
	    	echo update_member(get_param('member_id'));
	    	exit;
	    	
	    //creates a new user and member
	    case 'createUserMember':
	    	echo create_user_member(get_param('uf_id'));
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
        	
        	
  

 

  case 'createMember':
      printXML(stored_query_XML_fields('create_member', $login, $password, $name, $uf_id, $language, $color_scheme, $address, $zip, $city, $phone1, $phone2, $email, $active));
      exit;

  case 'updateMember':
      /* $firephp->log($active, 'active'); */
      /* $firephp->log($participant, 'participant'); */
      do_stored_query('update_member', $id, $name, $address, $zip, $city, $phone1, $phone2, $urls, $notes, $active, $participant, $adult);
      do_stored_query('update_user_email_language_login', $id, $email, $language, $login);
      echo('1');
      exit;

  case 'deactivateMember':
      do_stored_query('deactivate_member', $id);
      echo('1');
      exit;

  case 'changePassword':
      $user_id = $_SESSION['userdata']['user_id'];
      $rs = do_stored_query('check_password', $user_id, crypt($old_password, 'ax'));
      $row = $rs->fetch_assoc();
      if (!$row or $row['id'] != $user_id) {
          throw new Exception("Wrong username or password given");
      }
      DBWrap::get_instance()->free_next_results();      
      do_stored_query('update_password', $user_id, crypt($new_password, 'ax'));
      echo '1';
      exit;

  case 'changeOtherPassword':
      $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : 0;
      if (!$user_id)
          throw new Exception("User id " . $user_id . ' not valid');
      if ($_SESSION['userdata']['current_role'] != 'Hacker Commission')
          throw new Exception("Only Hackers can do that!");
      do_stored_query('update_password', $user_id, crypt($new_password, "ax"));
      echo '1';
      exit;
      
       case 'usersWithoutUFs':
	      printXML(stored_query_XML_fields('users_without_ufs'));
	      exit;
	

  default:
    throw new Exception("ctrlUserAndUf: oper=" . $_REQUEST['oper'] . " not valid in query");
  }
} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  


?>