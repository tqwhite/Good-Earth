steal('jquery/controller', 'jquery/view/ejs')
	.then('./views/init.ejs','./views/schoolAdmin.ejs', function($) {

	/**
	 * @class GoodEarthStore.Controller.Customer.Parent
	 */
	GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Customer.Parent',
	/** @Static */
	{
		defaults: {}
	},
	/** @Prototype */
	{

		init: function(el, options) {
			this.baseInits();
			qtools.validateProperties({
				targetObject: options,
				targetScope: this, //will add listed items to targetScope
				propList: [
					{
						name: 'loginUser'
					},
					{
						name: 'templateName',
						optional:true
					}
				],
				source: this.constructor._fullName
			});

			this.initControlProperties();
			this.initDisplayProperties();

			options = options ? options : {};
			if (options.initialStatusMessage) {
				this.initialStatusMessage = options.initialStatusMessage;
			}

			this.initDisplay();

		},

		update: function() {
			this.init();
		},

		initDisplayProperties: function() {

			nameArray = [];

			name = 'status'; nameArray.push({
				name: name
			});
			name = 'logoutButton'; nameArray.push({
				name: name,
				handlerName: name + 'Handler',
				targetDivId: name + 'Target'
			});

			this.displayParameters = $.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

		},

		initControlProperties: function() {
			this.viewHelper = new viewHelper2();
			
		},

		initDisplay: function(inData) {
			this.templateName=this.templateName?this.templateName:'init';
			var html = $.View('//good_earth_store/controller/customer/parent/views/'+this.templateName+'.ejs',
			$.extend(inData, {
				displayParameters: this.displayParameters,
				viewHelper: this.viewHelper,
				formData: {
					user: this.loginUser
				}
			})
			);
			this.element.html(html);
			this.initDomElements();
		},

		initDomElements: function() {

			this.displayParameters.logoutButton.domObj = $('#' + this.displayParameters.logoutButton.divId);

			this.displayParameters.logoutButton.domObj.good_earth_store_tools_ui_button2({
				ready: {
					classs: 'basicReady'
				},
				hover: {
					classs: 'basicHover'
				},
				clicked: {
					classs: 'basicActive'
				},
				unavailable: {
					classs: 'basicUnavailable'
				},
				accessFunction: this.displayParameters.logoutButton.handler,
				initialControl: 'setToReady', //initialControl:'setUnavailable'
				label: "Log Out"
			});

		},

		logoutButtonHandler: function(control, parameter) {
			var componentName = 'logoutButton';
			switch (control) {
				case 'click':

					if (this.isAcceptingClicks()) {
						this.turnOffClicksForAwhile();
					}
					else {
						return;
					}

					GoodEarthStore.Models.Session.logout({}, this.callback('catchLogout'));
					break;
				case 'setAccessFunction':
					if (!this[componentName]) {
						this[componentName] = {};
					}
					this[componentName].accessFunction = parameter;
					break;
			}
			//change dblclick mousedown mouseover mouseout dblclick
			//focusin focusout keydown keyup keypress select
		},

		catchLogout: function() {
			window.location.reload()
		}
	})

});
