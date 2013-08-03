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

	const tableName='offerings';
	
	/**
	* @var string $id
	* @column(name="refId", type="string", length=36, nullable=false, unique="true")
	* @Id
	**/
	private $refId;

	/**
	 * @column(type="string", length=100, nullable=false)
	 * @var string
	 **/
	private $name;

	/**
	 * @column(type="string", nullable=false)
	 * @var string
	 **/

	private $perYearFull;


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



    /**
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="Order", mappedBy="offering", cascade={"persist", "remove"});
     */
    private $orders;


	/**
	 * @column(type="datetime", nullable=false)
	 * @var datetime
	 **/

	private $created;

	/**
	 * @column(type="boolean", nullable=true)
	 * @var integer
	 **/

	private $alreadyInHelix;


	/**
	 * @column(type="boolean", nullable=true)
	 * @var integer
	 **/

	private $isActiveFlag;


public function __construct(){
	if (!$this->refId){$this->refId =  \Q\Utils::newGuid();}
	$this->created=new \DateTime(date("Y-m-d H:i:s"));

	$this->schoolNodes = new \Doctrine\Common\Collections\ArrayCollection();
	$this->dayNodes = new \Doctrine\Common\Collections\ArrayCollection();
	$this->gradeLevelNodes = new \Doctrine\Common\Collections\ArrayCollection();
	$this->orders = new \Doctrine\Common\Collections\ArrayCollection();
}



public function __get($property){
	switch($property){
		case 'price':
			return $this->$property/100;
			break;
		default:
			return $this->$property;
			break;
	}
}

public function __set($property, $value){
	switch($property){
		case 'price':
			$this->$property=$value*100;
			break;
		default:
			$this->$property=$value;
			break;
	}
}

}