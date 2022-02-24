<?php
// Send using Symfony-mailer
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

function send_symfony_mail($from, $reply_to, $to, $subject, $messageHTML, $options) {

    // 'email_SMTP_encryption': It is only used to determine if it has value or not
    //      See: https://www.ionos.es/digitalguide/servidores/seguridad/ssl-y-tls/
    $dsn = (get_config('email_SMTP_encryption') ? 'smtps://' : 'smtp://') . 
            get_config('email_SMTP_user', $from) . ':' .
            get_config('email_SMTP_pswd') . '@' .
            get_config('email_SMTP_host') . ':' .
            get_config('email_SMTP_port', 25);
    if (get_config('email_SMTP_encryption')) {
        if (!get_config('email_SMTP_verifyCert')) {
            $dsn .= '?verify_peer=0';
        }
    }

    // To understand how to send an email and that's it!
    // See https://www.reddit.com/r/PHPhelp/comments/sux8lh/symphony_how_do_i_send_a_mail_in_a_functional_way/
    
    // Mailer
    $transport = Transport::fromDsn($dsn);
    $mailer = new Mailer($transport);

    // Message
    $message = (new Email())
        ->subject($subject)
        ->html($messageHTML);

    // from
    try {
        $message->from($from);
    } catch(Exception $e) {
        throw new Exception("config['admin_email']=\"{$from}\" is not a valid email.");
    } 

    // Set addresses
    $cleanExplode = function($text) {
        return array_map('trim', explode(',', $text));
    };
    if ($to) {
        try {
            $message->to(...$cleanExplode($to));
        } catch(Exception $e) {
            echo $e->getMessage();
            throw new Exception("send_mail to: \"{$to}\" is not a list of valid emails.");
        }
    }
    if ($reply_to) {
        try {
            $message->replyTo(...$cleanExplode($reply_to));
        } catch(Exception $e) {
            throw new Exception("send_mail reply_to: {$reply_to} is not a list of valid emails.");
        }
    }
    if (isset($options['cc']) && $options['cc']) {
        try {
            $message->cc(...$cleanExplode($options['cc']));
        } catch(Exception $e) {
            echo $e->getMessage();
            throw new Exception("send_mail cc: {$options['cc']} is not a list of valid emails.");
        }
    }
    if (isset($options['bcc']) && $options['bcc']) {
        try {
            $message->bcc(...$cleanExplode($options['bcc']));
        } catch(Exception $e) {
            throw new Exception("send_mail bcc: {$options['bcc']} is not a list of valid emails.");
        }
    }

    // Send the message
    try {
        $mailer->send($message);
        return true;
    } catch(Exception $e) {
        return false;
    }
}
?>
