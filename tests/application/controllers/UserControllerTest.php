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

    public function testRequestResetAction()
    {
        $params = array('action' => 'requestReset', 'controller' => 'User', 'module' => 'default');
        $urlParams = $this->urlizeOptions($params);
        $url = $this->url($urlParams);
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($urlParams['module']);
        $this->assertController($urlParams['controller']);
        $this->assertAction($urlParams['action']);
        $this->assertQueryContentContains(
            'div#view-content p',
            'View script for controller <b>' . $params['controller'] . '</b> and script/action name <b>' . $params['action'] . '</b>'
            );
    }

    public function testResetpwAction()
    {
        $params = array('action' => 'resetpw', 'controller' => 'User', 'module' => 'default');
        $urlParams = $this->urlizeOptions($params);
        $url = $this->url($urlParams);
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($urlParams['module']);
        $this->assertController($urlParams['controller']);
        $this->assertAction($urlParams['action']);
        $this->assertQueryContentContains(
            'div#view-content p',
            'View script for controller <b>' . $params['controller'] . '</b> and script/action name <b>' . $params['action'] . '</b>'
            );
    }

    public function testChangepwAction()
    {
        $params = array('action' => 'changepw', 'controller' => 'User', 'module' => 'default');
        $urlParams = $this->urlizeOptions($params);
        $url = $this->url($urlParams);
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($urlParams['module']);
        $this->assertController($urlParams['controller']);
        $this->assertAction($urlParams['action']);
        $this->assertQueryContentContains(
            'div#view-content p',
            'View script for controller <b>' . $params['controller'] . '</b> and script/action name <b>' . $params['action'] . '</b>'
            );
    }

    public function testSetpwAction()
    {
        $params = array('action' => 'setpw', 'controller' => 'User', 'module' => 'default');
        $urlParams = $this->urlizeOptions($params);
        $url = $this->url($urlParams);
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($urlParams['module']);
        $this->assertController($urlParams['controller']);
        $this->assertAction($urlParams['action']);
        $this->assertQueryContentContains(
            'div#view-content p',
            'View script for controller <b>' . $params['controller'] . '</b> and script/action name <b>' . $params['action'] . '</b>'
            );
    }


}















