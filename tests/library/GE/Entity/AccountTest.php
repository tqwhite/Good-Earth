<?php
namespace GE\ENTITY;
class AccountTest extends \ModelTestCase
{
	public function XXtestCanCreateUser(){
		$this->assertInstanceOf('GE\Entity\User', new User());
	}

	public function testCanCreateAccount(){
		$name='Test Family';

        $testObj = new Account();
        $testObj->familyName = $name;

		$em=$this->doctrineContainer->getEntityManager();
		$em->persist($testObj);
		$em->flush();

		$users=$em->createQuery('select u from GE\Entity\Account u')->execute();
		$this->assertEquals(1, count($users));
		$this->assertEquals($name, $users[0]->familyName);
	}

	public function XXtestDuplicateRefIdCrash(){

		$u=$this->getTestUser('tq', 'white', '1234');
		$u2=$this->getTestUser('tq2', 'white', '1234');

		$em=$this->doctrineContainer->getEntityManager();
		$em->persist($u);
		$em->persist($u2);
		$em->flush();

		$users=$em->createQuery('select u from GE\Entity\User u')->execute();
		$this->assertEquals(2, count($users));
		$this->assertEquals('tq', $users[0]->firstName);
		$this->assertEquals('white', $users[0]->lastName);
	}


}