steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Customer.Parent", { 
	setup: function(){
		S.open("//good_earth_store/controller/customer/parent/parent.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Customer.Parent Demo","demo text");
});


});