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
class User{
/**
* @var integer $id
* @column(name="id", type="integer", nullable=false)
* @Id
* @GeneratedValue(strategy="IDENTITY")
**/
private $id;

/**
* @column(type="string", length=60, nullable=false)
* @var string
**/
private $firstname;

/**
* @column(type="string", length=60, nullable=false)
* @var string
**/

private $lastname;

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
	$this->$property=$value;
}
}