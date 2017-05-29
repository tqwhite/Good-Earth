steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs','./views/approved.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Customer.Checkout
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Customer.Checkout',
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

	this.user=GoodEarthStore.Models.Session.get('user');

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
	name='status'; nameArray.push({name:name});
	name='entryContainer'; nameArray.push({name:name});

	name='submitButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='cancelButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();



},

initDisplay:function(inData){

	var html=$.View("//good_earth_store/controller/customer/checkout/views/init.ejs",
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper,
			formData:{
				account:this.account,
				purchases:this.purchases,
				loginUser:this.user
			}
		})
		);
	this.element.html(html);
	this.initDomElements();
},

initDomElements:function(){
	var displayItem=this.displayParameters.submitButton;
	$('#'+displayItem.divId).good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:displayItem.handler,
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:"<div style='margin-top:4px;'>Submit</div>"
	});

	var displayItem=this.displayParameters.cancelButton;
	$('#'+displayItem.divId).good_earth_store_tools_ui_button2({
		ready:{classs:'basicReady'},
		hover:{classs:'basicHover'},
		clicked:{classs:'basicActive'},
		unavailable:{classs:'basicUnavailable'},
		accessFunction:displayItem.handler,
		initialControl:'setToReady', //initialControl:'setUnavailable'
		label:"Cancel"
	});


	this.element.find('input').qprompt();

},


submitButtonHandler:function(control, parameter){
	var componentName='submitButton';
	switch(control){
		case 'click':

	//		if (this.displayParameters.submitButton.submitted){return;}
	console.log("SUBMIT BUTTON LOCKOUT DISABLED IN /controller/customer/checkout/checkout.js");
			
			$('#'+this.displayParameters.submitButton.divId).html('Processing');
			this.displayParameters.submitButton.submitted=true;

// 			this.displayParameters.submitButton.timeoutId=setTimeout(
// 				function(){
// 					$('#'+this.displayParameters.entryContainer.divId).html($.View('//good_earth_store/controller/customer/checkout/views/timeout.ejs'));
// 
// 		$.ajax({
// 				url: '/utility/timeout',
// 				type: 'post',
// 				dataType: 'json',
// 				data: {
// 					purchase:this.purchases,
// 					account:this.account
// 					},
// 				success: function(){ return;},
// 				error: function(){ return;}
// 			});
// 
// 
// 				}.bind(this),
// 				45000
// 			);

			GoodEarthStore.Models.Purchase.process({
					cardData:this.element.formParams(),
					purchase:this.purchases,
					account:this.account
				},
				this.callback('catchProcessResult'));
		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},


cancelButtonHandler:function(control, parameter){
	var componentName='cancelButton';
	switch(control){
		case 'click':

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}

			this.dashboardContainer[this.returnClassName](this.returnClassOptions);
		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

catchProcessResult:function(inData){
		var statusDomObj=$('#'+this.displayParameters.status.divId);
		
		clearTimeout(this.displayParameters.submitButton.timeoutId);
		
	if (inData.status<0){
	
	
		$('#'+this.displayParameters.submitButton.divId).html('Submit');
		this.displayParameters.submitButton.submitted=false;
		
		statusDomObj.html('');
		var list=inData.messages;
		for (var i=0, len=list.length; i<len; i++){
			var element=list[i];
			$message=element[1]?element[1]:'Unknown internet error, please try again later<br/> (Grab a screenshot of this, please, if it persists and you are contacting us'+element[0]+', '+new Date()+')'; //element[0] is fieldname or category, element[1] is message
			statusDomObj.append("<div style=color:red;margin-left:4px;'>"+$message+"</div>");
		}
	}
	else{
		$(window).unbind('beforeunload');
		if (true){ //this can go away as soon as debugging is well into the past. 'false' makes it so that the payment process can run repeatedly.

			$('#'+this.displayParameters.submitButton.divId).html('Approved');
			
			switch(inData.status.toString()){
				case '1':
					$('#'+this.displayParameters.submitButton.divId).remove();
					$('#'+this.displayParameters.cancelButton.divId).remove();
					$('#'+this.displayParameters.entryContainer.divId).html($.View('//good_earth_store/controller/customer/checkout/views/approved.ejs'));
					break;
				case '2':
					$('#'+this.displayParameters.submitButton.divId).remove();
					$('#'+this.displayParameters.cancelButton.divId).remove();
					$('#'+this.displayParameters.entryContainer.divId).html($.View('//good_earth_store/controller/customer/checkout/views/deferred.ejs'));
					break;
				case '3':
					statusDomObj.append("<div style=color:red;margin-left:4px;'>Repeat</div>");
					break;
				case '4':
					$('#'+this.displayParameters.submitButton.divId).remove();
					$('#'+this.displayParameters.cancelButton.divId).remove();
					$('#'+this.displayParameters.entryContainer.divId).html($.View('//good_earth_store/controller/customer/checkout/views/fr.ejs'));
					break;

					break;
			}


					}
	}
}
})

});