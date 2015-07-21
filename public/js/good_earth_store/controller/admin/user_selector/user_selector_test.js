steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Admin.UserSelector", { 
	setup: function(){
		S.open("//good_earth_store/controller/admin/user_selector/user_selector.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Admin.UserSelector Demo","demo text");
});


});