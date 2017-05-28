<?php

class Application_Model_Account extends Application_Model_Base
{

	const entityName="Account";
	
	const helixImportRelationName="SLN_accounts";
	const helixImportViewName="accounts_Data";

	public function __construct(){
		parent::__construct();
	}

	static function validate($inData){

		$errorList=array();

		$name='userName';
		$datum=$inData[$name];
		if (strlen($datum)<6){
			$errorList[]=array($name, "Login Name too short");
		}

		$name='password';
		$datum=$inData[$name];
		if (strlen($datum)<6){
			$errorList[]=array($name, "Password too short");
		}

		return $errorList;
	}



	static function formatDetail($inData, $outputType){

		if (get_class($inData)=='GE\Entity\AccountPurchaseNode'){
			$inData=$inData->account;
		}

		if ($inData->refId){

		switch ($outputType){

			case 'limited':
				$outArray=array(
						'refId'=>$inData->refId
					);
				break;
			default:
				$outArray=array(
						'refId'=>$inData->refId,
						'familyName'=>$inData->familyName,
						'users'=>\Application_Model_User::formatOutput($inData->users, 'limited'),
						'students'=>\Application_Model_Student::formatOutput($inData->students, $outputType)
					);
				break;
			case 'export':
				$outArray=array(
						'refId'=>$inData->refId,
						'familyName'=>$inData->familyName,
						'users'=>\Application_Model_User::formatOutput($inData->users, $outputType),
						'students'=>\Application_Model_Student::formatOutput($inData->students, $outputType),
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
	
	protected function convertHelixData($data){

		$data['isActiveFlag']=($data['active?']=='Yes')?1:0; unset($data['active?']);
		$data['helixId']=$data['helix id']; unset($data['helix id']);

		return $data;
	}
	
	public function getByRefId($refId)
	{
		$query = $this->entityManager->createQuery('SELECT u from GE\Entity\Account u WHERE u.refId = :refId');
		$query->setParameters(array(
			'refId' => $refId
		));
		$result        = $query->getResult();
		$this->entity = $result[0];
		return $result[0];
	}

}

