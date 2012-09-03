steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Session.Forgot.Password", { 
	setup: function(){
		S.open("//good_earth_store/controller/session/forgot/password/password.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Session.Forgot.Password Demo","demo text");
});


});