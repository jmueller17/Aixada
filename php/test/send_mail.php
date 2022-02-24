<?php
define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 
require_once __ROOT__ . "php/utilities/general.php";


// Trick to force some config parameters
function put_config($forced_cfg) {
    $cfg = configuration_vars::get_instance();
    foreach ($forced_cfg as $k => $v) {
        $cfg->$k = $v;
    }
}


// Send a test email
function send_test($toEmail, $options) {
    global $Text;
    $testUTF = 'áàéèïíoóòüúçñ ÁÀÉÈÏÍOÓÒÜÚÇÑ €=EUR';
    $subject = "IT'S A TEST: ". $testUTF . "<br>";
    $messageHTML = "<b>Is a test using PHP v." . PHP_VERSION . "</b><br>
        Test utf-8: " . $testUTF . "<br><br>
        Options:<br><pre style='margin: 0 0 0 3em'>" . 
            'toEmail => ' . $toEmail ."\n" .
            var_export($options, true). 
        "</pre>
        Config:<br><pre style='margin: 0 0 0 3em'>" . var_export(array(
            'coop_name' => get_config('coop_name'),
            'admin_email' => get_config('admin_email'),
            'email_SMTP_host' => get_config('email_SMTP_host'),
            'email_SMTP_user' => null,
            'email_SMTP_pswd' => '(hidden)',
            'email_SMTP_port' => get_config('email_SMTP_port'),
            'email_SMTP_encryption' => get_config('email_SMTP_encryption'),
            'email_SMTP_verifyCert' => get_config('email_SMTP_verifyCert')
            ), true) . "</pre>";
    echo '<hr>';
    echo "Subject: " . $subject . "<br>";
    echo $messageHTML;
    echo "<hr><pre>";
    if ( send_mail($toEmail, $subject, $messageHTML, $options) ) {
        return 'Test email sent successfully';
    } else {
        return '<span style="color:red">'.$Text['msg_err_emailed'].'</span>';
    }
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php $language = 'ca'; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php

put_config(array(
    'admin_email' => '(set at private/send_mail-hidden.php)',
    'email_safe_replyTo' =>  true,
    'email_SMTP_host' => '(set at private/send_mail-hidden.php)',
    'email_SMTP_port' => 465,
    'email_SMTP_user' => null,
    'email_SMTP_pswd' => '(set at private/send_mail-hidden.php)',
    'email_SMTP_encryption' => 'ssl',
    'email_SMTP_verifyCert' => false
));

// See:      'private/send_mail-sample.php'
require_once 'private/send_mail-hidden.php';

?>

</body>
</html>
