<?php

function allowed_negative_balances()
{
    return isset(configuration_vars::get_instance()->allow_negative_balances) && configuration_vars::get_instance()->allow_negative_balances === true;
}

function negative_balances_grace_periode()
{
    return isset(configuration_vars::get_instance()->negative_balances_grace_periode) ? (int) configuration_vars::get_instance()->negative_balances_grace_periode : 14;
}

function get_negative_balances_disabled_pages()
{
    return isset(configuration_vars::get_instance()->negative_balances_disabled_pages) ? configuration_vars::get_instance()->negative_balances_disabled_pages : array();
}

function include_negative_balances_js()
{
    $disabled_pages = get_negative_balances_disabled_pages();
    if (in_array(substr($_SERVER['SCRIPT_NAME'], 1), $disabled_pages)) {
        ob_start();
        require('js/aixadautilities/negativeBalances.js.php');
        $script = ob_get_contents();
        ob_end_clean();
        return $script;
    }
}

function negative_balances_stagewrap_class()
{
    $disabled_pages = get_negative_balances_disabled_pages();
    return in_array(substr($_SERVER['SCRIPT_NAME'], 1), $disabled_pages) ? 'hidden' : '';
}
