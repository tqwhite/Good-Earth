<?php

class UserController extends Zend_Controller_Action
{

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
		$errorList=$userObj->validate($inData);

		if ($errorList){
			$this->_helper->json(array(
				status=>-1,
				messages=>$errorList,
				data=>array()
			));
		}
		else{

		$schoolObj=new \Application_Model_School();
		$school=$schoolObj->getSchool($inData['schoolRefId']);

			$u=new GE\Entity\User();
				$u->firstName=$inData['firstName'];
				$u->lastName=$inData['lastName'];
				$u->userName=$inData['userName'];
				$u->password=$inData['password'];
				$u->emailAdr=$inData['emailAdr'];
				$u->confirmationCode=md5($u->refId);
				$u->school=$school[0];

			$status=1; //unless error
			try{
				$this->doctrineContainer=Zend_Registry::get('doctrine');
				$em=$this->doctrineContainer->getEntityManager();
				$em->persist($u);
				$em->flush();
				$em->clear();
			}
			catch(Exception $e){
				$status=-1;
				$messages[]=array('server_error', $e);
			}

			$userObj=new \Application_Model_User();
			$user=$userObj->getUserByUserId($inData['userName']);
			$user->emailStatus=md5($user['refId']);

			$this->sendEmailConfirmation($user[0]);

			$this->_helper->json(array(
				status=>$status,
				messages=>$messages,
				data=>array(
					"identity"=>array(
						'firstName'=>$inData['firstName'],
						'lastName'=>$inData['lastName'],
						'emailAdr'=>$inData['emailAdr'],
						'userName'=>$inData['userName']
					)
				)
			));

    }
}
    private function sendEmailConfirmation($userObj)
    {
    
		$mail = new Zend_Mail();
		$tr=new Zend_Mail_Transport_Sendmail();
		
		$mail->setBodyHtml("<div style='color:blue;font-size:14pt;margin:20px 0px 20px 10px;'><A href='{$_SERVER['HTTP_REFERER']}confirm/".$userObj->confirmationCode."'>{$_SERVER['HTTP_REFERER']}confirm/".$userObj->confirmationCode."</a></div>");
		$mail->setFrom('tq@justkidding.com', "Good Earth Lunch Program");
		$mail->setSubject("Good Earth: Lunch Program Email Address Confirmation ".$userObj->userName);

		$mail->addTo('tq@justkidding.com', 'TQ White II');
		$mail->send();
    }

    public function confirmAction()
    {
		$requestUri=$_SERVER['REQUEST_URI'];
		$elements=explode('/', $requestUri);
		$confirmationCode=$elements['2'];

		if (strlen($confirmationCode)==32){
			$inData=$this->getRequest()->getParam();
			$userObj=new \Application_Model_User();
			$result=$userObj->confirmEmail($confirmationCode);

			switch ($result){
				case \Application_Model_User::confirmationSuccessful:
						$message="Thanks! Confirmation Successful";
					break;
				case \Application_Model_User::alreadyConfirmed:
						$message="Welcome Back, You are already confirmed";
					break;
				case \Application_Model_User::badConfirmationCode:
						$message="Sorry, that code was not found";

					break;

			}
		}
		else{
				$message="Sorry, your code is incorrect";
		}

		$serverComm=array();
			$serverComm[]=array("fieldName"=>"user_confirm_message", "value"=>$message);
			$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'login');

		$this->view->message=$this->_helper->WriteServerCommDiv($serverComm); //named: Q_Controller_Action_Helper_WriteServerCommDiv


    }


}





