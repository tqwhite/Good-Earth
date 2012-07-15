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
	 * @column(type="string", length=200, nullable=false)
	 * @var string
	 **/
	private $name;

	/**
	 * @column(type="string", length=60, nullable=false)
	 * @var string
	 **/
	private $shortName;

	/**
	 * @column(type="text", nullable=false)
	 * @var string
	 **/
	private $description;

	/**
	 *
	 * @column(name="suggestedPrice", type="integer", nullable=false)
	 **/

	 private $suggestedPrice;



	/**
	 * @column(type="datetime", nullable=false)
	 * @var datetime
	 **/

	private $created;


	/**
	 *
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="Offering", mappedBy="meal", cascade={"persist", "remove"});
	 **/

	private $offerings;


public function __construct(){
	if (!$this->refId){$this->refId =  uniqid();}
	$this->created=new \DateTime(date("Y-m-d H:i:s"));
}

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
	$this->$property=$value;
}
}