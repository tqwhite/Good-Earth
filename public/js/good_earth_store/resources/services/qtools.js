qtools = {

viewBreakpointHelper:function(args){
//qtools.viewBreakpointHelper();
	return true; //it's hard to get inside view scripts. calling this with a breakpoint set here lets one step into the view easily
},

htmlEntities:function(inString){
	return inString.replace(/</g,"&lt;").replace(/>/g,"&gt;");//add more later
},

passByValue:function(inObj){
	var outObj;
	if (typeof(inObj)!='object' || inObj===null){
		return inObj; //don't want to turn strings, etc into objects and they are a priori passed by value
	}
	else if (inObj instanceof Array){
		outObj=[];
	}
	else{
		outObj={};
	}
	$.extend(true, outObj, inObj);
	return outObj;
},

extendToNew:function(receivingObj, additionalObj){
	var tmpObj=qtools.passByValue(receivingObj);
	return $.extend(tmpObj, additionalObj);

},

displayRealms:function(realmGroup){
var outString, i;
outString='';
	for (i=0; i<realmGroup.length; i++){
	outString+='<div class=\'realmClass\' style=\'margin-top:10px;font-weight:bold;font-size:110%;\'>'+realmGroup[i].title+'</div>';
		outString+=this.displayStandards(0, realmGroup[i].standards);
	}
return outString;

},

displayStandards:function(indentLevel, standardsGroup){
var outString, i, indentSize;
outString='';
indentSize=20; //px
for (i=0; i<standardsGroup.length; i++){
	outString+='<div class=\'standardsLevel_\''+indentLevel+' style=\'margin-left:'+indentLevel*indentSize+'px\'>'+standardsGroup[i].title+'</div>';

	if (standardsGroup[i].activities){
		outString+=this.displayActivities(indentLevel+1, standardsGroup[i].activities);
	}

	if (standardsGroup[i].standards){
		outString+=this.displayStandards(indentLevel+1, standardsGroup[i].standards);
	}
}
return outString;
},

displayActivities:function(indentLevel, standardsGroup){
var outString, i, indentSize;
outString='';
indentSize=20; //px
for (i=0; i<standardsGroup.length; i++){
	outString+='<div class=\'standardsLevel_\''+indentLevel+' style=\'color:brown;font-style:italic;margin-left:'+indentLevel*indentSize+'px\'>'+standardsGroup[i].title+'</div>';
	if (standardsGroup[i].standards){
		outString+=this.displayStandards(indentLevel+1, standardsGroup[i].standards);
	}
}
return outString;
},

mergeRecursive:function(obj1, obj2) {
//thanks Markus (http://stackoverflow.com/questions/171251/how-can-i-merge-properties-of-two-javascript-objects-dynamically/383245#383245)

  for (var p in obj2) {
    try {
      // Property in destination object set; update its value.
      if ( obj2[p].constructor==Object ) {
        obj1[p] = MergeRecursive(obj1[p], obj2[p]);

      } else {
        obj1[p] = obj2[p];

      }

    } catch(e) {
      // Property in destination object not set; create it and set its value.
      obj1[p] = obj2[p];

    }
  }

  return obj1;
},

fadeOut:function(){
$('#dddd').fadeOut(5000)
},

sizeOf: function(incoming) {
        if (typeof incoming == 'object') {
            count = 0;
            for (x in incoming) {
                count++;
            }
            return count;
        }
        else {
            return incoming.length
        }
    },

exists:function(arg) { return typeof(arg)!='undefined';},

isEmpty: function(arg) {
var objectEmptyFlag;

if (typeof(arg)=='function'){
	return false;
}

if (typeof(arg)=='undefined'){
	return true;
}

if (typeof(arg)=='object'){
	objectEmptyFlag=true; //assume it's empty until further notice
	for(var item in arg){
		objectEmptyFlag=false; //found one
		}
}
else{
	objectEmptyFlag=false; //can't be full if it's not an object
}

	return (objectEmptyFlag || arg=='' || arg.length==0)
},

isNotEmpty:function(arg){
	return !this.isEmpty(arg);
},

length:function(inObj){
	var type=qtools.toType(inObj),
		outLen;
	switch(type){
		case 'array':
			outLen=inObj.length;
			break;
		case 'object':
			outLen=0;
			for (var i in inObj){
				outLen++;
			}
			break;
		case 'undefined':
		case 'null':
			outLen=null;
			break;
		default:
			outLen=inObj.length
			break;
	}
	return outLen;
},

timeoutProxy: function(func, milliseconds, scope, args) {
var timeoutId;
if ($.browser.msie == true) {
		timeoutId=setTimeout(function() {
		func(scope, args)
		}, milliseconds);
	}
	else {
		timeoutId=setTimeout(func, milliseconds, scope, args);
	}
return timeoutId;
},

tmpRep:function(template, replaceObject){
    return this.templateReplace({template:template, replaceObject:replaceObject});
    },

templateReplaceArray:function(args){
	var outString='';
	for (var i=0, len=args.replaceArray.length; i<len; i++){
		args.replaceObject=args.replaceArray[i];
		args.indexNumber=i;
		outString+=this.templateReplace(args);
	}
	return outString;
},

templateReplace:function(args){
	var template=args.template,
		replaceObject=args.replaceObject,
		leaveUnmatchedTagsIntact=args.leaveUnmatchedTagsIntact,
		transformations=args.transformations,

		outString='',
		localReplaceObject={};


	$.extend(this, {localReplaceObject:qtools.passByValue(replaceObject)}, args); //clones replaceObject
	this.localReplaceObject['leaveUnmatchedTagsIntact']=leaveUnmatchedTagsIntact?leaveUnmatchedTagsIntact:false;
	this.localReplaceObject['indexNumber']=args.indexNumber?args.indexNumber:0;

	if (qtools.isNotEmpty(transformations)){
		for (var fieldName in transformations){
			this.localReplaceObject[fieldName]=transformations[fieldName](replaceObject);
		}
	}

	outString=template.replace(/<!([a-zA-Z0-9]+)!>/g, this.evaluatorFunction);

//	outString='ttt'+outString+'qqq';
	return outString;
},

evaluatorFunction:function(matchedString, propertyName){
	/*
	* This works as a callback from replace() in this.templateReplace. Looks up the
	* appropriate property in an object and returns it to replace a tag.
	*
	* Tags are the form <!replaceName!>.
	* */
	var outString;
	if (typeof(this.qtools)!='undefined'){
		outString=this.qtools.getDottedPath(this.qtools.localReplaceObject, propertyName); //in this callback, 'this' is window object
	}
	else{
		outString=window.qtools.getDottedPath(window.qtools.localReplaceObject, propertyName); //IE 9 doesn't scope this to the same place, 'window' works
	}
	if (typeof outString !='undefined'){
		//console.log('propertyName='+propertyName+'==='+outString);
		return outString;
	}
	else{
		if (this.qtools.localReplaceObject['leaveUnmatchedTagsIntact']){
			return '<!'+propertyName+'!>';
		}
		else{
			return '';
		}
	}
},

navSpaceIdFunction:function(options){
	var suffixString;
	switch (options.type){
	case 'workspaceControl':
		suffixString='';
		break;
	case 'workspacePallette':
		suffixString=options.viewspaceIdPathSeparator+options.type;
		break;
	}
					return options.viewspaceIdPrefix+
						options.toolkitPath+
						options.viewspaceIdPathSeparator+
						options.workspacePath+
						suffixString
},

showLog:function(){
	logObj=$('#log');
	var a={
		position:'absolute',
		'z-index':'9000',
		cursor:'pointer',
		border:'2pt solid gray',
		overflow:'visible'
	};
	logObj.css(a);
	logObj.draggable();
	logObj.resizable();
	logObj.show();
	logObj.bind("dblclick", function(){
		$(this).hide();
	});

	var b={
		overflow:'scroll'
		};
	$('#logBody').css(b);
},

hideLog:function(){
$('#log').hide();
},

addToLog:function(inString){
$('#logBody').prepend(inString+'<p/>');
},

clearLog:function(){
$('#logBody').html('Log<hr/>');
var a={
	height:'200px',
	width:'400px'
};
$('#log').css(a);

},

newGuid:function() {
	//thanks 'broofa': http://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid-in-javascript
	 return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(
		/[xy]/g,
		function(c) {
			var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
			return v.toString(16);
		})
		.toLowerCase();
},

consoleMessage:function(message){
//var code=arguments.callee.caller.toString(); //gets the source code of the calling item
//var code=arguments.callee.caller.name; //gets teh name of the calling function, sadly, null if object method
		message='** '+message;
		if(true || GLOBALS.config.messageLevel == CONSTANTS.allowDebugMessages){
			if ( window.console && console.log ) {
				console.log(message);
			} else if ( window.opera && window.opera.postError ) {
				opera.postError(message);
			}
		}

	},

applyAttributesFromList:function(divObj, list){
		if (qtools.isNotEmpty(list)) {
			for (var key in list) {
				if (list.hasOwnProperty(key)) {
					switch(key.toLowerCase()){
						case 'class':
							divObj.addClass(list[key]);
							break;
						case 'setclass':
							divObj.attr('class', list[key]);
							break;
						case 'addclass':
							divObj.addClass(list[key]);
							break;
						default:
							divObj.attr(key, list[key]);
							break;
					}
				}
			}
		}
},

applyStylesFromList:function(divObj, list){
		if (!qtools.isEmpty(list)) {
			for (var key in list) {
				if (list.hasOwnProperty(key)) {
					var style=key.replace(/([A-Z])/, '-$1'); //converts camelCase to camel-case for css
					style=style.toLowerCase();
					switch(style){
						default:
							divObj.css(key, list[key]);
							break;
					}
				}
			}
		}
},

repeateableGuid:function(inString){
	var md5, outString, start, len, end;

	md5=$.md5(inString);
	outString='';
	end=0;

	start=end; len=8; end=start+len;
	outString+=md5.substring(start, end)+'-';

	start=end; len=4; end=start+len;
	outString+=md5.substring(start, end)+'-';

	start=end; len=4; end=start+len;
	outString+=md5.substring(start, end)+'-';

	start=end; len=4; end=start+len;
	outString+=md5.substring(start, end)+'-';

	start=end; len=12; end=start+len;
	outString+=md5.substring(start, end);

	return outString.toLowerCase();

},

mssqlFieldToDate:function(inString){
var outDate;
try {
	outDate=eval('new '+inString.replace(/\//gi, ''))
	}
	catch (err){
	outDate=null;
	}
return outDate;
},

getByProperty:function(inArray, propertyName, propertyValue){
	var len=inArray.length, inx=0;
	for (inx=0; inx<len; inx++){
		if (inArray[inx][propertyName]==propertyValue){ return inArray[inx];	}
	}
	return null;
},

notifyRestore:function(message, domObj, delay){
	delay=delay?delay:5000;
	var oldMessage=domObj.html();

	domObj.html(message);

	qtools.timeoutProxy(
			function(scope, args){
				args.domObj.hide();
				args.domObj.html(args.oldMessage);
				args.domObj.fadeTo(2000, 1);
			},
			delay,
			this,
			{oldMessage:oldMessage,
			domObj:domObj}
		);
},

byObjectProperty:function(fieldName, transformer){
		//called: resultArray=someArray.sort(qtools.byObjectProperty('somePropertyName'));
		//based on closure of fieldName
		return fullNameSort=function(a,b){
			var localFieldName=fieldName, //for debug
				localTransformer=transformer; //for debug
			var aa=qtools.getDottedPath(a, fieldName),
				bb=qtools.getDottedPath(b, fieldName);

			if (typeof(transformer)=='function'){
				aa=transformer(aa);
				bb=transformer(bb);
			}
			else if (transformer){
				switch(transformer){
					case 'caseInsensitive':
							aa=aa.toLowerCase();
							bb=bb.toLowerCase();
						break;
					default:
						qtools.consoleMessage('qtools.byObjectProperty says, No such transformer as: '+transformer);
						break;
				}
			}

			if (!bb && !aa){ return 0;}
			if (!bb){ return -1;}
			if (!aa){ return 1;}

			if (aa>bb){ return 1;}
			if (aa<bb){ return -1;}
			return 0;
		}
	},

bindScope:function(scope, functionName, args){
	var appliedArgs=$.makeArray(arguments);
		appliedArgs.splice(0, 2);
	return function(){
		var effectiveArgs=appliedArgs.concat($.makeArray(arguments));
		if (typeof(functionName)=='function'){
			functionName.apply(scope, effectiveArgs);
		}
		else{
			scope[functionName].apply(scope, effectiveArgs);
		}

	};
},

multiAjax:function(params){

	/*
		var controlObj={
			calls:{
				terms:{
					ajaxFunction:expressbook.models.terms.getList,
					argData:{refId:this.District.RefId}
				},
				gradeLevels:{
					ajaxFunction:expressbook.models.gradeLevels.getList,
					argData:{refId:this.District.RefId}
				}
			},
			success:this.callback('receiveSupportData'),
			error:function(){alert('the server broke down');},
			stripWrappers:true

		};*/
	var calls=params.calls,
		count={},
		outObj={};

	this.success=function(name, inData){
		outObj[name]=params.stripWrappers?inData.data:inData;
		delete count[name];
		if (qtools.length(count)<1){
			params.success(outObj);
		}

	};

	this.error=function(name, inData){
		if (typeof(params.error)=='function'){
			params.error(name);
		}
		else{
			qtools.consoleMessage('qtools.multiAjax says, The server stopped working. The call was in regards to '+name+'. Please tell tech support. Trying again is good, too.');
		}
	};

	for (var i in calls){
		count[i]=true;
	}

	for (var i in calls){
		calls[i].ajaxFunction(calls[i].argData, qtools.bindScope(this, 'success', i), qtools.bindScope(this, 'error', i));
	};

},

indexObjFromPath:function(inObj, fieldName){
	var outObj={},
	value='';

	if (typeof(inObj)!='undefined' && inObj && inObj[fieldName]){
		outObj[inObj[fieldName]]=inObj;
		return outObj;
	}

	for (var i in inObj){
		value=this.getDottedPath(inObj[i], fieldName);
		outObj[value]=inObj[i];
	}
	return outObj;
},

getDottedPath:function(baseObj, subPathString){
	var target=baseObj,
		elements;
		this.getDottedPathLastProgressiveString='';

	var elements=subPathString.split('.');

	if (!subPathString){ return baseObj;}

	if (elements.length<2){
		return baseObj[subPathString];
	}
	else{
		for (var i=0, len=elements.length; i<len; i++){
			if (elements[i]){ //mainly eliminates trailing periods but would also eliminate double periods
				target=target[elements[i]];

				this.getDottedPathLastProgressiveString+=elements[i]+'.';
				if (typeof(target)=='undefined'){
					qtools.consoleMessage('bad path='+this.getDottedPathLastProgressiveString);
					return null;
				}
			}
		}
	}
	return target;
},

initIfNeeded:function(baseObj, subPathString, newObj){
	var target=this.getDottedPath(baseObj, subPathString);
	if (!target){this.addBranch(baseObj, subPathString, newObj);}
},

ajaxError:function(inData){
	alert("The server is broken. Try again and call tech support if it still doesn't work.");
},

htmlHeaderError:function(inData){
	alert("The server did not respond correctly. Try again and call tech support if it still doesn't work.\nWrite this down just in case: "+inData.status);
},

addBranch:function(inObj, subPathString, propertyData){
	//notice that this function overwrites properties that are named in subPathString and are not objects
	var elements,
		target=inObj;

	elements=subPathString.split('.');

	if (elements.length<2){
		if (!inObj[subPathString]){inObj[subPathString]={}; }
		inObj[subPathString]=propertyData;
	}
	else{
		for (var i=0, len=elements.length; i<len-1; i++){
			if (!target[elements[i]] || typeof(target[elements[i]])!='object'){target[elements[i]]={}; }
			target=target[elements[i]];
		}

		target[elements[len-1]]=propertyData;

	}
},

explodeToIndexObj:function(args, parentObjectList){
	var inObj=args.sourceObj,
		fieldName=args.indexFieldName,
		inclusionFieldName=args.validationFlagName?args.validationFlagName:'includeAll',
		indexablePropertyString=args.indexablePropertyNames?args.indexablePropertyNames.join():'',
		outObj={},
		newParentList,
		currObj,
		keepNonDbObjects=args.keepNonDbObjects?args.keepNonDbObjects:false;;



	for (var i in inObj){
		if (inObj[i] && typeof(inObj[i])=='object'){ //turns out that typeof(null)=='object'
			currObj=inObj[i];

			if (!parentObjectList || typeof(parentObjectList.length)=='undefined'){
				parentObjectList=[{node:inObj, index:inObj[fieldName]?inObj[fieldName]:'root'}];
			}


				newParentList=$.extend([], parentObjectList);

				newParentList.push({node:currObj, index:i});


			var desiredProperty=(!indexablePropertyString || indexablePropertyString.match(i) || !isNaN(i)),
				validProperty=(currObj[inclusionFieldName] || inclusionFieldName=='includeAll'),
				isArray=currObj.length,
				includeThis=(desiredProperty && (validProperty || isArray)),
				inx;



			if (includeThis){

				qtools.arrayInx=qtools.arrayInx?qtools.arrayInx:0;
				if (keepNonDbObjects && !currObj[fieldName]){ inx='_missingIndexProperty_'+qtools.arrayInx; qtools.arrayInx++; }
				else {inx=currObj[fieldName];}

				outObj[inx]={}
					outObj[inx].target=currObj;
					outObj[inx].parentObjectList=parentObjectList;
					outObj[inx].finalIndex=i;
				outObj=$.extend(outObj, this.explodeToIndexObj($.extend(args, {sourceObj:currObj}), newParentList));
			}


		}

	}
	if (!keepNonDbObjects) {delete outObj['undefined'];} //array elements don't have indexableProperty and get stuffed into 'undefined'
	return outObj;
},

getParentOfIndexObj:function(indexEntry){
		var pathString='',
			parentList=indexEntry.parentObjectList,
			retrievedObject;

		for (var i in parentList){
			if (parentList[i].index!='root'){
				pathString+=parentList[i].index+".";
			}
		}

		//fyi, this points to the target: pathString+='.'+indexEntry.finalIndex;
		retrievedObject=qtools.getDottedPath(indexEntry.parentObjectList[0].node, pathString);
		return retrievedObject;
},

debugExplodedIndexList:function(counter){

	//This routine is used from the command line only.
	//it calculates the dotted path from the root
	//down to the indexed object and displays it.
	//it also displays the index entry and other things.
	//Note: the path calculation goes down to the parent of
	//the indexed object unless the indexed object
	//is an array. In that case its index is prefaced with
	// _missingIndexProperty_.
	//Here's the anomaly. It creates an entry for each legitimately
	//indexable element inside the array and THE DOTTED PATH
	//points to that indexable object.
	//This doesn't matter since the array has no index and isn't used
	//but it's confusing. It was too hard to program around it.

	console.clear();

	counter=counter?counter:10;

	for (var inx in A_editItemIndex){ //yes, it's a global, but this function is probably never going to be used again
		var outString='',
			indexEntry=A_editItemIndex[inx],
			parentList=indexEntry.parentObjectList;

		if (inx.match('_non')){ continue; }

		console.log('Index='+inx);

		for (var i in parentList){
			if (parentList[i].index!='root'){
				outString+=parentList[i].index+".";
			}
		}
		outString+='.'+A_editItemIndex[inx].finalIndex;


		var retrievedObject=qtools.getDottedPath(indexEntry.parentObjectList[0].node, outString);
		if (outString){console.log(outString);}
		else{console.log('empty path');}

		if (retrievedObject){console.dir(retrievedObject);}
		else{console.log('no object');}

		if (true){console.dir(indexEntry);}

		console.log('=======================');
			if (counter==0){
				break;
			}
			else{
				counter--;
			}
	};
},

characterizeIndexObject:function(inObj){
	var tmpObj={};
	for (var i in inObj){
		if (typeof (tmpObj[inObj[i]['$type']])=='undefined'){tmpObj[inObj[i]['$type']]=0;}
		else{ tmpObj[inObj[i]['$type']]++;}
	}
	if (typeof(console)!='undefined' && typeof(console.clear)!='undefined' && typeof(console.dir)!='undefined'){
		console.clear();
		console.dir(tmpObj);
	}
},

toType:function(obj) {
	if (obj===null){return 'null';}
	else if (typeof(obj)=='undefined'){return 'undefined';}
		else{return ({}).toString.call(obj).match(/\s([a-z|A-Z]+)/)[1].toLowerCase();}
},	//thanks: http://javascriptweblog.wordpress.com/2011/08/08/fixing-the-javascript-typeof-operator/

pruneMixedTree:function(inObj, args){ //by mixed, I mean it has both array and object properties at any level
	var decider,
		simpleDelete=function(inObj){
		if (inObj && inObj.Delete===true){
			return true;}
		else{
			return false;
		}};
	args=args?args:{};
	if (qtools.toType(args.decider)=='string'){
		switch(args.decider){
			case simpleDeleteFlag:
				decider=simpleDelete;
				break;

			default:
				alert('qtools.pruneMixedTree says, There is no decider named '+args.decider);
				break;
		}
	}
	else if (qtools.toType(args.decider)=='function'){
		decider=args.decider;
	}
	else{
		decider=simpleDelete;
	}


	if (qtools.toType(inObj)=='array'){
		for (var i=0, len=inObj.length; i<len; i++){
			if (qtools.toType(inObj)!='null'&& decider(inObj[i])){
//AAA.push({deleted:inObj[i]});
//console.log('pruneMixedTree says, deleted '+i+' '+qtools.toType(inObj));
				delete inObj[i];
			}
		}
		qtools.consolidateArray(inObj);

		for (var i=0, len=inObj.length; i<len; i++){
			if (qtools.toType(inObj)!='null' ){
				qtools.pruneMixedTree(inObj[i], args);
			}

		}
	}
	else if (qtools.toType(inObj)=='object'){
		for (var i in inObj){

			if (qtools.toType(inObj)!='null' && decider(inObj[i])){
//AAA.push({deleted:inObj[i]});
//qtools.consoleMessage('pruneMixedTree says, deleted '+i+' '+qtools.toType(inObj));
				delete inObj[i];
			}
		}


		for (var i in inObj){

			if (qtools.toType(inObj)!='null' && typeof(inObj[i])=='object'){ //want either array and object
				qtools.pruneMixedTree(inObj[i], args);
			}
		}

	}
	else{
		return; //can't do anything with other data types
	}



},

consolidateArray:function(inArray){
	var notNullItems=[], inx=0;
	if (qtools.toType(inArray)!='array'){ return;}

	for (var i=0, len=inArray.length; i<len; i++){
		if (qtools.toType(inArray[i])!='undefined'){
			notNullItems.push(inArray[i]);
		}
	}

	for (var i=0, len=notNullItems.length; i<len; i++){
		inArray[inx]=notNullItems[i];
		inx++;
	}
	inArray.length=notNullItems.length;
},

validateProperties:function(args){
	var name, type, notEmpty, element, importance,
		outList=[],
		outMessage='',
		inObj=args.targetObject,
		propList=args.propList,
		source=args.source,
		importance=args.importance,
		showAlertFlag=false, //should be pointing to a global but I don't have one today
		targetScope=args.targetScope?args.targetScope:'';

	if (typeof(qtools)=='undefined' && showAlertFlag){
		alert("qtools.validateProperties did not get a source object");
		console.trace();
	}

	if (qtools.toType(targetScope)!='object'){
		targetScope=false;
	}

	source=source?source+' (via qtools.checkProperties) ':'qtools.checkProperties ';

	for (var i=0, len=propList.length; i<len; i++){
		name=propList[i].name;
		importance=propList[i].importance;
		requiredType=propList[i].requiredType;
		assertNotEmptyFlag=propList[i].assertNotEmptyFlag;
		element=inObj[name];

		if (importance!='optional' && typeof(element)=='undefined'){
			outList.push(source+' says, '+name+' is missing');
		}
		if (requiredType && qtools.toType(element)==requiredType){
			outList.push(source+' says, '+name+' is not of type '+requiredType);
		}
		if (assertNotEmptyFlag && qtools.isEmpty(element)){
			outList.push(source+' says, '+name+' cannot be empty');
		}

		if (targetScope){
			targetScope[name]=inObj[name];
		}
	}

	for (var i=0, len=outList.length; i<len; i++){
		outMessage+=outList[i]+'\n';
	}
	if (showAlertFlag && outMessage){
		alert(outMessage);
	}
	else if(outMessage){
		qtools.consoleMessage(outMessage);
	}

},

dumpActivities:function(inObj){
	var outString='';
	inObj=inObj?inObj:A_activities;
	for (var i in A_activities){
	outString+=A_activities[i].Standard.RefId.substr(0, 4)+' --- '+A_activities[i].Title+' --- '+A_activities[i].RefId+'\r'

}
console.log('A_activityListString=\r'+outString);
A_activityListString=outString;

},

assembleDisplayParameters:function(args){
	qtools.validateProperties({
		targetObject:args,
		propList:[
			{name:'nameArray'},
			{name:'scope', importance:'optional'}

		], source:this.moduleName, templateToLog:false });

	var nameArray=args.nameArray,
		scope=args.scope?args.scope:this,
		componentDivIds={}
		divPrefix=scope.divPrefix?scope.divPrefix:this.divPrefix('assembleDisplayParameters');

	for (var i=0, len=nameArray.length; i<len; i++){
		if (typeof(nameArray[i])=='string'){
			componentDivIds[nameArray[i]]=divPrefix+nameArray[i];
		}
		else{
			componentDivIds[nameArray[i].name]={
				name:nameArray[i].name,
				divId:divPrefix+nameArray[i].name,
				handler:scope.callback(nameArray[i].handlerName)
			};

			if (nameArray[i].targetDivId){
				componentDivIds[nameArray[i].name].targetDivId=divPrefix+nameArray[i].targetDivId;
			}
		}
	}
	return componentDivIds;
},

divPrefix:function(inString, nonRandom){
    var outString, tmpArray, tmpString;

    tmpArray=inString.split('_');
    tmpString=tmpArray[tmpArray.length-2]+'_'+tmpArray[tmpArray.length-1];

   	outString=inString.replace(tmpString, '')
   		.replace(/(_\w)([a-zA-Z0-9]*)/g, '$1')
    	.replace(/^([a-zA-Z0-9])([a-zA-Z0-9]*_)(.*)/, '$1_$3')
    	.replace(/_/g, '')+'_'+tmpString+'_';

    	if (!nonRandom){
    		tmpString=Math.floor(Math.random()*9999999);
    		outString=outString+tmpString+'_';
    	}

    return outString;

},

dateToday:function(format){
	var x=new Date();
	return (x.getMonth()+1)+'/'+x.getDate()+'/'+x.getFullYear();
},

byObjectProperty:function(fieldName, transformer){
		//called: resultArray=someArray.sort(qtools.byObjectProperty('somePropertyName'));
		//based on closure of fieldName
		return fullNameSort=function(a,b){
			var localFieldName=fieldName, //for debug
				localTransformer=transformer; //for debug

			if (typeof(fieldName)=='function'){
				var aa=a,
					bb=b;
				var transformer=fieldName;
			}
			else{
				var aa=qtools.getDottedPath(a, fieldName),
					bb=qtools.getDottedPath(b, fieldName);
				}

			if (typeof(transformer)=='function'){
				aa=transformer(aa);
				bb=transformer(bb);
			}
			else if (transformer){
				switch(transformer){
					case 'caseInsensitive':
							aa=aa.toLowerCase();
							bb=bb.toLowerCase();
						break;
					default:
						qtools.consoleMessage('qtools.byObjectProperty says, No such transformer as: '+transformer);
						break;
				}
			}

			if (!bb && !aa){ return 0;}
			if (!bb){ return -1;}
			if (!aa){ return 1;}

			if (aa>bb){ return 1;}
			if (aa<bb){ return -1;}
			return 0;
		}
	}

}