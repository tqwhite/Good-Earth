steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Customer.Choices
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Customer.Choices',
/** @Static */
{
	defaults : {}
},
/** @Prototype */
{

init: function(el, options) {
	this.baseInits();

	qtools.validateProperties({
		targetObject:options,
		targetScope: this, //will add listed items to targetScope
		propList:[
			{name:'offerings'},
			{name:'parentAccessFunction'},
			{name:'student'},
			{name:'purchases'}
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
//	name='saveButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
	this.parentAccessFunction('setAccessFunction', this.callback('receiveFromParent'));
	this.offeringButtonControls={};
},

initDisplay:function(inData){

	var html=$.View('//good_earth_store/controller/customer/choices/views/init.ejs',
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

	for (var i=0, len=this.purchases.unpaid.length; i<len; i++){
		if (this.student.refId!=this.purchases.unpaid[i].student.refId){ continue; }
		this.updateDisplay({purchase:this.purchases.unpaid[i], deleteId:qtools.newGuid()});
	}
},

initDomElements:function(){
/*
	this.displayParameters.saveButton.domObj=$('#'+this.displayParameters.saveButton.divId);

	this.displayParameters.saveButton.domObj.good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:this.displayParameters.saveButton.handler,
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:"<div style='margin-top:5px;'>Login</div>"
	});
*/
},

receiveFromParent:function(control, parameter){
	switch(control){
		case 'sendPurchase':

		var offering=qtools.getByProperty(this.offerings, 'refId', parameter.offeringRefId),
			day=qtools.getByProperty(offering.days, 'refId', parameter.dayRefId),
			purchase=this.addToPurchases({
				offering:offering,
				student:this.student,
				day:day
			});

			this.updateDisplay({purchase:purchase, deleteId:qtools.newGuid()});
			parameter.offeringButtonAccessFunction('setUnavailable');
			this.offeringButtonControls[purchase.refId]=parameter.offeringButtonAccessFunction;
			break;
	}

},

addToPurchases:function(args){
	var purchase={
		offering:args.offering,
		day:args.day,
		student:args.student,
		refId:qtools.newGuid()
	};
	this.purchases.unpaid.push(purchase);
	return purchase;

},

updateDisplay:function(args){

	var html=$.View('//good_earth_store/controller/customer/choices/views/listElement.ejs', {
			purchase:args.purchase,
			deleteId:args.deleteId
		});
		this.element.append(html);


	var domObj=$('#'+args.deleteId);

	domObj.good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:this.callback('deleteButtonHandler'),
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:"Remove"
	});
},

deleteButtonHandler:function(control, parameter){
	var componentName='saveButton';
	switch(control){
		case 'click':
			var purchaseRefId=parameter.thisDomObj.attr('refId');
			this.deletePurchase(purchaseRefId);
			parameter.thisDomObj.parent().remove();
			this.offeringButtonControls[purchaseRefId]('setToReady');
		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

deletePurchase:function(purchaseRefId){
	var deletableInx;

	for (var i=0, len=this.purchases.length; i<len; i++){
		if (this.purchases.refId==purchaseRefId){
			deletableInx=i;
		}
	}

	this.purchases.unpaid.splice(deletableInx, 1);
}

})

});