steal(

	'./controller/base/base.js'
)
.then(
	'./good_earth_store.css', 			// application CSS file
	'./models/models.js',		// steals all your models
	'./fixtures/fixtures.js',	// sets up fixtures for your models
	'jquery/dom/form_params',

	'./controller/session/register/register.js',
	'./controller/session/dispatch/dispatch.js',
	'./controller/session/login/login.js',


	'./controller/tools/ui/button2/button2.js',
	'./controller/tools/ui/modal_screen/modal_screen.js',

	'./resources/services/qtools.js',
	'./resources/services/viewHelper2.js',
	'./resources/services/controllerHelper2.js',
	'./resources/services/static.js',


	function(){					// configure your application
		$('#onlineStore').good_earth_store_session_dispatch();
	})