<?php

class Application_Model_Picture extends Application_Model_File{
	private $_pictureFileList;
	private $_thumbnailDirName='thumbnails/';
	private $_slideshowUrlPrefix;
	private $_scaleFactor; //if less than 1, it's a percent, if >1, it's max dimension (h or w)

	public function __construct(){
		define('SLIDESHOW_DIRECTORY_PATH', DOCROOT_DIRECTORY_PATH."media/slideshows/");
		define('SLIDESHOW_RELURL_PREFIX', "/media/slideshows/");
		$this->_slideshowUrlPrefix=SLIDESHOW_RELURL_PREFIX;
	}

	public function getList($subDirName=''){

		$picturePath=SLIDESHOW_DIRECTORY_PATH.$this->subDirName.'/';

		$this->_pictureFileList=parent::_getFileList($picturePath);
		return $this->_fileListToImageList($this->_pictureFileList);
	}

	private function _fileListToImageList($fileList){
		$outArray=array();
		foreach($fileList as $fileObj){
			$thumbnailDirectoryPath=$fileObj['directoryPath'].$this->_thumbnailDirName;
			$fullsizeFilePath=$fileObj['directoryPath'].$fileObj['fileName'];
			$thumbnailFilePath=$thumbnailDirectoryPath.$fileObj['fileName'];
			list($width, $height) = getimagesize($fullsizeFilePath);

			$item=$fileObj;

			$item['imageType']=$this->_imageType($item['fileName']);
			$item['fullsize']['uri']=$this->_fullSizeUri($item);
			$item['fullsize']['size']=array(height=>$height, width=>$width);
			$item['fullsize']['path']=$fullsizeFilePath;
			$item['imageType']=$this->_imageType($item['fileName']);
			$item['thumbnailDirectoryPath']=$thumbnailDirectoryPath;

			$thumbInfo=$this->_thumbnailUri($item);
			$item['thumbnail']=array();
			foreach ($thumbInfo as $label=>$data){
				$item['thumbnail'][$label]=$data;
			}

			$item['thumbnail']['path']=$thumbnailFilePath;

			list($width, $height) = getimagesize($thumbnailFilePath);
			$item['thumbnail']['size']=array(height=>$height, width=>$width);

			$outArray[]=$item;
		}
		return $outArray;
	}

private function _fullsizeUri($fileObj){
	return $this->_slideshowUrlPrefix.$this->subDirName.'/'.$fileObj['fileName'];
}

private function _thumbnailUri($fileObj){
	$outObj=array();
	$fileUri=$this->_slideshowUrlPrefix.$this->subDirName.'/'.$this->_thumbnailDirName.$fileObj['fileName'];
	$thumbnailFilePath=$fileObj['thumbnailDirectoryPath'].$fileObj['fileName'];

	$outObj['thumbnailFilePath']=$this->_fileExists($thumbnailFilePath);

	if (!$this->_fileExists($thumbnailFilePath)){
		$outObj['noThumbFile']=true;
		$result=$this->_updateThumbnail($fileObj);
	}

	if ($result){$resultMsg='new thumbnail';}
	else{$resultMsg='no new thumbnail';}

	$outObj['uri']=$fileUri;
	$outObj['newThumbnail']=$resultMsg;

	return $outObj;
}

private function _updateThumbnail($fileObj){
	$fullsizeFilePath=$fileObj['directoryPath'].$fileObj['fileName'];
	$thumbnailFilePath=$fileObj['thumbnailDirectoryPath'].$fileObj['fileName'];


	switch ($this->_imageType($fileObj['fileName'])){
		case 'jpg':
			$size=$this->_createJpgImageFile($thumbnailFilePath, $fullsizeFilePath);
			break;
		case 'png':

			break;
	}

	return $size;
}

private function _imageType($fileName){
	$tmp=explode('.',$fileName);
	if (preg_match('/jpg|jpeg/i',$tmp[1])){
		return 'jpg';
	}
	if (preg_match('/png/i',$tmp[1])){
		return 'png';
	}
}

private function _createJpgImageFile($dest, $source, $newHeight, $newWidth){

	list($width, $height) = getimagesize($source);

	$scaleFactor=$this->_scaleFactor?$this->_scaleFactor:.15;

	if (true || $scaleFactor<=1){
		$newWidth=$scaleFactor*$width;
		$newHeight=$scaleFactor*$height;
	}
	else{
		//calculate max size alternative someday
	}

	$sourceImg=imagecreatefromjpeg($source);
	$destImg=ImageCreateTrueColor($newWidth, $newHeight);
	imagecopyresized($destImg, $sourceImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

	$result=imagejpeg($destImg, $dest);
	return $result;

}
}

