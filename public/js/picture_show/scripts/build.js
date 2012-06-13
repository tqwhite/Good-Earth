//js picture_show/scripts/build.js

load("steal/rhino/rhino.js");
steal('steal/build').then('steal/build/scripts','steal/build/styles',function(){
	steal.build('picture_show/scripts/build.html',{to: 'picture_show'});
});
