steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.Student
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend school services.
 */
GoodEarthStore.Models.Base.extend('GoodEarthStore.Models.Student',
/* @Static */
{

	add:function(data, success, error){

		success=success?success:function(){alert('success');};
		error=error?error:function(){console.log('error');};


		$.ajax({
				url: '/student/add',
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