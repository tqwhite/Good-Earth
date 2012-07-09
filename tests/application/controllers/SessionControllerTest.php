<?php

class SessionControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function testIndexAction()
    {
        $params = array('action' => 'index', 'controller' => '', 'module' => 'default');

		$this->dispatch("/Session/index");

		//echo $this->getResponse()->getBody();

		$this->assertController('Session');
		$this->assertAction('index');
		$this->assertResponseCode(200);

    }

    public function XXtestLoginAction()
    {
        $params = array('action' => 'login', 'controller' => 'Session', 'module' => 'default');
		$this->dispatch("/Session/login");

		//echo $this->getResponse()->getBody();

		$this->assertController('Session');
		$this->assertAction('login');
		$this->assertResponseCode(200);
    }

    public function XXtestStartAction()
    {
        $params = array('action' => 'start', 'controller' => 'Session', 'module' => 'default');
		$this->dispatch("/Session/start");

		//echo $this->getResponse()->getBody();

		$this->assertController('Session');
		$this->assertAction('start');
		$this->assertResponseCode(200);
    }

    public function XXtestLogoutAction()
    {
        $params = array('action' => 'logout', 'controller' => 'Session', 'module' => 'default');
		$this->dispatch("/Session/logout");

		//echo $this->getResponse()->getBody();

		$this->assertController('Session');
		$this->assertAction('start');
		$this->assertResponseCode(200);
    }


}









