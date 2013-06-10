<?php


require_once(__ROOT__ . 'php/lib/account.php');



class account_movement extends account{


	protected $operator_id = 0; 


	protected $currency_id = 1; 



	public function __construct ($operator_id, $currency=1, $account=0){


		if ($operator_id == 0){
			throw new Exception("Account movement error: operator cannot be 0. This should be the id of the logged user!");
			exit; 
		} 

		$this->operator_id = $operator_id; 

		$this->currency_id = $currency; 

		parent::__construct($account);

	}



	/**
	 *	Deposit cash into cashbox by HU/UF. Will be registered
	 *	in cashbox account (-3) and HU account. 
	 */
	public function deposit_cash_for_uf($quantity, $uf_or_account, $description=''){

		$this->set_account($this->construct_account($uf_or_account));
		$desc = ($description == '')? "Cash deposit":$description;

		//register deposit in UF account
		$this->deposit($quantity, $desc, $this->operator_id);

		//register deposit in cashbox
		$desc = ($description == '')? "Cash deposit by HU":$description;
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
	public function pay_provider_cash($quantity, $provider_id, $description){
		//register withdrawal from cashbox
		$this->set_account(-3);
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
	public function withdraw_cash_from_uf_account($quantity, $description, $uf_or_account){
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
	public function withdraw_member_quota($description, $uf_or_account){
		$desc = ($description == '')? "Member quota withdrawal":$description;
		
		//$this->set_account($this->construct_account($uf_or_account));
		//$this->withdraw(50, $desc, $this->operator_id);

		$this->set_account(-3);
		$desc = ($description == '')? ("Member quota withdrawal for HU ".$uf_or_account):$description;
		
		$this->withdraw(50, $desc, $this->operator_id);
	}




	public function construct_account($uf_or_account){
		return ($uf_or_account < 1000)? ($uf_or_account+1000):$uf_or_account;
	}




}



?>