steal("funcunit/qunit", "good_earth_store/fixtures", "good_earth_store/models/school.js", function(){
	module("Model: GoodEarthStore.Models.School")
	
	test("findAll", function(){
		expect(4);
		stop();
		GoodEarthStore.Models.School.findAll({}, function(schools){
			ok(schools)
	        ok(schools.length)
	        ok(schools[0].name)
	        ok(schools[0].description)
			start();
		});
		
	})
	
	test("create", function(){
		expect(3)
		stop();
		new GoodEarthStore.Models.School({name: "dry cleaning", description: "take to street corner"}).save(function(school){
			ok(school);
	        ok(school.id);
	        equals(school.name,"dry cleaning")
	        school.destroy()
			start();
		})
	})
	test("update" , function(){
		expect(2);
		stop();
		new GoodEarthStore.Models.School({name: "cook dinner", description: "chicken"}).
	            save(function(school){
	            	equals(school.description,"chicken");
	        		school.update({description: "steak"},function(school){
	        			equals(school.description,"steak");
	        			school.destroy();
						start();
	        		})
	            })
	
	});
	test("destroy", function(){
		expect(1);
		stop();
		new GoodEarthStore.Models.School({name: "mow grass", description: "use riding mower"}).
	            destroy(function(school){
	            	ok( true ,"Destroy called" )
					start();
	            })
	})
})