steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Admin.Control
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Admin.Control',
/** @Static */
{
	defaults : {}
},
/** @Prototype */
{
init: function(el, options) {
	this.baseInits();
	this.thisObj=el;
	this.directory='//good_earth_store/controller/admin/control/';

	qtools.validateProperties({
		targetObject:options || {},
		targetScope: this, //will add listed items to targetScope
		propList:[
			{name:'parentAccessFunction'},
			{name:'statusDomObject'}
		],
		source:this.constructor._fullName
 	});

	this.initControlProperties();
	this.initDisplayProperties();

	options=options?options:{};
	if (options.initialStatusMessage){this.initialStatusMessage=options.initialStatusMessage;}

	this.initDisplay();

},

update:function(){
	this.init();
},

initDisplayProperties:function(){

	nameArray=[];

	name='searchButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='newButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='userSelector'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	
	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
},

initDisplay:function(inData){
	var html=$.View(this.directory+'views/init.ejs',
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper,
			formData:{
				loginUser:this.loginUser,
				source:this.constructor._fullName
			}
		})
		);
	this.element.html(html);
	this.initDomElements();
},

initDomElements:function(){
	var name='searchButton'; //this.displayParameters.saveButton
	this.displayParameters[name].domObj=$('#'+this.displayParameters[name].divId);
	this.displayParameters[name].domObj.good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:this.displayParameters[name].handler,
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:'SEARCH'
		//label:name
	});
	
	var name='newButton'; //this.displayParameters.newButton
	this.displayParameters[name].domObj=$('#'+this.displayParameters[name].divId);
	this.displayParameters[name].domObj.good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:this.displayParameters[name].handler,
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:'NEW'
		//label:name
	});
	
	var name='userSelector'; //this.displayParameters.userSelector
	this.displayParameters[name].domObj=$('#'+this.displayParameters[name].divId);


},
searchButtonHandler:function(control, parameter){
	var componentName='searchButton';
	switch(control){
		case 'click':
			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}
			
			this.displayParameters.userSelector.domObj.good_earth_store_admin_user_selector({
				parentAccessFunction:this.displayParameters.userSelector.handler,
				parameters:this.element.formParams(),
				statusDomObject:this.statusDomObject
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

newButtonHandler:function(control, parameter){
	var componentName='newButton';
	switch(control){
		case 'click':
			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}
			
			this.parentAccessFunction('newUser', parameter);
		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

userSelectorHandler:function(control, parameter){
	var componentName='newButton';
	switch(control){
		case 'setUser':
			this.parentAccessFunction('setUser', parameter);
		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
}
}
)

});