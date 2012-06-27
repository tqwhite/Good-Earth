steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Session.Login", { 
	setup: function(){
		S.open("//good_earth_store/controller/session/login/login.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Session.Login Demo","demo text");
});


});