<?php

class TestControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function testCanTouchWebPage()
    {
		$this->dispatch("/test/index");

		//echo $this->getResponse()->getBody();

		$this->assertController('test');
		$this->assertAction('index');
		$this->assertResponseCode(200);

    }

    public function testInitAction()
    {
		$this->dispatch("/test/init");

		//echo $this->getResponse()->getBody();

		$this->assertController('test');
		$this->assertAction('init');
		$this->assertResponseCode(200);
    }

    public function testHeliportAction()
    {
        $params = array('action' => 'heliport', 'controller' => 'Test', 'module' => 'default');
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



