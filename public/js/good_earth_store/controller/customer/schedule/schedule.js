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