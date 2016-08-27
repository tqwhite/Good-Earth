<?php

class AccountController extends Q_Controller_Base {

	private $newPassword = '';

	public function init() {
		parent::updateAuditInfo('AccountController');
	}

	public function indexAction() {
		// action body

	}

	public function registerAction() {

		$inData = $this->getRequest()->getPost('data');
		$messages = array();

		$userObj = new \Application_Model_User();

		$errorList = \Application_Model_User::validate($inData);

		if (isset($inData['adminFlag']) && $inData['adminFlag']) {
			$auth = \Zend_Auth::getInstance();

			if ($auth->hasIdentity()) {
				$identity = $auth->getIdentity();
				$identity = $identity;
				if ($identity->role != 'admin') {
					$errorList[] = array('adminFlag', "Unauthorized post as admin not allowed");
				}
			}
			}

			if ($errorList) {
				$this->_helper->json(array(status => - 1, messages => $errorList, data => array()));
			} else {

				$this->newPassword = $inData['password'];

					$user = $userObj->getByRefId($inData['refId']);

					if (!$user){
						$user = new GE\Entity\User();
						$user->refId = $inData['refId'];
					
						$account = new GE\Entity\Account();
						$account->familyName = $inData['lastName'];

						$user->account = $account;
					}
				
				$user->account->alreadyInHelix=0;
				
				$user->firstName = $inData['firstName'];
				$user->lastName = $inData['lastName'];
				$user->userName = $inData['userName'];

				if (isset($inData['adminFlag']) && $inData['adminFlag'] && (!isset($inData['password']) || !$inData['password'])) {
					//if an admin enters no password, it means do not change it

				} else {
					$user->password = $inData['password'];
				}

				$user->emailAdr = $inData['emailAdr'];

				$user->street = $inData['street'];
				$user->city = $inData['city'];
				$user->state = $inData['state'];
				$user->zip = $inData['zip'];

				$user->phoneNumber = $inData['phoneNumber'];
				$user->confirmationCode = md5($user->refId);

				if ($inData['emailOverride']) {
					$user->emailStatus = 1;
				}

				$status = 1; //unless error
				try {
					$this->doctrineContainer = Zend_Registry::get('doctrine');
					$em = $this->doctrineContainer->getEntityManager();
					$em->persist($user);
					$em->flush();
				}
				catch(Exception $e) {
					$status = - 1;
					$messages[] = array('server_error', $e);
				}

				$userObj = new \Application_Model_User();
				$user = $userObj->getUserByUserId($inData['userName']);
				$user->emailStatus = md5($user->refId);

				if (!$inData['emailOverride'] && $inData['previousEmailAddress']!=$inData['emailAdr']) {
					$mailStatus = $this->sendEmailConfirmation($user);
					if ($mailStatus) {
						$messages[] = array('registration', 'Confirmation email sent');
					}
				} else {
					$messages[] = array('registration', "No confirmation email was sent.<br/>The account is confirmed. You can login at any time.<br/> {$inData['userName']}/{$inData['password']}");
				}
				
				//For the admin panel, I need to receive the entire user object
				//I don't know why but when I retrieve this, it doesn't have the users property filled
				//even though I saved a user. I tried flushing and such. No dice. So, it's a hack. I build it the hard way.
				$refreshUser = $userObj->searchByUserId($inData['userName']);
				$count=count($refreshUser['account']['users']);
				if ($count==0){
				$tmp=$refreshUser;
				unset($tmp['account']); //reduce this copy to user data only
				$refreshUser['account']['users'][]=$tmp;
				}

				$this->_helper->json(array(status => $status, messages => $messages, data => array('user'=>$refreshUser, 'firstName' => $inData['firstName'], 'lastName' => $inData['lastName'], 'emailAdr' => $inData['emailAdr'], 'userName' => $inData['userName'])));

			}
		}

