steal("funcunit/qunit", "good_earth_store/fixtures", "good_earth_store/models/offering.js", function(){
	module("Model: GoodEarthStore.Models.Offering")
	
	test("findAll", function(){
		expect(4);
		stop();
		GoodEarthStore.Models.Offering.findAll({}, function(offerings){
			ok(offerings)
	        ok(offerings.length)
	        ok(offerings[0].name)
	        ok(offerings[0].description)
			start();
		});
		
	})
	
	test("create", function(){
		expect(3)
		stop();
		new GoodEarthStore.Models.Offering({name: "dry cleaning", description: "take to street corner"}).save(function(offering){
			ok(offering);
	        ok(offering.id);
	        equals(offering.name,"dry cleaning")
	        offering.destroy()
			start();
		})
	})
	test("update" , function(){
		expect(2);
		stop();
		new GoodEarthStore.Models.Offering({name: "cook dinner", description: "chicken"}).
	            save(function(offering){
	            	equals(offering.description,"chicken");
	        		offering.update({description: "steak"},function(offering){
	        			equals(offering.description,"steak");
	        			offering.destroy();
						start();
	        		})
	            })
	
	});
	test("destroy", function(){
		expect(1);
		stop();
		new GoodEarthStore.Models.Offering({name: "mow grass", description: "use riding mower"}).
	            destroy(function(offering){
	            	ok( true ,"Destroy called" )
					start();
	            })
	})
})