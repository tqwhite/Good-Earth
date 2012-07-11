<?php

class Application_Model_Account
{



	private $_doctrineContainer;
	private $_entityManager;

	public function __construct(){

		$this->doctrineContainer=\Zend_Registry::get('doctrine');
		$this->_entityManager=$this->doctrineContainer->getEntityManager();
	}


	public function getByRefId($refId){

		$query = $this->_entityManager->createQuery('SELECT u from GE\Entity\Account u WHERE u.refId = :refId');
		$query->setParameters(array(
			'refId' => $refId
		));
		$result = $query->getResult();
		return $result[0];
	}

	public function validate($inData){

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

