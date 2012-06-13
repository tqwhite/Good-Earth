steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/init.ejs', function($){

/**
 * @class PictureShow.controls.slideshow
 */
$.Controller('PictureShow.controls.slideshow',
/** @Static */
{
	defaults : {}
},
/** @Prototype */
{
init : function(){
	this.idNameBase='block';
	this.blockCount=3;
	this.element.html("//picture_show/controls/slideshow/views/init.ejs",{
		message: "Hello World!",
		idNameBase:this.idNameBase,
		blockCount:this.blockCount
	});
	
	PictureShow.picture.getList({}, this.callback('catch'));
},

catch:function(inData){
	var html='<p/>'
		fileList=inData.data,
		sliderImages=[],
		size={},
		size.height=0,
		size.width=0,
		count=0,
		sliders=[];
		
	//console.dir(fileList);
	
	for (var i in fileList){
		html+="<img src="+fileList[i].thumbnail.uri+" ><br/>";
		sliderImages.push({src:fileList[i].thumbnail.uri, link:fileList[i].fullsize.uri});
		size.width=Math.max(size.width, fileList[i].thumbnail.size.width);
		size.height=Math.max(size.height, fileList[i].thumbnail.size.height);
		count++;
	//	if (count==3){break;}
	}
//	$('#mainContainer').append(html);
	
	upperLeftDomObj=$('#block0');
	upperRightDomObj=$('#block1');
	
	for (var i=0; i<this.blockCount; i++){
		sliders[i]={};
		sliders[i].domObj=$('#'+this.idNameBase+i);
		sliders[i].slider=new Slider(sliders[i].domObj)
			.setSize(upperLeftDomObj.height(), upperLeftDomObj.width())
			.setTheme('theme-dark')
			.setTransition('transition-right')
			.setDuration(3000)
			.setPhotos(sliderImages)
			.slide(this.blockCount-i);
	
	}
	

	$('.slider .options').hide();
	$('.slider.theme-dark').css('background', '#777');
	$('.slider.theme-dark .slide-image').css('background', '#777');	

}
});

$('body').picture_show_controls_slideshow();

});