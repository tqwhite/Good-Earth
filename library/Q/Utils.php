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
}