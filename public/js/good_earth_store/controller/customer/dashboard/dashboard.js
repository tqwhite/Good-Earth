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
		account:this.account,
		purchases:this.purchases
	});

},


lunchButtonHandler:function(control, parameter){
	var componentName='lunchButton';
	switch(control){
		case 'click':
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


getReferenceData:function(callback){


		var controlObj={
			calls:{
				account:{
					ajaxFunction:GoodEarthStore.Models.Account.find,
					argData:{refId:this.loginUser.account.refId}
				},
				schools:{
					ajaxFunction:GoodEarthStore.Models.School.getList,
					argData:{}
				},
				gradeLevels:{
					ajaxFunction:GoodEarthStore.Models.GradeLevel.getList,
					argData:{}
				},
				offerings:{
					ajaxFunction:GoodEarthStore.Models.Offering.getList,
					argData:{}
				}
			},
			success:this.callback('referenceCallback', callback),
			stripWrappers:true

		};
		qtools.multiAjax(controlObj);

},

referenceCallback:function(callback, inData){

		this.account=inData.account;
		this.schools=inData.schools;
		this.gradeLevels=inData.gradeLevels;
		this.offerings=inData.offerings;
		callback(); //initDisplay()
},

newPurchaseObj:function(){

	var purchaseObj=GoodEarthStore.Models.Session.get('purchases');
	if (!purchaseObj){
		purchaseObj={
			unpaid:[]
		};

	}

	GoodEarthStore.Models.Session.keep('purchases', purchaseObj);
	return purchaseObj;
}


})

});