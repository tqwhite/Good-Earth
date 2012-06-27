//js good_earth_store/scripts/doc.js

load('steal/rhino/rhino.js');
steal("documentjs").then(function(){
	DocumentJS('good_earth_store/good_earth_store.html', {
		markdown : ['good_earth_store']
	});
});