<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="purchaseOrderNodes")
* @author tqii
*
*
**/
class PurchaseOrderNode /*extends Base*/{
	/**
	* @var string $id
	* @column(name="refId", type="string", length=36, nullable=false, unique="true")
	* @Id
	**/
	private $refId;


	/**
	 *
	 * @ManyToOne(targetEntity="Purchase", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="purchaseRefId", referencedColumnName="refId")
	 *
	 **/
	private $purchase;

	/**
	 *
	 * @ManyToOne(targetEntity="Order", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="orderRefId", referencedColumnName="refId")
	 *
	 **/
	private $order;



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
}

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
	$this->$property=$value;
}
}