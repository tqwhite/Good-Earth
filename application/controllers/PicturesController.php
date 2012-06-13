<?php

class PicturesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

public function listAction(){
	$outArray=array();
	
	
	$this->fileModel = new Application_Model_Picture();
	
	$fileInfoList=$this->fileModel->set('subDirName', 'wedding');
	$fileInfoList=$this->fileModel->getList();

	
	$outArray['data']=$fileInfoList;
	
	$this->_helper->json($outArray);
}


}

