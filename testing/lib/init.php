<?php
    $user = readline('mysql username with sufficient privileges [default=root]:');
    if ($user == '') $user = 'root';
    echo "password for $user: ";
    $pwd = preg_replace('/\r?\n$/', '', `stty -echo; head -n1 ; stty echo`);
    echo "\n";

    $handle = @fopen('/tmp/init_user.sql', 'w');
    $script = <<<EOD
create user 'dumper'@'localhost' identified by 'dumper';
grant all privileges on {$dump_db_name}.* to 'dumper'@'localhost';
grant select on aixada.* to 'dumper'@'localhost';
flush privileges;

EOD
    ;
    fwrite ($handle, $script);
    fclose($handle);
    echo $script;
    $result = exec("mysql -u $user --password=$pwd $dump_db_name < /tmp/init_user.sql");
?>