viewHelper2=function(){

this.moduleName='viewHelper2';

	qtools.initIfNeeded(GLOBALS, 'viewHelpers', []);
	GLOBALS.viewHelpers.push(this);


this.makeSelectTag=function(args){

	qtools.validateProperties({
		targetObject:args,
		propList:[
			{name:'selectVarName'},
			{name:'sourceObj'},
			{name:'valuePropertyName'},
			{name:'labelPropertyName'},
			{name:'selectClassName', importance:'optional'},
			{name:'optionClassName', importance:'optional'},
			{name:'firstItemLabel', importance:'optional'},
			{name:'firstItemValue', importance:'optional'}
			
		], source:this.moduleName, templateToLog:false });
		
	var classString='',
		optionClassString='',
		firstItemString='',
		sourceObj=args.sourceObj;
		
	if (args.selectClassName){ classString=" class='"+args.selectClassName+"'"; }
	if (args.optionClassName){ optionClassString=" class='"+args.optionClassName+"'"; }
	
	if (args.firstItemLabel){ 
		if (typeof(args.firstItemValue)=='undefined'){args.firstItemValue='';}
		firstItemString="<option value="+args.firstItemValue+">"+args.firstItemLabel+"</option>";
	}
	
	var userString="<select name='"+args.selectVarName+"'"+classString+">"+firstItemString;
	for (var i=0, len=sourceObj.length; i<len; i++){
		userString+="<option "+optionClassString+" value='"+sourceObj[i][args.valuePropertyName]+"'>"+sourceObj[i][args.labelPropertyName]+"</option>";
	}
	userString+="</select>";
	
	return userString;
};

}