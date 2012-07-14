<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="meals")
* @author tqii
*
*
**/
class Meal /*extends Base*/{
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
	private $name;


public function __construct(){
	if (!$this->refId){$this->refId =  uniqid();}
}

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
	$this->$property=$value;
}
}