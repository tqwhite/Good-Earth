steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.Session
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend session services.
 */
GoodEarthStore.Models.Base.extend('GoodEarthStore.Models.Session',
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
				url: '/session/start',
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
				url: '/session/login',
				type: 'post',
				dataType: 'json',
				data: {data:data},
				success: success,
				error: error,
				fixture: false
			});
	},

	logout:function(data, success, error){

		success=success?success:function(){alert('success');};
		error=error?error:function(){alert('error');};

		$.ajax({
				url: '/session/logout',
				type: 'post',
				dataType: 'json',
				data: {data:data},
				success: success,
				error: error,
				fixture: false
			});
	},

	saveReferenceLocation:function(){
		this.referenceLocation=qtools.passByValue(window.location);
	},

	getControllerNameFromReference:function(){
		var hash, hash;
		if (this.referenceLocation){
			hash=this.referenceLocation.hashCode;
		}
		else{
			hash=window.location.hash;
		}

		switch (hash){
			default:
				controllerName='register';
				break;
			case 'new':
				controllerName='register';
				break;
			case 'login':
				controllerName='login';
				break;
		}

		return controllerName;

	}

},
/* @Prototype */
{});

})