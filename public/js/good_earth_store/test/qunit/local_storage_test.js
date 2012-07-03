steal("funcunit/qunit", "good_earth_store/fixtures", "good_earth_store/models/local_storage.js", function(){
	module("Model: GoodEarthStore.Models.localStorage")
	
	test("findAll", function(){
		expect(4);
		stop();
		GoodEarthStore.Models.localStorage.findAll({}, function(local_storages){
			ok(local_storages)
	        ok(local_storages.length)
	        ok(local_storages[0].name)
	        ok(local_storages[0].description)
			start();
		});
		
	})
	
	test("create", function(){
		expect(3)
		stop();
		new GoodEarthStore.Models.localStorage({name: "dry cleaning", description: "take to street corner"}).save(function(local_storage){
			ok(local_storage);
	        ok(local_storage.id);
	        equals(local_storage.name,"dry cleaning")
	        local_storage.destroy()
			start();
		})
	})
	test("update" , function(){
		expect(2);
		stop();
		new GoodEarthStore.Models.localStorage({name: "cook dinner", description: "chicken"}).
	            save(function(local_storage){
	            	equals(local_storage.description,"chicken");
	        		local_storage.update({description: "steak"},function(local_storage){
	        			equals(local_storage.description,"steak");
	        			local_storage.destroy();
						start();
	        		})
	            })
	
	});
	test("destroy", function(){
		expect(1);
		stop();
		new GoodEarthStore.Models.localStorage({name: "mow grass", description: "use riding mower"}).
	            destroy(function(local_storage){
	            	ok( true ,"Destroy called" )
					start();
	            })
	})
})