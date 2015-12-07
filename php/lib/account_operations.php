<?php
require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . 'php/lib/account_operations.config.php');

class account_operations {
    protected $cfg_use_providers;
    public $use_transaction;
    
    public function __construct ($lang='') { // TODO: Use a lang other than User. 
        $cfg_accounts = get_config('accounts', array());
        $this->cfg_use_providers = isset($cfg_accounts['use_providers']) ? 
                true : false;
        $this->use_transaction = true;
    }
    
    // --------------------------------------------
    // READ
    // --------------------------------------------

    public function uses_providers() {
        return $this->cfg_use_providers;
    }

    public function latest_movements_XML($limit, $account_types) {
		return rs_XML_fields(
			$this->latest_movements_rs($limit, $account_types),
			cnv_config_formats(array(
				'id' =>         '',
				'account_id' => '',
				'ts'=>          'datetime',
				'quantity' =>   'amount',
				'balance' =>    'amount'
			))
		);
	}
    protected function latest_movements_rs($limit, $account_types) {
        $sql = array();
        $db = DBWrap::get_instance();
        $filter = $this->get_account_types_filter($account_types);
        if ($filter['account_types']) {
            array_push($sql, "(
                select a.id, a.account_id, time(a.ts) as time, a.ts,
                    a.quantity, balance,
                    p.description as method,
                    c.name as currency, 
                    ad.description as account_name,
                    -- 'uf_id' use for reasons of compatibility
                    concat('A',account_id) as uf_id
                from aixada_account a
                join (
                    aixada_account_desc ad,
                    aixada_currency c,
                    aixada_payment_method p
                )
                on 
                    a.currency_id = c.id
                    and a.payment_method_id = p.id
                    and a.account_id = -ad.id
                where account_id < 1000
                and ad.account_type in(".$filter['account_types'].") )");
        }
        if ($filter['show_uf']) {
            array_push($sql, "(
                select a.id, a.account_id, time(a.ts) as time, a.ts,
                    a.quantity, balance,
                    p.description as method,
                    c.name as currency, 
                    concat(uf.name,'(',uf.id,')') as account_name,
                    -- 'uf_id' use for reasons of compatibility
                    concat(uf.id,' ',uf.name) as uf_id 
                from aixada_account a
                left join aixada_currency c       on a.currency_id = c.id
                left join aixada_payment_method p on a.payment_method_id = p.id
                left join aixada_uf uf            on a.account_id - 1000 = uf.id
                where account_id between 1000 and 1999)");
        }
        if ($filter['show_providers']) {
            array_push($sql, "(
                select a.id, a.account_id, time(a.ts) as time, a.ts,
                    a.quantity, balance,
                    p.description as method,
                    c.name as currency, 
                    concat(prv.name,'(',prv.id,')') as account_name,
                    -- 'uf_id' use for reasons of compatibility                    
                    concat(prv.name,'(',prv.id,')') as uf_id
                from aixada_account a
                left join aixada_currency c       on a.currency_id = c.id
                left join aixada_payment_method p on a.payment_method_id = p.id
                left join aixada_provider prv     on a.account_id - 2000 = prv.id
                where account_id between 2000 and 2999)");
        }
        if (count($sql) != 0) {
            $sql2 = implode($sql, ' union ').
                                   " order by ts desc, id desc limit {$limit};";
            return $db->Execute($sql2);
        } else	{
			return null;
		}
    }
    
