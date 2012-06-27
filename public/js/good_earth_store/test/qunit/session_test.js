steal("funcunit/qunit", "good_earth_store/fixtures", "good_earth_store/models/session.js", function(){
	module("Model: GoodEarthStore.Models.Session")
	
	test("findAll", function(){
		expect(4);
		stop();
		GoodEarthStore.Models.Session.findAll({}, function(sessions){
			ok(sessions)
	        ok(sessions.length)
	        ok(sessions[0].name)
	        ok(sessions[0].description)
			start();
		});
		
	})
	
	test("create", function(){
		expect(3)
		stop();
		new GoodEarthStore.Models.Session({name: "dry cleaning", description: "take to street corner"}).save(function(session){
			ok(session);
	        ok(session.id);
	        equals(session.name,"dry cleaning")
	        session.destroy()
			start();
		})
	})
	test("update" , function(){
		expect(2);
		stop();
		new GoodEarthStore.Models.Session({name: "cook dinner", description: "chicken"}).
	            save(function(session){
	            	equals(session.description,"chicken");
	        		session.update({description: "steak"},function(session){
	        			equals(session.description,"steak");
	        			session.destroy();
						start();
	        		})
	            })
	
	});
	test("destroy", function(){
		expect(1);
		stop();
		new GoodEarthStore.Models.Session({name: "mow grass", description: "use riding mower"}).
	            destroy(function(session){
	            	ok( true ,"Destroy called" )
					start();
	            })
	})
})