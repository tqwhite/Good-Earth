steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Customer.Offerings
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Customer.Offerings',
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
			{name:'student'}
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
	name='chooseOfferingButtonClassId'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
},

initDisplay:function(inData){

	var html=$.View('//good_earth_store/controller/customer/offerings/views/init.ejs',
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper,
			formData:{
				offerings:this.offerings,
				student:this.student
			}
		})
		);
	this.element.html(html);
	this.initDomElements();
},

initDomElements:function(){

	this.displayParameters.chooseOfferingButtonClassId.domObj=$('.'+this.displayParameters.chooseOfferingButtonClassId.divId);

	this.displayParameters.chooseOfferingButtonClassId.domObj.good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:this.displayParameters.chooseOfferingButtonClassId.handler,
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:'nolabel'
	});

},


chooseOfferingButtonClassIdHandler:function(control, parameter){
	var componentName='chooseOfferingButtonClassId';
	switch(control){
		case 'click':
			var dayRefId=parameter.thisDomObj.attr('dayRefId'),
				offeringRefId=parameter.thisDomObj.attr('offeringRefId');
				this.parentAccessFunction('sendPurchase', {
					dayRefId:dayRefId,
					offeringRefId:offeringRefId,
					offeringButtonAccessFunction:parameter.buttonAccessFunction
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