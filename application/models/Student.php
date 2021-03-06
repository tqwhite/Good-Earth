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
		if (!preg_match($pattern, $datum)){
			$errorList[]=array($name, "Invalid email address");
		}
*/
		return $errorList;
	}

	static function formatDetail($inData, $outputType){
	

		if ($inData->refId){
		switch ($outputType){
			default:
				$outArray=array(
					'firstName'=>$inData->firstName,
					'lastName'=>$inData->lastName,
					'isActiveFlag'=>$inData->isActiveFlag,
					'refId'=>$inData->refId,
					'vegetarianFlag'=>$inData->vegetarianFlag,
					'isTeacherFlag'=>$inData->isTeacherFlag,
					'allergyFlag'=>$inData->allergyFlag,
					'schoolRefId'=>$inData->school->refId,
					'accountRefId'=>$inData->account->refId,
					'gradeLevelRefId'=>$inData->gradeLevel->refId,
					
					'gradeLevel'=>\Application_Model_GradeLevel::formatOutput($inData->gradeLevel, $outputType),
					'school'=>\Application_Model_School::formatOutput($inData->school, $outputType)
				);
				break;
		case 'export':
			$outArray=array(
				'refId'=>$inData->refId,
				'firstName'=>$inData->firstName,
				'lastName'=>$inData->lastName,
				'isTeacherFlag'=>$inData->isTeacherFlag,
				'vegetarianFlag'=>$inData->vegetarianFlag,
					'allergyFlag'=>$inData->allergyFlag,
				'schoolRefId'=>$inData->school->refId,
				'accountRefId'=>$inData->account->refId,
				'gradeLevelRefId'=>$inData->gradeLevel->refId,
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
		$data['vegetarianFlag']=($data['vegetarianFlag?']=='Yes')?1:0; unset($data['vegetarianFlag?']);
		$data['isTeacherFlag']=($data['isTeacherFlag?']=='Yes')?1:0; unset($data['isTeacherFlag?']);
		$data['allergyFlag']=($data['allergyFlag?']=='Yes')?1:0; unset($data['allergyFlag?']);
		$data['helixId']=$data['helix id']; unset($data['helix id']);

		return $data;
	}

}

