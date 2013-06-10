<?php


require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);



require_once(__ROOT__ . 'php/lib/exceptions.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/inc/database.php');



class account {


	protected $account = 0; 



	public function __construct($account_number = 0){

			$this->account = $account_number;
	}




	protected function set_account($account_number){
		//global $firephp; 
		//$firephp->log($account_number, "anumber");
		
		$rs = do_stored_query('account_exists', $account_number); 
 		$row = $rs->fetch_array();
 		
 		//$firephp->log($row, "row");
 		$nr = $row[0];

		DBWrap::get_instance()->free_next_results();

		if ($nr > 0){
			$this->account = $account_number;
		} else {
			throw new Exception("Account number '{$account_number}' does not exists!");
			exit;
		}
	}




	protected function getBalance(){
		if ($this->account <= 0){
			throw new Exception("Account number is not set. Can't retrieve current balance;");
			exit;
		}
		return do_stored_query('get_account_balance', $this->account);
	}



	protected function deposit($quantity, $description='', $operator_id, $payment_method_id=7, $currency_type_id=1){
		
		if ($quantity <= 0) {
			throw new Exception("Deposit amount needs to be larger than zero!");
			exit; 
		}

		if ($this->account == 0){
			throw new Exception("Deposit error: account number is not set: {$this->account}");
			exit;
		}

		do_stored_query('move_money',$quantity,  $this->account, $payment_method_id, $operator_id, $description, $currency_type_id);

	}



	protected function withdraw($quantity, $description='', $operator_id, $payment_method_id=10, $currency_type_id=1){

		if ($quantity <= 0) {
			throw new Exception("Withdraw amount cannot be negative");
			exit; 
		}

		if ($this->account == 0){
			throw new Exception("Withdraw error: account number is not set: {$this->account}");
			exit;
		}


		do_stored_query('move_money',-$quantity,  $this->account, $payment_method_id, $operator_id, $description, $currency_type_id);

	}

}

?>