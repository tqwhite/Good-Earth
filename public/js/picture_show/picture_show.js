steal(
	'./picture_show.css', 			// application CSS file
	'./models/models.js',		// steals all your models
	'./fixtures/fixtures.js',	// sets up fixtures for your models
	'./controls/slideshow/slideshow.js',
	'./resources/slideshows/slider/slider.js',
	'./resources/services/qtools.js',
	'./resources/services/static.js',
	'./resources/slideshows/slider/slider.min.css'
)
.then(
	'./stylesheets/slideshow.css',
	function(){					// configure your application
		$('body').css('color', '#8844cc');
		$('title').text("TQ's Picture Show");
	})