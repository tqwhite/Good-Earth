<?php

class Application_Model_User extends Application_Model_Base
{
	
	const entityName = "User";
	
	const helixImportRelationName = "SLN_users";
	const helixImportViewName = "users_Data";
	
	const badConfirmationCode = -1;
	const alreadyConfirmed = 1;
	const confirmationSuccessful = 2;
	
	private $tmpStamp; //used to generate username in case Helix sends me an empty one
	
	public function __construct()
	{
		parent::__construct();
	}
	
	static function validate($inData)
	{
		
		$errorList = array();
		
		$name  = 'userName';
		$datum = $inData[$name];
		if (strlen($datum) < 6) {
			$errorList[] = array(
				$name,
				"User Name too short"
			);
		}
		
		$name  = 'password';
		$datum = $inData[$name];
		if (strlen($datum) < 6) {
			
			if (!$datum && $inData['adminFlag']){
				//if an admin doesn't enter a password, it means no change and is valid
			}
			else{
				$errorList[] = array(
					$name,
					"Password too short"
				);
			}
		}
		
		$userObj = new \Application_Model_User();
		
		$name  = 'emailAdr';
		$datum = $inData[$name];
		
		$user = $userObj->getByEmail($datum);
	
		$exempt = (in_array($datum, array(
			'sherry@genatural.com',
			'tq@justkidding.com'
		)) || $inData['emailOverride'] || ($inData['preExistingEmailAddress']=='true'));

echo "\n\ndatum={$datum}\n\n";

echo "inArray=".in_array($datum, array(
			'sherry@genatural.com',
			'tq@justkidding.com'
		))."\n\n";
echo "\n\ninData['emailOverride']={$inData['emailOverride']}\n\n";
echo "\n\ninData['preExistingEmailAddress']={$inData['preExistingEmailAddress']}\n\n";


echo "\n\nexempt={$exempt}\n\n";
echo "\n\ncount(user)=".count($user)."\n\n";
echo "{$user->refId}!={$inData['refId']}\n\n";

		if (count($user) > 0 && !$exempt) {
			$errorList[] = array(
				$name,
				"That {$exempt} email address is already associated with an account. Use 'Forgot Password' if you need to."
			);
		}


\Q\Utils::dumpCli($errorList, "errorList");

 
		exit;		
		
		$name  = 'userName';
		$datum = $inData[$name];
		
		$user = $userObj->getUserByUserId($datum);
		
		if (count($user) > 0 && $user->refId!=$inData['refId']) {
			$errorList[] = array(
				$name,
				"That User Name is already in use. User 'Forgot Password' if you need to."
			);
		}
		
		
		
		
		$name  = 'street';
		$datum = $inData[$name];
		
		if (!isset($datum)) {
			$errorList[] = array(
				$name,
				"Street is required"
			);
		}
		
		$name  = 'city';
		$datum = $inData[$name];
		
		if (!isset($datum)) {
			$errorList[] = array(
				$name,
				"City is required"
			);
		}
		
		$name  = 'state';
		$datum = $inData[$name];
		
		if (!isset($datum) || strlen($datum) != 2) {
			$errorList[] = array(
				$name,
				"State code must be two characters"
			);
		}
		
		
		$name  = 'zip';
		$datum = $inData[$name];
		
		if (!isset($datum) || strlen($datum) != 5) {
			$errorList[] = array(
				$name,
				"ZIP code must be five digits"
			);
		}
		
		
		return $errorList;
	}
	
	static function validateNewPw($inData)
	{
		
		$errorList = array();
		
		$name  = 'password';
		$datum = $inData[$name];
		if (strlen($datum) < 6) {
			$errorList[] = array(
				$name,
				"Password too short"
			);
		}
		
		
		if ($inData['password'] != $inData['confirmPassword']) {
			$errorList[] = array(
				$name,
				"Confirmation password doesn't match"
			);
		}
		
		return $errorList;
	}
	
