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
	//	this.session.saveReferenceLocation();
		this.getServerData();
		GoodEarthStore.Models.Session.start([], this.callback('receiveSessionStartup'));

		if (window.location.hash){
			this.startingHash=window.location.hash.replace('#', '');
		}


		if (console){
			console.log('dispatch.js says, debugg items exist');
			console.dir(window.GoodEarthStore);
		}
	},

	getServerData:function(){
		var serverDataDomObj=$('#serverData'), //'#serverData' is defined in Q_Controller_Action_Helper_WriteServerCommDiv()
			formParams;

			if (serverDataDomObj.length>0){
				formParams=serverDataDomObj.formParams();
				this.serverDataDomObj=serverDataDomObj;
			}
			else{
				formParams={};
			}

			if (formParams.user_confirm_message){this.userConfirmMessage=formParams.user_confirm_message;}
			if (formParams.assert_initial_controller){this.initialController=formParams.assert_initial_controller;}

	},

	receiveSessionStartup:function(inData){
		var userIdCookie=GoodEarthStore.Models.LocalStorage.getCookieData(GoodEarthStore.Models.LocalStorage.getConstant('loginCookieName')).data;


		if (inData.status<1 || this.initialController=='none'){
			if (!this.initialController && !this.startingHash && userIdCookie){this.initialController='login';}
			switch (this.startingHash || this.initialController){
				default:
				case 'register':
					this.element.good_earth_store_session_register({initialStatusMessage:this.userConfirmMessage});
					break;
				case 'login':
					this.element.good_earth_store_session_login({initialStatusMessage:this.userConfirmMessage});
					break;
				case 'none':
					this.serverDataDomObj.html('<div style="color:white;background:#cc9999;">aborting store app</div>').show();
					break;
			}
		}
		else{
			GoodEarthStore.Models.Session.keep('user', inData.data);
			this.element.html('');
			this.element.good_earth_store_customer_dashboard();
		}
	}

})

});