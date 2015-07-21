steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Session.UserEditor", { 
	setup: function(){
		S.open("//good_earth_store/controller/session/user_editor/user_editor.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Session.UserEditor Demo","demo text");
});


});