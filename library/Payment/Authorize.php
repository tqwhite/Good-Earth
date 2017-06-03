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

public function setPurchaseData($purchaseEntity, $cardData, $accountData){

	$nameArray=explode(' ',$cardData['cardName']);
	$firstName=$nameArray[0];
	$lastName=$nameArray[1];

	$outArray=array(
		'address'=>$cardData['street'],
		'city'=>$cardData['city'],
		'state'=>$cardData['state'],
		'zip'=>$cardData['zip'],
	//    'trans_id'=> $purchaseEntity->refId,
		'cust_id'=>$accountData['refId'],
	
		'first_name'=>$firstName,
		'last_name'=>$lastName,
	
	
		'amount' => $purchaseEntity->chargeTotal,
		'card_num' => $cardData['cardNumber'],
		'exp_date' => $cardData['expMonth'].$cardData['expYear']
		);
		
	$this->sale->setFields($outArray);


}

public function executeCharge(){
	
	$result=$this->sale->authorizeAndCapture();
	
	$outArray=$this->mapResult($result);
	$outArray['details']=$result;
	return $outArray;
}

public function mapResult($result){

				
	$name='approved'; $outArray[$name]=$result->$name;
	$name='transaction_id'; $outArray[$name]=$result->$name;
	$name='response_reason_text'; $outArray[$name]=$result->$name;
	$name='authorization_code'; $outArray[$name]=$result->$name;
	
	return $outArray;
}


}//end of class