		private function sendEmailConfirmation($userObj) {

			$mail = new Zend_Mail();

    	$emailSender=Zend_Registry::get('emailSender');
    	
    	if (!$emailSender){
			$tr=new Zend_Mail_Transport_Sendmail();
		}
		else{
			$tr=new Zend_Mail_Transport_Smtp($emailSender['hostName'], array(
				'username'=>$emailSender['authSet']['username'],
				'password'=>$emailSender['authSet']['password'],
				'port'=>$emailSender['authSet']['port'],
				'ssl'=>$emailSender['authSet']['ssl'],
				'auth'=>$emailSender['authSet']['auth']
			));

		}

			$emailMessage = "<body style='background:#F5E4C6;'><div style='color:#385B2B;font-size:12pt;margin:20px 0px 20px 10px;'>
			<div>Dear {$userObj->firstName},<p/>
				Thank you for your interest in the Good Earth School Lunch Program.<p/>
				Because it is so important that we be able to communicate with you, we ask
				that you click on the link below to confirm that your email address is:
				{$userObj->emailAdr}
			<div style='margin-top:15px;'>Please click this link:</div>
			<div style='margin:25px 0px 30px 50px;font-size:14pt;color:#E26437;'>
			<A href='http://{$_SERVER['SERVER_NAME']}/account/confirm/{$userObj->confirmationCode}' style='color:#E26437;text-decoration:none;'>CONFIRM</a>
			</div>
			Or, copy and paste this link:
			<div style='font-size:10pt;margin:20px 0px 30px 50px;color:#E26437;'>
			<A href='http://{$_SERVER['SERVER_NAME']}/account/confirm/{$userObj->confirmationCode}' style='color:#E26437;text-decoration:none;'>{$_SERVER['SERVER_NAME']}/account/confirm/{$userObj->confirmationCode}</a>
			</div>

		Thank You,<br/>
		Your Friends at Good Earth Natural Foods
			<div style='font-size:10pt;margin-top:20px;'>PS, User ID: {$userObj->userName}</div>
		</div></body>";

			$mail->setBodyHtml($emailMessage);
			$mail->setFrom('school@genatural.com', "Good Earth Lunch Program");
			$mail->setSubject("Good Earth: Lunch Program Email Address Confirmation " . $userObj->userName);

			$mail->addTo($userObj->emailAdr, $userObj->firstName . ' ' . $userObj->lastName);
			$mail->send($tr);
error_log("CONFIRMATION EMAIL SENT: email server: user: {$userObj->userName}, dest email: {$userObj->emailAdr}, {$emailSender['hostName']}, ");
			return true;
		}

		public function confirmAction() {

error_log("CONFIRMATION REDEEM START: {$_SERVER['REQUEST_URI']}");
			$serverComm = array();

			$store = Zend_Registry::get('store');
			if ($store['status'] == 'closed') {
				$serverComm[] = array("fieldName" => "assert_initial_controller", "value" => 'closed');
				if (isset($store['closedMessage'])) {
					$serverComm[] = array("fieldName" => "closedMessage", "value" => $store['closedMessage']);
				}
				$this->view->serverComm = $this->_helper->WriteServerCommDiv($serverComm); //named: Q_Controller_Action_Helper_WriteServerCommDiv
				return;
			}

			$requestUri = $_SERVER['REQUEST_URI'];
			$elements = explode('/', $requestUri);
			$confirmationCode = $elements['3'];
			if (!$confirmationCode) {
				$confirmationCode = $elements['2'];
			}

			if (strlen($confirmationCode) == 32) {
				$inData = $this->getRequest()->getParam();
				$userObj = new \Application_Model_User();
				$result = $userObj->confirmEmail($confirmationCode);

				switch ($result['status']) {
					case \Application_Model_User::confirmationSuccessful:
						$message = "Thanks, {$result['user'][0]->firstName}! Confirmation Successful.";
						$serverComm[] = array("fieldName" => "assert_initial_controller", "value" => 'login');
						$serverComm[] = array("fieldName" => "new_username", "value" => $result['user'][0]->userName);
					break;
					case \Application_Model_User::alreadyConfirmed:
						$message = "Welcome Back {$result['user'][0]->firstName}. You are already confirmed.";
						$serverComm[] = array("fieldName" => "assert_initial_controller", "value" => 'login');
						$serverComm[] = array("fieldName" => "new_username", "value" => $result['user'][0]->userName);
					break;
					case \Application_Model_User::badConfirmationCode:
						$message = "Sorry, that code was not found.";
						$serverComm[] = array("fieldName" => "assert_initial_controller", "value" => 'register');
					break;

				}
			} else {
				$message = "Sorry, your code is incorrect";
				$serverComm[] = array("fieldName" => "assert_initial_controller", "value" => 'register');
			}

error_log("CONFIRMATION REDEEM COMPLETE: {$_SERVER['REQUEST_URI']} $message");

			$serverComm[] = array("fieldName" => "user_confirm_message", "value" => $message);
			$this->view->serverComm = $this->_helper->WriteServerCommDiv($serverComm); //named: Q_Controller_Action_Helper_WriteServerCommDiv

		}

		public function getAction() {
			$inData = $this->getRequest()->getParam('data');

			$accessObj = new \Application_Model_Account();
			$result = $accessObj->getByRefId($inData['refId']);

			if (count($result)) {
				$status = 1;
			} else {
				$status = - 1;
			}

			$this->_helper->json(array(status => $status, data => \Application_Model_Account::formatOutput($result)));
		}

	}
	