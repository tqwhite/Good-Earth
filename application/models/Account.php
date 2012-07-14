<?php

class Application_Model_Account extends Application_Model_Base
{

	const entityName="Account";

	public function __construct(){
		parent::__construct();
	}

	public function getByRefId($refId){

		$query = $this->entityManager->createQuery('SELECT u from GE\Entity\Account u WHERE u.refId = :refId');
		$query->setParameters(array(
			'refId' => $refId
		));
		$result = $query->getResult();
		return $result[0];
	}

	static function validate($inData){

		$errorList=array();

		$name='userName';
		$datum=$inData[$name];
		if (strlen($datum)<6){
			$errorList[]=array($name, "User Name too short");
		}

		$name='password';
		$datum=$inData[$name];
		if (strlen($datum)<6){
			$errorList[]=array($name, "Password too short");
		}

		return $errorList;
	}

	static function formatOutput($inData){

		$users=Q\Utils::buildArray($inData->users, 'firstName lastName');
		$students=array();

		foreach ($inData->students as $data){
			$students[]=\Application_Model_Student::formatOutput($data);
		}
		return array(
						'refId'=>$inData->refId,
						'familyName'=>$inData->familyName,
						'users'=>$users,
						'students'=>$students
					);

	}


}

