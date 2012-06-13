<?php

//this is a base model for file handling classes

class Application_Model_File extends Application_Model_Base
{

protected $_directoryPath;

protected function _getFileList($directoryPath){
	$this->set(_directoryPath, $directoryPath.'/');
	
	$outArray=array();
	$directoryPath=$this->_directoryPath;
	
	if (is_dir($directoryPath)) {
		if ($handle = opendir($directoryPath)) {
			while (($file = readdir($handle)) !== false) {
			
				$fileType=filetype($directoryPath . $file);
				
				if ($fileType=='file' && $file!='.DS_Store'){
					$outArray[]=array(
						fileName=>$file,
						directoryPath=>$directoryPath,
						filetype=>$fileType
					);
				}
				
			}
			closedir($handle);
		}
	}
	
	return $outArray;

}

protected function _fileExists($fileString){
	return file_exists($fileString);
}

}

