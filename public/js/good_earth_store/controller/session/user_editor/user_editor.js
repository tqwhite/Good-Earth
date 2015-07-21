steal('jquery/controller', 'jquery/view/ejs')
	.then('./views/form.ejs', function($) {

	/**
	 * @class GoodEarthStore.Controller.Admin.User
	 */
	GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Session.UserEditor',
	/** @Static */
	{
		defaults: {}
	},
	/** @Prototype */
	{

init: function(el, options) {
	this.baseInits();
	this.directory = "//good_earth_store/controller/session/user_editor/";
	
	qtools.validateProperties({
		targetObject:options || {},
		targetScope: this, //will add listed items to targetScope
		propList:[
			{name:'statusDomObject'},
			{name:'subjectUser'},
			{name:'accessFunction', optional:true},
			{name:'adminFlag', optional:true}
		],
		source:this.constructor._fullName
 	});
	
	this.initControlProperties();
	this.initDisplayProperties();
	if (options && options.initialStatusMessage){this.initialStatusMessage=options.initialStatusMessage;}

	this.getReferenceData(this.callback('initDisplay'));

},

update:function(options){
	this.init(this.element, options);
},

initDisplayProperties:function(){

	nameArray=[];

	name='status'; nameArray.push({name:name});
	name='saveButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});

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
			subjectUser:this.subjectUser,
			adminFlag:this.adminFlag
		})
		);
	this.element.html(html);
	this.initDomElements();
},

initDomElements:function(){


	this.displayParameters.saveButton.domObj=$('#'+this.displayParameters.saveButton.divId);
			this.displayParameters.saveButton.domObj.good_earth_store_tools_ui_button2({
				ready:{classs:'basicReady'},
				hover:{classs:'basicHover'},
				clicked:{classs:'basicActive'},
				unavailable:{classs:'basicUnavailable'},
				accessFunction:this.displayParameters.saveButton.handler,
				initialControl:'setToReady', //initialControl:'setUnavailable'
				label:"<div style='margin-top:5px;'>Save</div>"
			});

	$($('.schoolIdClassString').find('option')[1]).attr('selected', 'selected'); //for debugg only, see form.ejs
	this.element.find('input').qprompt();


	this.setupEnterKey(this.displayParameters.saveButton.handler);

	this.element.find('input').qprompt();
},

//BUTTON HANDLERS =========================================================================================================


saveButtonHandler:function(control, parameter){
	var componentName='saveButton';
	if (control.which=='13'){control='click';}; //enter key
	switch(control){
		case 'click':

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}
		var formParams=this.element.formParams();
		formParams.adminFlag=this.adminFlag;
		formParams=this.manageConfirmationEmail(formParams);
		
		GoodEarthStore.Models.User.register(formParams, this.callback('resetAfterSave'));
		this.toggleSpinner();
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
		this.toggleSpinner();
		$('body').trigger('userSaveComplete');
	if (inData.status<1){
		this.statusDomObject.html(errorString).removeClass('good').addClass('bad');
	}
	else{
	
	this.completionCallback=(this.accessFunction)?this.accessFunction:this.completionCallback;
	this.completionCallback('saveResult', inData);
	}
},

completionCallback:function(unused, inData){
			var html=$.View(this.directory+'views/confirmEmail.ejs',
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper
		})
		);
	this.element.parent().html(html);
},

manageConfirmationEmail:function(formParams){
formParams=qtools.passByValue(formParams);

formParams.preExistingEmailAddress=false;

if (!formParams.previousEmailAddress){
	formParams.preExistingEmailAddress=true;
}
if (formParams.previousEmailAddress!=formParams.emailAdr){
	formParams.preExistingEmailAddress=true;
}
else{
	
	formParams.preExistingEmailAddress=false;
}

return formParams;
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
