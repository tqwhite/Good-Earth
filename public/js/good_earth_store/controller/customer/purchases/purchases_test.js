steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Customer.Purchases", { 
	setup: function(){
		S.open("//good_earth_store/controller/customer/purchases/purchases.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Customer.Purchases Demo","demo text");
});


});