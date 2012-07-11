<?php

class SchoolController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function listAction()
    {

		$schoolObj=new \Application_Model_School();
		$schoolList=$schoolObj->getList();

		if (count($schoolList)>0){ $status=1; }
		else {$status=-1; $messageList=array(array('school/list', 'school database is empty!'));}

		$this->_helper->json(array(
			status=>$status,
			messages=>$messageList,
			data=>$schoolList
			)
		);
    }


}



