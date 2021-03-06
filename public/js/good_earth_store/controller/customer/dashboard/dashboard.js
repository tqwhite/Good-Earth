steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/dashboardMain.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Customer.Dashboard
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Customer.Dashboard',
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
	name='kidSpace'; nameArray.push({name:name});
	name='purchaseSpace'; nameArray.push({name:name});
	name = 'adminButton'; nameArray.push({
		name: name,
		handlerName: name + 'Handler',
		targetDivId: name + 'Target'
	});
				name = 'instructions';
				nameArray.push({ name: name });

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
	this.loginUser=GoodEarthStore.Models.Session.get('user');
	this.purchases=this.newPurchaseObj();
	
			
			this.adminModeCookieName='adminMode';
},

initDisplay:function(inData){
		
	var adminMode=GoodEarthStore.Models.LocalStorage.getCookieData(this.adminModeCookieName);
	if (adminMode.data=='true'){
		this.adminButtonHandler('click');
		return;
	}

			
	var html=$.View('//good_earth_store/controller/customer/dashboard/views/dashboardMain.ejs',
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper,
			formData:{
				userName:GoodEarthStore.Models.LocalStorage.getCookieData(GoodEarthStore.Models.LocalStorage.getConstant('loginCookieName')).data
			},
			adminFlag:(this.loginUser.role == 'admin')
		})
		);
	this.element.html(html);
	this.initDomElements();
},

initDomElements:function(){

	this.displayParameters.myId.domObj=$('#'+this.displayParameters.myId.divId);

	this.displayParameters.accountSpace.domObj=$('#'+this.displayParameters.accountSpace.divId);
	this.displayParameters.kidSpace.domObj=$('#'+this.displayParameters.kidSpace.divId);
	this.displayParameters.purchaseSpace.domObj=$('#'+this.displayParameters.purchaseSpace.divId);
	this.displayParameters.instructions.domObj=$('#'+this.displayParameters.instructions.divId);

	this.displayParameters.accountSpace.domObj.good_earth_store_customer_parent({
		'loginUser':this.loginUser
		});
	this.displayParameters.kidSpace.domObj.good_earth_store_customer_schedule({
		account:this.account,
		schools:this.schools,
		gradeLevels:this.gradeLevels,
		lunchButtonHandler:this.callback('lunchButtonHandler'),
		adminFlag:(this.loginUser.role == 'admin')
		});
	this.displayParameters.purchaseSpace.domObj.good_earth_store_customer_purchases({
		dashboardContainer:this.element,
		returnClassName:this.constructor._fullName,
		returnClassOptions:this.startupOptions,
		account:this.account,
		purchases:this.purchases
	});
	
	this.displayParameters.adminButton.domObj = $('#' + this.displayParameters.adminButton.divId);
	if (this.loginUser.role == 'admin') {
		this.displayParameters.adminButton.domObj.good_earth_store_tools_ui_button2({
			ready: {
				classs: 'basicReady'
			},
			hover: {
				classs: 'basicHover'
			},
			clicked: {
				classs: 'basicActive'
			},
			unavailable: {
				classs: 'basicUnavailable'
			},
			accessFunction: this.displayParameters.adminButton.handler,
			initialControl: 'setToReady', //initialControl:'setUnavailable'
			label: "Admin"
		});
	} else {
		this.displayParameters.adminButton.domObj.remove();
	}


				$('[tooltip!=""]').qtip({
					style: {
						classes: 'qtip-dark',
						tip: {
							corner: 'bottom center',
							mimic: 'bottom left',
							border: 2,
							width: 88,
							height: 66
						}
					}
				});
				
				this.displayParameters.instructions.domObj.css('color', 'red');
	
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

		this.account=inData.account;
		this.schools=inData.schools;
		this.gradeLevels=inData.gradeLevels;
		this.offerings=inData.offerings;
		
		this.account=this.addOrdersToStudents(this.account);

		GoodEarthStore.Models.Session.keep('account', this.account);
		GoodEarthStore.Models.Session.keep('schools', this.schools);
		GoodEarthStore.Models.Session.keep('gradeLevels', this.gradeLevels);
		GoodEarthStore.Models.Session.keep('offerings', this.offerings);

		callback(); //initDisplay()
},

addOrdersToStudents:function(accountObject){

	var outObj=qtools.clone(accountObject);

	var students=outObj.students;
	var orderList=this.extractOrders(outObj.purchases);
	
	for (var i=0, len=students.length; i<len; i++){
		var student=students[i];
		student.orders=orderList[student.refId]?orderList[student.refId]:[];
	}
	return outObj;
},

extractOrders:function(purchases){
	var outObj={};


	for (var i=0, len=purchases.length; i<len; i++){
		var orderList=purchases[i].orders;
		
		for (var j=0, len2=orderList.length; j<len2; j++){
			var studentRefId=orderList[j].student.refId;
			if (typeof(outObj[studentRefId])=='undefined'){
				outObj[studentRefId]=[];
			}
			outObj[studentRefId].push(orderList[j]);
		}
	}
	return outObj;
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

adminButtonHandler: function(control, parameter) {
	var componentName = 'adminButton';
	switch (control) {
		case 'click':
			if (this.isAcceptingClicks()) {
				this.turnOffClicksForAwhile();
			}
			else {
				return;
			}
			
			GoodEarthStore.Models.LocalStorage.setCookie(this.adminModeCookieName, 'true', { expires: 1, path: '/'});

			this.element.good_earth_store_admin_admin({
				'loginUser': this.loginUser,
				'adminModeCookieName':this.adminModeCookieName,
				'sessionActivationPackage':{
					loginAccount:this.account,
					schools:this.schools,
					gradeLevels:this.gradeLevels,
					lunchButtonHandler:this.callback('lunchButtonHandler')
				}
			});
			break;
		case 'setAccessFunction':
			if (!this[componentName]) {
				this[componentName] = {};
			}
			this[componentName].accessFunction = parameter;
			break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
}


})

});