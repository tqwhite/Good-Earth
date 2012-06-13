// load('picture_show/scripts/crawl.js')

load('steal/rhino/rhino.js')

steal('steal/html/crawl', function(){
  steal.html.crawl("picture_show/picture_show.html","picture_show/out")
});
