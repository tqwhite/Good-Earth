<?php
namespace GE\ENTITY;
class UserTest extends \ModelTestCase
{
	public function XXtestCanCreateUser(){
		$this->assertInstanceOf('GE\Entity\User', new User());
	}

	public function testCanSaveFirstAndLastAndRetrieveThem(){

		$u=$this->getTestUser('tq', 'white');

		$em=$this->doctrineContainer->getEntityManager();
		$em->persist($u);
		$em->flush();

		$users=$em->createQuery('select u from GE\Entity\User u')->execute();
		$this->assertEquals(1, count($users));
		$this->assertEquals('tq', $users[0]->firstName);
		$this->assertEquals('white', $users[0]->lastName);
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