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

		$accessObj=new \Application_Model_School();
		$list=$accessObj->getList('record');

		if (count($list)){$status=1;}
		else {$status=-1;}

		$this->_helper->json(array(
			status=>$status,
			messages=>$messageList,
			data=>\Application_Model_School::formatOutput($list)
			)
		);

    }


}



