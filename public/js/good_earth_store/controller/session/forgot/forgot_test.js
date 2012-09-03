steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Session.Forgot", { 
	setup: function(){
		S.open("//good_earth_store/controller/session/forgot/forgot.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Session.Forgot Demo","demo text");
});


});