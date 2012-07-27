steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.Offering
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend offering services.
 */
$.Model('GoodEarthStore.Models.Offering',
/* @Static */
{

	getRetrievalFunction:function(data, success, error){
		return this.getList;
},

	getList:function(data, success, error){
		data={}; //no data for this listing

		success=success?success:function(){alert('success');};
		error=error?error:function(){alert('error');};


		$.ajax({
				url: '/offering/list',
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