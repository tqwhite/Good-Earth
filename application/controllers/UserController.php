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

		$u=new GE\Entity\User();
		$u->firstName=$inData['firstName'];
		$u->lastName=$inData['lastName'];
		$u->userName=$inData['userName'];
		$u->password=$inData['password'];
		$u->emailAdr=$inData['emailAdr'];

		$status=1;
		$message='success';
		try{
			$this->doctrineContainer=Zend_Registry::get('doctrine');
			$em=$this->doctrineContainer->getEntityManager();
			$em->persist($u);
			$em->flush();
			$em->clear();
		}
		catch(Exception $e){
			$status=-1;
			$message=$e->errorInfo[2];
		}

		$this->_helper->json(array(
			status=>$status,
			message=>$message,
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



