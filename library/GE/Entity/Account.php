<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="accounts")
* @author tqii
*
*
**/
class Account /*extends Base*/{
	/**
	* @var string $id
	* @column(name="refId", type="string", length=36, nullable=false, unique="true")
	* @Id
	 *
	**/
	private $refId;

	/**
	 * @column(type="string", length=60, nullable=false)
	 * @var string
	 *
	 **/
	private $familyName;


	/**
	 *
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="User", mappedBy="account", cascade={"persist", "remove"});
	 *
	 **/

	private $users;


	/**
	 *
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="Student", mappedBy="account", cascade={"persist", "remove"});
	 *
	 **/

	private $students;


public function __construct(){
	if (!$this->refId){$this->refId =  uniqid();}
     $this->users = new \Doctrine\Common\Collections\ArrayCollection();
     $this->students = new \Doctrine\Common\Collections\ArrayCollection();
}

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
	$this->$property=$value;
}
}