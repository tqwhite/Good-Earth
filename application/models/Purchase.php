<?php

class Application_Model_Purchase extends Application_Model_Base
{

	const entityName="Purchase";

	public function __construct(){
		parent::__construct();
	}

	static function validate($inData){


		$errorList=array();

		$name='cardNumber';
		$datum=$inData['cardData'][$name];
		if (!$datum){
			$errorList[]=array($name, "Credit card number required");
		}
		else if (strlen(preg_replace('/[^\S]/', '', $datum))<15){
			$errorList[]=array($name, "Credit card number is incorrect");
		}

		$name='expMonth';
		$datum=$inData['cardData'][$name];
		if (!$datum){
			$errorList[]=array($name, "First name is required");
		}
		else if ($datum<1 || $datum>12){
			$errorList[]=array($name, "Month is wrong");
		}

		$name='expYear';
		$datum=$inData['cardData'][$name];
		if (!$datum){
			$errorList[]=array($name, "Year is required");
		}
		else if ($datum<12){
			$errorList[]=array($name, "Year is wrong");
		}

		return $errorList;
	}

	static function formatDetail($inData, $outputType){

		if ($inData->refId){

		switch ($outputType){
			default:
				$outArray=array(
					'refId'=>$inData->refId,
					'created'=>$inData->created,
					'fdOrderId'=>$inData->fdOrderId,
					'deferredPaymentPreference'=>$inData->deferredPaymentPreference,
					'orders'=>\Application_Model_Order::formatOutput($inData->purchaseOrderNodes)
			);
				break;
			case 'export':
/*
echo get_class($inData->accountPurchaseNodes)." x<BR>";
//\Zend_Debug::dump($inData->accountPurchaseNodes);
$a=$inData->accountPurchaseNodes[0];
echo "<div style='background:red;height:10px;width:100%;'></div>";
*/

//			error_reporting(~E_ALL); //error_reporting(E_ERROR | E_WARNING | E_PARSE); //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
echo "<div style='padding-bottom:200px;'>";
echo "<HR>";

echo get_class($inData)."<BR>";
echo get_class($inData->accountPurchaseNodes)."<BR>";
echo get_class($inData->accountPurchaseNodes[0])."<BR>";
echo "<HR>";

echo get_class($inData)." x<BR>";
echo get_class($inData->purchaseOrderNodes)."<BR>";
echo 'aa'.get_class($inData->purchaseOrderNodes[0])."<BR>";
echo 'past lookup';
echo "</div>";
exit;;


				$outArray=array(
					'refId'=>$inData->refId,
					'phoneNumber'=>$inData->phoneNumber,
					'cardName'=>$inData->cardName,
					'street'=>$inData->street,
					'city'=>$inData->city,
					'state'=>$inData->state,
					'zip'=>$inData->zip,
					'chargeTotal'=>$inData->chargeTotal,
					'deferredPaymentPreference'=>$inData->deferredPaymentPreference,
					'lastFour'=>$inData->lastFour,
					'fdTransactionTime'=>$inData->fdTransactionTime,
					'fdProcessorReferenceNumber'=>$inData->fdProcessorReferenceNumber,
					'fdProcessorResponseMessage'=>$inData->fdProcessorResponseMessage,
					'fdProcessorResponseCode'=>$inData->fdProcessorResponseCode,
					'fdProcessorApprovalCode'=>$inData->fdProcessorApprovalCode,
					'fdErrorMessage'=>$inData->fdErrorMessage,
					'fdOrderId'=>$inData->fdOrderId,
					'fdApprovalCode'=>$inData->fdApprovalCode,
					'created'=>$inData->created,
					'accounts'=>\Application_Model_Account::formatOutput($inData->accountPurchaseNodes, 'export', 'accounts'),
					'orders'=>\Application_Model_Order::formatOutput($inData->purchaseOrderNodes, 'export', 'orders')/*,
					'purchaseOrderNodes'=>\Application_Model_Export::formatOutput($inData->purchaseOrderNodes, 'export', 'pon'),
					'accountPurchaseNodes'=>\Application_Model_Export::formatOutput($inData->accountPurchaseNodes, 'export', 'apn')*/

				);
				break;

		}

		}
		else{
			$outArray=array();
		}
		return $outArray;

	}

	public function addOrder($order){
		$node=new GE\Entity\PurchaseOrderNode();
		$node->order=$order;
		$node->purchase=$this->entity;
		$this->entityManager->persist($node);
	}

	public function addAccount($account){
		$node=new GE\Entity\AccountPurchaseNode();
		$node->account=$account;
		$node->purchase=$this->entity;
		$this->entityManager->persist($node);
	}
}

