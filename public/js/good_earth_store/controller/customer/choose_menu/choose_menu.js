steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/chooseMenu.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Customer.ChooseMenu
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Customer.ChooseMenu',
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
			{name:'returnClassName'},
			{name:'returnClassOptions'}
		],
		source:this.constructor._fullName 
 	});
	
	this.initControlProperties();
	this.initDisplayProperties();

	options=options?options:{};
	if (options.initialStatusMessage){this.initialStatusMessage=options.initialStatusMessage;}

	this.initDisplay();

},

update:function(options){
	this.init(this.element, options);
},

initDisplayProperties:function(){

	nameArray=[];
		
	name='myId'; nameArray.push({name:name});
	name='childNameSpace'; nameArray.push({name:name});
	name='offeringSpace'; nameArray.push({name:name});
	name='selectedSpace'; nameArray.push({name:name});

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
},

initDisplay:function(inData){

	var html=$.View('//good_earth_store/controller/customer/choose_menu/views/chooseMenu.ejs',
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

	this.displayParameters.childNameSpace.domObj=$('#'+this.displayParameters.childNameSpace.divId);
	this.displayParameters.offeringSpace.domObj=$('#'+this.displayParameters.offeringSpace.divId);
	this.displayParameters.selectedSpace.domObj=$('#'+this.displayParameters.selectedSpace.divId);
	
	this.displayParameters.childNameSpace.domObj.good_earth_store_customer_child({
		doneButtonHandler:this.callback('doneButtonHandler')
	});
	
	
	this.displayParameters.offeringSpace.domObj.good_earth_store_customer_offerings({});
	this.displayParameters.selectedSpace.domObj.good_earth_store_customer_choices({});

},


doneButtonHandler:function(control, parameter){
	var componentName='doneButton';
	switch(control){
		case 'click':
		
			this.element.html('');
			this.element[this.returnClassName](this.returnClassOptions);
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