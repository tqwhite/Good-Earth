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
				this.baseInits();
				this.toolName = 'student_editor'; //for view file path construction

				qtools.validateProperties({
					targetObject: options,
					targetScope: this, //will add listed items to targetScope
					propList: [
						{ name: 'loginUser' },
						{ name: 'student' },
						{ name: 'gradeLevels' },
						{ name: 'statusDomObj' },
						{ name: 'studentsToSaveList', optional: false }
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

				name = 'lunchButton';
				nameArray.push({ name: name, handlerName: name + 'Handler' });

				this.displayParameters = $.extend(
					this.componentDivIds,
					this.assembleComponentDivIdObject(nameArray)
				);
			},

			initControlProperties: function() {
				this.viewHelper = new viewHelper2();
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
							student: this.student,
							loginUser: this.loginUser
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

				this.displayParameters.lunchButton.domObj=$('#'+this.displayParameters.lunchButton.divId);

				this.displayParameters.lunchButton.domObj.good_earth_store_tools_ui_button2(
					{
						ready: { classs: 'basicReady' },
						hover: { classs: 'basicHover' },
						clicked: { classs: 'basicActive' },
						unavailable: { classs: 'basicUnavailable' },
						accessFunction: this.displayParameters.lunchButton.handler,
						initialControl: 'setToReady', //initialControl:'setUnavailable'
						label: "<div style='margin-top:1px;'>lunch</div>"
					}
				);

			},

			change: function(thisDomObj, thisEvent) {
				var changedItem = $(thisEvent.target);
				var name = changedItem.attr('name');
				var value = changedItem.val();
				this.student[name] = value;

				var errorList = GoodEarthStore.Models.Student.validate(this.student);

				if (!errorList.length) {
					this.statusDomObj
						.html('')
						.removeClass('badStatus')
						.addClass('noStatus');
					changedItem.parent().children().each(function(inx, item) {
						$(item).removeClass('badInput');
					});
					this.needsAddingToSaveList ||
						this.studentsToSaveList.push(this.student);
					this.needsAddingToSaveList = true;
				} else {
					changedItem.addClass('badInput');
					this.statusDomObj
						.html(errorList.join(' - ').replace(/ - $/, ''))
						.removeClass('noStatus')
						.addClass('badStatus');
				}
			},

			lunchButtonHandler: function(control, parameter) {
				var componentName = 'lunchButton';
				//if (control.which=='13'){control='click';}; //enter key
				switch (control) {
					case 'click':
						if (this.isAcceptingClicks()) {
							this.turnOffClicksForAwhile();
						} else {
							//turn off clicks for awhile and continue, default is 500ms
							return;
						}

						this.displayParameters.status.domObj.html('lunch button clicked');
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
			}
		}
	);
});
