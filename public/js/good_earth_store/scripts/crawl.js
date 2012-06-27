// load('good_earth_store/scripts/crawl.js')

load('steal/rhino/rhino.js')

steal('steal/html/crawl', function(){
  steal.html.crawl("good_earth_store/good_earth_store.html","good_earth_store/out")
});
