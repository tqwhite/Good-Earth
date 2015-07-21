steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Admin.User", { 
	setup: function(){
		S.open("//good_earth_store/controller/admin/user/user.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Admin.User Demo","demo text");
});


});