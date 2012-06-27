steal( 'jquery/controller','jquery/view/ejs' )
	.then( './views/modal_screen.ejs', function($){

/**
 * @class GoodEarthStore.Controller.Tools.Ui.ModalScreen
 */
GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Tools.Ui.ModalScreen',
/** @Static */
{
	defaults : {}
},
/** @Prototype */
{
	init: function(el, options) {

		this.mainViewName=this.constructor._shortName
		this.divPrefix=controllerHelper.divPrefix(this.constructor._fullName);

		this.initDataStructures(options);

		this.draw();

	},

update:function(){
	this.show();
},

initDataStructures:function(options){
	var options=options?options:{};

	this.employerSender=options.employerSender;
	this.employerInit(); //especially, send the accessFunction to it

	this.appearance={};
	this.appearance=options.appearance?$.extend(this.appearance, options.appearance):this.appearance;

	this.progressMessageId=this.divPrefix+'progressMessage'
	this.progressMessage=options.progressMessage?options.progressMessage:'WORKING ...';

	this.screenId=this.divPrefix+'modalScreen';
},

draw:function(){
	var viewName, cleanUpObject, html;

	viewName=this.mainViewName;


	var cleanUpObject=controllerHelper.newCleanUpObject(this.element, this);

	html=$.View(viewName, {
		scope:this,
		appearance:this.appearance,
		cleanUpObject:cleanUpObject,
		divPrefix:this.divPrefix,

		screenId:this.screenId,
		progressMessageId:this.progressMessageId,
		progressMessage:this.progressMessage
		}
	);

	this.element.append(html);
	cleanUpObject.executeCleanupFunction();

	this.modalObj=$('#'+this.screenId);
	this.progressMessageObj=$('#'+this.progressMessageId);

	this.show();
	return true;
},

show:function(){
	var height=this.element.height();
	var width=this.element.width();
	this.modalObj.height(height);
	this.modalObj.width(width);
	this.modalObj.show();
	var msgHeight=this.progressMessageObj.height();
	var msgWidth=this.progressMessageObj.width();

	var msgTop=this.progressMessageObj.css('top');
	var msgLeft=this.progressMessageObj.css('left');

	this.modalObj.css('padding-top', (height/2)-msgHeight/2);
	this.modalObj.css('padding-left', (width/2)-msgWidth/2);
/*
	this.modalObj.css('top', msgTop);
	this.modalObj.css('left', msgLeft);
*/

	if (qtools.isNotEmpty(this.appearance)){this.applyCustomAppearance();}
	return;
},

applyCustomAppearance:function(){
	if (qtools.isNotEmpty(this.appearance.attributes)){qtools.applyAttributesFromList(this.modalObj, this.appearance.attributes);}
	if (qtools.isNotEmpty(this.appearance.styles)){qtools.applyStylesFromList(this.modalObj, this.appearance.styles);}

},

hide:function(){
	this.modalObj.hide();
},

employerInit:function(){
	this.employerSender('setAccessFunction', this.callback('employerReceiver'));
},

employerReceiver:function(control, parameter){
	if (!this.element){return;}

	switch(control){
		case 'setMessage':
			this.progressMessageObj.html(parameter);
		break;
		case 'clear':
			this.hide();
			this.employerSender('allDone');
			this.destroy();
		break;
	}

}

})

});