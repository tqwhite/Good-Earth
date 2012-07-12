<?php

class GradeLevelController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function getAction()
    {
        // action body
    }

    public function listAction()
    {

		$accessObj=new \Application_Model_GradeLevel();
		$list=$accessObj->getList('record');

		$outList=\Application_Model_GradeLevel::formatOutput($list);

		$this->_helper->json(array(
			status=>$status,
			messages=>$messageList,
			data=>$outList
			)
		);

    }


}





