<?php


require_once(__ROOT__ . "php/lib/aixmodel.php");


/**
 *	Class to manage the aixada_account(s). Standard accounts serve for keeping track of purchases even if this information
 *	is never retrieved and displayed. This module/class is mainly needed if cash transations are made in the cooperative,
 *	i.e. people pay with cash when they buy or providers are paid through this plaform internal accounts.
 *	This has nothing to do with eventual external "real" bank accounts; however external movements could be mirrored 
 *	in any additional account here. 
 *
 *	In general, household accounts are composed by taking their id (e.g. 82) and adding 1000: thus household 82 
 *	has account 1082. 
 *
 *	Shared accounts are numbered negatively: 
 *
 */
	
class Account extends Aixmodel {


	/**
	 *	@var $accnum The current account number
	 */ 
	private $accnum; 


	/**
	 *	@var $op_id The operator_id doing the transactions
	 */
	public $op_id; 




	/**
	 *	
	 *	@param int $accnum The account number. Account or uf number
	 *	@param int $op_id The operator id for any subsequent transactions; this is the session_user_id. 
	 */
	public function __construct($accnum, $op_id){

		$this->set_account($accum);

		if ($op_id > 0){
			$this->op_id = $op_id; 
		} else {
			throw new Exception("Account exception: no operator id given!");
		}

		parent::__construct("aixada_account");
	}



	/**
	 *	Sets the current account number, performing sanity checks along the way: does this account exist at all?
	 *	@param int $accnum The account or of number. 
	 */
	public function set_account($accnum){

		//definitly needs to be numeric
		if (is_numeric($accnum)){
			$accnum = $this->construct_account($accnum);
		} else {
			throw new Exception("Account exception: account number needs to be numeric");
		}

		$db = DBWrap::get_instance();
		$rs = $db->squery('account_exists', $accnum);
  		$db->free_next_results();
		 
 		$row = $rs->fetch_array();
		
		//if we have one result
		if ($row[0] == 1){
			$this->accnum = $accnum;
		} else {
			throw new Exception("Account number '{$account_number}' does not exists!");
			exit;
		}
	}


	/**
	 *	@param bool $inactive If set to true lists accouns of all UFs, including inactive ones, otherwise only active ufs are listed
	 */
	public static function list_accounts($inactive){

	  $strXML = '<accounts>'
	    . '<row><id f="id">-3</id><name f="name">Caixa</name></row>'
	    . '<row><id f="id">-2</id><name f="name">Consum</name></row>'
	    . '<row><id f="id">-1</id><name f="name">Manteniment</name></row>';	
	  $sqlStr = ($inactive)? "SELECT id+1000, id, name FROM aixada_uf":"SELECT id+1000, id, name FROM aixada_uf where active=1";  
	  $rs = DBWrap::get_instance()->Execute($sqlStr);
	  
	  while ($row = $rs->fetch_array()) {
	    $strXML 
	      .= '<row>'
	      . '<id f="id">' . $row[0] . '</id>'
	      . '<name f="name"><![CDATA[UF ' . $row[1] . ' ' . $row[2] . ']]></name>'
	      . '</row>';
	  }
	  return $strXML . '</accounts>';
	}



	public function getBalance(){
		if ($this->accnum <= 0){
			throw new Exception("Account number is not set. Can't retrieve current balance;");
			exit;
		}
		
		return do_stored_query('get_account_balance', $this->accnum);
	}



	public function deposit($quantity, $description='', $operator_id, $payment_method_id=7, $currency_type_id=1){
		
		if ($quantity <= 0) {
			throw new Exception("Deposit amount needs to be larger than zero!");
			exit; 
		}

		if ($this->accnum == 0){
			throw new Exception("Deposit error: account number is not set: {$this->accnum}");
			exit;
		}

		do_stored_query('move_money',$quantity,  $this->accnum, $payment_method_id, $operator_id, $description, $currency_type_id);

	}



	public function withdraw($quantity, $description='', $operator_id, $payment_method_id=10, $currency_type_id=1){

		if ($quantity <= 0) {
			throw new Exception("Withdraw amount cannot be negative");
			exit; 
		}

		if ($this->accnum == 0){
			throw new Exception("Withdraw error: account number is not set: {$this->accnum}");
			exit;
		}


		do_stored_query('move_money',-$quantity,  $this->accnum, $payment_method_id, $operator_id, $description, $currency_type_id);

	}


		/**
	 *	Deposit cash into cashbox by HU/UF. Will be registered
	 *	in cashbox account (-3) and HU account. 
	 */
	public function deposit_cash_for_uf($quantity, $uf_or_account, $description=''){

		$account_nr = $this->construct_account($uf_or_account);
		
		$this->set_account($account_nr);
		$desc = ($description == '')? "Cash deposit":$description;

		//register deposit in UF account
		$this->deposit($quantity, $desc, $this->operator_id);

		//register deposit in cashbox
		$desc = ($description == '')? "Cash deposit for account ".$account_nr:$description;
		$this->set_account(-3);
		$this->deposit($quantity, $desc, $this->operator_id);
	}



