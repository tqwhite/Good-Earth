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
		$schoolList=$schoolObj->getList('record');

		$schoolOutList=\Application_Model_School::formatOutput($schoolList);

		$this->_helper->json(array(
			status=>$status,
			messages=>$messageList,
			data=>$schoolOutList
			)
		);

    }


}



