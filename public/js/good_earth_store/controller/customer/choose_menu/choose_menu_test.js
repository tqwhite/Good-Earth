steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Customer.ChooseMenu", { 
	setup: function(){
		S.open("//good_earth_store/controller/customer/choose_menu/choose_menu.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Customer.ChooseMenu Demo","demo text");
});


});