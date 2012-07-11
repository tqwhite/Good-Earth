<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="schools")
* @author tqii
*
*
**/
class School /*extends Base*/{
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

	/**
	 * @column(type="string", length=60, nullable=true)
	 * @var string
	 **/
	private $testField;



	/**
	 *
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="Student", mappedBy="school", cascade={"persist", "remove"});
	 **/

	private $students;



public function __construct(){
	if (!$this->refId){$this->refId =  uniqid();}
	$this->users=new \Doctrine\Common\Collections\ArrayCollection();
}

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
	$this->$property=$value;
}
}