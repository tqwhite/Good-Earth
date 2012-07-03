steal("funcunit/qunit", "good_earth_store/fixtures", "good_earth_store/models/base.js", function(){
	module("Model: GoodEarthStore.Models.base")
	
	test("findAll", function(){
		expect(4);
		stop();
		GoodEarthStore.Models.base.findAll({}, function(bases){
			ok(bases)
	        ok(bases.length)
	        ok(bases[0].name)
	        ok(bases[0].description)
			start();
		});
		
	})
	
	test("create", function(){
		expect(3)
		stop();
		new GoodEarthStore.Models.base({name: "dry cleaning", description: "take to street corner"}).save(function(base){
			ok(base);
	        ok(base.id);
	        equals(base.name,"dry cleaning")
	        base.destroy()
			start();
		})
	})
	test("update" , function(){
		expect(2);
		stop();
		new GoodEarthStore.Models.base({name: "cook dinner", description: "chicken"}).
	            save(function(base){
	            	equals(base.description,"chicken");
	        		base.update({description: "steak"},function(base){
	        			equals(base.description,"steak");
	        			base.destroy();
						start();
	        		})
	            })
	
	});
	test("destroy", function(){
		expect(1);
		stop();
		new GoodEarthStore.Models.base({name: "mow grass", description: "use riding mower"}).
	            destroy(function(base){
	            	ok( true ,"Destroy called" )
					start();
	            })
	})
})