<?php

//namespace GE\Entity;

/**
*
* @author tqii
*
*
**/

class Application_Model_Stats{

	protected $countries=array();

	public function addCountry($country=null){

		if ($country==null){
			$this->clearList();
		}

		if (array_key_exists($country, $this->countries)){
			$this->countries[$country]++;
		}
		else{
			$this->countries[$country]=1;
		}
	}

	public function getCountries(){
		return array_keys($this->countries);
	}

	private function clearList(){
		$this->countries=array();
	}

} //end of class