// map fixtures for this application

steal("jquery/dom/fixture", function(){
	

	$.fixture.make("picture", 5, function(i, picture){
		var descriptions = ["grill fish", "make ice", "cut onions"]
		return {
			name: "picture "+i,
			description: $.fixture.rand( descriptions , 1)[0]
		}
	})
})