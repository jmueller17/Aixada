<?php
put_config(array(
    'admin_email' => 'your-user@your-host.com',
    'email_SMTP_host' => 'mail.your-host.com',
    'email_SMTP_pswd' =>  'your-password'
));

echo send_test(
    'some-user@gmail.com', 
    array(
        'cc' => 'another-user@gmail.com,yet-another-user@gmail.com',
        'bcc' => 'and-another-user@gmail.com'
    )
);

?>
