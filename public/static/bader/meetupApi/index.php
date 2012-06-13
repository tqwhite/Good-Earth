<?/*php require_once($_SERVER['DOCUMENT_ROOT'].'/tools/mailNotificationInclude.php');*/?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN'
   'http://www.w3.org/TR/html4/strict.dtd'>
<html>
<head>
<title>Template Page Title</title>

<script type='text/javascript' src='http://code.jquery.com/jquery-1.6.1.min.js'></script>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type='text/javascript' src='http://genericWhite.local/static/bader/meetupApi/gmaps/gmaps.js'></script>

<script type='text/javascript' src='http://genericWhite.local/static/bader/meetupApi/jquery.qMeetup.js'></script>


<!--link rel='stylesheet' type='text/css' href='css/main.css' /-->

<style type='text/css'>

	body {color:orange;}
	
</style>

</head>
<body>
	<div style='border:1pt solid gray;clear:both;height:100px;width:600px;margin-bottom:5px;'>
		Next Event:<br/>
		<div  id='C'></div></div>
	<div style='float:left;border:1pt solid red;margin-left:5px;' id='A'></div>
	<div style='float:left;border:1pt solid green;margin-left:5px;' id='B'></div>
	
</body>


<script type='text/javascript'>
/* <![CDATA[ */
	$(document).ready(function(){
		

		$('#A').meetup({
			blockWrapperTemplate:"<div style='width:400px;font-size:10pt;color:gray;margin-left:15px;'><!blockText!></div>",
			defaultTemplate:"<div style='font-family:sans-serif;margin-bottom:10px;'><span style='color:black;'><!title!></span><br/><!dateTime!>&nbsp;&nbsp;&nbsp;<a href='<!url!>' style='color:#19BBB6;text-decoration:none;'>Details/Signup</a></div>",
			specialTemplates:[
				{
					selectionString:'Political',
					template:"<div style='font-family:sans-serif;margin-bottom:10px;'><span style='color:red;'><!title!></span><br/><!dateTime!> <a href='<!url!>'>Details/Signup</a></div>"
				},
				{
					selectionString:'Coffee',
					template:"<div style='font-family:sans-serif;margin-bottom:10px;'><span style='color:green;'><!title!></span><br/><!dateTime!> <a href='<!url!>'>Details/Signup</a></div>"
				}
			],
			exclusionStringList:[ //optional, ignored if inclusionStringList is specified
				{selectionString:'Door'}
			],
			inclusionStringList:[
			],
			dateTimeFormat:2, //optional, defaults to zero; 0 - strip year and seconds, 1 - strip seconds, 2 - complete (Wed Jun 20 2012 13:30:00)
			highlightStyles:{
					'background':'#F7CAED',
					'padding':'5px',
					'border':'1pt solid gray',
					'font-family':'sans-serif'
				},
		//	highlightClassName:'highlightMeetupItemClass', //if defined, used instead of highlightStyles
			accessParameters:{
		//		url:"https://api.meetup.com/2/events", //optional, defaults to https://api.meetup.com/2/events
				key:'a473b1333b67532920534c14a6212',
				group_urlname:'baderzone',
				page:20 //optional, defaults to 1000
			}
		});
		
		
		
		$('#B').meetup({
			blockWrapperTemplate:"<div style='width:400px;font-size:10pt;color:gray;margin-left:15px;'><!blockText!></div>",
			defaultTemplate:"<div style='font-family:sans-serif;margin-bottom:10px;'><span style='color:black;'><!title!></span><br/><!dateTime!>&nbsp;&nbsp;&nbsp;<a href='<!url!>' style='color:#19BBB6;text-decoration:none;'>Details/Signup</a></div>",
			specialTemplates:[
				{
					selectionString:'Door',
					template:"<div style='font-family:sans-serif;margin-bottom:10px;'><span style='color:red;'><!title!></span><br/><!dateTime!> <a href='<!url!>'>Details/Signup</a></div>"
				}
			],
			exclusionStringList:[
			],
			inclusionStringList:[
				{selectionString:'Door'}
			],
			dateTimeFormat:2, //optional, defaults to zero; 0 - strip year and seconds, 1 - strip seconds, 2 - complete (Wed Jun 20 2012 13:30:00)
			highlightStyles:{
					'background':'#C9F6E8',
					'padding':'5px',
					'border':'1pt solid gray',
					'font-family':'sans-serif'
				},
		//	highlightClassName:'highlightMeetupItemClass', //if defined, used instead of highlightStyles
			accessParameters:{
		//		url:"https://api.meetup.com/2/events", //optional, defaults to https://api.meetup.com/2/events
				key:'a473b1333b67532920534c14a6212',
				group_urlname:'baderzone',
				page:20 //optional, defaults to 1000
			}
		});		
		
		$('#C').meetup({
			blockWrapperTemplate:"<div style='width:400px;font-size:10pt;color:gray;margin-left:15px;'><!blockText!></div>",
			defaultTemplate:"<div style='font-family:sans-serif;margin-bottom:10px;'><span style='color:black;'><!title!></span><br/><!dateTime!>&nbsp;&nbsp;&nbsp;<a href='<!url!>' style='color:#19BBB6;text-decoration:none;'>Details/Signup</a></div>",
			specialTemplates:[
				{
					selectionString:'Door',
					template:"<div style='font-family:sans-serif;margin-bottom:10px;'><span style='color:red;'><!title!></span><br/><!dateTime!> <a href='<!url!>'>Details/Signup</a></div>"
				}
			],
			exclusionStringList:[
			],
			inclusionStringList:[
			],
			dateTimeFormat:2, //optional, defaults to zero; 0 - strip year and seconds, 1 - strip seconds, 2 - complete (Wed Jun 20 2012 13:30:00)
			highlightStyles:{
					'background':'#C9F6E8',
					'padding':'5px',
					'border':'1pt solid gray',
					'font-family':'sans-serif'
				},
		//	highlightClassName:'highlightMeetupItemClass', //if defined, used instead of highlightStyles
			accessParameters:{
		//		url:"https://api.meetup.com/2/events", //optional, defaults to https://api.meetup.com/2/events
				key:'a473b1333b67532920534c14a6212',
				group_urlname:'baderzone',
				page:1 //optional, defaults to 1000
			}
		});
	
	});
/* ]]> */
</script>

</html>