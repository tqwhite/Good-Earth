<?php

class OfferingController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function listAction()
    {
		$periodList=$this->getCurrPeriodList();
	
		$accessObj=new \Application_Model_Offering();
		$list=$accessObj->getByCurrPeriod($periodList, 'record');

		if (count($list)){$status=1;}
		else {$status=-1;}

		$this->_helper->json(array(
			status=>$status,
			messages=>$messageList,
			data=>\Application_Model_Offering::formatFilteredOutput($list, 'limited', $periodList)
			)
		);

    }
    
    private function getCurrPeriodList(){
    
    	$outArray=array();
    
		$accessObj=new \Application_Model_School();
		$list=$accessObj->getList('record');

		for ($i=0, $len=count($list); $i<$len; $i++){
			$element=$list[$i];
				$outArray[$element->refId]=$element->currPeriod;
		}
		
		return $outArray;
    }

    public function getAllAction()
    {

		$accessObj=new \Application_Model_Offering();
		$list=$accessObj->getList('record');

		if (count($list)){$status=1;}
		else {$status=-1;}

		$this->_helper->json(array(
			status=>$status,
			messages=>$messageList,
			data=>\Application_Model_Offering::formatOutput($list, 'limited')
			)
		);

    }


}



