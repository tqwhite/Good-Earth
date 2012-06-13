<?php

//this is a base model for file handling classes

class Application_Model_Base
{

public function set($name, $value){
	$this->$name=$value;
}

}

