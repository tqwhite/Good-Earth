steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.User
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend session services.
 */
GoodEarthStore.Models.Base.extend('GoodEarthStore.Models.User',
/* @Static */
{
	findAll: "/sessions.json",
  	findOne : "/sessions/{id}.json",
  	create : "/sessions.json",
 	update : "/sessions/{id}.json",
  	destroy : "/sessions/{id}.json",

	register:function(data, success, error){

		success=success?success:function(){alert('success');};
		error=error?error:this.defaultError;

		var errors=this.validate(data);
		if (errors.length>0){
			success({status:-1, messages:errors, data:{}});
			return;
		}

		$.ajax({
				url: '/account/register',
				type: 'post',
				dataType: 'json',
				data: {data:data},
				success: success,
				error: error,
				fixture: false
			});

},

	resetPassword:function(data, success, error){

		success=success?success:function(){alert('success');};
		error=error?error:this.defaultError;

		var errors=this.validateReset(data);
		if (errors.length>0){
			success({status:-1, messages:errors, data:{}});
			return;
		}

		$.ajax({
				url: '/user/resetpw',
				type: 'post',
				dataType: 'json',
				data: {data:data},
				success: success,
				error: error,
				fixture: false
			});

},

	changePassword:function(data, success, error){

		success=success?success:function(){alert('success');};
		error=error?error:this.defaultError;

		var errors=this.validateChange(data);
		if (errors.length>0){
			success({status:-1, messages:errors, data:{}});
			return;
		}

		$.ajax({
				url: '/user/setpw',
				type: 'post',
				dataType: 'json',
				data: {data:data},
				success: success,
				error: error,
				fixture: false
			});

},

validateChange:function(inData){
	var name, datum,
		errors=[];

	name='password';
	datum=inData[name];
	if (!datum || datum.toLowerCase()=='required')
	{errors.push([name, "Password is required"]);}

	name='confirmPassword';
	datum=inData[name];
	if (!datum || datum.toLowerCase()=='required')
	{errors.push([name, "Confirmation Password is required"]);}

	if (inData['password']!=inData['confirmPassword'])
	{errors.push([name, "Confirmation Password does not match"]);}

	name='password';
	datum=inData[name];
	if (!datum || datum.length<6)
	{errors.push([name, "Password must be six or more characters"]);}


	return errors;
},

validateReset:function(inData){
	var name, datum,
		errors=[];



	name='identifier';
	datum=inData[name];
	if (!datum || datum.toLowerCase()=='required')
	{errors.push([name, "Last name is required"]);}


	return errors;
},

validate:function(inData){
	var name, datum,
		errors=[];



	name='lastName';
	datum=inData[name];
	if (!datum || datum.toLowerCase()=='required')
	{errors.push([name, "Last name is required"]);}

	name='firstName';
	datum=inData[name];
	if (!datum || datum.toLowerCase()=='required')
	{errors.push([name, "First name is required"]);}

	name='userName';
	datum=inData[name];
	if (!datum || datum.length<6 || datum.toLowerCase()=='required')
	{errors.push([name, "Login Name must be six or more characters"]);}
	if (datum && datum.match(/\s/))
	{errors.push([name, "Login Name cannot contain spaces"]);}

	name='password';
	datum=inData[name];
	if (!datum || datum.length<6)
	{
		if (inData.adminFlag && !datum){
			//if an admin doesn't enter a password, it says 'no change' and is valid
		}
		else{
		errors.push([name, "Password must be six or more characters"]);
		}
	
	}

	name='emailAdr';
	datum=inData[name];
	var emailRegexTest = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/; //thanks: http://www.marketingtechblog.com/javascript-regex-emailaddress/
	if (!datum || !emailRegexTest.test(datum) || datum.toLowerCase()=='required')
	{errors.push([name, "Invalid email address"]);}

	name='emailAdr';
	datum=inData[name];
	if (inData['confirmEmail'] && !datum || datum.toLowerCase()=='required')
	{errors.push([name, "Confirmation email address is required"]);}

	if (inData['emailAdr'] && inData['confirmEmail'] && inData['emailAdr']!=inData['confirmEmail'])
	{errors.push([name, "Confirmation email address does not match email address"]);}

	name='phoneNumber';
	datum=inData[name];
	if (!datum || datum.toLowerCase()=='000-000-0000')
	{errors.push([name, "Phone Number is required"]);}
		else if (datum.length!=12)
		{errors.push([name, "Phone Number must be 000-000-0000"]);}
		else if (!datum.match(/\d{3}[ -]\d{3}[- ]\d{4}/))
		{errors.push([name, "Phone Number must be 000-000-0000"]);}

	name='street';
	datum=inData[name];
	if (!datum || datum=='required')
	{errors.push([name, "Street is required"]);}

	name='city';
	datum=inData[name];
	if (!datum || datum=='required')
	{errors.push([name, "City is required"]);}

	name='state';
	datum=inData[name];
	if (datum.length!=2)
	{errors.push([name, "State code must be two characters"]);}

	name='zip';
	datum=inData[name];
	if (!datum || datum=='00000')
	{errors.push([name, "Zip Code is required"]);}
		else if (datum.length!=5)
		{errors.push([name, "Zip must be five digits (00000)"]);}
		else if (!datum.match(/\d{5}/))
		{errors.push([name, "Zip must be five digits (00000)"]);}




	return errors;
},

searchByFragment:function(data, success, error){

		success=success?success:function(){alert('success');};
		error=error?error:this.defaultError;

		var errors=(function(data){
			if (!data.searchFragment){
			return [
				['searchFragment', "No search term supplied"]
			];
			}
			return [];
		})(data);
		
		
		if (errors.length>0){
			success({status:-1, messages:errors, data:{}});
			return;
		}

		$.ajax({
				url: '/user/search',
				type: 'get',
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