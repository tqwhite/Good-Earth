steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Customer.Choices", { 
	setup: function(){
		S.open("//good_earth_store/controller/customer/choices/choices.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Customer.Choices Demo","demo text");
});


});