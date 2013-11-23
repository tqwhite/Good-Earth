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



	$paymentObj=new \Payment\Authorize();
	$paymentObj->setPurchaseData($inData);
	
	$result=$paymentObj->executeCharge();


	return $result;
}
	
	protected function convertHelixData($data){
		$data['isActiveFlag']=($data['active?']=='Yes')?1:0; unset($data['active?']);

		return $data;
	}

}

