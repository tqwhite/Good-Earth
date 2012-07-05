steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Customer.Offerings", { 
	setup: function(){
		S.open("//good_earth_store/controller/customer/offerings/offerings.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Customer.Offerings Demo","demo text");
});


});