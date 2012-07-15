<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="gradeLevels")
* @author tqii
*
*
**/
class GradeLevel /*extends Base*/{
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
	private $title;



    /**
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="GradeSchoolNode", mappedBy="gradeLevel", cascade={"persist", "remove"});
     */
    private $schoolNodes;




    /**
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="OfferingGradeLevelNode", mappedBy="days", cascade={"persist", "remove"});
     */
    private $offeringGradeLevelNodes;



	/**
	 * @column(type="datetime", nullable=false)
	 * @var datetime
	 **/

	private $created;

public function __construct(){
	if (!$this->refId){$this->refId =  uniqid();}
	$this->created=new \DateTime(date("Y-m-d H:i:s"));
}

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
	$this->$property=$value;
}
}