<?/*php require_once($_SERVER['DOCUMENT_ROOT'].'/tools/mailNotificationInclude.php');*/?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN'
   'http://www.w3.org/TR/html4/strict.dtd'>
<html>
<head>
<title>Mysql Testing</title>

<script type='text/javascript' src='http://code.jquery.com/jquery-1.6.1.min.js'></script>
<!--link rel='stylesheet' type='text/css' href='css/main.css' /-->

<style type='text/css'>

	body {color:orange;}
	
</style>

</head>
<body>

<?php

$mysqlHandle=mysql_connect('localhost','tq','');

if (!$mysqlHandle){ 
echo "mysql_error()=".mysql_error()."</br>";
}
else{
	
$result = mysql_query("show databases")
or die(mysql_error());  


$row = mysql_fetch_array( $result );

print_r($row);
}

phpinfo();
?>
	
</body>

<script type='text/javascript'>
/* <![CDATA[ */
$(document).ready(function(){


	$('body').append("<p>Thanks for visiting!");


});
/* ]]> */
</script>

</html>