    /**
     * 
     * Retrieves list of accounts
     * @param boolean $all if set to true, list active and non-active accounts. when set to false, list only active UFs
     */
    public function get_accounts_XML($all=0, $account_types) {
        $filter = $this->get_account_types_filter($account_types);
        // start XML
        $strXML = '';
        // Specific accounts
        if ($filter['account_types']) {
            $sqlStr = "SELECT -id id, description name
				FROM aixada_account_desc ad
				where account_type in(".implode(',', $account_types).")";
            $sqlStr .= $all ? "" :" and active=1";
            $sqlStr .= " order by ad.id";
            $strXML .= query_to_XML($sqlStr);
        }    
        // UF accounts
        if ($filter['show_uf_generic']) {
            $strXML .= array_to_XML(array(
                'id'    => 1000,
                'name'  => i18n('mon_all_active_uf')
            ));            
        }
        if ($filter['show_uf']) {
            $sqlStr = "SELECT id+1000 id, concat(id,' ',name) name FROM aixada_uf";
            $sqlStr .= $all ? "" :" where active=1";
            $sqlStr .= " order by id";
            $strXML .= query_to_XML($sqlStr);
        }
        // Providers
        if ($filter['show_providers']) {
            $sqlStr = "SELECT id+2000 id, concat(name,'#',id) name FROM aixada_provider";
            $sqlStr .= $all ? "" :" where active=1";
            $sqlStr .= " order by id";
            $strXML .= query_to_XML($sqlStr);
        }
        return '<accounts>'.$strXML.'</accounts>';
    }
	
	/**
	 *
	 */
    public function get_uf_balances_XML($all, $negative) {
		return rs_XML_fields(
			$this->get_uf_balances_rs($all, $negative),
			cnv_config_formats(array(
				'last_update'=> 'datetime',
				'balance' => 'amount'
			))
		);
	}
	protected function get_uf_balances_rs($all, $negative) {
		$sql = 
			"select
				a.account_id,
				uf.id as uf, uf.name, 
				a.balance, a.ts as last_update 
			from (select 
					account_id, max(id) as MaxId 
					from aixada_account group by account_id) r		
			join (aixada_account a, aixada_uf uf)
			on a.account_id = r.account_id 
				and a.id = r.MaxId
				and uf.id = a.account_id -1000";
		$sql_where = "";
		$sql_where .= $all ? "" :" and uf.active=1";
		$sql_where .= $negative ? " and a.balance < 0" : "";
		if ($sql_where !== "") {
			$sql .= " where ".substr($sql_where,5);
		}
		$sql .= " order by ";
		if ($negative) {
			$sql .= "a.balance;";
		} else {
			$sql .= "uf.id;";
		}
		return DBWrap::get_instance()->Execute($sql);
	}
		
	/**
	 *
	 */
    public function get_provider_balances_XML($all, $negative) {
		return rs_XML_fields(
			$this->get_provider_balances_rs($all, $negative),
			cnv_config_formats(array(
				'last_update'=> 'datetime',
				'balance' => 'amount'
			))
		);
	}
	protected function get_provider_balances_rs($all, $negative) {
		$sql = 
			"select
				a.account_id,
				prv.id as prv_id, prv.name, 
				a.balance, a.ts as last_update 
			from (select 
					account_id, max(id) as MaxId 
					from aixada_account group by account_id) r		
			join aixada_account a
			on a.account_id = r.account_id 
				and a.id = r.MaxId
			left join aixada_provider prv
			on a.account_id - 2000 = prv.id";
		$sql_where = "";
		$sql_where .= $all ? "" :" and prv.active=1";
		$sql_where .= $negative ? " and a.balance < 0" : "";
		if ($sql_where !== "") {
			$sql .= " where ".substr($sql_where,5);
		}
		$sql .= " order by ";
		if ($negative) {
			$sql .= "a.balance;";
		} else {
			$sql .= "prv.id;";
		}
		return DBWrap::get_instance()->Execute($sql);
	}
	
    public function get_balances_XML($account_types) {
		$formats = cnv_config_formats(array(
			'account_id' => '',
			'balance' => 'amount',
			'result' => 'amount',
			'last_update' => 'datetime'
		));
        $strXML = '';
        $result = 0;
        $rs = $this->balances_rs($account_types);
        while ($row = $rs->fetch_assoc()) {
            $result += 
                    $row['account_id'] < 0 ? $row['balance'] : -$row['balance'];
            $strXML .= array_to_XML(
				array(
                    'account_id' => $row['account_id'],
                    'name' => $row['name'].(
                        $row['account_id'] > 1 ? '#'.$row['account_count'] : ''
                    ),
                    'balance' => $row['balance'],
                    'result' => $result,
                    'last_update' => $row['last_update']
				), $formats
			);
        }
        $rs->free();
        return '<rowset>'.$strXML.'</rowset>';
    }
    
