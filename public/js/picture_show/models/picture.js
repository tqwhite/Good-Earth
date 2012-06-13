steal('jquery/model', function(){

/**
 * @class PictureShow.picture
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend picture services.  
 */
$.Model('PictureShow.picture',
/* @Static */
{
	findAll: "/pictures/list",
  	findOne : "/pictures/{id}.json", 
  	create : "/pictures.json",
 	update : "/pictures/{id}.json",
  	destroy : "/pictures/{id}.json",
  	
getList:function(args, success, error){
		error=error?error:function(){alert('picture/getList server failed');};
		$.ajax({
			url: '/pictures/list',
			type: 'get',
			dataType: 'json',
			data: args,
			success: success,
			error: error,
			fixture: false
		});
}
},
/* @Prototype */
{});

})