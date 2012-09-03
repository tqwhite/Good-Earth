steal(

	'./controller/base/base.js'
)
.then(
	'./resources/jqueryPlugins/jquery.rc4.js',
	'./resources/jqueryPlugins/jquery.cookie.js',
	'./resources/jqueryPlugins/jquery.md5.js',
	'./resources/jqueryPlugins/jquery.qprompt.js',
	'./resources/jqueryPlugins/spin.js',

	'./resources/other/json2.js',
	'./resources/other/mousetrap.js',


	'./good_earth_store.css', 			// application CSS file
	'./models/models.js',		// steals all your models
	'jquery/dom/fixture',	// sets up fixtures for your models
	'jquery/dom/form_params',

	'./controller/session/register/register.js',
	'./controller/session/dispatch/dispatch.js',
	'./controller/session/login/login.js',
	'./controller/session/forgot/forgot.js',
	'./controller/session/forgot/password/password.js',

	'./controller/customer/dashboard/dashboard.js',
	'./controller/customer/choose_menu/choose_menu.js',
	'./controller/customer/parent/parent.js',
	'./controller/customer/schedule/schedule.js',
	'./controller/customer/schedule/add_student/add_student.js',
	'./controller/customer/purchases/purchases.js',
	'./controller/customer/checkout/checkout.js',

	'./controller/customer/child/child.js',
	'./controller/customer/offerings/offerings.js',
	'./controller/customer/choices/choices.js',


	'./controller/tools/ui/button2/button2.js',
	'./controller/tools/ui/modal_screen/modal_screen.js',

	'./resources/services/qtools.js',
	'./resources/services/viewHelper2.js',
	'./resources/services/controllerHelper2.js',
	'./resources/services/static.js',


	function(){					// configure your application
		$('#onlineStore').good_earth_store_session_dispatch();
	})