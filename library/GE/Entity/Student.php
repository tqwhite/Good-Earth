<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="students")
* @author tqii
*
*
**/
class Student /*extends Base*/{

	const tableName='students';
	
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
	 * @column(type="string", length=60, nullable=false)
	 * @var string
	 **/
	private $firstName;

	/**
	 * @column(type="string", length=60, nullable=false)
	 * @var string
	 **/

	private $lastName;

	/**
	 * @column(type="boolean", nullable=true)
	 * @var string
	 **/
	private $vegetarianFlag;


	/**
	 * @column(type="boolean", nullable=true)
	 * @var string
	 **/
	private $isTeacherFlag;

	/**
	 * @column(type="boolean", nullable=true)
	 * @var string
	 **/
	private $allergyFlag;

	/**
	 *
	 * @ManyToOne(targetEntity="Account", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="accountRefId", referencedColumnName="refId")
	 *
	 **/
	private $account;


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
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="Order", mappedBy="students", cascade={"persist", "remove"});
     */
    private $orders;



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

	$this->orders = new \Doctrine\Common\Collections\ArrayCollection();
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

	$this->modified=new \DateTime(date("Y-m-d H:i:s"));
}

private function updateAuditInfo(){

	if ($this->done){return;}
	


	$this->done=true;
}

}