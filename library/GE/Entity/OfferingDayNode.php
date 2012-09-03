<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="offeringDayNodes")
* @author tqii
*
*
**/
class OfferingDayNode /*extends Base*/{
	/**
	* @var string $id
	* @column(name="refId", type="string", length=36, nullable=false, unique="true")
	* @Id
	**/
	private $refId;


	/**
	 *
	 * @ManyToOne(targetEntity="Offering", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="offeringRefId", referencedColumnName="refId")
	 *
	 **/
	private $offering;


	/**
	 *
	 * @ManyToOne(targetEntity="Day", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="dayRefId", referencedColumnName="refId")
	 *
	 **/
	private $day;


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