<?php

class SessionController extends  Q_Controller_Base
{

    public function init()
    {
        
    }

    public function checkAction()
    {
		$hello=$this->getRequest()->getPost('data');

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
    	$messageList=array();
		$inData=$this->getRequest()->getPost('data');
        $check = new Q\Plugin\Authorize\Check();
		$check->credentials($inData); //updates \Zend_Auth::getInstance()

        $auth = \Zend_Auth::getInstance();

        if ($auth->hasIdentity()){
        	$identity=$auth->getIdentity();
        	$identity=$identity;
        	if($identity->emailStatus){
        		$status=1;
				error_log("LOGIN: {$inData['userName']}, refId: {$identity->refId}");
        	}
        	else{
        		$status=-2;
        		$messageList[]='Email address has not been confirmed';
     		   \Zend_Auth::getInstance()->clearIdentity(); //HACKERY:I do not have time to go back and make it so that auth accounts for email confirmation status and transmits status out to user
        	}
        }
        else{
        	$status=-1;
        	$identity='';
        }

		$this->_helper->json(array(
			status=>$status,
			messages=>$messageList,
			data=>\Application_Model_User::formatOutput($identity)
		));
    }

    public function startAction()
    {
        $auth = \Zend_Auth::getInstance();

        if ($auth->hasIdentity()){
        	$identity=$auth->getIdentity();
        	$identity=$identity;
        	$status=1;
        }
        else{
        	$status=-1;
        	$identity='';
        }

	$userDataPackage=\Application_Model_User::formatOutput($identity);
	
// 	$accountRefId=$userDataPackage['account']['refId'];
// 	
// 	$purchaseObj=new \Application_Model_Purchase();
// 	
// 	$purchaseList=$purchaseObj->getByAccountRefIdForJson($accountRefId);
// 	$purchaseOutList=array();
// 	
// // 			for ($i=0, $len=count($purchaseList); $i<$len; $i++){
// // 			$element=$purchaseList[$i];
// // 			$purchaseOutList[]=\Application_Model_Purchase::formatOutput($element);
// // 		}
// 	
// 	$userDataPackage['purchases']=$purchaseList;

		$this->_helper->json(array(
			status=>$status,
			data=>$userDataPackage
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







