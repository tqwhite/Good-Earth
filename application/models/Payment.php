<?php

class Application_Model_Payment
{


static function process($inData){

	$cardNumber=$inData['cardData']['cardNumber']; //only approve for one dollar even, remember to void transaction
	$expMonth=$inData['cardData']['expMonth'];
	$expYear=$inData['cardData']['expYear'];
	$chargeTotal=$inData['cardData']['chargeTotal'];

	$purchaseRefId=$inData['purchase']['refId'];

	$cardNumber=preg_replace('/[^\S]/', '', $cardNumber);


error_log("start Authorize ".microtime()." {$purchaseRefId}");
	$paymentObj=new \Payment\Authorize();
	$paymentObj->setPurchaseData($inData);
	
	$result=$paymentObj->executeCharge();
error_log("end Authorize ".microtime()." {$purchaseRefId}");


	return $result;
}
	
	protected function convertHelixData($data){
		$data['isActiveFlag']=($data['active?']=='Yes')?1:0; unset($data['active?']);
		$data['helixId']=$data['helix id']; unset($data['helix id']);

		return $data;
	}

}

