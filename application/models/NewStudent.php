<?php

class Application_Model_Student extends Application_Model_Base
{

	const entityName="Student";
	
	const helixImportRelationName="SLN_students";
	const helixImportViewName="students_Data";

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

	static function formatDetail($inData, $outputType){}

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
		$data['isActiveFlag']=($data['active?']=='No')?0:1;
		$data['isTeacherFlag']=($data['isTeacherFlag']=='Yes')?1:0;
	
		$data['firstName']=(isset($data['First Name']))?$data['First Name']:$data['firstName'];
		$data['lastName']=(isset($data['Last Name']))?$data['Last Name']:$data['lastName'];
		$data['helixId']=$data['helix id']; unset($data['helix id']);


		return $data;
	}

}

