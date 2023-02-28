<?php
$seed = substr(md5(rand()), 0, 7);
echo crypt($argv[1], "$1$".$seed."$");
