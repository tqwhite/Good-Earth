<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="gradeSchoolNodes")
* @author tqii
*
*
**/
class GradeSchoolNode /*extends Base*/{
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
	private $descriptor;


	/**
	 *
	 * @ManyToOne(targetEntity="School", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="schoolRefId", referencedColumnName="refId")
	 *
	 **/
	private $school;


	/**
	 *
	 * @ManyToOne(targetEntity="GradeLevel", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="gradeLevelRefId", referencedColumnName="refId")
	 *
	 **/
	private $gradeLevel;

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
	$this->$property=$value;
}
}