    protected function get_balances_filter($account_types) {
        $where_array = array();
        $filter = $this->get_account_types_filter($account_types);
        if ($filter['show_uf']) {
            array_push($where_array," a.account_id between 1000 and 1999"); 
        }
        if ($filter['show_providers']) {
            array_push($where_array," a.account_id between 2000 and 2999"); 
        }
        if ($filter['account_types']) {
            array_push($where_array,
                " ad.account_type in(".$filter['account_types'].")"
            ); 
        }
        return ( count($where_array) > 0 ? 
                '( '.implode($where_array, ' or ').' )' : '1=0' );
    }
    /**
     * There are five types: 
     *    * 1 service, 2 treasury, 
     *    * 1000 ufs, 1999 selection of all active uf,
     *    * 2000 providers
     */
    protected function get_account_types_filter($account_types) {
        $response = array(
            'show_uf' => false,
            'show_uf_generic' => false,
            'show_providers' => false,
            'account_types' => null
        );
        $_key = array_search(1000, $account_types, true);
        if ($_key !== false) {
            array_splice($account_types, $_key, 1);
            $response['show_uf'] = true;
        }
        $_key = array_search(1999, $account_types, true);
        if ($_key !== false) {
            array_splice($account_types, $_key, 1);
            $response['show_uf_generic'] = true;
        }
        $_key = array_search(2000, $account_types, true);
        if ($_key !== false) {
            array_splice($account_types, $_key, 1);
            if ($this->cfg_use_providers) {
                $response['show_providers'] = true;
            }
        }
        if (count($account_types)) {
            $response['account_types'] = implode(',', $account_types);
        }
        return $response;
    }
    protected function balances_rs($account_types) {
        $sql = "
            select account_group_id account_id, account_desciption name,
                count(*) account_count,
                sum(aa.balance) as balance,
                max(aa.ts) as last_update 
            from (
				select 
					case 
						when account_id < 0 then account_id
						when account_id between 1000 and 1999 then 1000
						when account_id between 2000 and 2999 then 2000
						else 0
					end as account_group_id,
					case 
						when a.account_id < 0 then -account_id
						when a.account_id between 1000 and 1999 then 1000
						when a.account_id between 2000 and 2999 then 2000
						else 0
					end as account_group_or,
					case 
						when account_id < 0 then ad.description
						when account_id between 1000 and 1999 then 'UFs'
						when account_id between 2000 and 2999 then 'Providers'
						else '??'
					end as account_desciption,
					account_id,
					max(a.id) as MaxId 
				from aixada_account a
				left join aixada_account_desc ad on account_id = -ad.id
				where ".$this->get_balances_filter($account_types)."
				group by account_id ) r,
                aixada_account aa
            where 
                r.account_id = aa.account_id
                and aa.id = r.MaxId
            group by account_group_id, account_group_or, account_desciption
            order by account_group_or;";
        return DBWrap::get_instance()->Execute($sql);
    }
	
	public function get_income_spending_XML($date, $account_types) {
		return rs_XML_fields(
			$this->get_income_spending_rs($date, $account_types),
			cnv_config_formats(array(
				'account_id' => '',
				'income' => 'amount',
				'spending' => 'amount',
				'balance' => 'amount'
			))
		);
	}
    protected function get_income_spending_rs($date, $account_types) {
        $sql = "
            select account_group_id account_id,
					concat(account_desciption,'#',count(*)) name, 
					sum(case when quantity_r > 0 then quantity_r
							 else 0 end) as income,				
					sum(case when quantity_r < 0 then quantity_r
							 else 0 end) as spending,
					sum(quantity_r) as balance   
            from (   
				select 
					quantity quantity_r,
					case 
						when a.account_id < 0 then account_id
						when a.account_id between 1000 and 1999 then 1000
						when a.account_id between 2000 and 2999 then 2000
						else 0
					end as account_group_id,
					case 
						when a.account_id < 0 then -account_id
						when a.account_id between 1000 and 1999 then 1000
						when a.account_id between 2000 and 2999 then 2000
						else 0
					end as account_group_or,
					case 
						when a.account_id < 0 then ad.description
						when a.account_id between 1000 and 1999 then 'UFs'
						when a.account_id between 2000 and 2999 then 'Providers'
						else '??'
					end as account_desciption
				from aixada_account a
				left join aixada_account_desc ad on a.account_id = -ad.id
				where a.ts between '{$date}' and date_add('{$date}', interval 1 day)
					and ".$this->get_balances_filter($account_types).") r
            group by account_group_id, account_group_or, account_desciption
            order by account_group_or;";
        return DBWrap::get_instance()->Execute($sql);
	}

