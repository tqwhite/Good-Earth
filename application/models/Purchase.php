<?php

class Application_Model_Purchase extends Application_Model_Base
{

	const entityName="Purchase";

	public function __construct(){
		parent::__construct();
	}

	static function validate($inData){

		$errorList=array();

		$name='firstName';
		$datum=$inData[$name];
		if (!$datum){
			$errorList[]=array($name, "First name is required");
		}

		return $errorList;
	}

	static function formatDetail($inData, $originsArray){

		if ($inData->refId){
			$outArray=array(
				'firstName'=>$inData->firstName,
				'lastName'=>$inData->lastName,
				'refId'=>$inData->refId,
				'schoolRefId'=>$inData->school->refId,
				'accountRefId'=>$inData->account->refId,
				'gradeLevelRefId'=>$inData->gradeLevel->refId
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

