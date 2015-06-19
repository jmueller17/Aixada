<?php
/*
 * Notes for translation:
 *  * Keys of $config_account_operations: 
 *      * Must exist on translation with the prefix 'mon_op_'
 *  * Sub-keys 'default_desc' and 'auto_desc':
 *      * The value of the key must exist on translation with the
 *        prefix 'mon_desc_'
 */
$config_account_operations = array(
    'deposit_uf' => array(
        'accounts' => array(
            'uf_from' => array('sign' => +1, 'method_id' => 7, // 7 deposit
                    'default_desc' => 'deposit_uf'), 
            'account_to' => array('sign' => +1, 'method_id' => 7, 
                    'auto_desc' => 'deposit_from_uf')
        )
    ),
    'deposit_others' => array(
        'correction' => true,
        'accounts' => array(
            'account_to' => array('sign' => +1, 'method_id' => 7) // 7 deposit
        )
    ),
    'debit_uf' => array(
        'accounts' => array(
            'uf_to' => array('sign' => -1, 'method_id' => 8) // 8 bill
        )
    ),
    'refund_uf' => array(
        'accounts' => array(
            'account_from' => array('sign' => -1, 'method_id' => 10,// 10 withdrawal
                    'auto_desc' => 'refund_to_uf'),
            'uf_to' => array('sign' => -1, 'method_id' => 10)
        )
    ),
    'pay_pr' => array(
        'accounts' => array(
            'account_from' => array('sign' => -1, 'method_id' => 10,// 10 withdrawal
                    'auto_desc' => 'payment_to_provider'), 
            'provider_to' => array('sign' => -1, 'method_id' => 10,
                    'default_desc' => 'payment')
        )
    ),
    'withdraw_others' => array(
        'accounts' => array(
            'account_from' => array('sign' => -1, 'method_id' => 10)// 10 withdrawal
        )
    ),
    'invoice_pr' => array(
        'accounts' => array(
            'provider_from' => array('sign' => +1, 'method_id' => 8, // 8 bill
                    'default_desc' => 'invoice')
        )
    ),
    'move' => array(
        'accounts' => array(
            'account_from' => array('sign' => -1, 'method_id' => 1, // 1 cash
                    'default_desc' => 'treasury_movement', 'auto_desc' => 'move_to'),
            'account_to' => array('sign' => +1, 'method_id' => 1,
                    'auto_desc' => 'move_from')
        )
    ),
    'correction' => array(
        'correction' => true,
        'accounts' => array(
            'account_to' => array('sign' => 0, 'method_id' => 9, // 9 correction
                    'default_desc' => 'correction')
        )
    )
    ,   
    'a_debit_uf' => array(
        'correction' => true,
        'accounts' => array(
            'uf_to' => array('sign' => +1, 'method_id' => 8) // 8 bill
        )
    ),
    'a_pay_pr' => array(
        'correction' => true,
        'accounts' => array(
            'provider_from' => array('sign' => +1, 'method_id' => 10,
                    'default_desc' => 'a_payment'),
            'account_to' => array('sign' => +1, 'method_id' => 10,// 10 withdrawal
                    'auto_desc' => 'a_payment_to_provider')
        )
    ),
    'a_invoice_pr' => array(
        'correction' => true,
        'accounts' => array(
            'provider_from' => array('sign' => -1, 'method_id' => 8, // 8 bill
                    'default_desc' => 'a_invoice')
        )
    )
);

?>
