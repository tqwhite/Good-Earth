steal('jquery/controller', 'jquery/view/ejs').then('./views/main.ejs', function(
	$
) {
	/**
 * @class GoodEarthStore.Controller.Admin.User
 */
	GoodEarthStore.Controller.Base.extend(
		'GoodEarthStore.Controller.SchoolAdmin.StudentList',
		/** @Static */
		{
			defaults: {}
		},
		/** @Prototype */
		{
			//GoodEarthStore.Controller.Base.extend()
			init: function(el, options) {
				this.baseInits();
				this.toolName = 'student_list'; //for view file path construction

				qtools.validateProperties({
					targetObject: options,
					targetScope: this, //will add listed items to targetScope
					propList: [
						{ name: 'loginUser', optional: false },
						{ name: 'account', optional: false },
						{ name: 'schools', optional: false },
						{ name: 'gradeLevels', optional: false },
						{ name: 'statusDomObj', optional: false },
						{name:'studentsToSaveList', optional:false}
					],
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
				name = 'studentItemIdClass';
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
							students: this.account.students
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

				this.displayParameters.studentItemIdClass.domObj = $(
					'.' + this.displayParameters.studentItemIdClass.divId
				);

				for (var i = 0, len = this.account.students.length; i < len; i++) {
					var student = this.account.students[i];

					var newStudent = $('<div/>');

					newStudent.good_earth_store_school_admin_student_editor({
						'loginUser':this.loginUser,
						student: student,
						gradeLevels: this.gradeLevels,
						statusDomObj:this.statusDomObj,
						studentsToSaveList:this.studentsToSaveList
					});

					this.displayParameters.myId.domObj.append(newStudent);
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
			}
		}
	);
});
