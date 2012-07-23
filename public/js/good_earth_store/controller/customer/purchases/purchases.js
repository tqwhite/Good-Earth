steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Customer.Purchases
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Customer.Purchases',
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
			{name:'dashboardContainer'},
			{name:'returnClassName'},
			{name:'returnClassOptions'},
			{name:'account'},
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
	name='price'; nameArray.push({name:name});
	name='checkoutButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
},

initDisplay:function(inData){

	var html=$.View('//good_earth_store/controller/customer/purchases/views/init.ejs',
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper,
			formData:{
				purchases:this.purchases
			}
		})
		);
	this.element.html(html);
	this.initDomElements();
},

initDomElements:function(){

	$('#'+this.displayParameters.checkoutButton.divId).good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailableHidden'},
		accessFunction:this.displayParameters.checkoutButton.handler,
		initialControl:'setUnavailable', //initialControl:'setUnavailable'
		label:"Checkout"
	});
	this.updateTotal();
},

updateTotal:function(){

	var list=this.purchases.unpaid,
		total=0;
	for (var i=0, len=list.length; i<len; i++){
		var element=list[i];
		total=total+element.offering.price;
	}
	if (total){
		$('#'+this.displayParameters.price.divId).html('$'+total.toFixed(2));
		this.checkoutButton.accessFunction('setToReady');
	}
},

checkoutButtonHandler:function(control, parameter){
	var componentName='checkoutButton';
	switch(control){
		case 'click':
			this.dashboardContainer.good_earth_store_customer_checkout({
				dashboardContainer:this.dashboardContainer,
				returnClassName:this.returnClassName,
				returnClassOptions:this.returnClassOptions,
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
}


})

});