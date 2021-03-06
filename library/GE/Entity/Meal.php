<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="meals")
* @author tqii
*
*
**/
class Meal /*extends Base*/{

	const tableName='meals';
	
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
	 * @column(type="string", length=200, nullable=false)
	 * @var string
	 **/
	private $name;

	/**
	 * @column(type="text", nullable=false)
	 * @var string
	 **/
	private $description;




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
	 *
	 * @param \Doctrine\Common\Collections\Collection $property
	 * @OneToMany(targetEntity="Offering", mappedBy="meal", cascade={"persist", "remove"});
	 **/

	private $offerings;


public function __construct(){
	if (!$this->refId){$this->refId =  \Q\Utils::newGuid();}
	$this->created=new \DateTime(date("Y-m-d H:i:s"));
	$this->modified=new \DateTime(date("Y-m-d H:i:s"));

	$this->offerings = new \Doctrine\Common\Collections\ArrayCollection();
}

public function __get($property){
	switch($property){
		case 'suggestedPrice':
			return $this->$property/100;
			break;
		default:
			return $this->$property;
			break;
	}
}

public function __set($property, $value){
	
	switch($property){
		case 'suggestedPrice':
			$this->$property=$value*100;
			break;
		default:
			$this->$property=$value;
			break;
	}

	$this->modified=new \DateTime(date("Y-m-d H:i:s"));
}

}