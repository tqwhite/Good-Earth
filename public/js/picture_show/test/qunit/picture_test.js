steal("funcunit/qunit", "picture_show/fixtures", "picture_show/models/picture.js", function(){
	module("Model: PictureShow.picture")
	
	test("findAll", function(){
		expect(4);
		stop();
		PictureShow.picture.findAll({}, function(pictures){
			ok(pictures)
	        ok(pictures.length)
	        ok(pictures[0].name)
	        ok(pictures[0].description)
			start();
		});
		
	})
	
	test("create", function(){
		expect(3)
		stop();
		new PictureShow.picture({name: "dry cleaning", description: "take to street corner"}).save(function(picture){
			ok(picture);
	        ok(picture.id);
	        equals(picture.name,"dry cleaning")
	        picture.destroy()
			start();
		})
	})
	test("update" , function(){
		expect(2);
		stop();
		new PictureShow.picture({name: "cook dinner", description: "chicken"}).
	            save(function(picture){
	            	equals(picture.description,"chicken");
	        		picture.update({description: "steak"},function(picture){
	        			equals(picture.description,"steak");
	        			picture.destroy();
						start();
	        		})
	            })
	
	});
	test("destroy", function(){
		expect(1);
		stop();
		new PictureShow.picture({name: "mow grass", description: "use riding mower"}).
	            destroy(function(picture){
	            	ok( true ,"Destroy called" )
					start();
	            })
	})
})