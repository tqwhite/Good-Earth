<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="users")
* @author tqii
*
*
**/
class User /*extends Base*/{
	/**
	* @var string $id
	* @column(name="refId", type="string", length=36, nullable=false, unique="true")
	* @Id
	**/
	private $refId;

	/**
	 * @column(type="string", length=60, nullable=false)
	 * @var string
	 **/
	private $firstName;

	/**
	 * @column(type="string", length=60, nullable=true)
	 * @var string
	 **/
	private $emailAdr;

	/**
	 * @column(type="string", length=60, nullable=false)
	 * @var string
	 **/

	private $lastName;

	/**
	 * @column(type="string", length=60, nullable=false, unique="true")
	 * @var string
	 **/

	private $userName;

	/**
	 * @column(type="string", length=60, nullable=false, unique="true")
	 * @var string
	 **/

	private $password;

public function __construct(){
	$this->refId=  uniqid();
}

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
	$this->$property=$value;
}
}