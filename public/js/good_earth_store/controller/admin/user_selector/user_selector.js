steal('jquery/controller', 'jquery/view/ejs')
	.then('./views/init.ejs', function($) {

	/**
	 * @class GoodEarthStore.Controller.Admin.User
	 */
	GoodEarthStore.Controller.Base.extend('GoodEarthStore.Controller.Admin.UserSelector',
	/** @Static */
	{
		defaults: {}
	},
	/** @Prototype */
	{

		/*

		This works with tqViewScaffold, 5.16.15
		It has all of the JMVC organization TQ likes and create one working button.
		*/

		init: function(el, options) {
			this.baseInits();
			this.thisObj = el;
			this.directory = "//good_earth_store/controller/admin/user_selector/";

			qtools.validateProperties({
				targetObject: options || {},
				targetScope: this, //will add listed items to targetScope
				propList: [
					{
						name: 'parentAccessFunction'
					},
					{
						name: 'parameters'
					},
					{
						name: 'statusDomObject'
					}],
				source: this.constructor._fullName
			});

			this.initControlProperties();
			this.initDisplayProperties();



			// being done in search callback this.initDisplay();

		},

		update: function(options) {
			this.init(this.element, options);
		},

		initDisplayProperties: function() {

			nameArray = [];

			name = 'chooseName'; nameArray.push({
				name: name,
				handlerName: name + 'Handler',
				targetDivId: name + 'Target'
			});

			this.displayParameters = $.extend(this.componentDivIds, this.assembleComponentDivIdObject(nameArray));

		},

		initControlProperties: function() {
			this.viewHelper = new viewHelper2();

			this.getUserList();

		},

		initDisplay: function(inData) {

			qtools.validateProperties({
				targetObject: inData || {},
				propList: [
					{
						name: 'userList'
					}
				],
				source: this.constructor._fullName + '/initDisplay()'
			});

			var html = $.View(this.directory + 'views/init.ejs',
			$.extend(inData, {
				displayParameters: this.displayParameters,
				viewHelper: this.viewHelper,
				formData: {
					source: this.constructor._fullName
				}
			})
			);
			this.element.html(html);
			this.initDomElements();
		},

		initDomElements: function() {
			var name = 'chooseName'; //this.displayParameters.saveButton
			this.displayParameters[name].domObj = $('.' + this.displayParameters[name].divId);
			this.displayParameters[name].domObj.bind('click', this.displayParameters[name].handler);

			$('body').bind('userSaveComplete', this.callback('getUserList'));
		},

		getUserList: function(eventObj) {

			if (eventObj && typeof (eventObj.stopPropagation) == 'function') {
				eventObj.stopPropagation(); //this acts a jquery event callback, too
			}

			$('body').unbind('userSaveComplete');

			this.toggleSpinner();
			GoodEarthStore.Models.User.searchByFragment(this.parameters, this.callback('userListCallback'));
		},

		userListCallback: function(inData) {
			var errorString = this.listMessages(inData.messages);
			this.toggleSpinner();
			if (inData.status < 1) {
				this.statusDomObject.html(errorString).removeClass('good').addClass('bad');
			} else {
				this.userList = inData.data;
				this.initDisplay({
					userList: this.userList
				});
			}
		},

		chooseNameHandler: function(eventObj) {
			var componentName = 'chooseName';
			if (this.isAcceptingClicks()) {
				this.turnOffClicksForAwhile(); //turn off clicks for awhile and continue, default is 500ms
			} else {
				return;
			}

			var refId = $(eventObj.target).attr('data-refId');

			var selectedUser = qtools.getByProperty(this.userList, 'refId', refId);
			this.parentAccessFunction('setUser', selectedUser);

		}
	})

});


