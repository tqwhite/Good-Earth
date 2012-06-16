<?php

//namespace GE\Entity;

/**
*
* @author tqii
*
*
**/

class Model_StatsTest extends ControllerTestCase{

	protected $stats;

	public function setUp(){
		parent::setUp();

		$this->stats=new Application_Model_Stats();

	}

	public function testCanDoUnitTest(){
		$this->assertTrue(true);
	}

	public function testCanDoUnitTest2(){
		$this->assertTrue(true);
	}

	public function testCanAddCountry(){
		$this->assertEquals(0, count($this->stats->getCountries()));

		$testCountry='canada';

		$this->stats->addCountry($testCountry);

		foreach ($this->stats->getCountries() as $country){
			$this->assertEquals($country, $testCountry);
		}

		$this->assertEquals(1, count($this->stats->getCountries()));
	}

} //end of class