steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.LocalStorage
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend local_storage services.
 */
GoodEarthStore.Models.Base.extend('GoodEarthStore.Models.LocalStorage',
/* @Static */
{

//COOKIE LIBRARY ====================================================================

cookieContainer:[],

cookieIndex:function(key){
	return key;
},

_getCookie:function(key){
	if (!this.initialized){this.receiveCookies();} //receiveCookies() does the actual touching of document.cookie

	cookieIndex=this.cookieIndex(key);
	result=this.cookieContainer[cookieIndex];
	return result;
},

getCookieData:function(key){
var result, prev, cookie;
	cookie=this._getCookie(key);
	cookie=cookie?cookie:'';
	return this.wrapDataForReturn({data:cookie.data});
},

updateCookie:function(key, value, meta){
	var cookie, data, meta;
	cookie=this._getCookie(key);

	if(cookie){
		if (typeof(cookie.data.length)=='undefined'){cookie.data=[cookie.data];} //if we are updating a cookie, then it is defined as array, even if it didn't use to be
		meta=$.extend(cookie.meta, meta);
		cookie.data.push(value);
		this.setCookie(key, cookie.data, meta);
	}
	else{
		this.setCookie(key, [value], meta); //if it was updateCookie(), it's an array of values, period
	}

},

setCookie:function(key, value, options){
	var cookieIndex, outValue, jsonValue, saveObj;
	var cookieArray;

	if (!options){options={};}

	if (!this.initialized){this.receiveCookies();}

	cookieIndex=this.cookieIndex(key);

	saveObj={meta:options, data:value};

	jsonValue=JSON.stringify(saveObj)
	outValue=$.rc4EncryptStr(jsonValue, '59dc387e-88cf-4597-abd8-d2eb5f8780fa');

	$.cookie(cookieIndex, outValue, { expires: 7, path: '/'});

	this.cookieContainer=saveObj;
},

receiveCookies:function(pw){
	var cookieArray, scope;

	cookieArray=$.cookie();

	scope=this;
	$.each(cookieArray, function(inx, item){
		var key, cookie, value, goodCookie;
		goodCookie=true;
		try{
			cookie=$.rc4DecryptStr(item.value, '59dc387e-88cf-4597-abd8-d2eb5f8780fa');
			}
		catch(err){
			goodCookie=false;
			}

		if (goodCookie){

		try {
			value=JSON.parse(cookie);
		}
		catch (e) {
			return; //bad cookie does not need processing
		}

		key=item.key;
		if (value.meta && value.meta.expireOnRead==true){
				scope._clearCookie(key);
				scope.cookieContainer[key]={prev:value, data:[], meta:{}};
			}
			else{
				scope.cookieContainer[key]=value;

			}
		}
	});
	this.initialized=true; //let everyone know this happened
},

_clearCookie:function(key){
	$.cookie(key, null, { expires: -1, path: '/'});
},

deleteCookie:function(name){
	this._clearCookie(this.cookieIndex(name));
},

//DICTIONARY ====================================================================

constantList:{
	loginCookieName:'loginName'
},

getConstant:function(key){
	if (this.constantList[key]){
		return this.constantList[key];
	}

	alert("LocalStorage.getConstant says, no such thing as "+key);
}

},
/* @Prototype */
{});

})