	static function formatDetail($inData, $outputType)
	{
		if ($inData->refId) {		
			switch ($outputType) {
				default:
					$outArray = array(
						'firstName' => $inData->firstName,
						'lastName' => $inData->lastName,
						'emailAdr' => $inData->emailAdr,
						'userName' => $inData->userName,
						
						'street' => $inData->street,
						'city' => $inData->city,
						'state' => $inData->state,
						'zip' => $inData->zip,
						'role' => $inData->role,
						
						'phoneNumber' => $inData->phoneNumber,
						'refId' => $inData->refId,
						
						'account' => \Application_Model_Account::formatOutput($inData->account, $outputType)
					);
					break;
				case 'export':
					$outArray = array(
						'refId' => $inData->refId,
						'firstName' => $inData->firstName,
						'emailAdr' => $inData->emailAdr,
						'lastName' => $inData->lastName,
						'userName' => $inData->userName,
						'password' => $inData->password,
						
						'street' => $inData->street,
						'city' => $inData->city,
						'state' => $inData->state,
						'zip' => $inData->zip,
						
						'emailStatus' => $inData->emailStatus,
						'confirmationCode' => $inData->confirmationCode,
						'phoneNumber' => $inData->phoneNumber,
						'accountRefId' => $inData->account->refId,
						'created' => $inData->created
					);
					break;
			}
		} else {
			$outArray = array();
		}
		
		
		return $outArray;
		
		
	}
	
	
	public function getByRefId($refId)
	{
		$query = $this->entityManager->createQuery('SELECT u from GE\Entity\User u WHERE u.refId = :refId');
		$query->setParameters(array(
			'refId' => $refId
		));
		$users        = $query->getResult();
		$this->entity = $users[0];
		return $users[0];
	}
	
	
	public function getUserByUserId($userName)
	{
		$query = $this->entityManager->createQuery('SELECT u from GE\Entity\User u WHERE u.userName = :name');
		$query->setParameters(array(
			'name' => $userName
		));
		$users        = $query->getResult();
		$this->entity = $users[0];
		return $users[0];
	}
	
	public function getByEmail($emailAdr)
	{
		$query = $this->entityManager->createQuery('SELECT u from GE\Entity\User u WHERE u.emailAdr = :emailAdr');
		$query->setParameters(array(
			'emailAdr' => $emailAdr
		));
		$users        = $query->getResult();
		$this->entity = $users[0];
		return $users[0];
	}
	
	public function getByResetCode($resetCode)
	{
		$query = $this->entityManager->createQuery('SELECT u from GE\Entity\User u WHERE u.resetCode = :resetCode');
		$query->setParameters(array(
			'resetCode' => $resetCode
		));
		$users        = $query->getResult();
		$this->entity = $users[0];
		return $users[0];
	}
	
	public function confirmEmail($confirmationCode)
	{
		$user = $this->getUserByConfirmationCode($confirmationCode);
		if ($user) {
			$result      = $this->setEmailStatusConfirmed($confirmationCode);
			$status      = $result ? self::confirmationSuccessful : self::alreadyConfirmed;
			$resultArray = array(
				status => $status,
				user => $user
			);
		} else {
			$resultArray = array(
				status => self::badConfirmationCode
			);
		}
		
		return $resultArray;
	}
	
	public function setEmailStatusConfirmed($confirmationCode)
	{
		$query = $this->entityManager->createQuery('UPDATE GE\Entity\User u Set u.emailStatus=1 WHERE u.confirmationCode = :confirmationCode');
		$query->setParameters(array(
			'confirmationCode' => $confirmationCode
		));
		$result = $query->getResult(); //result==0 if emailStatus was already zero, 1 if it was changed
		return $result;
	}
	
	public function getUserByConfirmationCode($confirmationCode)
	{
		$query = $this->entityManager->createQuery('SELECT u from GE\Entity\User u WHERE u.confirmationCode = :confirmationCode');
		$query->setParameters(array(
			'confirmationCode' => $confirmationCode
		));
		$users = $query->getResult();
		return $users;
	}
	
	protected function convertHelixData($data)
	{
		
		if (!isset($this->tmpStamp)) {
			$this->tmpStamp = time();
		}
		
		$data['isActiveFlag'] = ($data['active?'] == 'Yes') ? 1 : 0;
		unset($data['active?']);
		
		
		$this->tmpStamp = $this->tmpStamp + 1;
		
		if (!$data['userName']) {
			$data['userName'] = 'empty_' . ($this->tmpStamp + 1);
		}
		$data['auditInfo'] = \Q\Utils::dumpWebString($this->getAuditInfo(), "auditInfo");
		$data['helixId']   = $data['helix id'];
		unset($data['helix id']);
		return $data;
	}
	
	
	
	public function searchByUserName($userName)
	{
		$qb = $this->entityManager->createQueryBuilder();
		$dql = $qb->getDql();
		
		$qb->select(array(
			'u'
		)) // string 'u' is converted to array internally
			->from('GE\Entity\User', 'u')->where($qb->expr()->orX($qb->expr()->like('u.userName', $qb->expr()->literal("$userName%")), $qb->expr()->like('u.lastName', $qb->expr()->literal("$userName%"))))->orderBy('u.lastName', 'ASC');
		$query    = $qb->getQuery();
		$userList = $query->getResult();
		$outList=array();
		
		for ($i = 0, $len = count($userList); $i < $len; $i++) {
			$element = $userList[$i];
			$outList[]=$this->formatDetail($element);
// 			$studentList = $element->account->students;
// 			for ($j = 0, $len2 = count($studentList); $j < $len2; $j++) {
// 				$student = $studentList[$j];
// // 				echo "<br/>{$student->firstName} ";
// // 				echo "{$student->refId}<br/>";
// 			}
		}


		return $outList;
	}
	
}
