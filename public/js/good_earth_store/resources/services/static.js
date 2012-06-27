var initGlobalNameSpace=function(){

if (typeof(AAA)=='undefined'){AAA=[];}; //useful for debugging loops

if (typeof(GLOBALS)=='undefined'){GLOBALS={};};
if (typeof(CONSTANTS)=='undefined'){CONSTANTS={};};

CONSTANTS.allowDebugMessages=1;
CONSTANTS.allowProductionMessages=-1;

GLOBALS.config={
messageLevel:CONSTANTS.allowDebugMessages
}

}();