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

defaultError:function(){
	var modelName=this._fullName
	return function(){
		console.log('Model Error ('+modelName+')');
	}
},

getStorage:function(){
	var self=qtools.getDottedPath(window, this.fullName);
	if (!self.storage){self.storage={};}
	return self.storage;
},

keep:function(name, inData){
	this.getStorage()[name]=inData;
},

get:function(name){
	return this.getStorage()[name];
},

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