steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.Purchase
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend purchase services.  
 */
$.Model('GoodEarthStore.Models.Purchase',
/* @Static */
{
	findAll: "/purchases.json",
  	findOne : "/purchases/{id}.json", 
  	create : "/purchases.json",
 	update : "/purchases/{id}.json",
  	destroy : "/purchases/{id}.json"
},
/* @Prototype */
{});

})