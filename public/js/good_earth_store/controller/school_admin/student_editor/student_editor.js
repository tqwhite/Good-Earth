steal('jquery/controller', 'jquery/view/ejs').then('./views/main.ejs', function(
	$
) {
	/**
 * @class GoodEarthStore.Controller.Admin.User
 */
	GoodEarthStore.Controller.Base.extend(
		'GoodEarthStore.Controller.SchoolAdmin.StudentEditor',
		/** @Static */
		{
			defaults: {}
		},
		/** @Prototype */
		{
			//GoodEarthStore.Controller.Base.extend()

			init: function(el, options) {
console.log("\n=-=============   GoodEarthStore.Controller.SchoolAdmin.StudentEditor  =========================\n");


				this.baseInits();
				this.toolName = 'student_editor'; //for view file path construction

				qtools.validateProperties({
					targetObject: this,
					propList: [],
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
			}
		}
	);
});
