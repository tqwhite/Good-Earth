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
	milliseconds=milliseconds?milliseconds:2000;
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
}

})

});