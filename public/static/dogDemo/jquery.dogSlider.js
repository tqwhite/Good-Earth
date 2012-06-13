(function($){
    $.fn.tqCustomPlugin= function() {
    
    var self=this,
    	domObj=$(this),
    	prefix='_assets/slideshow/pix/',
    	prefix='pix/';
var sliderImages=[
{src:prefix+'a.jpg'},
{src:prefix+'b.jpg'},
{src:prefix+'c.jpg'},
{src:prefix+'d.jpg'},
{src:prefix+'e.jpg'},
{src:prefix+'f.jpg'},
{src:prefix+'g.jpg'},
{src:prefix+'h.jpg'},
{src:prefix+'i.jpg'},
{src:prefix+'j.jpg'},
{src:prefix+'k.jpg'},
{src:prefix+'l.jpg'},
{src:prefix+'m.jpg'},
{src:prefix+'n.jpg'},
{src:prefix+'o.jpg'},
{src:prefix+'p.jpg'},
{src:prefix+'q.jpg'},
{src:prefix+'r.jpg'},
{src:prefix+'s.jpg'},
{src:prefix+'t.jpg'}
];

	var s = new Slider(domObj)
		.setPhotos(sliderImages)
		.setTheme('no-control')
		.setSize(437, 210)
		.setTransition('transition-left');

};  
})(jQuery);