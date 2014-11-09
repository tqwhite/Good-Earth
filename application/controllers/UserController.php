<?php

class UserController extends  Q_Controller_Base
{
	private $expirationInterval;
	private $resetDate;

    public function init()
    {
        parent::updateAuditInfo($this->getFileName());
    }

    public function indexAction()
    {
        // action body
    }


public function resetpwAction()
    {
		$inData=$this->getRequest()->getPost('data');
		$messages=array();
		$userObj=new \Application_Model_User();

		if (preg_match('/@/', $inData['identifier'])){
			$user=$userObj->getByEmail($inData['identifier']);
		}

		if (!$user){
			$user=$userObj->getUserByUserId($inData['identifier']);
		}


		if (!$user){
			$this->_helper->json(array(
				status=>-1,
				messages=>array('identifier', "No user with that identifier found"),
				data=>array()
			));
		}
		else{
			$this->expirationInterval=15;
			$now=new \DateTime();
			$this->resetDate=$now->add(DateInterval::createFromDateString("{$this->expirationInterval} minutes"));
			$resetDateDb=$this->resetDate->format('Y-m-d H:i:s');

			$user->resetDate=$this->resetDate;
			$user->resetCode=md5(\Q\Utils::newGuid());

			$userObj->persist(Application_Model_Base::yesFlush);

			$mailStatus=$this->sendResetEmail($user);

			if ($mailStatus){$messages[]=array('registration', 'Reset email sent'); }

			$this->_helper->json(array(
				status=>$status,
				messages=>$messages,
				data=>array(
						'firstName'=>$user->firstName,
						'lastName'=>$user->lastName,
						'emailAdr'=>$user->emailAdr,
						'userName'=>$user->userName,
						'expirationDate'=>$resetDateDb
					)
			));
		}
	}

private function sendResetEmail($userObj){

		$showResetDate=$this->resetDate->format('h:i');
		$mail = new Zend_Mail();
		$tr=new Zend_Mail_Transport_Sendmail();

		$emailMessage="<body style='background:#F5E4C6;'><div style='color:#385B2B;font-size:12pt;margin:20px 0px 20px 10px;'>
			<div>Dear {$userObj->firstName}, <p/>
				This link will take you to a form where you can specify a new password for your user account. After that, you can login with the new password immediately.<p/>
				Please note that, for security reasons, this code will expire in about {$this->expirationInterval} minutes at $showResetDate today.
			<div style='margin-top:15px;'>Please click this link:</div>
			<div style='margin:25px 0px 30px 50px;font-size:14pt;color:#E26437;'>
			<A href='http://{$_SERVER['SERVER_NAME']}/user/changepw/{$userObj->resetCode}' style='color:#E26437;text-decoration:none;'>RESET</a>
			</div>
			Or, copy and paste this link:
			<div style='font-size:10pt;margin:20px 0px 30px 50px;color:#E26437;'>
			<A href='http://{$_SERVER['SERVER_NAME']}/user/changepw/{$userObj->resetCode}' style='color:#E26437;text-decoration:none;'>
				http://{$_SERVER['SERVER_NAME']}/user/changepw/{$userObj->resetCode}
			</a>
			</div>

		Thank You,<br/>
		Your Friends at Good Earth Natural Foods
			<div style='font-size:10pt;margin-top:20px;'>PS, User ID: {$userObj->userName}</div>
		</div></body>";

		$mail->setBodyHtml($emailMessage);
		$mail->setFrom('school@genatural.com', "Good Earth Lunch Program");
		$mail->setSubject("Good Earth: Lunch Program Password Reset for ".$userObj->userName);

		$mail->addTo($userObj->emailAdr, $userObj->firstName.' '.$userObj->lastName);

		$mail->send();

		return true;
    }

public function changepwAction()
    {
		$serverComm=array();

		$store=	Zend_Registry::get('store');
		if ($store['status']=='closed'){
			$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'closed');
			if (isset($store['closedMessage'])){
				$serverComm[]=array("fieldName"=>"closedMessage", "value"=>$store['closedMessage']);
			}
			$this->view->serverComm=$this->_helper->WriteServerCommDiv($serverComm); //named: Q_Controller_Action_Helper_WriteServerCommDiv
			return;
		}

		$requestUri=$_SERVER['REQUEST_URI'];
		$elements=explode('/', $requestUri);
		$resetCode=$elements['3'];


		if (!$resetCode){
			$resetCode=$elements['2'];
		}
		if (strlen($resetCode)==32){
			$userObj=new \Application_Model_User();
			$result=$userObj->getByResetCode($resetCode);

			if (!$result->refId){
						$message="That code is not in our system.";
						$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'resetPw');
						$serverComm[]=array();
			}
			elseif ($now>$result->resetDate){
						$message="That code has expired.";
						$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'resetPw');
						$serverComm[]=array();
			}
			else{

						$message="The code is good. Please enter a new password.";
						$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'changePw');
						$serverComm[]=array("fieldName"=>"userName", "value"=>$result->userName);
						$serverComm[]=array("fieldName"=>"firstName", "value"=>$result->firstName);
						$serverComm[]=array("fieldName"=>"lastName", "value"=>$result->lastName);
						$serverComm[]=array("fieldName"=>"refId", "value"=>$result->refId);
			}
		}
		else{
				$message="Sorry, your code is incorrect";
				$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'register');
		}



		$serverComm[]=array("fieldName"=>"user_reset_message", "value"=>$message);
		$this->view->serverComm=$this->_helper->WriteServerCommDiv($serverComm); //named: Q_Controller_Action_Helper_WriteServerCommDiv

    }

    public function setpwAction()
    {
		$inData=$this->getRequest()->getPost('data');
		$messages=array();


		$errorList=\Application_Model_User::validateNewPw($inData);

		if (count($errorList)>0){
			$this->_helper->json(array(
				status=>-1,
				messages=>$errorList,
				data=>array()
			));
		}
		else{

		$userObj=new \Application_Model_User();
		$user=$userObj->getByRefId($inData['refId']);

			$status=1; //unless error
			try{

				if (count($user)==0){
					die("UserController says, User record was not found.");
				}
				else{
					$userObj->updateFromArray($user, array('password'=>$inData['password']));
				}
			}
			catch(Exception $e){
				$status=-1;
				$messages[]=array('server_error', $e->errorInfo);
			}

			$userObj->persist(Application_Model_Base::yesFlush);

			$this->_helper->json(array(
				status=>$status,
				messages=>$messages,
				data=>array()
			));


    }
}


} //end of class
















