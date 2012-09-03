<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="orders")
* @author tqii
*
*
**/
class Order /*extends Base*/{
	/**
	* @var string $id
	* @column(name="refId", type="string", length=36, nullable=false, unique="true")
	* @Id
	**/
	private $refId;

	/**
	 *
	 * @ManyToOne(targetEntity="Student", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="studentRefId", referencedColumnName="refId")
	 *
	 **/
	private $student;

	/**
	 *
	 * @ManyToOne(targetEntity="Day", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="dayRefId", referencedColumnName="refId")
	 *
	 **/
	private $day;

	/**
	 *
	 * @ManyToOne(targetEntity="Offering", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="offeringRefId", referencedColumnName="refId")
	 *
	 **/
	private $offering;



    /**
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="PurchaseOrderNode", mappedBy="order", cascade={"persist", "remove"});
     */
    private $purchaseOrderNodes;




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

public function __construct(){
	if (!$this->refId){$this->refId =  \Q\Utils::newGuid();}
	$this->created=new \DateTime(date("Y-m-d H:i:s"));

	$this->purchaseOrderNodes = new \Doctrine\Common\Collections\ArrayCollection();
}

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
	$this->$property=$value;
}
}