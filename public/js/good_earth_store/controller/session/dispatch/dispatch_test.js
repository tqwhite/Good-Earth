steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Session.Dispatch", { 
	setup: function(){
		S.open("//good_earth_store/controller/session/dispatch/dispatch.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Session.Dispatch Demo","demo text");
});


});