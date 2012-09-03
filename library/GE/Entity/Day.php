<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="days")
* @author tqii
*
*
**/
class Day /*extends Base*/{
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
	private $title;

	/**
	 *
	 * @column(name="seqNum", type="integer", nullable=true)
	 **/

	 private $seqNum;



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
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="OfferingDayNode", mappedBy="days", cascade={"persist", "remove"});
     */
    private $offeringDayNodes;

    /**
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="Order", mappedBy="days", cascade={"persist", "remove"});
     */
    private $orders;


public function __construct(){
	if (!$this->refId){$this->refId =  \Q\Utils::newGuid();}
	$this->created=new \DateTime(date("Y-m-d H:i:s"));

	$this->offeringDayNodes = new \Doctrine\Common\Collections\ArrayCollection();
	$this->orders = new \Doctrine\Common\Collections\ArrayCollection();
}

public function __get($property){
	switch($property){
		case 'created':
			return $this->created->format("Y-m-d H:i:s");
			break;
		default:
			return $this->$property;
			break;
	}
}

public function __set($property, $value){
	$this->$property=$value;
}
}