	/**
 * 
 * produces an extract of the money movements for the selected account and time-period
 * @param unknown_type $account_id
 * @param unknown_type $filter
 * @param unknown_type $from_date
 * @param unknown_type $to_date
 */
function get_account_extract_XML($account_id, $filter, $from_date, $to_date) {
	
	$today = date('Y-m-d', strtotime('Today'));
	$tomorrow = date('Y-m-d', strtotime('Today + 1 day'));
	$prev_2month = date('Y-m-d', strtotime('Today - 2 month'));
	$prev_year	 = 	date('Y-m-d', strtotime('Today - 13 month'));
	
	$very_distant_future = '9999-12-30';
	$very_distant_past	= '1980-01-01';
	
	$account_id = (0< $account_id and $account_id < 1000)? $account_id+1000:$account_id;
	
	$formats = cnv_config_formats(array(
		'id' => '',
		'account_id' => '',
		'balance' => 'amount',
		'quantity' => 'amount',
		'ts' => 'datetime'
	));
	
	switch ($filter) {
	// all orders where date_for_order = today
	case 'past2Month':
		return rs_XML_fields(
			do_stored_query('get_extract_in_range', $account_id,
				$prev_2month, $tomorrow),
			$formats);
	case 'pastYear':
		return rs_XML_fields(
			do_stored_query('get_extract_in_range', $account_id,
				$prev_year, $tomorrow),
			$formats);
	case 'today':
		return rs_XML_fields(
			do_stored_query('get_extract_in_range', $account_id,
				$today, $tomorrow),
			$formats);
	case 'exact':
		return rs_XML_fields(
			do_stored_query('get_extract_in_range', $account_id,
				$from_date, $to_date),
			$formats
		);
	case 'all':
		return rs_XML_fields(
			do_stored_query('get_extract_in_range', $account_id, 
				$very_distant_past, $very_distant_future),
			$formats
		);
	default:
		throw new Exception("account_extract: param={$filter} not supported");  
		break;
	}
}
	
