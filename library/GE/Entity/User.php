<?php
namespace GE\Entity;
/**
*
* @Entity
* @Table(name="users")
* @author tqii
*
*
**/
class User /*extends Base*/{

	const tableName='users';
	
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
	private $firstName;

	/**
	 * @column(type="string", length=60, nullable=false)
	 * @var string
	 **/
	private $emailAdr;

	/**
	 * @column(type="string", length=60, nullable=false)
	 * @var string
	 **/

	private $lastName;

	/**
	 * @column(type="string", length=60, nullable=false, unique="true")
	 * @var string
	 **/

	private $userName;

	/**
	 * @column(type="string", length=60, nullable=false)
	 * @var string
	 **/

	private $password;

	/**
	 * @column(type="integer", nullable=true)
	 * @var string
	 **/

	private $emailStatus;

	/**
	 * @column(type="string", length=60)
	 * @var string
	 **/

	private $confirmationCode; //has md5(refId) if email is NOT confirmed

	/**
	 * @column(type="string", length=20)
	 * @var string
	 **/

	private $phoneNumber;

	/**
	 * @column(type="string", length=60)
	 * @var string
	 **/

	private $street;

	/**
	 * @column(type="string", length=60)
	 * @var string
	 **/

	private $city;

	/**
	 * @column(type="string", length=2)
	 * @var string
	 **/

	private $state;

	/**
	 * @column(type="string", length=5)
	 * @var string
	 **/

	private $zip;


	/**
	 *
	 * @ManyToOne(targetEntity="Account", cascade={"all"}, fetch="EAGER")
	 * @JoinColumn(name="accountRefId", referencedColumnName="refId")
	 *
	 **/
	private $account;



	/**
	 * @column(type="string", length=60, nullable=true)
	 * @var string
	 **/

	private $resetCode;

	/**
	 * @column(type="datetime", nullable=true)
	 * @var datetime
	 **/

	private $resetDate;


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
	 * @column(type="string", length=10, nullable=true)
	 * @var string
	 **/

	private $role;

public function __construct(){
	if (!$this->refId){$this->refId =  \Q\Utils::newGuid();}
	$this->created=new \DateTime(date("Y-m-d H:i:s"));
	$this->modified=new \DateTime(date("Y-m-d H:i:s"));
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
	
	switch ($property){
		case 'password':
			$this->$property=md5($value);
		break;
		default:
			$this->$property=$value;
		break;
	}

	$this->modified=new \DateTime(date("Y-m-d H:i:s"));
}

}