steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Customer.Dashboard", { 
	setup: function(){
		S.open("//good_earth_store/controller/customer/dashboard/dashboard.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Customer.Dashboard Demo","demo text");
});


});