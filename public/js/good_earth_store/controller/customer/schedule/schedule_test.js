steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Customer.Schedule", { 
	setup: function(){
		S.open("//good_earth_store/controller/customer/schedule/schedule.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Customer.Schedule Demo","demo text");
});


});