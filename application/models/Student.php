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

	static function formatDetail($inData, $outputType){
// 	if ($inData->refId=='827fad18-1bbe-4de7-87d1-1e6476fa5a0e'){
// 		echo "\n\ninData->refId={$inData->refId}\n\n";
// 		echo "\n\ninData->refId={$inData->firstName} {$inData->lastName}\n\n";
// 		echo '1) '.get_class($inData)."\n\n";
// 		echo '2) '.get_class($inData->orders)."\n\n";
// 
// 		\Doctrine\Common\Util\Debug::dump($inData->orders);
// 	}
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
						
						'school'=>\Application_Model_School::formatOutput($inData->school, $outputType),
						'orders'=>\Application_Model_Order::formatOutput($inData->offeringNodes)
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
		$data['helixId']=$data['helix id']; unset($data['helix id']);

		return $data;
	}

}

