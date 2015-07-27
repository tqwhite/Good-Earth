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

		var errors=this.validate(data);
		if (errors.length>0){
			success({status:-1, messages:errors, data:{}});
			return;
		}

		$.ajax({
				url: 'user/register',
				type: 'post',
				dataType: 'json',
				data: {data:data},
				success: success,
				error: error,
				fixture: false
			});

},

validate:function(inData){
	var name, datum,
		errors=[];
	name='userName';
	datum=inData[name];
	if (!datum || datum.length<6)
	{errors.push([name, "Login Name too short"]);}
	if (datum && datum.match(/ /))
	{errors.push([name, "Login Name cannot contain spaces"]);}
	name='password';
	datum=inData[name];
	if (!datum || datum.length<6)
	{errors.push([name, "Password too short"]);}
	name='emailAdr';
	datum=inData[name];
	var emailRegexTest = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/; //thanks: http://www.marketingtechblog.com/javascript-regex-emailaddress/
	if (!datum || !emailRegexTest.test(datum))
	{errors.push([name, "Invalid email address"]);}

	return errors;
}

},

/* @Prototype */
{});

})