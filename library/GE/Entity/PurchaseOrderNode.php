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

	const tableName='purchaseOrderNodes';
	
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
	 * @column(type="datetime", nullable=false)
	 * @var datetime
	 **/

	private $modified;

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
	$this->modified=new \DateTime(date("Y-m-d H:i:s"));
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

	$this->modified=new \DateTime(date("Y-m-d H:i:s"));
}
}