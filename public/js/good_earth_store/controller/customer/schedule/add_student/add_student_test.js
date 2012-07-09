steal('funcunit').then(function(){

module("GoodEarthStore.Controller.Customer.Schedule.AddStudent", { 
	setup: function(){
		S.open("//good_earth_store/controller/customer/schedule/add_student/add_student.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "GoodEarthStore.Controller.Customer.Schedule.AddStudent Demo","demo text");
});


});