	/**
	 *	Deposit cash into caixa. This is generic function which should have 
	 *	a short description of purpose. 
	 */
	public function deposit_cash($quantity, $description){

		if ($description == ""){
			throw new Exception("Account movement warning: should write a short comment on the type of deposit!");
			exit; 
		} 

		//register deposit in cashbox
		$this->set_account(-3);
		$this->deposit($quantity, $description, $this->operator_id);
	}



	/**
	 *	Make a cash deposit into the banc account of the money that
	 *  was taken at the end of the turn from the cashbox
	 */
	public function deposit_sales_cash($quantity, $description){
		$this->set_account(-2);
		$description = ($description == '')? "Deposit cash from end of torn ":$description;
		$this->deposit($quantity, $description, $this->operator_id);
	}




	/**
	 *	Withdraw cash from cashbox in order to pay provider
	 */
	public function pay_provider_cash($quantity, $provider_id=0, $description){
		//register withdrawal from cashbox
		$this->set_account(-3);
		$description = ($description == '')? "Cash payment for provider ":$description;
		$this->withdraw($quantity, $description, $this->operator_id);

		//could register transfer here in order to keep track of what was paid
		//when to each provider. 		
	}
	
	
	/**
	 *	Transfer money from banc account to pay provider
	 */
	public function pay_provider_bank($quantity, $provider_id=0, $description){
		//register withdrawal from cashbox
		$this->set_account(-2);
		$description = ($description == '')? "Payment for provider ":$description;
		$this->withdraw($quantity, $description, $this->operator_id);

		//could register transfer here in order to keep track of what was paid
		//when to each provider. 		
	}



	/**
	 *	Generic withdraw function for cashbox
	 */
	public function withdraw_cash($quantity, $description){

		if ($description == ""){
			throw new Exception("Account movement warning: should write a short comment on the type of withdrawal!");
			exit; 
		} 

		//withdraw from cashbox
		$this->set_account(-3);
		$this->withdraw($quantity, $description, $this->operator_id);
	
	}



	/**
	 *	register money that is taken at the end of the turn and deposited
	 * 	in the banc account. 
	 */
	public function withdraw_cash_for_bank($quantity, $description){
		$description = ($description == '')? "Withdraw cash to deposit in bank":$description;
		
		//withdraw from cashbox
		$this->set_account(-3);
		$this->withdraw($quantity, $description, $this->operator_id);

		//register in bank account automatically?

	}


	/**
	 *	HU withdraws money from their account. Needs to be paid from cashbox. 
	 */
	public function withdraw_cash_from_uf_account($quantity, $uf_or_account, $description=''){
		$desc = ($description == '')? "Cash withdrawal":$description;
		
		//withdraw from HU account
		$this->set_account($this->construct_account($uf_or_account));
		$this->withdraw($quantity, $desc, $this->operator_id);

		//and register in cashbox
		$this->set_account(-3);
		$desc = ($description == '')? ("Cash withdrawal for HU ".$uf_or_account):$description;
		$this->withdraw($quantity, $desc, $this->operator_id);

	}

	/** 
	 *	HU withdraws member quota. Needs to be registered as withdrawal from cashbox
	 * 	but not from HU account (where the initial 50 euro deposit is not)
	 */	
	public function withdraw_member_quota($quantity, $uf_or_account, $description=''){
		$desc = ($description == '')? "Member quota withdrawal":$description;
		
		//$this->set_account($this->construct_account($uf_or_account));
		//$this->withdraw(50, $desc, $this->operator_id);

		$this->set_account(-3);
		$desc = ($description == '')? ("Member quota withdrawal for account " . $uf_or_account . "!"):$description;
		
		$this->withdraw($quantity, $desc, $this->operator_id);
	}




	public function construct_account($uf_or_account){
		return ($uf_or_account < 1000)? ($uf_or_account+1000):$uf_or_account;
	}



	/**
	 * 
	 * retrieves list of UFs with negative account balance
	 */
	function get_negative_accounts()
	{
	    $strXML = '<negative_accounts>';
	    $rs = do_stored_query('negative_accounts');
	    while ($row = $rs->fetch_assoc()) {
	        $strXML .= '<account>';
	        foreach ($row as $field => $value) {
	            $strXML .= "<{$field}><![CDATA[{$value}]]></{$field}>";
	        }
	        $strXML .= '</account>';
	    }
	    $strXML .= '</negative_accounts>';
	    return $strXML;
	}




}

?>