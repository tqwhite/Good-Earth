<?php

class Application_Model_Student
{

	private $_doctrineContainer;
	private $_entityManager;

	public function __construct(){

		$this->doctrineContainer=\Zend_Registry::get('doctrine');
		$this->_entityManager=$this->doctrineContainer->getEntityManager();
	}


	public function getByRefId($refId){
		$query = $this->_entityManager->createQuery('SELECT u from GE\Entity\Student u WHERE u.refId = :refId');
		$query->setParameters(array(
			'refId' => $refId
		));
		$result = $query->getResult();
		return $result;
	}

	public function validate($inData){

		$errorList=array();
		$name='firstName';
		$datum=$inData[$name];
		if (!$datum){
			$errorList[]=array($name, "First name is required");
		}

		$errorList=array();
		$name='lastName';
		$datum=$inData[$name];
		if (!$datum){
			$errorList[]=array($name, "Last name is required");
		}

		$errorList=array();
		$name='schoolRefId';
		$datum=$inData[$name];
		if (!$datum){
			$errorList[]=array($name, "School is required");
		}

		$errorList=array();
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

	static function formatOutput($identity){

	return array(
				"identity"=>array(
					'firstName'=>$identity->firstName,
					'lastName'=>$identity->lastName,
	/*				'school'=>array(
						'name'=>$identity->school->name,
						'refId'=>$identity->school->refId
						),
	*/				'account'=>array(
						'familyName'=>$identity->account->familyName,
						'refId'=>$identity->account->refId
						)
				)
				);

	}

}

