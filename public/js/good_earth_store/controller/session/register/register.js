steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/form.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Session.Register
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Session.Register',
/** @Static */
{
	defaults : {}
},
/** @Prototype */
{

init: function(el, options) {
	this.baseInits();
	this.directory = "//good_earth_store/controller/session/register/";
	
	this.initControlProperties();
	this.initDisplayProperties();
	if (options && options.initialStatusMessage){this.initialStatusMessage=options.initialStatusMessage;}


	this.getReferenceData(this.callback('initDisplay'));

},

update:function(){
	this.init();
},

initDisplayProperties:function(){

	nameArray=[];

	name='status'; nameArray.push({name:name});
	name='loginForm'; nameArray.push({name:name});
	name='loginButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='forgotButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
	this.enterKeyEnabled=false;
},

initDisplay:function(inData){

	var html=$.View(this.directory+'views/form.ejs',
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper,
			emailOverride:(window.location.search=='?rulesDontApplyToSherry')?true:false
		})
		);
	this.element.html(html);
	this.initDomElements();
},

initDomElements:function(){



	this.displayParameters.loginButton.domObj=$('#'+this.displayParameters.loginButton.divId);
			this.displayParameters.loginButton.domObj.good_earth_store_tools_ui_button2({
				ready:{classs:'smallReady'},
				hover:{classs:'smallHover'},
				clicked:{classs:'smallActive'},
				unavailable:{classs:'smallUnavailable'},
				accessFunction:this.displayParameters.loginButton.handler,
				initialControl:'setToReady', //initialControl:'setUnavailable'
				label:"<div>Login Instead</div>"
			});

			this.displayParameters.forgotButton.domObj=$('#'+this.displayParameters.forgotButton.divId);
			this.displayParameters.forgotButton.domObj.good_earth_store_tools_ui_button2({
				ready:{classs:'smallReady'},
				hover:{classs:'smallHover'},
				clicked:{classs:'smallActive'},
				unavailable:{classs:'smallUnavailable'},
				accessFunction:this.displayParameters.forgotButton.handler,
				initialControl:'setToReady', //initialControl:'setUnavailable'
				label:"<div>Forgot Password/Login</div>"
			});

	$($('.schoolIdClassString').find('option')[1]).attr('selected', 'selected'); //for debugg only, see form.ejs
	this.element.find('input').qprompt();

	if (this.initialStatusMessage){
		$('#'+this.displayParameters.status.divId).html(this.initialStatusMessage).removeClass('bad').addClass('good');
	}
	
	this.statusDomObject=$('#'+this.displayParameters.status.divId);


	this.displayParameters.loginForm.domObj=$('#'+this.displayParameters.loginForm.divId);
	this.displayParameters.loginForm.domObj.good_earth_store_session_user_editor({statusDomObject:this.statusDomObject});

},

//BUTTON HANDLERS =========================================================================================================


saveButtonHandler:function(control, parameter){
	var componentName='saveButton';
	if (control.which=='13'){control='click';}; //enter key
	switch(control){
		case 'click':

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}

		GoodEarthStore.Models.User.register(this.element.formParams(), this.callback('resetAfterSave'));

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
	var errorString=this.listMessages(inData.messages);
	if (inData.status<1){
		$('#'+this.displayParameters.status.divId).html(errorString).removeClass('good').addClass('bad');
	}
	else{
			var html=$.View(this.directory+'views/confirmEmail.ejs',
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper
		})
		);
	this.element.html(html);
	}
},

loginButtonHandler:function(control, parameter){
	var componentName='loginButton';
	switch(control){
		case 'click':

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}

			this.element.good_earth_store_session_login();
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

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}

		this.element.good_earth_store_session_forgot({selector:'password'});

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
},

getReferenceData:function(callback){


		var controlObj={
			calls:{
				schools:{
					ajaxFunction:GoodEarthStore.Models.School.getList,
					argData:{}
				}
			},
			success:this.callback('referenceCallback', callback),
			error:function(){alert('the server broke down');},
			stripWrappers:true

		};
		qtools.multiAjax(controlObj);

},

referenceCallback:function(callback, inData){

		this.schoolList=inData.schools
		callback();
}


})

});