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
	 * @column(type="text", nullable=false)
	 * @var string
	 **/
	private $comment;
	/**
	 *
	 * @column(name="suggestedPrice", type="integer", nullable=false)
	 **/

	 private $suggestedPrice;

	/**
	 *
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="OfferingSchoolNode", mappedBy="offering", cascade={"persist", "remove"});
	 **/

	private $offeringSchoolNodes;

	/**
	 *
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="OfferingDayNode", mappedBy="offering", cascade={"persist", "remove"});
	 **/

	private $offeringDayNodes;

	/**
	 *
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="OfferingGradeLevelNode", mappedBy="offering", cascade={"persist", "remove"});
	 **/

	private $offeringGradeLevelNodes;


	/**
	 *
	 * @ManyToOne(targetEntity="Meal", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="mealRefId", referencedColumnName="refId")
	 *
	 **/
	private $meal;

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