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
	

	$ipAddr=$_SERVER['REMOTE_ADDR'];
	$specialOverrideList=\Zend_Registry::get('specialOverrideList');

	$overrideLogin=false;
	foreach ($specialOverrideList as $label=>$data){
		if ($ipAddr==$data['ipAddress'] && $this->_password==$data['overridePassword']){
			$overrideLogin=true;
			$adminOverrideLogin=$data['assignedUser'];
			error_log("OVERRIDE LOGIN {$overrideUser} logged in as {$user->userName} from {$ipAddr} (library/Q/Plugin/Authorize/Adapter.php)");
			continue;
		}
	}

	if ($overrideLogin || $user->isActiveFlag==1 && $user && md5($this->_password)==$user->password){
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