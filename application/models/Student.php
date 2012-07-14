<?php

class Application_Model_Student extends Application_Model_Base
{

	const entityName="Student";

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
		if (!ereg($pattern, $datum)){
			$errorList[]=array($name, "Invalid email address");
		}
*/
		return $errorList;
	}

	static function formatScalar($inData, $originsArray){
		return array(
			'firstName'=>$inData->firstName,
			'lastName'=>$inData->lastName,
			'refId'=>$inData->refId,
			'schoolRefId'=>$inData->school->refId,
			'accountRefId'=>$inData->account->refId,
			'gradeLevelRefId'=>$inData->gradeLevel->refId,
		);
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

	public function getByRefId($refId){

		$query = $this->entityManager->createQuery("SELECT u from GE\\Entity\\{$this->entityName} u WHERE u.refId = :refId");
		$query->setParameters(array(
			'refId' => $refId
		));
		$schoolList = $query->getResult();
		return $schoolList;

	}

}

