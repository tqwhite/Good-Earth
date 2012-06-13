<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function databaseAction()
    {
        $db = new Zend_Db_Adapter_Pdo_Mysql(array(
			'host'     => '127.0.0.1',
			'username' => 'tq',
			'password' => '',
			'dbname'   => 'test1'
		));
		
		$stmt = $db->query('select * from example');
		
		print_r($stmt->fetch());
		echo Zend_Version::VERSION ;
    }


}



