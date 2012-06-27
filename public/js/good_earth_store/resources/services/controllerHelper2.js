controllerHelper = {


centerInWindow:function(domObj){
var bHeight, bWidth, myHeight, myWidth, top, left;
	bWidth=$(window).width();
	bHeight=$(window).height();
	myWidth=domObj.width();
	myHeight=domObj.height();
	
	top=Math.floor(bHeight/2)-Math.floor(myHeight/2);
	left=Math.floor(bWidth/2)-Math.floor(myWidth/2);

	var newCss;
	newCss={
	position:'absolute',
	top:top,
	left:left
	};
	domObj.css(newCss);
	
},
	newCleanUpObject:function(domObj, employer){
		//these are convenience properties since most cases of deferred execution refer to one of these
		//if the view doesn't need them, the author need not send them
			if (!employer){employer={};}
			if (!domObj){domObj={};}
		
		return {
			employer:employer,
			domObj:domObj,
			cleanupFunctions:[],
			
			getDivId:function(){
				return this.domObj[0].id;
			},
			
			addCleanupFunction:function(func, values){
				if (!values){values={};}
				this.cleanupFunctions.push({func:func, values:values});
			},
	
			executeCleanupFunction:function(){
				//this must be placed AFTER the HTML from the view have been inserted into the DOM
				if (this.cleanupFunctions.length>0){
					$.each(this.cleanupFunctions, function(inx, item, values){
						item.func(item.values);
					});
				}
			
			}
		}
	},
	
	hideLoadingIndicator:function(employer){
		$('#'+employer.element[0].id+' .loadingIndicator').hide();
	},
	
	updateToolTip:function(selector, text){
		$(selector).qtip({content:{text:text}, style:GLOBAL.qtip_styles['GLOBAL.editListingButtonStyle']});
	},
	
	addStandardToolTip:function(selector, options){

		if (qtools.isEmpty(options)){options={};}
		if (qtools.isEmpty(options) || qtools.isEmpty(options.delay)){options.delay=1000;} //NOTE: delay option is invalidated if options.show is specified
		if (qtools.isEmpty(options) || qtools.isEmpty(options.style)){}
		if (qtools.isEmpty(options) || qtools.isEmpty(options.position)){
			options.position={
				corner: {
					target: 'bottomMiddle',
					tooltip: 'topLeft'
				}
			};
		}
		if (qtools.isEmpty(options) || qtools.isEmpty(options.show)){
			options.show={
			delay: options.delay,
			solo:true
			};
		}
		
	    $(selector).qtip({
    		style:options.style,
			position: options.position,
			show:options.show
		 });
	},
	
	copyNavBits:function(employer, navBits){
		$.each(navBits, function(inx, item){
			employer['a']=item;
		});
	},
	
	getDivId:function(idString){
		return this.getKillCode(idString);
	},
	
	getKillCode:function(idString){
		var d;
		d=new Date();
		return idString+this.sep+d.getTime();
	},
	
	extractParms: function(argString, schema) {
        rawParms = argString.split('/');
        if (typeof schema == 'undefined') {
            return rawParms; //let the receiver sort out the positions
        }
        else {
            output = new Object;
            viewSchema = schema;
            count = qtools.sizeOf(schema);
            for (var i = 0; i < count; i++) {
                output[schema[i]] = rawParms[i];
            }
            return output;
        }
    },
    
    divIdDISCARD:function(employer){
		var toolPart;
		var sep=CONSTANT.VIEWSPACEPATHSEPARATOR;
		if (typeof employer.toolName=='undefined'){
			toolPart='';
		}
		else{
			toolPart=sep+employer.toolName;
		}
	
		outString=employer.toolkitName+sep+employer.workspaceName+toolPart;
		return outString;
    },
    
    formatNavBits:function(inObj){
		return new Object({
			toolkit:inObj.toolkit,
			workspace:inObj.workspace,
			controller:inObj.controller,
			tool:inObj.tool,
			args:inObj.args
		
		});
    },
    
    navBitsToPath:function(navBits){
    
		var sep='/';
		var argPart, toolPart, controllerPart;
		if (typeof navBits.controller=='undefined'){
			controllerPart='';
		}
		else{
			controllerPart=sep+navBits.controller;
		}
		
		if (typeof navBits.tool=='undefined' || controllerPart==''){
			toolPart='';
		}
		else{
			toolPart=sep+navBits.tool;
		}
		
		if (navBits.args!='/' || toolPart=='' || controllerPart==''){
			argPart='';
		}
		else{
			argPart=navBits.tool;
		}
	
		outString=navBits.toolkit+sep+navBits.workspace+controllerPart+toolPart+argPart;
		return outString;
    
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
    
getFilterDecider:function(rosmat, currentUserFilterRefId, deciderAccessFunction, deciderDomObj){
		var userFilterList, currentUserListItem, parameters, filterControllerName;
		
		userFilterList=rosmat.JsonStorage.UserFilterList;
	
		if (qtools.isNotEmpty(userFilterList) && currentUserFilterRefId){
			currentUserListItem=repository.getByRefId(userFilterList, currentUserFilterRefId);	
		}
		
		if (qtools.isNotEmpty(currentUserListItem)){
			filterControllerName=currentUserListItem.studentFilterTypeRefId;
			parameters=currentUserListItem.parameters;
		}
		else{
			filterControllerName='expressbook_tools_entryforms_rosmat_filtr_filter_show_all';
			parameters={};
		}

	
		deciderDomObj[filterControllerName]({
			request:'getDecider', 
			deciderAccessFunction:deciderAccessFunction, 
			rosmat:rosmat,
			filterSpecParameters:parameters
		});
		
		return true;
},

dirtyObject:function(employer){

	this.participants={};
	
	this.register=function(registration){
		var id=qtools.newGuid(),
			self=this;
			
		if (registration){
				registration.dirtyFlag=false;
				this.participants[id]=$.extend(registration, {dirtyFlag:false, dirtyStack:[]});
				
				var customAccessFunction=function(control, parameter){
					//relies on closure of id, self
					var thisId=id,
						scope=self
						registration=self.participants[thisId],
						label='';
					switch (control){
						case 'setAllClean':
							scope.setAllClean(thisId, parameter);
							break;
						case 'killChanges':
							scope.setAllClean(thisId, parameter);
							break;
						case 'setIsDirty':
							scope.setIsDirty(thisId, parameter);
							break;
						case 'setIsNotDirty':
							scope.setIsNotDirty(thisId);
							break;
						case 'areWeDirty':
							return scope.areWeDirty(thisId);
							break;
						case 'isMeDirty':
							return scope.isMeDirty(thisId);
							break;
						case 'ping':
							label=registration.label?registration.label:'no label supplied';
							qtools.consoleMessage('dirtyObject.customAccessFunction.ping() says, id='+thisId+" is labeled "+label);
							break;
						case 'isValid':
							return scope.validateComponents();
							break;
					}
				}
				
				return customAccessFunction;
			}
			else{
				qtools.consoleMessage('controllerHelper.dirtyObject.register() says, empty registration');
				return false;
			}
	};
	
	this.isMeDirty=function(id){
		return this.participants[id].dirtyFlag;
	};
	
	this.areWeDirty=function(id){
		for (var i in this.participants){
			if (this.participants[i].dirtyFlag){
				return true;	
			}
		}
		return false;
		};
	
	this.setIsDirty=function(id, dirtyBit){
		var registration=this.participants[id];
		if (!registration){
			qtools.consoleMessage('controllerHelper.dirtyObject.register() says, no registration with id='+id);
		}
		else{
			dirtyBit=dirtyBit?dirtyBit:'no dirtyBit provided';
			registration.dirtyStack.push(dirtyBit);
			if (!registration.dirtyFlag || registration.alwaysNotify){ //only send the first time unless 
				this.executeDirtyFunctions(id, dirtyBit);
			}
			registration.dirtyFlag=true;
		}
	};
	
	this.setIsNotDirty=function(id){
		var registration=this.participants[id];
		registration.dirtyFlag=false;
	};
	
	this.executeDirtyFunctions=function(originatingId, dirtyBit){

		for (var i in this.participants){
			if (typeof(this.participants[i].onSetIsDirty)=='function'){
				this.participants[i].onSetIsDirty(originatingId, dirtyBit);
			}
		}
	};
	
	this.validateComponents=function(requestingId){
		var partialResult, result=true;
		for (var i in this.participants){
			if (typeof(this.participants[i].validate)=='function'){
				partialResult=this.participants[i].validate();
				var name=this.participants[i].label;
				result=result&&partialResult;
			}
		}
		return result;
	};

	this.setAllClean=function(){
	
			for (var i in this.participants){
				this.participants[i].dirtyFlag=false;
				this.participants[i].dirtyStack=[];
			}
	};


}

}