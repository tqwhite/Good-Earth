<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="offerings")
* @author tqii
*
*
**/
class Offering /*extends Base*/{
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
	 * @column(type="datetime", nullable=false)
	 * @var datetime
	 **/

	private $created;


	/**
	 * @column(type="text", nullable=true)
	 * @var string
	 **/
	private $comment;
	/**
	 *
	 * @column(type="integer", nullable=false)
	 **/

	 private $price;

	/**
	 *
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="OfferingSchoolNode", mappedBy="offering", cascade={"persist", "remove"});
	 **/

	private $schoolNodes;

	/**
	 *
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="OfferingDayNode", mappedBy="offering", cascade={"persist", "remove"});
	 **/

	private $dayNodes;

	/**
	 *
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="OfferingGradeLevelNode", mappedBy="offering", cascade={"persist", "remove"});
	 **/

	private $gradeLevelNodes;


	/**
	 *
	 * @ManyToOne(targetEntity="Meal", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="mealRefId", referencedColumnName="refId")
	 *
	 **/
	private $meal;



public function __construct(){
	if (!$this->refId){$this->refId =  \Q\Utils::newGuid();}
	$this->created=new \DateTime(date("Y-m-d H:i:s"));
}

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
	$this->$property=$value;
}
}