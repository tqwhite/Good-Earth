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
			{name:'schools'},
			{name:'gradeLevels'},
			{name:'redrawSchedule'},
			{name:'studentRefId', importance:'optional'}
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
	name='gradeLevelSelect'; nameArray.push({name:name});
	name='schoolSelect'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='saveButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='cancelButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
	if (this.studentRefId){
		this.student=qtools.getByProperty(this.account.students, 'refId', this.studentRefId);
		this.isNew=false;
	}
	else{
		this.student={refId:qtools.newGuid()};
		this.isNew=true;
	}


	if (this.student.gradeLevelRefId){
		this.gradeLevel=qtools.getByProperty(this.gradeLevels, 'refId', this.student.gradeLevelRefId);
	}
	else{
		this.gradeLevel={};
	}


	if (this.student.schoolRefId){
		this.school=qtools.getByProperty(this.schools, 'refId', this.student.schoolRefId);
	}
	else{
		this.school={};
	}


	if (!this.student){
		this.student={};
		this.student.refId=this.studentRefId;
		this.student.lastName=this.account.familyName;
	}
	
	
	this.savedOrigParentHeight=this.element.parent().height();
		
},

initDisplay:function(inData){
	
	this.element.parent().height('60px')
	
	var html=$.View('//good_earth_store/controller/customer/schedule/add_student/views/init.ejs',
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper,
			formData:{
				account:this.account,
				student:this.student,
				gradeLevel:this.gradeLevel,
				school:this.school,
				schools:this.schools
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

	this.displayParameters.cancelButton.domObj=$('#'+this.displayParameters.cancelButton.divId);

	this.displayParameters.cancelButton.domObj.good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:this.displayParameters.cancelButton.handler,
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:"X"
	});

	this.element.find('input').qprompt();

	this.displayParameters.schoolSelect.domObj=$('#'+this.displayParameters.schoolSelect.divId);
	this.displayParameters.schoolSelect.domObj.change(this.callback('schoolSelectHandler'));

},

schoolSelectHandler:function(){ //this is a jquery type of event handler, not jmvc

	var schoolRefId=this.displayParameters.schoolSelect.domObj.val();
	if (!schoolRefId){return;}
	else{
		var school=qtools.getByProperty(this.schools, 'refId', schoolRefId);

		var gradeLevelSelectString=this.viewHelper.makeSelectTag({
			selectedValue:'',
			selectVarName:'gradeLevelRefId',
			sourceObj:school.gradeLevels.sort(qtools.byObjectProperty('seqNum')),
			valuePropertyName:'refId',
			labelPropertyName:'title',
			firstItemLabel:'Select Grade Level',
			firstItemValue:''
		});

	$('#'+this.displayParameters.gradeLevelSelect.divId).html(gradeLevelSelectString);
	}
},

saveButtonHandler:function(control, parameter){
	var componentName='saveButton';
	switch(control){
		case 'click':

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}

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

cancelButtonHandler:function(control, parameter){
	var componentName='cancelButton';
	switch(control){
		case 'click':
			this.redrawSchedule();
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
	if (!this.formParams.vegetarianFlag){this.formParams.vegetarianFlag=0;}
	if (!this.formParams.isTeacherFlag){this.formParams.isTeacherFlag=0;}
	if (!this.formParams.allergyFlag){this.formParams.allergyFlag=0;}
	GoodEarthStore.Models.Student.add(this.formParams, this.callback('catchSave'));
},

catchSave:function(inData){
	var errorString=this.listMessages(inData.messages);
	if (inData.status<1){
		this.element.find('.errorMsg').remove();
		this.element.prepend("<div class='errorMsg'>"+errorString+"</div>").removeClass('good').addClass('bad');
	}
	else{
		if (this.isNew) {this.account.students.push(this.formParams);}
		else{
			for (var i in this.student){
				this.student[i]=this.formParams[i];
			}
		}
		this.element.parent().height(this.savedOrigParentHeight);
		this.redrawSchedule();
	}
}



})

});