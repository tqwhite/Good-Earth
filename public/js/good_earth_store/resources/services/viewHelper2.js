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
			{name:'filterFunction', importance:'optional'},
			{name:'selectClassName', importance:'optional'},
			{name:'optionClassName', importance:'optional'},
			{name:'firstItemLabel', importance:'optional'},
			{name:'firstItemValue', importance:'optional'},
			{name:'selectedValue', importance:'optional'},
			{name:'divId', importance:'optional'}

		], source:this.moduleName, templateToLog:false });

	var classString='',
		optionClassString='',
		firstItemString='',
		divIdString='',
		sourceObj=args.sourceObj,
		selectedAttrString;

	if (args.divId){ divIdString=" id='"+args.divId+"'"; }
	if (args.selectClassName){ classString=" class='"+args.selectClassName+"'"; }
	if (args.optionClassName){ optionClassString=" class='"+args.optionClassName+"'"; }
	
	if (!args.filterFunction){args.filterFunction=function(){return true;};};

	if (args.firstItemLabel){
		if (typeof(args.firstItemValue)=='undefined'){args.firstItemValue='';}
		firstItemString="\n<option value="+args.firstItemValue+">"+args.firstItemLabel+"</option>";
	}

	var userString="<select "+divIdString+" name='"+args.selectVarName+"'"+classString+">"+firstItemString;
	for (var i=0, len=sourceObj.length; i<len; i++){
		if (!args.filterFunction(sourceObj[i])){continue;}
		if (args.selectedValue && args.selectedValue==sourceObj[i][args.valuePropertyName]){
			selectedAttrString='selected';
		}
		else{
			selectedAttrString='';
		}
		userString+="<option "+optionClassString+" value='"+sourceObj[i][args.valuePropertyName]+"' "+selectedAttrString+">"+sourceObj[i][args.labelPropertyName]+"</option>\n";
	}
	userString+="\n</select>";

	return userString;
};

}