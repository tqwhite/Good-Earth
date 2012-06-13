<?php require_once($_SERVER['DOCUMENT_ROOT'].'/php/mailNotificationInclude.php');?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN'
   'http://www.w3.org/TR/html4/strict.dtd'>
<html>
<head>
<title>Template Page Title</title>

<script type='text/javascript' src='http://code.jquery.com/jquery-1.6.1.min.js'></script>
<script type='text/javascript' src='slider/lib/slider.js'></script>
<script type="text/javascript" src="jquery.dogSlider.js"></script>
<link href="slider/demo/slider.min.css" rel="stylesheet" type="text/css" /> 

<style type='text/css'>

	body {color:orange;}
	
</style>

</head>
<body>

	Hello World
	<div id='sliderHolder' style='height:308px;width:605px;'></div>
	
</body>

<script type='text/javascript'>
/* <![CDATA[ */
$(document).ready(function(){

var domObj=$('#sliderHolder');

	domObj.tqCustomPlugin();

});
/* ]]> */
</script>

</html>