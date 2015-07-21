steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Admin.Control", { 
	setup: function(){
		S.open("//good_earth_store/controller/admin/control/control.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Admin.Control Demo","demo text");
});


});