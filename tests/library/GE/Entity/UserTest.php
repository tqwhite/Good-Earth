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
		$this->assertEquals('tq', $users[0]->firstname);
		$this->assertEquals('white', $users[0]->lastname);
	}

	public function testCanAddPurchasesToUser(){
		$u=$this->getTestUser();

		$purchase1=new Purchase();
		$purchase1->amount=12.99;
		$purchase1->storeName='Store A';

		$purchase2=new Purchase();
		$purchase2->amount=5.99;
		$secondProd='Store A';
		$purchase2->storeName=$secondProd;

		$u->purchases=array($purchase1, $purchase2);
		$em=$this->doctrineContainer->getEntityManager();
		$em->persist($u);
		$em->flush();

		$users=$em->createQuery('select u from GE\Entity\User u')->execute();
		$this->assertEquals(1, count($users));

		$this->assertEquals(2, count($users[0]->purchases));

		$this->assertEquals($secondProd,$users[0]->purchases[1]->storeName);

	//	var_dump($users[0]->purchases->toArray());
	}

}