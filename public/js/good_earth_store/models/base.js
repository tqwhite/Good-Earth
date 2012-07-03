steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.base
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend base services.
 */
$.Model('GoodEarthStore.Models.Base',
/* @Static */
{
wrapDataForReturn:function(args, passByReference){
	var outObj, fieldName;

	outObj={};

	fieldName='data';
		if (qtools.isNotEmpty(args[fieldName])){
			outObj[fieldName]=args[fieldName];

			if (passByReference=='passByReference'){
				outObj[fieldName]=args[fieldName];
				outObj['comment']='passByReference==true';
			}
			else{
				outObj[fieldName]=qtools.passByValue(args[fieldName]);
				outObj['comment']='passByReference==false; passing by value';
			}

			outObj['status']=this.goodData;

		}
		else{
			outObj.status=this.noObject;
		}
	fieldName='container';
		if (qtools.isNotEmpty(args[fieldName])){
			outObj[fieldName]=args[fieldName];
		}
	fieldName='status';
		if (qtools.isNotEmpty(args[fieldName])){
			outObj[fieldName]=args[fieldName];
		}
	return outObj;
	}
},
/* @Prototype */
{});

})