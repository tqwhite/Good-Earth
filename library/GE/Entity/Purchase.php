<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="purchases")
* @author tqii
*
*
**/
class Purchase /*extends Base*/{
	/**
	* @var string $id
	* @column(name="refId", type="string", length=36, nullable=false, unique="true")
	* @Id
	**/
	private $refId;


	/**
	 *
	 * @column(name="amountTendered", type="integer", nullable=true)
	 **/

	 private $amountTendered;


	/**
	 * @column(type="string", length=200, nullable=false)
	 * @var string
	 **/
	private $transactionId;


    /**
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="AccountPurchaseNode", mappedBy="purchase", cascade={"persist", "remove"});
     */
    private $accountPurchaseNodes;

    /**
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="PurchaseOrderNode", mappedBy="purchases", cascade={"persist", "remove"});
     */
    private $purchaseOrderNodes;


	/**
	 * @column(type="datetime", nullable=false)
	 * @var datetime
	 **/

	private $created;

public function __construct(){
	if (!$this->refId){$this->refId =  \Q\Utils::newGuid();}
	$this->created=new \DateTime(date("Y-m-d H:i:s"));
}

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
echo "E/ $property=$value\n";
	$this->$property=$value;
}
}