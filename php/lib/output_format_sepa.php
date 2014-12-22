<?php


require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);


require_once(__ROOT__ . 'php/lib/output_format.php');


require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/PaymentInformation.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/GroupHeader.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/Exception.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/Util/StringHelper.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/DomBuilder/DomBuilderInterface.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/TransferInformation/TransferInformationInterface.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/TransferFile/TransferFileInterface.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/TransferFile/Facade/CustomerTransferFileFacadeInterface.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/DomBuilder/BaseDomBuilder.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/TransferInformation/BaseTransferInformation.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/TransferFile/Facade/BaseCustomerTransferFileFacade.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/TransferFile/BaseTransferFile.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/DomBuilder/CustomerDirectDebitTransferDomBuilder.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/DomBuilder/DomBuilderFactory.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/DomBuilder/CustomerCreditTransferDomBuilder.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/Exception/Exception.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/Exception/InvalidTransferFileConfiguration.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/Exception/InvalidArgumentException.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/Exception/InvalidPaymentMethodException.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/Exception/InvalidTransferTypeException.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/TransferInformation/CustomerCreditTransferInformation.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/TransferInformation/CustomerDirectDebitTransferInformation.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/TransferFile/Factory/TransferFileFacadeFactory.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/TransferFile/Facade/CustomerDirectDebitFacade.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/TransferFile/CustomerDirectDebitTransferFile.php');
require_once(__ROOT__ . 'php/external/sepa/lib/Digitick/Sepa/TransferFile/CustomerCreditTransferFile.php');



/** 
 *	Custom class that accepts a mysqli_result and returns a SEPA file
 *	
 */
class output_format_sepa extends output_format{





	public function __construct($data){

		parent::__construct($data, "sepa");
	}




	/** 
	 *
	 *	Converts the mysqli result set into CSV string. Since we need the column names from the result set, 
	 *	we need to work always from the data_table representation and not the db result set directly. 
	 */
	public function format_rs(){

		if (!$this->rs_exists()){
			throw new Exception("SEPA format mysqli_result exception: result set is null");
		}

	
	} //end 




	/**
	 *
	 *	Converts the data_table representation to CSV.  Assumes that a valid 
	 *	data table exists. 
	 */
	public function format_data_table(){

		if (!$this->data_table_exists()){
			throw new Exception("SEPA format data table exception: data table is null");
		}


		//Set the initial information
		$directDebit = Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory::createDirectDebit('test123', 'Me');

		// create a payment, it's possible to create multiple payments,
		// "firstPayment" is the identifier for the transactions
		$directDebit->addPaymentInfo('firstPayment', array(
		    'id'                    => 'firstPayment',
		    'creditorName'          => 'My Company',
		    'creditorAccountIBAN'   => 'FI1350001540000056',
		    'creditorAgentBIC'      => 'PSSTFRPPMON',
		    'seqType'               => Digitick\Sepa\PaymentInformation::S_ONEOFF,
		    'creditorId'            => 'DE21WVM1234567890'
		));
		// Add a Single Transaction to the named payment
		$directDebit->addTransfer('firstPayment', array(
		    'amount'                => '500',
		    'debtorIban'            => 'FI1350001540000056',
		    'debtorBic'             => 'OKOYFIHH',
		    'debtorName'            => 'Their Company',
		    'debtorMandate'         =>  'AB12345',
		    'debtorMandateSignDate' => '13.10.2012',
		    'remittanceInformation' => 'Purpose of this direct debit'
		));

		$this->out_str = $directDebit->asXML();

		return $this->out_str; 

	}


}



?>