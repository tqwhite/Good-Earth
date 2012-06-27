steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Session.Register", { 
	setup: function(){
		S.open("//good_earth_store/controller/session/register/register.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Session.Register Demo","demo text");
});


});