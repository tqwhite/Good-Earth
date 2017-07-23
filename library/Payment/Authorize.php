<?php
namespace Payment;

require_once(dirname(__FILE__) . '/AuthorizeDistribution/AuthorizeNet.php');

class Authorize{

var $sale;
private $toAuthorize;
public $fake; //useful for debugging separate steps from payment.php

public function __construct(){

$authorizeSpecs=\Zend_Registry::get('authorize');


$this->sale = new \AuthorizeNetAIM($authorizeSpecs['AUTHORIZENET_API_LOGIN_ID'], $authorizeSpecs['AUTHORIZENET_TRANSACTION_KEY']);


$this->sale->setSandbox(false); //false means use the real server

}

public function setPurchaseData($purchaseEntity, $cardData, $accountData, $transactionId){

	$nameArray=explode(' ',$cardData['cardName']);
	$firstName=$nameArray[0];
	$lastName=$nameArray[1];

	$outArray=array(
		'address'=>$cardData['street'],
		'city'=>$cardData['city'],
		'state'=>$cardData['state'],
		'zip'=>$cardData['zip'],
	    'trans_id'=> $transactionId,
		'cust_id'=>$accountData['refId'],
	
		'first_name'=>$firstName,
		'last_name'=>$lastName,
	
	
		'amount' => $purchaseEntity->chargeTotal,
		'card_num' => $cardData['cardNumber'],
		'exp_date' => $cardData['expMonth'].$cardData['expYear']
		);
	$this->toAuthorize=$outArray;
	$this->sale->setFields($outArray);


}

public function executeCharge($fake=false){

	if ($fake){
		$this->fake=$fake;
		return array(
			'deferredPaymentPreference'=>'FAKETRANSACTION',
			'approved'=>$fake['approval'],
			'transaction_id'=>$this->toAuthorize['trans_id'],
			'response_reason_text'=>$fake['approval']?'forced approval of fake transaction':'forced decline of fake transaction',
			'authorization_code'=>$this->toAuthorize['amount'],
			'amount'=>$this->toAuthorize['amount'],
		
		);
	}
	
	$result=$this->sale->authorizeAndCapture();
	
	$outArray=$this->mapResult($result);
	$outArray['details']=$result;
	return $outArray;
}

public function authorizeOnly($fake=false){

	if ($fake){
		error_log("authorizeOnly FAKE TRANSACTION {$this->toAuthorize['trans_id']} ['Authorize.php]");
		$this->fake=$fake;
		return array(
			'deferredPaymentPreference'=>'FAKETRANSACTION',
			'approved'=>$fake['approval'],
			'transaction_id'=>$this->toAuthorize['trans_id'],
			'response_reason_text'=>$fake['approval']?'forced approval of fake authorization_only':'forced decline of fake authorization_only',
			'authorization_code'=>$this->toAuthorize['amount'],
			'amount'=>$this->toAuthorize['amount'],
		
		);
	}
	
	$result=$this->sale->authorizeOnly();
	$outArray=$this->mapResult($result);
	$outArray['details']=$result;
	return $outArray;
}

public function voidAuthorization($transactionId){

	if ($this->fake){
		error_log("voidAuthorization FAKE TRANSACTION {$this->toAuthorize['trans_id']} ['Authorize.php]");
		return array(
			'deferredPaymentPreference'=>'FAKETRANSACTION',
			'approved'=>true,
			'transaction_id'=>$this->toAuthorize['trans_id'],
			'response_reason_text'=>'forced approval of fake voidAuthorization',
			'authorization_code'=>$this->toAuthorize['amount'],
			'amount'=>$this->toAuthorize['amount'],
		
		);
	}
	
	$result=$this->sale->void($transactionId);
	$outArray=$this->mapResult($result);
	$outArray['details']=$result;
	return $outArray;
}

public function captureAuthorized($transactionId, $amount){

	if ($this->fake){
		error_log("captureAuthorized FAKE TRANSACTION {$this->toAuthorize['trans_id']} ['Authorize.php]");
		return array(
			'deferredPaymentPreference'=>'FAKETRANSACTION',
			'approved'=>true,
			'transaction_id'=>$this->toAuthorize['trans_id'],
			'response_reason_text'=>'forced approval of fake captureAuthorized',
			'authorization_code'=>$this->toAuthorize['amount'],
			'amount'=>$this->toAuthorize['amount'],
		
		);
	}
	
	$result=$this->sale->priorAuthCapture($transactionId, $amount);
	$outArray=$this->mapResult($result);
	$outArray['details']=$result;
	return $outArray;
}

public function mapResult($result){

				
	$name='approved'; $outArray[$name]=$result->$name;
	$name='transaction_id'; $outArray[$name]=$result->$name;
	$name='response_reason_text'; $outArray[$name]=$result->$name;
	$name='authorization_code'; $outArray[$name]=$result->$name;
	
	$name='fake'; $outArray[$name]=$this->$name;
	
	return $outArray;
}


}//end of class