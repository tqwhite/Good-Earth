<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // This executes /layouts/scripts/layout.phtml
        // which shows the common graphic context
        // and sends the javascript application call
        // which does the real work.

		$store=	Zend_Registry::get('store');
		if ($store['status']=='closed'){
			$serverComm[]=array("fieldName"=>"assert_initial_controller", "value"=>'closed');
			if (isset($store['closedMessage'])){
				$serverComm[]=array("fieldName"=>"closedMessage", "value"=>$store['closedMessage']);
			}
			$this->view->serverComm=$this->_helper->WriteServerCommDiv($serverComm); //named: Q_Controller_Action_Helper_WriteServerCommDiv

  	  }
	}
    public function payAction()
    {
        // action body
    }

    public function exportAction()
    {
        // action body
    }


}











