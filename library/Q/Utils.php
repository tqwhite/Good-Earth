<?php
namespace Q;

//Q\Utils::dump('hello');

class Utils{

static function dump($data, $name = false, $html = null, $echo_out = true){
	require_once('dump.php');
	dump($data, $name, $htm, $echo_out);
}

}