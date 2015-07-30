steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Customer.Schedule
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Customer.Schedule',
/** @Static */
{
	defaults : {}
},
/** @Prototype */
{

init: function(el, options) {
	this.baseInits();
	
	this.options=options;

	qtools.validateProperties({
		targetObject:options,
		targetScope: this, //will add listed items to targetScope
		propList:[
			{name:'account'},
			{name:'schools'},
			{name:'gradeLevels'},
			{name:'lunchButtonHandler', optional:true},
			{name:'adminFlag', optional:true}
		],
		source:this.constructor._fullName
 	});
 	
 	if (!this.lunchButtonHandler && !this.adminFlag){
 		qtools.consoleMessage("schedule/schedule.js says, '!this.lunchButtonHandler && !this.adminFlag'");
 	}

	qtools.validateProperties({
		targetObject:this.account,
		targetScope: this, //will add listed items to targetScope
		propList:[
			{name:'users'},
			{name:'students'}
		],
		source:this.constructor._fullName
 	});

	this.initControlProperties();
	this.initDisplayProperties();

	options=options?options:{};
	if (options.initialStatusMessage){this.initialStatusMessage=options.initialStatusMessage;}
	this.options=options;

	this.initDisplay();

},

update:function(options){
	this.init(this.element, options || this.options);
},

initDisplayProperties:function(){

	nameArray=[];

	name='newButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='editButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});

	if (!this.adminFlag){
	name='lunchButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	}

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));


},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
	this.lockAddStudent=false;
	
},

initDisplay:function(inData){

	var html=$.View('//good_earth_store/controller/customer/schedule/views/init.ejs',
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper,
			formData:{
				account:this.account,
				schools:this.schools
			}
		})
		);
	this.element.html(html);
	this.initDomElements();
},

initDomElements:function(){


/*

var cookieData=GoodEarthStore.Models.LocalStorage.getCookieData('gradeLevelNotification').data;

if (!GLOBALS.done && typeof(cookieData)=='undefined'){

	$('#notification').show();
	$('body')
	.click(function(){
	$('#notification').fadeOut(3000);
	});

	GLOBALS.done=true;
	GoodEarthStore.Models.LocalStorage.setCookie('gradeLevelNotification', true, { expires: 7, path: '/'});
}

sample notification =====

<div id='notification' style='
display:none;
background: none repeat scroll 0 0 #A0F4F7;
border: 1pt solid gray;
bottom: 40px;
color: red;
height: 90px;
padding: 10px;
position: absolute;
top: 40px;
left:30px;
width: 400px;'>
We have had some trouble with our database  having incorrect grade levels for students. Please click on each student name and confirm that the grade level specified is correct.
<div style='position:absolute;bottom:10px;color:gray;'>Click anywhere to dismiss this panel. It will not show up again.</div>
</div>

*/





	this.displayParameters.newButton.domObj=$('#'+this.displayParameters.newButton.divId);

	this.displayParameters.newButton.domObj.good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:this.displayParameters.newButton.handler, //NOTE: this handler is passed from the parent controller (dashboard)
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:"Add Student"
	});

	this.displayParameters.editButton.domObj=$('.'+this.displayParameters.editButton.divId);

	this.displayParameters.editButton.domObj.good_earth_store_tools_ui_button2({
		ready:{classs:'textReady'},
		hover:{classs:'textHover'},
		clicked:{classs:'textActive'},
		unavailable:{classs:'textUnavailable'},
		accessFunction:this.displayParameters.editButton.handler, //NOTE: this handler is passed from the parent controller (dashboard)
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:"nolabel"
	});


if (!this.adminFlag && this.lunchButtonHandler){
	$('.'+this.displayParameters.lunchButton.divId).good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:this.displayParameters.lunchButton.handler, //NOTE: this handler is passed from the parent controller (dashboard)
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:"Buy Lunches"
	});
	}
	

},


newButtonHandler:function(control, parameter){
	var componentName='newButton';
	switch(control){
		case 'click':

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}

					
			if (this.lockAddStudent) {
				$($('#' + this.displayParameters.newButton.divId).parent()).append('Cannot add/edit another student when one is open for change');
				return;
			}
			
			this.lockAddStudent=true;

			$($('#'+this.displayParameters.newButton.divId).parent()).good_earth_store_customer_schedule_add_student({
				'account':this.account,
				'redrawSchedule':this.callback('update'),
				schools:this.schools,
				gradeLevels:this.gradeLevels,
				adminFlag:this.adminFlag
				//studentRefId empty signals new in add_student()
			});
		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},


editButtonHandler:function(control, parameter){
	/*
	this has a bug. If you open a second editor, it shows the message.
	if you open a third, it opens the editor. Not sure why.
	*/
	var componentName='newButton';
	switch(control){
		case 'click':

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}

			var studentRefId=parameter.thisDomObj.attr('refId'),
				rowObject=$(parameter.thisDomObj.parent());

			rowObject.html("<td colspan=7><span style='font-size:80%;'>Only one student can be edited at a time</span></td>");
			if (this.lockAddStudent){
			return;
			}
			
			this.lockAddStudent=true;
			
			
			rowObject.find('td').good_earth_store_customer_schedule_add_student({
				'account':this.account,
				'redrawSchedule':this.callback('update'),
				studentRefId:studentRefId,
				schools:this.schools,
				gradeLevels:this.gradeLevels
			});
		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},


editButtonHandlerALLOWSOPENINGTWOORMORE:function(control, parameter){
/*
the reason I mothballed this is that the callback from the student redraws the entire schedule.
this closes all of the opened editors but only saves the one whose button was pressed.
making it so that the editors are independent is out of scope for now.
*/
	var componentName='newButton';
	switch(control){
		case 'click':

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}

			var studentRefId=parameter.thisDomObj.attr('refId');

			$(parameter.thisDomObj.parent()).good_earth_store_customer_schedule_add_student({
				'account':this.account,
				'redrawSchedule':this.callback('update'),
				studentRefId:studentRefId,
				schools:this.schools,
				gradeLevels:this.gradeLevels
			});
		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
}

})

});