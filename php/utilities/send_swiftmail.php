<?php
 // Send using swiftmailer
 function send_swiftmail($from, $reply_to, $to, $subject, $messageHTML, $options) {
    $cleanExplode = function($text) {
        return array_map('trim', explode(',', $text));
    };
    $transport = Swift_SmtpTransport::newInstance(
        get_config('email_SMTP_host'), 
        get_config('email_SMTP_port', 25)
    );
    $encryp = get_config('email_SMTP_encryption');
    if ($encryp) {
        if (!in_array($encryp, stream_get_transports())) {
            throw new Exception("config['email_SMTP_encryption']=\"{$encryp}\" is not supported on your hosting");
        }
        $transport->setEncryption($encryp);
        if (!get_config('email_SMTP_verifyCert')) {
            // See at the end of: https://github.com/swiftmailer/swiftmailer/issues/544
            $https['ssl']['verify_peer'] = false;
            $https['ssl']['verify_peer_name'] = false; // seems to work fine without this line so far
            $transport->setStreamOptions($https);
        }
    }
    if (get_config('email_SMTP_pswd')) {
        $transport
            ->setUsername(get_config('email_SMTP_user', $from))
            ->setPassword(get_config('email_SMTP_pswd'));
    }
    $mailer = Swift_Mailer::newInstance($transport);
    $message = Swift_Message::newInstance()
        ->setSubject($subject)
        ->setBody($messageHTML, 'text/html');
    try {
        $message->setFrom($from);
    } catch(Exception $e) {
        throw new Exception("config['admin_email']=\"{$from}\" is not a valid email");
    } 
    try {
        $message->setTo($cleanExplode($to));
    } catch(Exception $e) {
        throw new Exception("send_mail to: \"{$to}\" is not a list of valid emails.");
    }
    if ($reply_to) {
        try {
            $message->setReplyTo($cleanExplode($reply_to));
        } catch(Exception $e) {
            throw new Exception("send_mail reply_to: {$reply_to} is not a list of valid emails.");
        }
    }
    if (isset($options['cc']) && $options['cc']) {
        try {
            $message->setCc($cleanExplode($options['cc']));
        } catch(Exception $e) {
            throw new Exception("send_mail cc: {$options['cc']} is not a list of valid emails.");
        }
    }
    if (isset($options['bcc']) && $options['bcc']) {
        try {
            $message->setBcc($cleanExplode($options['bcc']));
        } catch(Exception $e) {
            throw new Exception("send_mail bcc: {$options['bcc']} is not a list of valid emails.");
        }
    }
    return !!$mailer->send($message);
}
?>
