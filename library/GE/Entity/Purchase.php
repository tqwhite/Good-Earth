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

	const tableName='purchases';
	
	/**
	* @var string $id
	* @column(name="refId", type="string", length=36, nullable=false, unique="true")
	* @Id
	**/
	private $refId;
	
	/**
	* @var string
	* @column(name="helixId", type="string", length=36, nullable=true)
	**/
	private $helixId;

	/**
	 * @column(type="string", length=100)
	 * @var string
	 **/

	private $phoneNumber;

	/**
	 * @column(type="string", length=100)
	 * @var string
	 **/

	private $cardName;

	/**
	 * @column(type="string", length=60)
	 * @var string
	 **/

	private $street;

	/**
	 * @column(type="string", length=100)
	 * @var string
	 **/

	private $city;

	/**
	 * @column(type="string", length=100)
	 * @var string
	 **/

	private $state;

	/**
	 * @column(type="string", length=100)
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
	 * @OneToMany(targetEntity="PurchaseOrderNode", mappedBy="purchase", cascade={"persist", "remove"});
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
	private $firstFour;

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

	/**
	 * @column(type="string", length=4, nullable=true)
	 * @var string
	 **/
	private $lastFour;
	
	/**
	* @var string
	* @column(type="string", length=36, nullable=true)
	**/
	private $merchantAccountId;

public function __construct(){
	if (!$this->refId){$this->refId =  \Q\Utils::newGuid();}
	$this->created=new \DateTime(date("Y-m-d H:i:s"));
	$this->modified=new \DateTime(date("Y-m-d H:i:s"));


	$this->accountPurchaseNodes = new \Doctrine\Common\Collections\ArrayCollection();
	$this->purchaseOrderNodes = new \Doctrine\Common\Collections\ArrayCollection();
}

public function __get($property){
	switch($property){
		case 'chargeTotal':
		if ($this->$property){
			return $this->$property/100;
		}
		else{
			return 0;
		}
			break;
		case 'created':
			return $this->created->format("Y-m-d H:i:s");
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

	$this->modified=new \DateTime(date("Y-m-d H:i:s"));
}

}