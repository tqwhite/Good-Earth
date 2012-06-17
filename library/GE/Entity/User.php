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
	 * @column(type="string", length=60, nullable=true)
	 * @var string
	 **/
	private $phone;

	/**
	 * @column(type="string", length=60, nullable=false)
	 * @var string
	 **/

	private $lastname;

	/**
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="Purchase", mappedBy="user", cascade={"persist", "remove"});
	 **/

	private $purchases;

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
	$this->$property=$value;
}
}