steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.Account
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend session services.
 */
GoodEarthStore.Models.Base.extend('GoodEarthStore.Models.Account',
/* @Static */
{

	getRetrievalFunction:function(data, success, error){
		return this.find;
},

	find:function(data, success, error){

		success=success?success:function(){alert('success');};
		error=error?error:GoodEarthStore.Models.Base.defaultError();

		$.ajax({
				url: '/account/get',
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