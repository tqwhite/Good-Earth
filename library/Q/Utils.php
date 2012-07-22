<?php
namespace Q;

//Q\Utils::dump('hello');

class Utils{

static function dump($data, $name = false, $html=null, $echo_out = true){
	require_once('dump.php');
	dump($data, $name, $html, $echo_out);
}

static function dumpWeb($name, $label=null){
		$dumpHtml=true; // want html, not newlines, etc
		$dumpEcho=true; //sends result to string, true to use echo
		return self::dump($name, $label, $dumpHtml, $dumpEcho);
	}

static function dumpCli($name, $label=null){
		$dumpHtml=false; // want newlines, not html, etc
		$dumpEcho=true; //sends result to string, true to use echo
		return self::dump($name, $label, $dumpHtml, $dumpEcho);
	}

static function dumpWebString($name, $label=null){
		$dumpHtml=true; // want html, not newlines, etc
		$dumpEcho=false; //sends result to string, true to use echo
		return self::dump($name, $label, $dumpHtml, $dumpEcho);
	}

static function dumpCliString($name, $label=null){
		$dumpHtml=false; // want newlines, not html, etc
		$dumpEcho=false; //sends result to string, true to use echo
		return self::dump($name, $label, $dumpHtml, $dumpEcho);
	}

static function buildArray($inObj, $fieldNames){

	if (is_string($fieldNames)){
		$fieldNames=preg_replace('/,/', ' ', $fieldNames);
		$fieldNames=preg_replace('/\W+/', ' ', $fieldNames);
		$fieldNames=trim($fieldNames);
		$nameList=explode(' ', $fieldNames);
	}
	else if (is_array($fieldNames)){
		$nameList=$fieldNames;
	}
	else{
    throw new \Exception('Q\\Utils::buildArray says, $fieldNames must be string or array');
	}

	$outArray=array();
	if ($inObj[0]){ //can be addressed as an array

		$list=$inObj;
		for ($i=0, $len=count($list); $i<$len; $i++){
			$itemObj=$list[$i];
			$itemArray=array();

			$outList=array();
			for ($j=0, $len2=count($nameList); $j<$len2; $j++){
				$itemArray[$nameList[$j]]=$itemObj->$nameList[$j];
			}
			$outArray[]=$itemArray;

		}

	}
	else{
		$hasPropertiesFlag=false;
		foreach ($inObj as $label=>$data){
			$hasPropertiesFlag=true;
			break;
		}

		if ($hasPropertiesFlag){
			$list=$nameList;
			$outList=array();
			for ($i=0, $len=count($list); $i<$len; $i++){
				$outList[$list[$i]]=$inObj->$list[$i];
			}
			$outArray=$outList;
		}

	}

	return $outArray;
}

function newGuid(){
	//thanks: Kristof_Polleunis, http://php.net/manual/en/function.com-create-guid.php
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
        return $uuid;
    }
}

}//end of class


