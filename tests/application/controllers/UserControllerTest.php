<?php

class UserControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function testIndexAction()
    {
        $params = array('action' => 'index', 'controller' => 'User', 'module' => 'default');
		$this->dispatch("/User/index");

		//echo $this->getResponse()->getBody();

		$this->assertController('User');
		$this->assertAction('index');
		$this->assertResponseCode(200);
    }

    public function XXtestRegisterAction()
    {
        $params = array('action' => 'register', 'controller' => 'User', 'module' => 'default');
		$this->dispatch("/User/register");

		//echo $this->getResponse()->getBody();

		$this->assertController('User');
		$this->assertAction('register');
		$this->assertResponseCode(200);
    }

    public function XXtestConfirmEmailAction()
    {
        $params = array('action' => 'confirmEmail', 'controller' => 'User', 'module' => 'default');
		$this->dispatch("/User/confirmEmail");

		//echo $this->getResponse()->getBody();

		$this->assertController('User');
		$this->assertAction('confirmEmail');
		$this->assertResponseCode(200);
    }


}







