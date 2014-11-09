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

	const tableName='orders';
	
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
	* @var string
	* @column(name="auditInfo", type="string", length=16777215, nullable=true)
	**/
	private $auditInfo;

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
	 * @column(type="string", nullable=true)
	 * @var string
	 **/

	private $currPeriodFull;


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

	$this->purchaseOrderNodes = new \Doctrine\Common\Collections\ArrayCollection();
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

	$this->updateAuditInfo();
	$this->modified=new \DateTime(date("Y-m-d H:i:s"));
}

private function updateAuditInfo(){

	if ($this->done){return;}
	
	$debugObject=\Zend_Registry::get('debugObject');
	$debugObject=\Q\Utils::dumpWebString($debugObject, "debugObject");

	if (isset($this->auditInfo)){
		$this->auditInfo.=$debugObject;
	}
	else{
		$this->auditInfo=$debugObject;
	}

	$this->done=true;
}

}