    // --------------------------------------------
    // ACTIONS
    // --------------------------------------------
    public function add_operation(
                       $account_operation, $accounts, $quantity, $description) {
        global $config_account_operations;
        $_operations = $config_account_operations;
        $_currency_type_id = get_config('currency_type_id',1);

        // chk account_operation
        if (!isset($_operations[$account_operation])) {
            throw new Exception(
              "&account_operation=\"{$account_operation}\" is not configured.");
            exit; 
        }
        $cfg_operation = $_operations[$account_operation]['accounts'];
        
        // chk Amount decimals
        if ($quantity != floor(round($quantity*100, 6))/100) {
            throw new Exception(i18n('mon_war_decimals'));
            exit; 
        }

        // chk accounts and set description if not present
		$op_descr = $description;
        foreach ($cfg_operation as $account_id_name => $o_params) {
            // chk Amount getter that 0
            if ($o_params['sign']) {
                if (!$quantity || $quantity <= 0) {
                    throw new Exception(i18n('mon_war_gt_zero'));
                    exit; 
                }
            }
            if (!isset($accounts[$account_id_name.'_id'])) {
                throw new Exception(i18n('mon_war_accounts_not_set'));
                exit;
            }
            if ($accounts[$account_id_name.'_id']==1000 && 
                    count($cfg_operation)>1) {
                throw new Exception(i18n('mon_war_no_all_hu',
                    array('mon_all_active_uf'=>'"'.i18n('mon_all_active_uf').'"')
                ));
                exit;
            }
            if ($description == '' && isset($o_params['default_desc'])) {
				$op_descr = i18n('mon_desc_'.$o_params['default_desc']);
			}
        }
		if ($op_descr == '') {
			throw new Exception(i18n('mon_war_description'));
			exit;
		}
        
        // All ok!, so do movements
        $success_count = 0;
		$r_replace = array(
			'comment' => $description !== '' ? '"'.$description.'"' : '');
		foreach ($accounts as $account_id_name => $account_id_value) {
			$r_replace[$account_id_name] = $account_id_value;
		}
		$db = DBWrap::get_instance();
		try {
			if ($this->use_transaction) {
				$db->start_transaction();
			}
			foreach ($cfg_operation as $account_id_name => $o_params) {
				$_account_id = $accounts[$account_id_name.'_id'];
				if (isset($o_params['auto_desc'])) {
					$item_description = i18n(
						'mon_desc_'.$o_params['auto_desc'],$r_replace);
				} else {
					$item_description = $op_descr;
				}
				$sign = $o_params['sign'];
				if ($sign === 0) {
					if ( $this->correct_balance($db,
							$_account_id, $o_params['method_id'], 
							round($quantity, 2), 
							$item_description,
							$_currency_type_id) ) {
						$success_count++;
					}
				} else {
					if ($_account_id == 1000) { // All active UF is 1000!
						$rs = $db->Execute(
						   "select id from aixada_uf where active = 1 order by id");
						while ($row = $rs->fetch_assoc()) {
							if ( $this->add_movement($db,
										$row['id']+1000, $o_params['method_id'], 
										round($sign * $quantity, 2), 
										$item_description,
										$_currency_type_id) ) {
								$success_count++;
							}
						}
						$db->free_next_results();
					} else {
						if ( $this->add_movement($db,
									$_account_id, $o_params['method_id'], 
									round($sign * $quantity, 2), 
									$item_description,
									$_currency_type_id) ) {
							$success_count++;
						}
					}
				}
			}
			if ($this->use_transaction) {
				$db->commit();
			}
		} catch (Exception $e) {
			if ($this->use_transaction) {
				$db->rollback();
			}
			throw new Exception($e->getMessage());
		} 
        return i18n('mon_success', array('count'=>$success_count));
    }
	
	private function add_movement($db, $account_id, $method_id, 
			$quantity, $description, 
			$currency_type_id) {
		$current_balance = 
			$this->chk_account_balance($db, $account_id, $currency_type_id);
		return $db->Insert(array(
			'table' => 'aixada_account', 
			'account_id' => $account_id,
			'quantity' => $quantity,
			'balance' => $current_balance + $quantity,
			'payment_method_id' => $method_id,
			'description' => $description,
			'currency_id' => $currency_type_id,
			'operator_id' => get_session_user_id()	
        ));
    }
    
    private function correct_balance($db, $account_id, $method_id, 
			$new_balance, $description, 
			$currency_type_id) {
        $current_balance = 
			$this->chk_account_balance($db, $account_id, $currency_type_id);
		return $db->Insert(array(
			'table' => 'aixada_account', 
			'account_id' => $account_id,
			'quantity' => $new_balance - $current_balance,
			'balance' => $new_balance,
			'payment_method_id' => $method_id,
			'description' => $description,
			'currency_id' => $currency_type_id,
			'operator_id' => get_session_user_id()	
        ));
    }
    
    private function chk_account_balance($db, $account_id, $currency_type_id) {
        // Create account if not exist
		$row = get_row_query("
			select balance from aixada_account 
				where account_id = {$account_id}
				order by ts desc, id desc
				limit 1");
        if ($row) {
            return $row[0];
        } else {
            if ($db->Insert(array(
                'table' => 'aixada_account',
                'account_id' => $account_id, 
                'quantity' => 0, 
                'payment_method_id' => 11,
                'currency_id' => $currency_type_id, 
                'description' => 'setup',
                'operator_id' => get_session_user_id(),
                'balance' => 0
            ))) {
				return 0;
			} else {
				throw new Exception("Account setup {$account_id} failed");
				exit;
			}
        }
    }
}
?>
