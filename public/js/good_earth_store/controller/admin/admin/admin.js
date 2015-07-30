steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Admin.User
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Admin.Admin',
/** @Static */
{
	defaults : {}
},
/** @Prototype */
{

init: function(el, options) {
	this.baseInits();
	this.thisObj=el;
	this.directory='//good_earth_store/controller/admin/admin/';

	qtools.validateProperties({
		targetObject:options || {},
		targetScope: this, //will add listed items to targetScope
		propList:[
			{name:'loginUser'},
			{name:'adminModeCookieName'},
			{name:'sessionActivationPackage', optional:true}
		],
		source:this.constructor._fullName
 	});

	qtools.validateProperties({
		targetObject:this.sessionActivationPackage,
		targetScope: this, //will add listed items to targetScope
		propList:[
			{name:'loginAccount'},
			{name:'schools'},
			{name:'gradeLevels'}
			//,{name:'lunchButtonHandler'}
		],
		source:this.constructor._fullName
 	});

	this.initControlProperties();
	this.initDisplayProperties();

	options=options?options:{};
	if (options.initialStatusMessage){this.initialStatusMessage=options.initialStatusMessage;}

	this.initDisplay();

},

update:function(options){
	this.init(this.element, options);
},

initDisplayProperties:function(){

	nameArray=[];

	name='status'; nameArray.push({name:name});
	name='closeButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='userEditor'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='studentEditor'; nameArray.push({name:name});
	name='selectionControl'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	
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

	this.statusDomObject=$('#'+this.displayParameters.status.divId);

	this.element.addClass('adminPanelBase');
	

	this.displayParameters.closeButton.domObj=$('#'+this.displayParameters.closeButton.divId);
	this.displayParameters.closeButton.domObj.good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:this.displayParameters.closeButton.handler,
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:"X"
	});
	
	var name='selectionControl'; //this.displayParameters.selectionControl
	this.displayParameters[name].domObj=$('#'+this.displayParameters[name].divId);
	this.displayParameters[name].domObj.good_earth_store_admin_control({
		parentAccessFunction:this.displayParameters[name].handler,
		statusDomObject:this.statusDomObject
	});
	
	

	
	this.element.find('.mainContainer').css({width:'1120px'}).find('td').css({width:'100px'}).find('.smallButton').hide();

},

initEditors:function(){
	this.statusDomObject.html('Good Earth Customer Center');

	var emptySubjectUser={
		account:{
			users:[],
			students:[]
		}
	}

var reviseUserEditor=function(editorDomObj){
	editorDomObj.find('.basicButton').css({
	'float':'left',
'margin':'2px 100px 0px 300px',
'height':'21px'
.find('div').css({'margin':'2px auto'});

};

	var name='userEditor'; //this.displayParameters.userSelector
	this.displayParameters[name].domObj=$('#'+this.displayParameters[name].divId);
	this.displayParameters[name].domObj.good_earth_store_session_user_editor({
				subjectUser:this.subjectUser,
				accessFunction:this.displayParameters[name].handler,
				adminFlag:true,
		statusDomObject:this.statusDomObject,
		instantiationCallback:reviseUserEditor
	});
	
	this.displayParameters.studentEditor.domObj=$('#'+this.displayParameters.studentEditor.divId);

	if (this.subjectUser.account){
	this.displayParameters.studentEditor.domObj.good_earth_store_customer_schedule({
	account:this.subjectUser.account || emptySubjectUser.account,
	schools:this.sessionActivationPackage.schools,
	gradeLevels:this.sessionActivationPackage.gradeLevels,
	adminFlag:true
	
	});	
	this.displayParameters.studentEditor.domObj.find('.basicButton').css({float:'none'});
	}
	else{
		this.displayParameters.studentEditor.domObj.html('');
	}
	},


closeButtonHandler:function(control, parameter){
	var componentName='closeButton';
	switch(control){
		case 'click':

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}
			
			GoodEarthStore.Models.LocalStorage.updateCookie(this.adminModeCookieName, 'false');
			
			this.element.good_earth_store_customer_dashboard({
				'loginUser':this.loginUser
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


lunchButtonHandler:function(control, parameter){
	var componentName='lunchButton';
	switch(control){
		case 'click':

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}

			var studentRefId=parameter.thisDomObj.attr('refId');
			this.element.html('');
			this.element.good_earth_store_customer_choose_menu({
				returnClassName:this.constructor._fullName,
				returnClassOptions:this.startupOptions,
				studentRefId:studentRefId,
				account:this.account,
				offerings:this.offerings,
				purchases:this.purchases
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


selectionControlHandler:function(control, parameter){
	var componentName='selectionControl';
	switch(control){
		case 'setUser':
			this.subjectUser=parameter;
			this.initEditors();
		break;
		case 'newUser':
			this.subjectUser='';
			this.initEditors();
		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},


userEditorHandler:function(control, parameter){

	var componentName='userEditor';
	switch(control){
		case 'saveResult':
				this.subjectUser=parameter.data.user;
				this.initEditors();
				this.writeStatus("<span style='color:green;font-weight:bold;'>User Info Saved</span><div style='font-size:80%;'>"+parameter.messages[0][1]+"</div>");
			break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

writeStatus:function(message){
	this.statusDomObject.html(message);
}

})

});