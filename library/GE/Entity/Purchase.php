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
	 * @column(type="string", length=20)
	 * @var string
	 **/

	private $phoneNumber;

	/**
	 * @column(type="string", length=20)
	 * @var string
	 **/

	private $cardName;

	/**
	 * @column(type="string", length=60)
	 * @var string
	 **/

	private $street;

	/**
	 * @column(type="string", length=20)
	 * @var string
	 **/

	private $city;

	/**
	 * @column(type="string", length=20)
	 * @var string
	 **/

	private $state;

	/**
	 * @column(type="string", length=20)
	 * @var string
	 **/

	private $zip;


	/**
	 *
	 * @column(name="chargeTotal", type="integer", nullable=false)
	 **/

	 private $chargeTotal;


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
	 * @column(type="string", length=200, nullable=true)
	 * @var string
	 **/
	private $deferredPaymentPreference;
	/**
	 * @column(type="string", length=4, nullable=true)
	 * @var string
	 **/
	private $lastFour;

	//======================================

	/**
	 * @column(type="string", length=200, nullable=true)
	 * @var string
	 **/
	private $fdTransactionTime;


	/**
	 * @column(type="string", length=200, nullable=true)
	 * @var string
	 **/
	private $fdProcessorReferenceNumber;


	/**
	 * @column(type="string", length=200, nullable=true)
	 * @var string
	 **/
	private $fdProcessorResponseMessage;


	/**
	 * @column(type="string", length=200, nullable=true)
	 * @var string
	 **/
	private $fdProcessorResponseCode;


	/**
	 * @column(type="string", length=200, nullable=true)
	 * @var string
	 **/
	private $fdProcessorApprovalCode;


	/**
	 * @column(type="string", length=200, nullable=true)
	 * @var string
	 **/
	private $fdErrorMessage;


	/**
	 * @column(type="string", length=200, nullable=true)
	 * @var string
	 **/
	private $fdOrderId;

	/**
	 * @column(type="string", length=200, nullable=true)
	 * @var string
	 **/
	private $fdApprovalCode;


	/**
	 * @column(type="datetime", nullable=false)
	 * @var datetime
	 **/

	private $created;

public function __construct(){
	if (!$this->refId){$this->refId =  \Q\Utils::newGuid();}
	$this->created=new \DateTime(date("Y-m-d H:i:s"));


	$this->accountPurchaseNodes = new \Doctrine\Common\Collections\ArrayCollection();
	$this->purchaseOrderNodes = new \Doctrine\Common\Collections\ArrayCollection();
}

public function __get($property){
	switch($property){
		case 'chargeTotal':
			return $this->$property/100;
			break;
		default:
			return $this->$property;
			break;
	}
}

public function __set($property, $value){
	switch($property){
		case 'chargeTotal':
			$this->$property=$value*100;
			break;
		default:
			$this->$property=$value;
			break;
	}
}
}