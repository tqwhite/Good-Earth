<?php

namespace Q\Plugin\Authorize;

/**
*
* @author tqii
*
*
**/

class Adapter implements \Zend_Auth_Adapter_Interface{

private $_userName;
private $_password;

public function __construct($userName, $password){
	$this->_userName=$userName;
	$this->_password=$password;
}

public function authenticate(){

	$userObj=new \Application_Model_User();
	$user=$userObj->getUserByUserId($this->_userName);

	if ($user && $this->_password==$user->password){
		return (new \Zend_Auth_Result(
				\Zend_Auth_Result::SUCCESS,
				$user
			));
	}
	else
	{
		return (new \Zend_Auth_Result(
				\Zend_Auth_Result::FAILURE,
				'Password and User ID were not correct'
			));
	}
}

} //end of class