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
	private $name;

	/**
	 * @column(type="string", length=60, nullable=true)
	 * @var string
	 **/
	private $emailAdr;



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

public function __construct(){
	if (!$this->refId){$this->refId =  \Q\Utils::newGuid();}
	$this->created=new \DateTime(date("Y-m-d H:i:s"));

	$this->students = new \Doctrine\Common\Collections\ArrayCollection();
	$this->gradeLevelNodes = new \Doctrine\Common\Collections\ArrayCollection();
	$this->offeringSchoolNodes = new \Doctrine\Common\Collections\ArrayCollection();
}

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
	$this->$property=$value;
}
}