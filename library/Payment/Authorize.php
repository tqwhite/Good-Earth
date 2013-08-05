<?php
namespace Payment;

require_once(dirname(__FILE__) . '/AuthorizeDistribution/AuthorizeNet.php');

class Authorize{

var $sale;

public function __construct(){

$authorizeSpecs=\Zend_Registry::get('authorize');


$this->sale = new \AuthorizeNetAIM($authorizeSpecs['AUTHORIZENET_API_LOGIN_ID'], $authorizeSpecs['AUTHORIZENET_TRANSACTION_KEY']);


$this->sale->setSandbox(false); //false means use the real server

}

public function setPurchaseData($inData){

	$nameArray=explode(' ',$inData['cardData']['cardName']);
	$firstName=$nameArray[0];
	$lastName=$nameArray[1];

	$outArray=array(
		'address'=>$inData['cardData']['street'],
		'city'=>$inData['cardData']['city'],
		'state'=>$inData['cardData']['state'],
		'zip'=>$inData['cardData']['zip'],
	//    'trans_id'=> $inData['purchase']['refId'],
		'cust_id'=>$inData['account']['refId'],
	
		'first_name'=>$firstName,
		'last_name'=>$lastName,
	
	
		'amount' => $inData['cardData']['chargeTotal'],
		'card_num' => $inData['cardData']['cardNumber'],
		'exp_date' => $inData['cardData']['expMonth'].$inData['cardData']['expYear']
		);

	$this->sale->setFields($outArray);


}

public function executeCharge(){
	$result=$this->sale->authorizeAndCapture();
	return $this->mapResult($result);
}

public function mapResult($result){

	$outArray=array(
	
		'approved'=>($result->response_code==1)?true:false,
		'explanation'=>$result->response_reason_text,
		'transactionId'=>$result->transaction_id
	);
	return $outArray;
}


}//end of class