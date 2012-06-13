steal("funcunit", function(){
	module("picture_show test", { 
		setup: function(){
			S.open("//picture_show/picture_show.html");
		}
	});
	
	test("Copy Test", function(){
		equals(S("h1").text(), "Welcome to JavaScriptMVC 3.2!","welcome text");
	});
})