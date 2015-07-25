steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Base
 */
$.Controller('GoodEarthStore.Controller.Base',
/** @Static */
{
	defaults : {}
},
/** @Prototype */
{

baseInits: function(params) {
	this.options=params;
	this.divPrefix=qtools.divPrefix(this.constructor._fullName);
	this.uniquePrefix=this.divPrefix+Math.floor(Math.random()*100000);
	this.acceptClicks=true;
},

assembleComponentDivIdObject:function(nameArray){
	var componentDivIds={}
		divPrefix=this.divPrefix;
	for (var i=0, len=nameArray.length; i<len; i++){
		if (typeof(nameArray[i])=='string'){ //the use of the simple string is DEPRECATED, 11/8/11
			componentDivIds[nameArray[i]]=divPrefix+nameArray[i];
		}
		else{
			componentDivIds[nameArray[i].name]={
				name:nameArray[i].name,
				divId:divPrefix+nameArray[i].name,
				handler:this.callback(nameArray[i].handlerName),
				targetDivId:divPrefix+nameArray[i].targetDivId
			};

			if (nameArray[i].handlerName){
				componentDivIds[nameArray[i].name].handler=this.callback(nameArray[i].handlerName);
			}

			if (nameArray[i].targetDivId){
				componentDivIds[nameArray[i].name].targetDivId=divPrefix+nameArray[i].targetDivId;
			}
		}
	}
	return componentDivIds;
},
// CLICK MANAGEMENT ========================================================================

turnOffClicksForAwhile: function(milliseconds, callbacks) {
	milliseconds=milliseconds?milliseconds:500;
	callbacks=callbacks?callbacks:{};

	this.setNotAcceptClicks();

    var setAcceptClicks = function(scope, args) {
        scope.setAcceptClicks();
    }
	if (typeof(callbacks.beforeFunction)=='function'){ callbacks.beforeFunction(callbacks.beforeArgs);}

	var setAcceptClicks=this.callback('setAcceptClicks');
	var postFunc=(typeof(callbacks.afterFunction)=='function'?callbacks.afterFunction:function(){return;});
	var afterArgs=callbacks.postArgs;
	var timeFunction=function(){
		setAcceptClicks();
		postFunc();
	};

    qtools.timeoutProxy(timeFunction, milliseconds);

},

setNotAcceptClicks:function(){
	this.acceptClicks=false;
},

setAcceptClicks:function(){
	this.acceptClicks=true;
},

isAcceptingClicks:function(){
	return this.acceptClicks;
},

listMessages:function(messageArray, separator, itemIndex){
	separator=separator?separator:'<br/>';
	itemIndex=itemIndex?itemIndex:1;
	var outString='';

	if (messageArray){
		for (var i=0, len=messageArray.length; i<len; i++){
			outString+=messageArray[i][itemIndex]+separator;
		}
	}

	return outString;
},

setupEnterKey:function(handler){
	$('input', this.element).addClass('mousetrap');
	Mousetrap.bind("enter", handler);
},

startProgressIndicator:function(args){
		if (typeof(args)!='object'){args={};}
		domObj=args.domObj?args.domObj:this.element;
		styleString=args.styleString?"style='"+args.styleString+"'":'';
		classString=args.classString?"class='"+args.classString+"'":'';
		divPrefix=this.divPrefix?this.divPrefix:'';
		divId=args.divId?args.divId:divPrefix+'_progressIndicator';

		if (!styleString && !classString){
			styleString="style='margin-left:100px;margin-top:100px;'";
		}

		domObj.html("<div "+classString+" "+styleString+" id='"+divId+"'></div>");

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
	$('#'+divId).append(spinner.el);
},

toggleSpinner:function(){
if (!this.spinner){
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
		var spinnerContainer=$("<div style='height: 100%; left: 50%; position: absolute; top: 50%; width: 100%;'></div>")
		.append(spinner.el);
		
		this.element.append(spinnerContainer);
		this.spinner=spinnerContainer;
	}
	else{
	$(this.spinner).remove();
		this.spinner=false;
	}
}

})

});