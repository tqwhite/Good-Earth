steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.School
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend school services.
 */
GoodEarthStore.Models.Base.extend('GoodEarthStore.Models.School',
/* @Static */
{

	getList:function(data, success, error){
		data={}; //no data for this listing

		success=success?success:function(){alert('success');};
		error=error?error:function(){alert('error');};


		$.ajax({
				url: '/school/list',
				type: 'post',
				dataType: 'json',
				data: {data:data},
				success: success,
				error: error,
				fixture: false
			});

},
},
/* @Prototype */
{});

})