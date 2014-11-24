steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Session.Forgot
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Session.Forgot',
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
			{name:'selector'}
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
	name='saveButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='registerButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});
	name='loginButton'; nameArray.push({name:name, handlerName:name+'Handler', targetDivId:name+'Target'});

	this.displayParameters=$.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

},

initControlProperties:function(){
	this.viewHelper=new viewHelper2();
	this.enterKeyEnabled=false;
},

initDisplay:function(inData){

	var html=$.View('//good_earth_store/controller/session/forgot/views/init.ejs',
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
	this.displayParameters.saveButton.domObj=$('#'+this.displayParameters.saveButton.divId);
	this.displayParameters.registerButton.domObj=$('#'+this.displayParameters.registerButton.divId);
	this.displayParameters.loginButton.domObj=$('#'+this.displayParameters.loginButton.divId);

			this.displayParameters.saveButton.domObj.good_earth_store_tools_ui_button2({
				ready:{classs:'basicReady'},
				hover:{classs:'basicHover'},
				clicked:{classs:'basicActive'},
				unavailable:{classs:'basicUnavailable'},
				accessFunction:this.displayParameters.saveButton.handler,
				initialControl:'setToReady', //initialControl:'setUnavailable'
				label:"<div style='margin-top:5px;'>Send Reset</div>"
			});

			this.displayParameters.registerButton.domObj.good_earth_store_tools_ui_button2({
				ready:{classs:'smallReady'},
				hover:{classs:'smallHover'},
				clicked:{classs:'smallActive'},
				unavailable:{classs:'smallUnavailable'},
				accessFunction:this.displayParameters.registerButton.handler,
				initialControl:'setToReady', //initialControl:'setUnavailable'
				label:"<div >Register Instead</div>"
			});

			this.displayParameters.loginButton.domObj.good_earth_store_tools_ui_button2({
				ready:{classs:'smallReady'},
				hover:{classs:'smallHover'},
				clicked:{classs:'smallActive'},
				unavailable:{classs:'smallUnavailable'},
				accessFunction:this.displayParameters.loginButton.handler,
				initialControl:'setToReady', //initialControl:'setUnavailable'
				label:"<div>Login Instead</div>"
			});

			if (!this.initialStatusMessage){
				this.initialStatusMessage='You will be sent an email with a link so you can create a new password.';
			}
				$('#'+this.displayParameters.status.divId)
				.html(this.initialStatusMessage)
				.removeClass('bad')
				.addClass('good');


			this.setupEnterKey(this.displayParameters.saveButton.handler);
},

//BUTTON HANDLERS =========================================================================================================


saveButtonHandler:function(control, parameter){



	var componentName='saveButton';
	if (control.which=='13'){control='click';}; //enter key
	switch(control){
		case 'click':

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}

			GoodEarthStore.Models.User.resetPassword(this.element.formParams(), this.callback('resetAfterSave'));
		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

resetAfterSave:function(inData){

	if (inData.status>0){
		GoodEarthStore.Models.Session.keep('user', inData.data);
		$('#'+this.displayParameters.status.divId).html("Welcome back, "+inData.data.firstName+" <span style=color:gray;font-size:6pt'>("+inData.data.school+")</span>").removeClass('bad').addClass('good');
		GoodEarthStore.Models.LocalStorage.setCookie(GoodEarthStore.Models.LocalStorage.getConstant('loginCookieName'), inData.data.userName);
		this.element.html("<div style='margin-left:450px;margin-top:200px;' id='spinner'></div>").good_earth_store_customer_dashboard();


	var opts={
	  lines: 7, // The number of lines to draw
	  length: 20, // The length of each line
	  width: 4, // The line thickness
	  radius: 10, // The radius of the inner circle
	  color: '#436235', // #rbg or #rrggbb
	  speed: 1, // Rounds per second
	  trail: 60, // Afterglow percentage
	  shadow: true // Whether to render a shadow
	};

	var spinner = new Spinner(opts).spin();
	$('#spinner').append(spinner.el);

	}
	else{
					var outMessage='';
					for (var i=0, len=inData.messages.length; i<len; i++){
						var mainMessage=inData.messages[i][1];
						if (mainMessage=="Reset email sent"){
							mainMessage+="<div style='font-size:70%;'>Check spam filter.<br/>Reset message only good for fifteen minutes.</div>";
							}
						outMessage+=mainMessage+'<br/>';
					}
				$('#'+this.displayParameters.status.divId).html(outMessage).removeClass('good').addClass('bad');
	}
},



registerButtonHandler:function(control, parameter){
	var componentName='registerButton';
	switch(control){
		case 'click':

		if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
		else{return;}

		this.element.good_earth_store_session_register();

		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

loginButtonHandler:function(control, parameter){
	var componentName='loginButton';
	switch(control){
		case 'click':

			if (this.isAcceptingClicks()){this.turnOffClicksForAwhile();} //turn off clicks for awhile and continue, default is 500ms
			else{return;}

			this.element.good_earth_store_session_login();
		break;
		case 'setAccessFunction':
			if (!this[componentName]){this[componentName]={};}
			this[componentName].accessFunction=parameter;
		break;
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

//ENTER KEY =========================================================================================================

enableKeyManager:function(eventObj){
	if (eventObj.type=='focus'){
		this.disableEnterKey();
	}
	else{
		this.enableEnterKey();
	}
	//change dblclick mousedown mouseover mouseout dblclick
	//focusin focusout keydown keyup keypress select
},

enterKeyHandler:function(eventObj){
	if (this.fieldsAreValid() && eventObj.which==13){
		this.saveButtonHandler('click');
	}
},

disableEnterKey:function(){
	if (this.enterKeyEnabled){
		this.element.unbind('keydown');
		this.enterKeyEnabled=false;
	}
},

enableEnterKey:function(){
	if (!this.enterKeyEnabled && this.fieldsAreValid()){
		this.element.bind('keydown',this.callback('enterKeyHandler'));
		this.enterKeyEnabled=true;
	}
},

lastFieldHandler:function(eventObj){
	var firstFieldObj=$('#'+this.displayParameters.firstField.divId);
	if (eventObj.type=='blur'){
		qtools.timeoutProxy(function(){
			firstFieldObj.focus();
		}, 100);
	}
}



})

});