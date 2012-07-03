<?php

class SessionController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function checkAction()
    {

		$hello=$this->getRequest()->getPost('data');
//Q\Utils::dumpCli($_POST);

		$namespace = new Zend_Session_Namespace('sessionData'); // default namespace
		$namespace->setExpirationSeconds(60);

		if (isset($namespace->count)) {
			$namespace->count=$namespace->count+1;
		}
		else{
			$namespace->count=1;
		}


		$namespace->name="You say, '{$hello['hello']}' and I say, 'I love you' {$namespace->count} times.";


		$this->_helper->json(array(
			status=>1,
			data=>array(
				"count"=>$namespace->count,
				"name"=>$namespace->name
				)
		));
    }

    public function loginAction()
    {
		$inData=$this->getRequest()->getPost('data');

        $check = new Q\Plugin\Authorize\Check();
		$check->credentials($inData); //updates \Zend_Auth::getInstance()

        $auth = \Zend_Auth::getInstance();

        if ($auth->hasIdentity()){
        	$identity=$auth->getIdentity();
        	$identity=$identity[0];
        	$status=1;
        }
        else{
        	$status=-1;
        	$identity='';
        }

		$this->_helper->json(array(
			status=>$status,
			data=>array(
				"identity"=>array(
					'firstName'=>$identity->firstName,
					'lastName'=>$identity->lastName,
					'emailAdr'=>$identity->emailAdr,
					'userName'=>$identity->userName,
					'school'=>$identity->school->name
				)
				)
		));

    }

    public function startAction()
    {
        $auth = \Zend_Auth::getInstance();

        if ($auth->hasIdentity()){
        	$identity=$auth->getIdentity();
        	$identity=$identity[0];
        	$status=1;
        }
        else{
        	$status=-1;
        	$identity='';
        }


		$this->_helper->json(array(
			status=>$status,
			data=>array(
				"identity"=>array(
					'firstName'=>$identity->firstName,
					'lastName'=>$identity->lastName,
					'emailAdr'=>$identity->emailAdr,
					'userName'=>$identity->userName
				)
				)
		));

    }

    public function logoutAction()
    {
        \Zend_Auth::getInstance()->clearIdentity();


        $status=1;
		$this->_helper->json(array(
			status=>$status,
			data=>array()
		));


    }


}







