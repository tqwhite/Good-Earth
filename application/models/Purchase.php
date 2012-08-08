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

	static function formatDetail($inData, $originsArray){

		if ($inData->refId){
			$outArray=array(
				'refId'=>$inData->refId,
				'created'=>$inData->created,
				'fdOrderId'=>$inData->fdOrderId,
				'deferredPaymentPreference'=>$inData->deferredPaymentPreference,
				'orders'=>\Application_Model_Order::formatOutput($inData->purchaseOrderNodes)
			);
		}
		else{
			$outArray=array();
		}

		return $outArray;

	}

	public function getList($hydrationMode){

		$query = $this->entityManager->createQuery("SELECT u from GE\\Entity\\{$this->entityName} u");

		switch ($hydrationMode){
			default:
			case 'array':
				$list = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
				break;
			case 'record':
				$list = $query->getResult();
				break;
		}

		return $list;

	}

	public function addOrder($order){
		$node=new GE\Entity\PurchaseOrderNode();
		$node->order=$order;
		$node->purchase=$this->entity;
		$this->entityManager->persist($node);
	}
}

