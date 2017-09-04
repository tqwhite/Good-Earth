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
			{name:'student'},
			{name:'purchases'},
			{name:'daysIdClassLookup'}
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

	var outArray=[];
	var list=this.offerings;
	for (var i=0, len=list.length; i<len; i++){
		var element=list[i];


			outArray.push(element);
		
	}
	this.offerings=outArray;
	var html=$.View('//good_earth_store/controller/customer/offerings/views/init.ejs',
		$.extend(inData, {
			displayParameters:this.displayParameters,
			viewHelper:this.viewHelper,
			formData:{
				offerings:this.offerings,
				student:this.student
			},
			daysIdClassLookup:this.daysIdClassLookup
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

	this.disableDaysAlreadyBought();


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
},


chooseOfferingButtonClassIdHandler:function(control, parameter){
	var componentName='chooseOfferingButtonClassId';
	switch(control){
		case 'click':

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}
			
			$(window).bind('beforeunload.lunch', function(){ console.log("bind('beforeunload.lunch'");return 'LUNCHES WERE SELECTED AND NOT ORDERED. ARE YOU SURE YOU WANT TO LEAVE THE STORE?';});

			var dayRefId=parameter.thisDomObj.attr('dayRefId'),
				offeringRefId=parameter.thisDomObj.attr('offeringRefId');
				this.parentAccessFunction('sendPurchase', {
					dayRefId:dayRefId,
					dayIdClass:this.daysIdClassLookup[dayRefId],
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
},

disableDaysAlreadyBought:function(){

	var list=this.purchases.orders,
		dayRefId, dayIdClass;
	for (var i=0, len=list.length; i<len; i++){
		var element=list[i];
		dayRefId=element.day.refId;
		dayIdClass=this.daysIdClassLookup[dayRefId],
		orderStudentRefId=element.student.refId;

		if (this.student.refId==orderStudentRefId){
			$('.'+dayIdClass).good_earth_store_tools_ui_button2('setUnavailable');
		}
	}
}




})

});