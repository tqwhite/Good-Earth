steal('jquery/controller', 'jquery/view/ejs').then('./views/main.ejs', function(
	$
) {
	/**
 * @class GoodEarthStore.Controller.Admin.User
 */
	GoodEarthStore.Controller.Base.extend(
		'GoodEarthStore.Controller.SchoolAdmin.LunchEditor',
		/** @Static */
		{
			defaults: {}
		},
		/** @Prototype */
		{
			//GoodEarthStore.Controller.Base.extend()

			init: function(el, options) {
				this.baseInits();
				this.toolName = 'lunch_editor'; //for view file path construction

				qtools.validateProperties({
					targetObject: this,
					propList: [
					{name:'statusDomObj', importance:'optional'}
					],
					source: this.constructor._fullName
				});
				this.startupOptions = options ? options : {};

				this.initControlProperties();
				this.initDisplayProperties();
				//	this.getReferenceData(this.callback('initDisplay'));
				this.initDisplay();
			},

			update: function(options) {
				this.init(this.element, options);
			},

			initDisplayProperties: function() {
				nameArray = [];

				name = 'myId';
				nameArray.push({ name: name });

				this.displayParameters = $.extend(
					this.componentDivIds,
					this.assembleComponentDivIdObject(nameArray)
				);
			},

			initControlProperties: function() {
				this.viewHelper = new viewHelper2();
				this.loginUser = GoodEarthStore.Models.Session.get('user');
			},

			initDisplay: function(inData) {
				var html = $.View(
					'//good_earth_store/controller/school_admin/' +
						this.toolName +
						'/views/main.ejs',
					$.extend(inData, {
						displayParameters: this.displayParameters,
						viewHelper: this.viewHelper,
						formData: {
							userName: GoodEarthStore.Models.LocalStorage.getCookieData(
								GoodEarthStore.Models.LocalStorage.getConstant(
									'loginCookieName'
								)
							).data
						}
					})
				);
				this.element.html(html);
				this.initDomElements();
			},

			initDomElements: function() {
				this.displayParameters.myId.domObj = $(
					'#' + this.displayParameters.myId.divId
				);
				
				this.element.good_earth_store_customer_choose_menu(this.startupOptions);
				
				//eventually this function will add 'apply to all appropriate students' button
			},




addToPurchases:function(args){
	var purchase={
		offering:args.offering,
		day:args.day,
		student:args.student,
		refId:qtools.newGuid()
	};
	this.purchases.orders.push(purchase);
	return purchase;

}
		}
	);
});
