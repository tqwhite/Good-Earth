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

		var errors=this.validate(data);
		if (errors.length>0){
			success({status:-1, messages:errors, data:{}});
			return;
		}

		$.ajax({
				url: '/account/get',
				type: 'post',
				dataType: 'json',
				data: {data:data},
				success: success,
				error: error,
				fixture: false
			});
},

validate:function(inData){ //unused
	var name, datum,
		errors=[];
	name='firstName';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "First name is required"]);}

	return errors;
}
},

/* @Prototype */
{});

})