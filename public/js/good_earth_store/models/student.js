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
		error=error?error:this.defaultError;


		var errors=this.validate(data);
		if (errors.length>0){
			success({status:-1, messages:errors, data:{}});
			return;
		}
	var outData=qtools.clone(data);
	for (var i in data){
		var element=data[i];
		if (typeof(element)!='object'){
			outData[i]=data[i];
		}
	}
		$.ajax({
				url: '/student/add',
				type: 'post',
				dataType: 'json',
				data: {data:outData},
				success: success,
				error: error,
				fixture: false
			});

},

validate:function(inData){
	var name, datum,
		errors=[];
	name='firstName';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "First Name is required"]);}
	name='lastName';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "Last Name is required"]);}
	name='schoolRefId';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "School Choice is required"]);}
	name='gradeLevelRefId';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "Grade Level is required"]);}

	return errors;
}
},
/* @Prototype */
{});

})