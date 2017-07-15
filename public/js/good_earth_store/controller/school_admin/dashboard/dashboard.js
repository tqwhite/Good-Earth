steal( 'jquery/controller','jquery/view/ejs',
		'../../school_admin/student_list/student_list.js',
		'../../school_admin/lunch_editor/lunch_editor.js' ,
		'../../school_admin/student_editor/student_editor.js' )
	.then( './views/main.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Admin.User
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.SchoolAdmin.Dashboard',
/** @Static */
{
	defaults : {}
},
/** @Prototype */
{
//GoodEarthStore.Controller.Base.extend()

init: function(el, options) {
	this.baseInits();
	this.toolName='dashboard'; //for view file path construction

	qtools.validateProperties({
		targetObject:this,
		propList:[],
		source:this.constructor._fullName
 	});
 	
 	this.startupOptions=options?options:{};

	this.startProgressIndicator({styleString:'margin-left:300px;margin-top:200px;'});

	this.initControlProperties();
	this.initDisplayProperties();
	this.getReferenceData(this.callback('initDisplay'));

},

update:function(options){
	this.init(this.element, options);
},

initDisplayProperties:function(){

	nameArray=[];

	name='myId'; nameArray.push({name:name});
	name='status'; nameArray.push({name:name});
	name='accountSpace'; nameArray.push({name:name});
	name='studentList'; nameArray.push({name:name});
	name='lunchEditor'; nameArray.push({name:name});
	name='saveButton'; nameArray.push({name:name});
	name='checkoutButton'; nameArray.push({name:name});

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
	this.loginUser=GoodEarthStore.Models.Session.get('user');
	this.purchases=this.newPurchaseObj();
this.studentsToSaveList=[];

},

initDisplay:function(inData){

			
	var html=$.View('//good_earth_store/controller/school_admin/'+this.toolName+'/views/main.ejs',
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

	this.displayParameters.myId.domObj=$('#'+this.displayParameters.myId.divId);

	this.displayParameters.accountSpace.domObj=$('#'+this.displayParameters.accountSpace.divId);
	this.displayParameters.studentList.domObj=$('#'+this.displayParameters.studentList.divId);
	this.displayParameters.lunchEditor.domObj=$('#'+this.displayParameters.lunchEditor.divId);
	this.displayParameters.status.domObj=$('#'+this.displayParameters.status.divId);
	this.displayParameters.saveButton.domObj=$('#'+this.displayParameters.saveButton.divId);

	this.displayParameters.accountSpace.domObj.good_earth_store_customer_parent({
		'loginUser':this.loginUser,
		templateName:'schoolAdmin'
		});
	this.displayParameters.studentList.domObj.good_earth_store_school_admin_student_list({
		loginUser:this.loginUser,
		account:this.account,
		schools:this.schools,
		gradeLevels:this.gradeLevels,
		statusDomObj:this.displayParameters.status.domObj,
		studentsToSaveList:this.studentsToSaveList,
		lunchEditorHandler:this.callback('lunchEditorHandler')
		});

	this.displayParameters.saveButton.domObj.good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:this.callback('saveButtonHandler'),
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:"<div style='margin-top:1px;'>Save Students</div>"
	});

	$('#'+this.displayParameters.checkoutButton.divId).good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailableHidden'},
		accessFunction:this.callback('checkoutButtonHandler'),
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:"Lunch Checkout"
	});

},

saveButtonHandler:function(control, parameter){
	var componentName='editButton';
	switch(control){
		case 'click':
			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}
		this.displayParameters.status.domObj.html('Save is not yet implemented');


console.dir({"this.studentsToSaveList":this.studentsToSaveList});


	for (var i=0, len=this.studentsToSaveList.length; i<len; i++){
		var student=this.studentsToSaveList[i];
			this.saveStudent(student)
	}

		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

saveStudent:function(student){
	if (!student.vegetarianFlag){student.vegetarianFlag=0;}
	if (!student.isTeacherFlag){student.isTeacherFlag=0;}
	if (!student.allergyFlag){student.allergyFlag=0;}
	if (!student.isActiveFlag){student.isActiveFlag=1;}
	this.toggleSpinner();
console.dir({"saveStudent()":student});


	GoodEarthStore.Models.Student.add(student, this.callback('catchSave'));
},
catchSave:function(status){

console.dir({"status":status});
/*
 Current: saves students correctly.
 
 next: 
 
 1) make the save endpoint detect a list and save all students.
 2) finish the other fields of being a Student (teacher, etc)
 2.1) make it so that Save button is disabled when there are errors
 3) make it so that there is an empty student input row that generates a new student when typed
 4) that generates a new empty row when you start typing in the current empty row
 5) when a student is selected, show a lunch purchase editor for that student
 6) make a behind the scenes auto-checkout, perhaps with a confirmation report NO! USE REGULAR CHECKOUT
 7) maybe, add apply to all button
 8) figure out how to get already purchased orders (or at least days) to the UI

*/

},

queueReferenceLookup:function(controlObj, name, modelName, argData){
		var data;

		data=GoodEarthStore.Models.Session.get(name);

		if (data){
			this[name]=data;
		}
		else{
			controlObj[name]={
					ajaxFunction:GoodEarthStore.Models[modelName].getRetrievalFunction(),
					argData:argData?argData:{}
				};
		}

},

getReferenceData:function(callback){

		var controlObj={},
			calls={};

		this.queueReferenceLookup(calls, 'account', 'Account', {refId:this.loginUser.account.refId});
		this.queueReferenceLookup(calls, 'schools', 'School');
		this.queueReferenceLookup(calls, 'gradeLevels', 'GradeLevel');
		this.queueReferenceLookup(calls, 'offerings', 'Offering');

		if (qtools.isNotEmpty(calls)){

			controlObj.calls=calls;
			controlObj.success=this.callback('referenceCallback', callback);
			controlObj.stripWrappers=true;

			qtools.multiAjax(controlObj);
		}
		else{
			callback();
		}
},

referenceCallback:function(callback, inData){
referenceData=inData;
		this.account=inData.account;
		this.schools=inData.schools;
		this.gradeLevels=inData.gradeLevels;
		this.offerings=inData.offerings;


		GoodEarthStore.Models.Session.keep('account', this.account);
		GoodEarthStore.Models.Session.keep('schools', this.schools);
		GoodEarthStore.Models.Session.keep('gradeLevels', this.gradeLevels);
		GoodEarthStore.Models.Session.keep('offerings', this.offerings);

		callback(); //initDisplay()
},

newPurchaseObj:function(){
	var purchaseObj=GoodEarthStore.Models.Session.get('purchases');
	if (!purchaseObj){
		purchaseObj={
			orders:[],
			refId:qtools.newGuid()
		};

	}

	GoodEarthStore.Models.Session.keep('purchases', purchaseObj);
	return purchaseObj;
},

lunchEditorHandler:function(control, parameter){
	var componentName='lunchButton';
	switch(control){
		case 'click':
			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}
			this.displayParameters.status.domObj.html('lunchEditorHandler');
			
			this.displayParameters.lunchEditor.domObj.good_earth_store_school_admin_lunch_editor({
				studentRefId:parameter.refId,
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

checkoutButtonHandler:function(control, parameter){
	var componentName='checkoutButton';
	switch(control){
		case 'click':
			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}
			this.element.good_earth_store_customer_checkout({
				dashboardContainer:this.element,
				returnClassName:this.constructor._fullName,
				returnClassOptions:this.startupOptions,
				account:this.account,
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



})

});