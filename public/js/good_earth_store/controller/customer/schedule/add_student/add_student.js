steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Customer.Schedule.AddStudent
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Customer.Schedule.AddStudent',
/** @Static */
{
	defaults : {}
},
/** @Prototype */
{
//GoodEarthStore.Controller.Base.extend()

init: function(el, options) {
	this.baseInits();

	qtools.validateProperties({
		targetObject:options,
		targetScope: this, //will add listed items to targetScope
		propList:[
			{name:'account'},
			{name:'redrawSchedule'}
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

	name='status'; nameArray.push({name:name});
	name='saveButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
},

initDisplay:function(inData){

	var html=$.View('//good_earth_store/controller/customer/schedule/add_student/views/init.ejs',
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper,
			formData:{
				account:this.account
			}
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
		label:"Save"
	});

},

saveButtonHandler:function(control, parameter){
	var componentName='saveButton';
	switch(control){
		case 'click':
			this.saveStudent();
		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

saveStudent:function(){
	this.formParams=this.element.formParams();
	GoodEarthStore.Models.Student.add(this.formParams, this.callback('catchSave'));
},

catchSave:function(inData){
	var errorString=this.listMessages(inData.messages);
	if (inData.status<1){
		this.element.prepend(errorString).removeClass('good').addClass('bad').fade(5000);
	}
	else{
		this.account.students.push(this.formParams);
			this.redrawSchedule();
	}
}



})

});