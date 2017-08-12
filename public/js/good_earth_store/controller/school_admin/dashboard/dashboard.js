steal(
	'jquery/controller',
	'jquery/view/ejs',
	'../../school_admin/student_list/student_list.js',
	'../../school_admin/lunch_editor/lunch_editor.js',
	'../../school_admin/student_editor/student_editor.js'
).then('./views/main.ejs', function($) {
	/**
 * @class GoodEarthStore.Controller.Admin.User
 */
	GoodEarthStore.Controller.Base.extend(
		'GoodEarthStore.Controller.SchoolAdmin.Dashboard',
		/** @Static */
		{
			defaults: {}
		},
		/** @Prototype */
		{
			//GoodEarthStore.Controller.Base.extend()

			init: function(el, options) {
				this.baseInits();
				this.toolName = 'dashboard'; //for view file path construction

				qtools.validateProperties({
					targetObject: this,
					propList: [],
					source: this.constructor._fullName
				});

				this.startupOptions = options ? options : {};

				this.startProgressIndicator({
					styleString: 'margin-left:300px;margin-top:200px;'
				});

				this.initControlProperties();
				this.initDisplayProperties();
				this.getReferenceData(this.callback('initDisplay'));
			},

			update: function(options) {
				this.init(this.element, options);
			},

			initDisplayProperties: function() {
				nameArray = [];

				name = 'myId';
				nameArray.push({ name: name });
				name = 'status';
				nameArray.push({ name: name });
				name = 'accountSpace';
				nameArray.push({ name: name });
				name = 'studentList';
				nameArray.push({ name: name });
				name = 'lunchEditor';
				nameArray.push({ name: name });
				name = 'saveButton';
				nameArray.push({ name: name });
				name = 'checkoutButton';
				nameArray.push({ name: name });
				name = 'showInactiveButton';
				nameArray.push({ name: name });

				this.displayParameters = $.extend(
					this.componentDivIds,
					this.assembleComponentDivIdObject(nameArray)
				);
			},

			initControlProperties: function() {
				this.viewHelper = new viewHelper2();
				this.loginUser = GoodEarthStore.Models.Session.get('user');
				this.purchases = this.newPurchaseObj();
				this.studentsToSaveList = [];
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

				this.displayParameters.accountSpace.domObj = $(
					'#' + this.displayParameters.accountSpace.divId
				);
				this.displayParameters.studentList.domObj = $(
					'#' + this.displayParameters.studentList.divId
				);
				this.displayParameters.lunchEditor.domObj = $(
					'#' + this.displayParameters.lunchEditor.divId
				);
				this.displayParameters.status.domObj = $(
					'#' + this.displayParameters.status.divId
				);
				this.displayParameters.saveButton.domObj = $(
					'#' + this.displayParameters.saveButton.divId
				);
				this.displayParameters.showInactiveButton.domObj = $(
					'#' + this.displayParameters.showInactiveButton.divId
				);

				this.displayParameters.accountSpace.domObj.good_earth_store_customer_parent(
					{
						loginUser: this.loginUser,
						templateName: 'schoolAdmin'
					}
				);

				this.initStudentList();

				this.displayParameters.saveButton.domObj.good_earth_store_tools_ui_button2(
					{
						ready: { classs: 'basicReady' },
						hover: { classs: 'basicHover' },
						clicked: { classs: 'basicActive' },
						unavailable: { classs: 'basicUnavailable' },
						accessFunction: this.callback('saveButtonHandler'),
						initialControl: 'setToReady', //initialControl:'setUnavailable'
						label: "<div style='margin-top:1px;'>Save Students</div>"
					}
				);

				$(
					'#' + this.displayParameters.checkoutButton.divId
				).good_earth_store_tools_ui_button2({
					ready: { classs: 'basicReady' },
					hover: { classs: 'basicHover' },
					clicked: { classs: 'basicActive' },
					unavailable: { classs: 'basicUnavailable' },
					accessFunction: this.callback('checkoutButtonHandler'),
					initialControl: 'setToReady', //initialControl:'setUnavailable'
					label: 'Lunch Checkout'
				});

				$(
					'#' + this.displayParameters.showInactiveButton.divId
				).good_earth_store_tools_ui_button2({
					ready: { classs: 'basicReady' },
					hover: { classs: 'basicHover' },
					clicked: { classs: 'basicActive' },
					unavailable: { classs: 'basicUnavailableHidden' },
					accessFunction: this.callback('showInactiveButtonHandler'),
					initialControl: 'setToReady', //initialControl:'setUnavailable'
					label: 'Show/Hide Inactive'
				});
			},

			initStudentList: function(showInactive) {
				this.displayParameters.studentList.domObj.good_earth_store_school_admin_student_list(
					{
						loginUser: this.loginUser,
						account: this.account,
						schools: this.schools,
						gradeLevels: this.gradeLevels,
						statusDomObj: this.displayParameters.status.domObj,
						studentsToSaveList: this.studentsToSaveList,
						lunchEditorHandler: this.callback('lunchEditorHandler'),
						showInactive: showInactive
					}
				);
			},

			saveButtonHandler: function(control, parameter) {
				var componentName = 'editButton';
				switch (control) {
					case 'click':
						if (this.isAcceptingClicks()) {
							this.turnOffClicksForAwhile();
						} else {
							//turn off clicks for awhile and continue, default is 500ms
							return;
						}
						this.savedStudentCounter=0;
						for (
							var i = 0, len = this.studentsToSaveList.length;
							i < len;
							i++
						) {
							var student = this.studentsToSaveList[i];
							this.saveStudent(student);
						}

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

			saveStudent: function(student) {
				if (student.newAddition && student.doNotSave) {
					return;
				}
				this.savedStudentCounter++;
				if (!student.vegetarianFlag) {
					student.vegetarianFlag = 0;
				}
				if (!student.isTeacherFlag) {
					student.isTeacherFlag = 0;
				}
				if (!student.allergyFlag) {
					student.allergyFlag = 0;
				}

				if (typeof student.isActiveFlag == 'undefined') {
					student.isActiveFlag = 1;
				}
				if (student.isActiveFlag === false) {
					student.isActiveFlag = 0;
				}
				if (student.isActiveFlag === true) {
					student.isActiveFlag = 1;
				}

				this.toggleSpinner();
				GoodEarthStore.Models.Student.add(student, this.callback('catchSave'));
			},
			catchSave: function(status) {
				this.toggleSpinner();
				$(window).unbind('beforeunload.student');
				if (status.status==1){
					this.displayParameters.status.domObj.html("Successfully saved "+this.savedStudentCounter+" students");
					
				for (var i=0, len=this.studentsToSaveList.length; i<len; i++){
					this.studentsToSaveList.pop(); //this allows the array to stay in existence so it can be used again elsewhere
				}
				this.lunchEditorHandler('setLunchButtonStatus', 'setToReady');
				}
			},

			queueReferenceLookup: function(controlObj, name, modelName, argData) {
				var data;

				data = GoodEarthStore.Models.Session.get(name);

				if (data) {
					this[name] = data;
				} else {
					controlObj[name] = {
						ajaxFunction: GoodEarthStore.Models[
							modelName
						].getRetrievalFunction(),
						argData: argData ? argData : {}
					};
				}
			},

			getReferenceData: function(callback) {
				var controlObj = {}, calls = {};

				this.queueReferenceLookup(calls, 'account', 'Account', {
					refId: this.loginUser.account.refId
				});
				this.queueReferenceLookup(calls, 'schools', 'School');
				this.queueReferenceLookup(calls, 'gradeLevels', 'GradeLevel');
				this.queueReferenceLookup(calls, 'offerings', 'Offering');

				if (qtools.isNotEmpty(calls)) {
					controlObj.calls = calls;
					controlObj.success = this.callback('referenceCallback', callback);
					controlObj.stripWrappers = true;

					qtools.multiAjax(controlObj);
				} else {
					callback();
				}
			},

			referenceCallback: function(callback, inData) {
				referenceData = inData;
				this.account = inData.account;
				this.schools = inData.schools;
				this.gradeLevels = inData.gradeLevels;
				this.offerings = inData.offerings;

				GoodEarthStore.Models.Session.keep('account', this.account);
				GoodEarthStore.Models.Session.keep('schools', this.schools);
				GoodEarthStore.Models.Session.keep('gradeLevels', this.gradeLevels);
				GoodEarthStore.Models.Session.keep('offerings', this.offerings);

				callback(); //initDisplay()
			},

			newPurchaseObj: function() {
				var purchaseObj = GoodEarthStore.Models.Session.get('purchases');
				if (!purchaseObj) {
					purchaseObj = {
						orders: [],
						refId: qtools.newGuid()
					};
				}

				GoodEarthStore.Models.Session.keep('purchases', purchaseObj);
				return purchaseObj;
			},

			lunchEditorHandler: function(control, parameter) {
				var componentName = 'lunchEditor';
				switch (control) {
					case 'setLunchButtonStatus':
						this['checkoutButton'].accessFunction(parameter);
						if(parameter=='setUnavailable'){
						this.displayParameters.status.domObj.html('Save Students to reactivate Lunch Checkout button');
						}
						break;
					case 'click':
						if (this.isAcceptingClicks()) {
							this.turnOffClicksForAwhile();
						} else {
							//turn off clicks for awhile and continue, default is 500ms
							return;
						}
						this.displayParameters.status.domObj.html('Choose a tasty lunch');

						this.displayParameters.lunchEditor.domObj.good_earth_store_school_admin_lunch_editor(
							{
								studentRefId: parameter.refId,
								account: this.account,
								offerings: this.offerings,
								purchases: this.purchases,
								statusDomObj:this.displayParameters.status.domObj
							}
						);
						
						if (!this.studentsToSaveList.length){
							this['checkoutButton'].accessFunction('setToReady');
						}

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

			checkoutButtonHandler: function(control, parameter) {
				var componentName = 'checkoutButton';
				switch (control) {
					case 'click':
						if (this.isAcceptingClicks()) {
							this.turnOffClicksForAwhile();
						} else {
							//turn off clicks for awhile and continue, default is 500ms
							return;
						}
						this.element.good_earth_store_customer_checkout({
							dashboardContainer: this.element,
							returnClassName: this.constructor._fullName,
							returnClassOptions: this.startupOptions,
							account: this.account,
							purchases: this.purchases
						});
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

			showInactiveButtonHandler: function(control, parameter) {
				var componentName = 'showInactiveButton';
				switch (control) {
					case 'click':
						if (this.isAcceptingClicks()) {
							this.turnOffClicksForAwhile();
						} else {
							//turn off clicks for awhile and continue, default is 500ms
							return;
						}

						this.showingInactiveStudentsFlag = this.showingInactiveStudentsFlag
							? false
							: true;
						this.initStudentList(this.showingInactiveStudentsFlag);

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
