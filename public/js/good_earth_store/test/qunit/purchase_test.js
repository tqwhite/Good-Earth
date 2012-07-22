steal("funcunit/qunit", "good_earth_store/fixtures", "good_earth_store/models/purchase.js", function(){
	module("Model: GoodEarthStore.Models.Purchase")
	
	test("findAll", function(){
		expect(4);
		stop();
		GoodEarthStore.Models.Purchase.findAll({}, function(purchases){
			ok(purchases)
	        ok(purchases.length)
	        ok(purchases[0].name)
	        ok(purchases[0].description)
			start();
		});
		
	})
	
	test("create", function(){
		expect(3)
		stop();
		new GoodEarthStore.Models.Purchase({name: "dry cleaning", description: "take to street corner"}).save(function(purchase){
			ok(purchase);
	        ok(purchase.id);
	        equals(purchase.name,"dry cleaning")
	        purchase.destroy()
			start();
		})
	})
	test("update" , function(){
		expect(2);
		stop();
		new GoodEarthStore.Models.Purchase({name: "cook dinner", description: "chicken"}).
	            save(function(purchase){
	            	equals(purchase.description,"chicken");
	        		purchase.update({description: "steak"},function(purchase){
	        			equals(purchase.description,"steak");
	        			purchase.destroy();
						start();
	        		})
	            })
	
	});
	test("destroy", function(){
		expect(1);
		stop();
		new GoodEarthStore.Models.Purchase({name: "mow grass", description: "use riding mower"}).
	            destroy(function(purchase){
	            	ok( true ,"Destroy called" )
					start();
	            })
	})
})