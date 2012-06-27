steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.Session
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend session services.
 */
$.Model('GoodEarthStore.Models.Session',
/* @Static */
{
	findAll: "/sessions.json",
  	findOne : "/sessions/{id}.json",
  	create : "/sessions.json",
 	update : "/sessions/{id}.json",
  	destroy : "/sessions/{id}.json",

	start:function(placeholder, success, error){

		success=success?success:function(){alert('success');};
		error=error?error:function(){alert('error');};

		$.ajax({
				url: 'session/start',
				type: 'post',
				dataType: 'json',
				data: {data:{hello:'hello from UI'}},
				success: success,
				error: error,
				fixture: false
			});
	},

	login:function(data, success, error){

		success=success?success:function(){alert('success');};
		error=error?error:function(){alert('error');};

		$.ajax({
				url: 'session/login',
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