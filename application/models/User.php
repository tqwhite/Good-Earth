<?php

class Application_Model_User
{
	const	badConfirmationCode=-1;
	const	alreadyConfirmed=1;
	const	confirmationSuccessful=2;

	private $_doctrineContainer;
	private $_entityManager;

	public function __construct(){

	$this->doctrineContainer=\Zend_Registry::get('doctrine');
	$this->_entityManager=$this->doctrineContainer->getEntityManager();
	}

	public function confirmEmail($confirmationCode){
		$user=$this->getUserByConfirmationCode($confirmationCode);
		if ($user){
			$result=$this->setEmailStatusConfirmed($confirmationCode);
			return $result?self::confirmationSuccessful:self::alreadyConfirmed;
		}
		else{
			return self::badConfirmationCode;
		}
	}

	public function setEmailStatusConfirmed($confirmationCode){
		$query = $this->_entityManager->createQuery('UPDATE GE\Entity\User u Set u.emailStatus=1 WHERE u.confirmationCode = :confirmationCode');
		$query->setParameters(array(
			'confirmationCode' => $confirmationCode
		));
		$result = $query->getResult(); //result==0 if emailStatus was already zero, 1 if it was changed
		return $result;
	}

	public function getUserByConfirmationCode($confirmationCode){
		$query = $this->_entityManager->createQuery('SELECT u from GE\Entity\User u WHERE u.confirmationCode = :confirmationCode');
		$query->setParameters(array(
			'confirmationCode' => $confirmationCode
		));
		$users = $query->getResult();
		return $users;

		/*

		$users=$em
			->createQuery('select u from GE\Entity\User u')
			->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
		*/
	}

	public function getUserByUserId($userName){
		$query = $this->_entityManager->createQuery('SELECT u from GE\Entity\User u WHERE u.userName = :name');
		$query->setParameters(array(
			'name' => $userName
		));
		$users = $query->getResult();
		return $users;
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

}

