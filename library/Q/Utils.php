<?php
namespace Q;

//Q\Utils::dump('hello');

class Utils{

static function dump($data, $name = false, $html=null, $echo_out = true){
	require_once('dump.php');
	return dump($data, $name, $html, $echo_out);
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

static function flattenToList($inArray){
	$outArray=array();
	foreach ($inArray as $label=>$data){
		$outArray[]=array($label, $data);
	}
	return $outArray;
}

static function callStack($stringFlag){
	$stackArray=debug_backtrace();

	$colorA='#ddf';
	$colorB='#dfd';

	$list=$stackArray;
	$outString='';
	$currColor=$colorA;
	for ($i=0, $len=count($list); $i<$len; $i++){
		$element=$list[$i];

		$element['class']=(isset($element['class']))?$element['class']:'';
		$element['line']=(isset($element['line']))?$element['line']:'';
		$element['file']=(isset($element['file']))?$element['file']:'';
		$outString.="<tr style='background:$currColor;'><td>$i</td><td>{$element['class']}::{$element['class']} (line {$element['line']})</td></tr>";
		$outString.="<tr style='background:$currColor;'><td>&nbsp;</td><td>{$element['file']}</td></tr>";
		$outString.="<tr style='background:transparent;'><td colspan='2'>&nbsp;</td></tr>";
		$currColor=($currColor==$colorA)?$colorB:$colorA;
	}

	$outString="<table style='font-family:sans-serif;'>$outString</table>";

	if ($stringFlag){
		return $outString;
	}
	else{
		echo $outString;
	}
}

static function isList($inData, $debug=false){
if ($debug){
error_log("$debug is a ".get_class($inData)." [library/Q/Utils.php]");
}
	if (get_class($inData)=='Doctrine\ORM\PersistentCollection'){
		$isList=true;
	}
	else{
		foreach ($inData as $label=>$data){
			if ($label===0){
				$isList=true;
				break;
			}
			else{
				$isList=false;
			}
		}
	}
	return $isList;

}

static function getFromDottedPath($assocArray, $path){
	$target=$assocArray;
	$elements=explode('.', $path);

	if (!$path){
		return $assocArray;
	}

	if (count($elements)<2){
		return $assocArray[$path];
	}

	//else
	for ($i=0, $len=count($elements); $i<$len; $i++){
		$element=$elements[$i];
		if ($element!==''){ //mainly eliminates trailing periods but would also eliminate double periods
			$target=$target[$element];
			if (!$target){return '';}
		}
	}
	return $target;
}

static function intoSimpleArray($listArray, $fieldName){
	$outArray=array();
	
		foreach ($listArray as $label=>$data){
			$outArray[]=$data[$fieldName];
		}
	return $outArray;
}

static function filterAllowed($haystack, $fieldName, $needleList){
		$outArray=array();
		
		foreach ($haystack as $label=>$data){
			if (in_array($data[$fieldName], $needleList)){
				$outArray[]=$data;
			}
		}
		
		return $outArray;

}

}//end of class


