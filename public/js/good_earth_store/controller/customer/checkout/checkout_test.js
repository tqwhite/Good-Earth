steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Customer.Checkout", { 
	setup: function(){
		S.open("//good_earth_store/controller/customer/checkout/checkout.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Customer.Checkout Demo","demo text");
});


});