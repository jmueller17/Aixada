<?php
if (configuration_vars::get_instance()->allow_negative_balances === false) {
    include("js/aixadautilities/jquery.aixadaNegativeBalance.js.php");
}

