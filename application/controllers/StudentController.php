<?php

class StudentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function addAction()
    {
		$inData=$this->getRequest()->getPost('data');
		$messages=array();

		$modelObj=new \Application_Model_Student();
		$errorList=$modelObj->validate($inData);

		if ($errorList){
			$this->_helper->json(array(
				status=>-1,
				messages=>$errorList,
				data=>array()
			));
		}
		else{

		$accountObj=new \Application_Model_Account();
		$account=$accountObj->getByRefId($inData['accountRefId']);

		$schoolObj=new \Application_Model_School();
		$school=$schoolObj->getByRefId($inData['schoolRefId']);

			$u=new GE\Entity\Student();
				$u->firstName=$inData['firstName'];
				$u->lastName=$inData['lastName'];
				$u->refId=$inData['refId'];
		//		$u->school=$school[0];
				$u->account=$account;


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

			$this->_helper->json(array(
				status=>$status,
				messages=>$messages,
				data=>array(
					"identity"=>array(
						'firstName'=>$inData['firstName'],
						'lastName'=>$inData['lastName']
					)
				)
			));


    }
}


}



