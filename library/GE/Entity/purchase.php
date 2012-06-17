<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tqwhite
 * Date: 6/17/12
 * Time: 8:36 AM
 * To change this template use File | Settings | File Templates.
 */

namespace GE\Entity;

/**
 *@Table(name="purchased")
 * @Entity
 * @HasLifecycleCallbacks
 */
class Purchase {
	/**
	 * @var integer $id
	 * @column(name="id", type="integer", nullable=false)
	 * @Id
	 * @GeneratedValue(strategy="IDENTITY")
	 **/
	private $id;

	/**
	 * @var User
	 * @ManyToOne(targetEntity="User")
	 * @JoinColumns({
	 * 	@JoinColumn(name="user_id", referencedColumnName="id")
	 * })
	 **/
	private $user;


	/**
	 * @ManyToMany(targetEntity="Product", inversedBy="purchases", cascade={"persist,remove"})
	 * @JoinTable(
	 *	name="purchases_products",
	 * 	joinColumns={@joinColumn(name="purchase_id", referencedColumnName="id")},
	 *	inverseJoinColumns={@joinColumn(name="product_id", referencedColumnName="id")}
	 * )
	 **/
	private $products;

	/**
	 * @column(type="datetime", nullable=false)
	 * @var datetime
	 **/

	private $created;

	/**
	 * @column(type="string")
	 * @var string
	 **/

	private $storeName;

	/**
	 * for mysql type should be equal to "float"
	 * or, edit generated sequel to change NUMERIC to DECIMAL (mistake made by Doctrine)
	 * @column(type="decimal", precision=2, scale=4)
	 * @var decimal
	 **/

	private $amount=0;

	public function __construct(){

		$this->created=new \DateTime(date("Y-m-d H:i:s"));
		$this->products=new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function __get($property){
		return $this->$property;
	}

	public function __set($property, $value){
		$this->$property=$value;
	}

	/**
	 * @PrePersist @PreUpdate
	 */
	public function updateTotal(){
	    $total=0;
	    foreach($this->products as $product){
		$total=$total+$product->amount;
	    }
	    $this->amount=$total;
	}
}
