<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="students")
* @author tqii
*
*
**/
class Student /*extends Base*/{
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
	 * @column(type="string", length=60, nullable=false)
	 * @var string
	 **/

	private $lastName;

	/**
	 *
	 * @ManyToOne(targetEntity="Account", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="accountRefId", referencedColumnName="refId")
	 *
	 **/
	private $account;


	/**
	 *
	 * @ManyToOne(targetEntity="School", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="schoolRefId", referencedColumnName="refId")
	 *
	 **/
	private $school;



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