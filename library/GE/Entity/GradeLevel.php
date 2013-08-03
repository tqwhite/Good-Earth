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

	const tableName='gradeLevels';
	
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
	 *
	 * @column(name="seqNum", type="integer", nullable=true)
	 **/

	 private $seqNum;



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

	$this->schoolNodes = new \Doctrine\Common\Collections\ArrayCollection();
	$this->offeringGradeLevelNodes = new \Doctrine\Common\Collections\ArrayCollection();

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
}
}