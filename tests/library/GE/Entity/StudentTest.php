<?php
namespace GE\ENTITY;
class StudentTest extends \ModelTestCase
{
	public function testCanCreateStudent(){
		$this->assertInstanceOf('GE\Entity\Student', new Student());
	}

	public function testCanCreateAccount(){
		$firstName='Jimmie';
		$lastName='Doe';

        $testObj = new Student();
        $testObj->firstName = $firstName;
        $testObj->lastName = $lastName;

		$em=$this->doctrineContainer->getEntityManager();
		$em->persist($testObj);
		$em->flush();

		$users=$em->createQuery('select u from GE\Entity\Student u')->execute();
		$this->assertEquals(1, count($users));
		$this->assertEquals($firstName, $users[0]->firstName);
		$this->assertEquals($lastName, $users[0]->lastName);
	}

	public function XXXtestCreateWithAccount(){
echo "xxxxxxxxxxxxxxxx\n";
		$firstName='Jimmie';
		$lastName='Doe';

		$accountObj=new Account();
		$accountObj->familyName='Does';

        $testObj = new Student();
        $testObj->firstName = $firstName;
        $testObj->lastName = $lastName;
        $testObj->account=$accountObj;

		$em=$this->doctrineContainer->getEntityManager();
		$em->persist($testObj);
		$em->flush();

echo "aaaaaaaaaaaaaaaaaaaa\n";
		$users=$em->createQuery('select u from GE\Entity\Student u')->execute();
echo "bbbbbbbbbbbbbbbbbb\n";


	}


}