steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.User
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend session services.
 */
$.Model('GoodEarthStore.Models.User',
/* @Static */
{
	findAll: "/sessions.json",
  	findOne : "/sessions/{id}.json",
  	create : "/sessions.json",
 	update : "/sessions/{id}.json",
  	destroy : "/sessions/{id}.json",

	register:function(data, success, error){

		success=success?success:function(){alert('success');};
		error=error?error:function(){alert('error');};

		$.ajax({
				url: 'user/register',
				type: 'post',
				dataType: 'json',
				data: {data:data},
				success: success,
				error: error,
				fixture: false
			});
	}

},
/* @Prototype */
{});

})