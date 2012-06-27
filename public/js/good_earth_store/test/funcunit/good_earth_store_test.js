steal("funcunit", function(){
	module("good_earth_store test", { 
		setup: function(){
			S.open("//good_earth_store/good_earth_store.html");
		}
	});
	
	test("Copy Test", function(){
		equals(S("h1").text(), "Welcome to JavaScriptMVC 3.2!","welcome text");
	});
})