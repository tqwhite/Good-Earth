//js picture_show/scripts/doc.js

load('steal/rhino/rhino.js');
steal("documentjs").then(function(){
	DocumentJS('picture_show/picture_show.html', {
		markdown : ['picture_show']
	});
});