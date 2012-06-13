steal('funcunit').then(function(){

module("PictureShow.controls.slideshow", { 
	setup: function(){
		S.open("//picture_show/controls/slideshow/slideshow.html");
	}
});

test("Text Test", function(){
	equals(S("h1").text(), "PictureShow.controls.slideshow Demo","demo text");
});


});