<?php
namespace GE\ENTITY;
class UserTest extends \ModelTestCase
{
	public function XXtestCanCreateUser(){
		$this->assertInstanceOf('GE\Entity\User', new User());
	}

	public function testCanSaveFirstAndLastAndRetrieveThem(){
		$first='tq';
		$last='white';

        $user = new User();
        $user->firstName = $first;
        $user->lastName = $last;
        $user->userName = $first.$last;
        $user->password = $first.$last;
        $user->confirmationCode = $first.$last;
        $user->refId = uniqid();

		$em=$this->doctrineContainer->getEntityManager();
		$em->persist($user);
		$em->flush();

		$users=$em->createQuery('select u from GE\Entity\User u')->execute();
		$this->assertEquals(1, count($users));
		$this->assertEquals('tq', $users[0]->firstName);
		$this->assertEquals('white', $users[0]->lastName);
	}

	public function testDuplicateRefIdCrash(){
		$first='tq';
		$last='white';
		$refId=uniqid();

        $user = new User();
        $user->firstName = $first;
        $user->lastName = $last;
        $user->userName = $first.$last;
        $user->password = $first.$last;
        $user->confirmationCode = $first.$last;
        $user->refId = $refId;
        $u=$user;

		$refId=uniqid();
        $user = new User();
        $user->firstName = $first;
        $user->lastName = $last;
        $user->userName = $first.$last.$refId;
        $user->password = $first.$last;
        $user->confirmationCode = $first.$last;
        $user->refId = $refId;
        $u2=$user;

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