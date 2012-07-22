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
//	name='lunchButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
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

}


})

});