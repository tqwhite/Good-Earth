steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Session.Dispatch
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Session.Dispatch',
/** @Static */
{
	defaults : {}
},

//js jquery/generate/controller good_earth_store/controller/session/login


/** @Prototype */
{
	init : function(){

		GoodEarthStore.Models.Session.start([], this.callback('receiveSessionStartup'));

	},

	receiveSessionStartup:function(inData){
		if (inData.status<1){
			this.element.good_earth_store_session_login('show login');
		}
		else{
			this.element.html("dispatch.js says, Show the correct control for "+inData.data.identity.firstName);
		}
	}

})

});