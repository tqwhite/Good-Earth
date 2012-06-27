// map fixtures for this application

steal("jquery/dom/fixture", function(){
	
	$.fixture.make("session", 5, function(i, session){
		var descriptions = ["grill fish", "make ice", "cut onions"]
		return {
			name: "session "+i,
			description: $.fixture.rand( descriptions , 1)[0]
		}
	})
})