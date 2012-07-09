steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Session.Login
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Session.Login',
/** @Static */
{
	defaults : {}
},
/** @Prototype */
{

init: function(el, options) {
	this.baseInits();
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

	name='status'; nameArray.push({name:name});
	name='saveButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='registerButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='forgotButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
	this.enterKeyEnabled=false;
},

initDisplay:function(inData){

	var html=$.View('//good_earth_store/controller/session/login/views/init.ejs',
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper,
			formData:{
				userName:GoodEarthStore.Models.LocalStorage.getCookieData(GoodEarthStore.Models.LocalStorage.getConstant('loginCookieName')).data
			}
		})
		);
	this.element.html(html);
	this.initDomElements();
},

initDomElements:function(){
	this.displayParameters.saveButton.domObj=$('#'+this.displayParameters.saveButton.divId);
	this.displayParameters.registerButton.domObj=$('#'+this.displayParameters.registerButton.divId);
	this.displayParameters.forgotButton.domObj=$('#'+this.displayParameters.forgotButton.divId);

			this.displayParameters.saveButton.domObj.good_earth_store_tools_ui_button2({
				ready:{classs:'basicReady'},
				hover:{classs:'basicHover'},
				clicked:{classs:'basicActive'},
				unavailable:{classs:'basicUnavailable'},
				accessFunction:this.displayParameters.saveButton.handler,
				initialControl:'setToReady', //initialControl:'setUnavailable'
				label:"<div style='margin-top:5px;'>Login</div>"
			});

			this.displayParameters.registerButton.domObj.good_earth_store_tools_ui_button2({
				ready:{classs:'smallReady'},
				hover:{classs:'smallHover'},
				clicked:{classs:'smallActive'},
				unavailable:{classs:'smallUnavailable'},
				accessFunction:this.displayParameters.registerButton.handler,
				initialControl:'setToReady', //initialControl:'setUnavailable'
				label:"<div >New Customer</div>"
			});

			this.displayParameters.forgotButton.domObj.good_earth_store_tools_ui_button2({
				ready:{classs:'smallReady'},
				hover:{classs:'smallHover'},
				clicked:{classs:'smallActive'},
				unavailable:{classs:'smallUnavailable'},
				accessFunction:this.displayParameters.forgotButton.handler,
				initialControl:'setToReady', //initialControl:'setUnavailable'
				label:"<div style='text-decoration:line-through;'>Forgot Password</div>"
			});

			if (this.initialStatusMessage){
				$('#'+this.displayParameters.status.divId).html(this.initialStatusMessage).removeClass('bad').addClass('good');
			}

},

//BUTTON HANDLERS =========================================================================================================


saveButtonHandler:function(control, parameter){
	var componentName='saveButton';
	switch(control){
		case 'click':
			GoodEarthStore.Models.Session.login(this.element.formParams(), this.callback('resetAfterSave'));
		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

resetAfterSave:function(inData){
	if (inData.status<1){
		$('#'+this.displayParameters.status.divId).html("Invalid User Name/Password").removeClass('good').addClass('bad');
	}
	else{
		GoodEarthStore.Models.Session.keep('user', inData.data.identity);
		$('#'+this.displayParameters.status.divId).html("Welcome back, "+inData.data.identity.firstName+" <span style=color:gray;font-size:6pt'>("+inData.data.identity.school+")</span>").removeClass('bad').addClass('good');
		GoodEarthStore.Models.LocalStorage.setCookie(GoodEarthStore.Models.LocalStorage.getConstant('loginCookieName'), inData.data.identity.userName);
		this.element.html('').good_earth_store_customer_dashboard();

	}
},



registerButtonHandler:function(control, parameter){
	var componentName='registerButton';
	switch(control){
		case 'click':

		this.element.good_earth_store_session_register();

		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

forgotButtonHandler:function(control, parameter){
	var componentName='forgotButton';
	switch(control){
		case 'click':

		console.log(componentName);

		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

//ENTER KEY =========================================================================================================

enableKeyManager:function(eventObj){
	if (eventObj.type=='focus'){
		this.disableEnterKey();
	}
	else{
		this.enableEnterKey();
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

enterKeyHandler:function(eventObj){
	if (this.fieldsAreValid() && eventObj.which==13){
		this.saveButtonHandler('click');
	}
},

disableEnterKey:function(){
	if (this.enterKeyEnabled){
		this.element.unbind('keydown');
		this.enterKeyEnabled=false;
	}
},

enableEnterKey:function(){
	if (!this.enterKeyEnabled && this.fieldsAreValid()){
		this.element.bind('keydown',this.callback('enterKeyHandler'));
		this.enterKeyEnabled=true;
	}
},

lastFieldHandler:function(eventObj){
	var firstFieldObj=$('#'+this.displayParameters.firstField.divId);
	if (eventObj.type=='blur'){
		qtools.timeoutProxy(function(){
			firstFieldObj.focus();
		}, 100);
	}
}



})

});