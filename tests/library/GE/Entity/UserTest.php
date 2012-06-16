<?php
namespace GE\ENTITY;
class UserTest extends \ModelTestCase
{
	public function testCanCreateUser(){
		$this->assertInstanceOf('GE\Entity\User', new User());
	}

	public function XXtestCanSaveFirstAndLastAndRetrieveThem(){
		$u=new User();
		$u->firstname='tq';
		$u->lastname='white';

		$em=$this->doctrineContainer->getEntityManager();
		$em->persist($u);
		$em->flush();

		$users=$em->createQuery('select u from GE/Entity/User u')->execute();
		$this->assertEquals(1, count($users));
		$this->assertEquals('tq', $users[0]->firstname);
		$this->assertEquals('white', $users[0]->lastname);
	}
}