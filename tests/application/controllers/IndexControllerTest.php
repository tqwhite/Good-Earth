<?php

class IndexControllerTest extends ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

	public function testCanDoUnitTest(){
		$this->assertTrue(true);
	}
//this is definitely being found and executed by phpunit
}

