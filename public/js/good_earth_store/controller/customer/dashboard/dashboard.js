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
		source:'session.dashboard.dashboardMain'
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

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
	this.loginUser=GoodEarthStore.Models.Session.get('user');
	this.purchases=this.newPurchaseObj();
AAA_purchases=this.purchases;
},

initDisplay:function(inData){

	var html=$.View('//good_earth_store/controller/customer/dashboard/views/dashboardMain.ejs',
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
	this.displayParameters.kidSpace.domObj=$('#'+this.displayParameters.kidSpace.divId);
	this.displayParameters.purchaseSpace.domObj=$('#'+this.displayParameters.purchaseSpace.divId);

	this.displayParameters.accountSpace.domObj.good_earth_store_customer_parent({
		'loginUser':this.loginUser
		});
	this.displayParameters.kidSpace.domObj.good_earth_store_customer_schedule({
		account:this.account,
		schools:this.schools,
		gradeLevels:this.gradeLevels,
		lunchButtonHandler:this.callback('lunchButtonHandler')
		});
	this.displayParameters.purchaseSpace.domObj.good_earth_store_customer_purchases({
		dashboardContainer:this.element,
		returnClassName:this.constructor._fullName,
		returnClassOptions:this.startupOptions,
		account:this.account,
		purchases:this.purchases
	});

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
}


})

});