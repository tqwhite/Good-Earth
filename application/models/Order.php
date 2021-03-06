<?php

class Application_Model_Order extends Application_Model_Base
{

	const entityName="Order";

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


		$name='lastName';
		$datum=$inData[$name];
		if (!$datum){
			$errorList[]=array($name, "Last name is required");
		}


		$name='schoolRefId';
		$datum=$inData[$name];
		if (!$datum){
			$errorList[]=array($name, "School is required");
		}


		$name='gradeLevelRefId';
		$datum=$inData[$name];
		if (!$datum){
			$errorList[]=array($name, "Grade Level is required");
		}
/*
		$name='emailAdr';
		$datum=$inData[$name];
		$pattern='/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
		if (!preg_match($pattern, $datum)){
			$errorList[]=array($name, "Invalid email address");
		}
*/
		return $errorList;
	}

	static function formatDetail($inData, $outputType){

		if (get_class($inData)=='GE\Entity\PurchaseOrderNode'){
			$inData=$inData->order;
		}


		if ($inData->refId){
			switch ($outputType){
				default:
					$outArray=array(
						'refId'=>$inData->refId,
						'created'=>$inData->created,
						'day'=>array(
							'refId'=>$inData->day->refId,
							'title'=>$inData->day->title
						),
						'student'=>array(
							'refId'=>$inData->student->refId,
							'firstName'=>$inData->student->firstName,
							'lastName'=>$inData->student->lastName
						),
						'offering'=>array(
							'refId'=>$inData->offering->refId,
							'name'=>$inData->offering->name
						)
					);
					break;
				case 'export':
					$outArray=array(
						'refId'=>$inData->refId,
						'studentRefId'=>$inData->student->refId,
						'dayRefId'=>$inData->day->refId,
						'offeringRefId'=>$inData->offering->refId,
						'created'=>$inData->created
					);
					break;

			}
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
	
	protected function convertHelixData($data){
		$data['isActiveFlag']=($data['active?']=='Yes')?1:0; unset($data['active?']);
		$data['helixId']=$data['helix id']; unset($data['helix id']);

		return $data;
	}

} //end of class

