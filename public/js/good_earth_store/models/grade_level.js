steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.GradeLevel
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend grade_level services.
 */
$.Model('GoodEarthStore.Models.GradeLevel',
/* @Static */
{

	getList:function(data, success, error){
		data={}; //no data for this listing

		success=success?success:function(){alert('success');};
		error=error?error:function(){alert('error');};


		$.ajax({
				url: '/gradelevel/list',
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