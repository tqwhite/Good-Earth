<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="schools")
* @author tqii
*
*
**/
class School /*extends Base*/{

	const tableName='schools';
	
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
	 * @column(type="string", length=60, nullable=false)
	 * @var string
	 **/
	private $name;



	/**
	 * @column(type="string", length=60, nullable=true)
	 * @var string
	 **/
	private $emailAdr;



	/**
	 * @column(type="string", nullable=false)
	 * @var string
	 **/

	private $currPeriod;

	/**
	 * @column(type="date", nullable=false)
	 * @var date
	 **/

	private $dateOrderingBegin;


	/**
	 * @column(type="date", nullable=false)
	 * @var date
	 **/

	private $dateOrderingEnd;

	/**
	 * @column(type="date", nullable=false)
	 * @var date
	 **/

	private $datePeriodBegin;


	/**
	 * @column(type="date", nullable=false)
	 * @var date
	 **/

	private $datePeriodEnd;



	/**
	 *
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="Student", mappedBy="school", cascade={"persist", "remove"});
	 **/

	private $students;


	/**
	 *
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="GradeSchoolNode", mappedBy="school", cascade={"persist", "remove"});
	 **/

	private $gradeLevelNodes;


    /**
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="OfferingSchoolNode", mappedBy="school", cascade={"persist", "remove"});
     */
    private $offeringSchoolNodes;



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
	 * @column(type="boolean", nullable=true)
	 * @var integer
	 **/

	private $suppressDisplay;



public function __construct(){
	if (!$this->refId){$this->refId =  \Q\Utils::newGuid();}
	$this->created=new \DateTime(date("Y-m-d H:i:s"));
	$this->modified=new \DateTime(date("Y-m-d H:i:s"));

	$this->students = new \Doctrine\Common\Collections\ArrayCollection();
	$this->gradeLevelNodes = new \Doctrine\Common\Collections\ArrayCollection();
	$this->offeringSchoolNodes = new \Doctrine\Common\Collections\ArrayCollection();
}

public function __get($property){
	switch($property){
		case 'datePeriodBegin':
		case 'datePeriodEnd':
		case 'dateOrderingBegin':
		case 'dateOrderingEnd':
			return $this->$property->format("Y-m-d");
			break;
		case 'created':
			return $this->$property->format("Y-m-d H:i:s");
			break;
		default:
			return $this->$property;
			break;
	}
}

public function __set($property, $value){
	
	switch($property){
		case 'datePeriodBegin':
		case 'datePeriodEnd':
		case 'dateOrderingBegin':
		case 'dateOrderingEnd':
			$this->$property=$this->helixToDate($value);
			break;
		default:
			$this->$property=$value;
			break;
	}

	$this->modified=new \DateTime(date("Y-m-d H:i:s"));
}

}