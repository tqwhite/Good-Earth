steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Customer.Child", { 
	setup: function(){
		S.open("//good_earth_store/controller/customer/child/child.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Customer.Child Demo","demo text");
});


});