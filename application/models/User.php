<?php

class Application_Model_User extends Application_Model_Base
{

	const entityName="User";

	const	badConfirmationCode=-1;
	const	alreadyConfirmed=1;
	const	confirmationSuccessful=2;

	public function __construct(){
		parent::__construct();
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

	static function validateNewPw($inData){

		$errorList=array();

		$name='password';
		$datum=$inData[$name];
		if (strlen($datum)<6){
			$errorList[]=array($name, "Password too short");
		}


		if ($inData['password']!=$inData['confirmPassword']){
			$errorList[]=array($name, "Confirmation password doesn't match");
		}

		return $errorList;
	}

	static function formatDetail($inData, $outputType){
	if ($inData->refId){
		switch ($outputType){
		default:
			$outArray=array(
				'firstName'=>$inData->firstName,
				'lastName'=>$inData->lastName,
				'emailAdr'=>$inData->emailAdr,
				'userName'=>$inData->userName,
				'account'=>\Application_Model_Account::formatOutput($inData->account, $outputType)
			);
			break;
		case 'export':
			$outArray=array(
				'refId'=>$inData->refId,
				'firstName'=>$inData->firstName,
				'emailAdr'=>$inData->emailAdr,
				'lastName'=>$inData->lastName,
				'userName'=>$inData->userName,
				'password'=>$inData->password,
				'emailStatus'=>$inData->emailStatus,
				'confirmationCode'=>$inData->confirmationCode,
				'phoneNumber'=>$inData->phoneNumber,
				'accountRefId'=>$inData->account->refId,
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


	public function getUserByUserId($userName){
		$query = $this->entityManager->createQuery('SELECT u from GE\Entity\User u WHERE u.userName = :name');
		$query->setParameters(array(
			'name' => $userName
		));
		$users = $query->getResult();
		$this->entity=$users[0];
		return $users[0];
	}

	public function getByEmail($emailAdr){
		$query = $this->entityManager->createQuery('SELECT u from GE\Entity\User u WHERE u.emailAdr = :emailAdr');
		$query->setParameters(array(
			'emailAdr' => $emailAdr
		));
		$users = $query->getResult();
		$this->entity=$users[0];
		return $users[0];
	}

	public function getByResetCode($resetCode){
		$query = $this->entityManager->createQuery('SELECT u from GE\Entity\User u WHERE u.resetCode = :resetCode');
		$query->setParameters(array(
			'resetCode' => $resetCode
		));
		$users = $query->getResult();
		$this->entity=$users[0];
		return $users[0];
	}

	public function confirmEmail($confirmationCode){
		$user=$this->getUserByConfirmationCode($confirmationCode);
		if ($user){
			$result=$this->setEmailStatusConfirmed($confirmationCode);
			$status=$result?self::confirmationSuccessful:self::alreadyConfirmed;
			$resultArray=array(
				status=>$status,
				user=>$user
			);
		}
		else{
			$resultArray=array(
				status=>self::badConfirmationCode
			);
		}

		return $resultArray;
	}

	public function setEmailStatusConfirmed($confirmationCode){
		$query = $this->entityManager->createQuery('UPDATE GE\Entity\User u Set u.emailStatus=1 WHERE u.confirmationCode = :confirmationCode');
		$query->setParameters(array(
			'confirmationCode' => $confirmationCode
		));
		$result = $query->getResult(); //result==0 if emailStatus was already zero, 1 if it was changed
		return $result;
	}

	public function getUserByConfirmationCode($confirmationCode){
		$query = $this->entityManager->createQuery('SELECT u from GE\Entity\User u WHERE u.confirmationCode = :confirmationCode');
		$query->setParameters(array(
			'confirmationCode' => $confirmationCode
		));
		$users = $query->getResult();
		return $users;
	}

}

