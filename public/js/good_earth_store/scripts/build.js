//js good_earth_store/scripts/build.js

load("steal/rhino/rhino.js");
steal('steal/build').then('steal/build/scripts','steal/build/styles',function(){
	steal.build('good_earth_store/scripts/build.html',{to: 'good_earth_store'});
});
