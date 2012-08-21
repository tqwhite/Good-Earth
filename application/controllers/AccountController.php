<?php

class AccountController extends Zend_Controller_Action
{
	private $newPassword='';

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function registerAction()
    {
		$inData=$this->getRequest()->getPost('data');
		$messages=array();

		$userObj=new \Application_Model_User();
		$errorList=\Application_Model_User::validate($inData);

		if ($errorList){
			$this->_helper->json(array(
				status=>-1,
				messages=>$errorList,
				data=>array()
			));
		}
		else{

		$account=new GE\Entity\Account();
		$account->familyName=$inData['lastName'];

		$this->newPassword=$inData['password'];

			$u=new GE\Entity\User();
				$u->firstName=$inData['firstName'];
				$u->lastName=$inData['lastName'];
				$u->userName=$inData['userName'];
				$u->password=$inData['password'];
				$u->emailAdr=$inData['emailAdr'];
				$u->phoneNumber=$inData['phoneNumber'];
				$u->confirmationCode=md5($u->refId);
				$u->account=$account;



			$status=1; //unless error
			try{
				$this->doctrineContainer=Zend_Registry::get('doctrine');
				$em=$this->doctrineContainer->getEntityManager();
				$em->persist($u);
				$em->flush();
			}
			catch(Exception $e){
				$status=-1;
				$messages[]=array('server_error', $e);
			}

			$userObj=new \Application_Model_User();
			$user=$userObj->getUserByUserId($inData['userName']);
			$user->emailStatus=md5($user->refId);

			$mailStatus=$this->sendEmailConfirmation($user);

			if ($mailStatus){$messages[]=array('registration', 'Confirmation email sent'); }

			$this->_helper->json(array(
				status=>$status,
				messages=>$messages,
				data=>array(
						'firstName'=>$inData['firstName'],
						'lastName'=>$inData['lastName'],
						'emailAdr'=>$inData['emailAdr'],
						'userName'=>$inData['userName']
					)
			));


    }
	}

	private function sendEmailConfirmation($userObj)
	{

		$mail = new Zend_Mail();
		$tr=new Zend_Mail_Transport_Sendmail();

		$emailMessage="<body style='background:#F5E4C6;'><div style='color:#385B2B;font-size:12pt;margin:20px 0px 20px 10px;'>
			<div>Dear {$userObj->firstName},<p/>
				Thank you for your interest in the Good Earth School Lunch Program.<p/>
				Because it is so important that we be able to communicate with you, we ask
				that you click on the link below to confirm that your email address is:
				{$userObj->emailAdr}
			<div style='margin-top:15px;'>Please click this link:</div>
			<div style='margin:25px 0px 30px 50px;font-size:14pt;color:#E26437;'>
			<A href='{$_SERVER['HTTP_REFERER']}account/confirm/{$userObj->confirmationCode}' style='color:#E26437;text-decoration:none;'>CONFIRM</a>
			</div>
			Or, copy and paste this link:
			<div style='font-size:10pt;margin:20px 0px 30px 50px;color:#E26437;'>
			<A href='{$_SERVER['HTTP_REFERER']}account/confirm/{$userObj->confirmationCode}' style='color:#E26437;text-decoration:none;'>{$_SERVER['HTTP_REFERER']}account/confirm/{$userObj->confirmationCode}</a>
			</div>

		Thank You,<br/>
		Your Friends at Good Earth Natural Foods
			<div style='font-size:10pt;margin-top:20px;'>PS, User ID/Password: {$userObj->userName}/{$this->newPassword}}</div>
		</div></body>";

		$mail->setBodyHtml($emailMessage);
		$mail->setFrom('school@genatural.com', "Good Earth Lunch Program");
		$mail->setSubject("Good Earth: Lunch Program Email Address Confirmation ".$userObj->userName);

		$mail->addTo($userObj->emailAdr, $userObj->firstName.' '.$userObj->lastName);
		$mail->send();

		return true;
	}

	public function confirmAction()
	{

		$serverComm=array();

		$store=	Zend_Registry::get('store');
		if ($store['status']=='closed'){
			$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'closed');
			$this->view->serverComm=$this->_helper->WriteServerCommDiv($serverComm); //named: Q_Controller_Action_Helper_WriteServerCommDiv
			return;
		}

		$requestUri=$_SERVER['REQUEST_URI'];
		$elements=explode('/', $requestUri);
		$confirmationCode=$elements['3'];
		if (!$confirmationCode){
			$confirmationCode=$elements['2'];
		}

		if (strlen($confirmationCode)==32){
			$inData=$this->getRequest()->getParam();
			$userObj=new \Application_Model_User();
			$result=$userObj->confirmEmail($confirmationCode);

			switch ($result['status']){
				case \Application_Model_User::confirmationSuccessful:
						$message="Thanks, {$result['user'][0]->firstName}! Confirmation Successful.";
						$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'login');
						$serverComm[]=array("fieldName"=>"new_username", "value"=>$result['user'][0]->userName);
					break;
				case \Application_Model_User::alreadyConfirmed:
						$message="Welcome Back {$result['user'][0]->firstName}. You are already confirmed.";
						$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'login');
						$serverComm[]=array("fieldName"=>"new_username", "value"=>$result['user'][0]->userName);
					break;
				case \Application_Model_User::badConfirmationCode:
						$message="Sorry, that code was not found.";
						$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'register');
					break;

			}
		}
		else{
				$message="Sorry, your code is incorrect";
				$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'register');
		}



		$serverComm[]=array("fieldName"=>"user_confirm_message", "value"=>$message);
		$this->view->serverComm=$this->_helper->WriteServerCommDiv($serverComm); //named: Q_Controller_Action_Helper_WriteServerCommDiv

	}

	public function getAction()
	{
		$inData=$this->getRequest()->getParam('data');

		$accessObj=new \Application_Model_Account();
		$result=$accessObj->getByRefId($inData['refId']);

		if (count($result)){$status=1;}
		else {$status=-1;}

		$this->_helper->json(array(
			status=>$status,
			data=>\Application_Model_Account::formatOutput($result)
		));
	}


}



