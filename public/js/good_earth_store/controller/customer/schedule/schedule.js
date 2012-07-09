steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Customer.Schedule
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Customer.Schedule',
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
			{name:'lunchButtonHandler'}
		],
		source:this.constructor._fullName
 	});

	this.initControlProperties();
	this.initDisplayProperties();

	options=options?options:{};
	if (options.initialStatusMessage){this.initialStatusMessage=options.initialStatusMessage;}
	this.options=options;

	this.initDisplay();

},

update:function(){
	this.init(this.element, this.options);
},

initDisplayProperties:function(){

	nameArray=[];

	name='newButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='lunchButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
},

initDisplay:function(inData){

	var html=$.View('//good_earth_store/controller/customer/schedule/views/init.ejs',
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper,
			formData:{
				account:this.account
			}
		})
		);
	this.element.html(html);
	this.initDomElements();
},

initDomElements:function(){
	this.displayParameters.newButton.domObj=$('#'+this.displayParameters.newButton.divId);

	this.displayParameters.newButton.domObj.good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:this.displayParameters.newButton.handler, //NOTE: this handler is passed from the parent controller (dashboard)
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:"Add Student"
	});



	$('.'+this.displayParameters.lunchButton.divId).good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:this.displayParameters.lunchButton.handler, //NOTE: this handler is passed from the parent controller (dashboard)
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:"Buy Lunches"
	});
},


newButtonHandler:function(control, parameter){
	var componentName='newButton';
	switch(control){
		case 'click':

			$($('#'+this.displayParameters.newButton.divId).parent()).good_earth_store_customer_schedule_add_student({
				'account':this.account,
				'redrawSchedule':this.callback('update')
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