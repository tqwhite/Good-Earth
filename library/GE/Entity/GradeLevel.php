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



public function __construct(){
	if (!$this->refId){$this->refId =  uniqid();}
}

public function __get($property){
	return $this->$property;
}

public function __set($property, $value){
	$this->$property=$value;
}
}