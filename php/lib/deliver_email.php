<?php 

require_once(__ROOT__ . 'php/lib/deliver.php');

/**
 *	Deliver contents as email. 
 *
 */
class Deliver_email extends Deliver {


	/**
	 *	Sends HTML email. If $to is null, will send individual emails to all active platform members.  
	 *	@param string $subject The subject of the email
	 *	@param string $bodyHTML The email is send as HTML document. 
	 *	@param string $to Email to send to. If left empty, emails will be send to all active members of platform
	 *	@param string $from Send of this email. If left blank is admin email from config file
	 *	@param array $options Additional options for the email header such as CC, Bcc, etc. 
	 *
	 */
	public function send($subject, $bodyHTML, $to=null, $from=null, $options=null) {
		$cfg = configuration_vars::get_instance();

		if (!isset($options)) {
			$options = array();
		}

		if (!isset($from)) {
			$from = $cfg->admin_email;
		}

		//send to all active members
		if (!isset($to)){

			$db = DBWrap::get_instance();
			$rs = $db->squery('get_member_listing', 1); 
			$db->free_next_results();

			$member_emails = array();
			while ($row = $rs->fetch_assoc()) {
	      		array_push($member_emails, $row['email']);
	    	}
	    	
	    	$to = implode(", ", $member_emails);
		}

		// get HTML message
		$subject = $cfg->coop_name.': '.$subject;
		$messageHTML =
			'<html><head><title>'.$subject."</title></head>\r\n".
			'<body style="font-family: Lucida Grande, Lucida Sans, Arial, sans-serif;">'.
			"\r\n".$bodyHTML."\r\n".
			'<hr><div style="color:#888; text-align: center;">'.
			'<a href="'.$cfg->basedir.'" style="color:#888;">'.$cfg->coop_name.'</a>'.
			"</div>\r\n".
			"</body></html>";
		
		$headers =
		'From: '.$from."\r\n".
		'Reply-To: '.
			(isset($options['reply_to']) ? $options['reply_to'] : $from)."\r\n".
			(isset($options['cc']) ? 'Cc :'.$options['cc']."\r\n" : '').
			(isset($options['bcc']) ? 'Bcc :'.$options['bcc']."\r\n" : '').
		'Return-Path: '.$from."\r\n".
		"X-Mailer: PHP\r\n".
		"MIME-Version: 1.0\r\n".
		"Content-Type: text/html; charset=UTF-8\r\n";
		mb_language("uni");
		mb_internal_encoding("UTF-8");
		$subject64 = mb_encode_mimeheader($subject);

		$response = mail($to, $subject64, $messageHTML, $headers);

		if ($cfg->development){
			error_log('send_mail(): '.$to."\r\n".$headers);
			error_log($messageHTML);
		}

		return $response;
	}



}


?>