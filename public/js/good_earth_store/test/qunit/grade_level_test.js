steal("funcunit/qunit", "good_earth_store/fixtures", "good_earth_store/models/grade_level.js", function(){
	module("Model: GoodEarthStore.Models.GradeLevel")
	
	test("findAll", function(){
		expect(4);
		stop();
		GoodEarthStore.Models.GradeLevel.findAll({}, function(grade_levels){
			ok(grade_levels)
	        ok(grade_levels.length)
	        ok(grade_levels[0].name)
	        ok(grade_levels[0].description)
			start();
		});
		
	})
	
	test("create", function(){
		expect(3)
		stop();
		new GoodEarthStore.Models.GradeLevel({name: "dry cleaning", description: "take to street corner"}).save(function(grade_level){
			ok(grade_level);
	        ok(grade_level.id);
	        equals(grade_level.name,"dry cleaning")
	        grade_level.destroy()
			start();
		})
	})
	test("update" , function(){
		expect(2);
		stop();
		new GoodEarthStore.Models.GradeLevel({name: "cook dinner", description: "chicken"}).
	            save(function(grade_level){
	            	equals(grade_level.description,"chicken");
	        		grade_level.update({description: "steak"},function(grade_level){
	        			equals(grade_level.description,"steak");
	        			grade_level.destroy();
						start();
	        		})
	            })
	
	});
	test("destroy", function(){
		expect(1);
		stop();
		new GoodEarthStore.Models.GradeLevel({name: "mow grass", description: "use riding mower"}).
	            destroy(function(grade_level){
	            	ok( true ,"Destroy called" )
					start();
	            })
	})
})