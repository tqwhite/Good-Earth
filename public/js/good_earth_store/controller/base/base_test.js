steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Base", { 
	setup: function(){
		S.open("//good_earth_store/controller/base/base.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Base Demo","demo text");
});


});