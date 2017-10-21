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
						{ name: 'studentsToSaveList', optional: false },
						{ name: 'lunchEditorHandler', optional: false },
						{ name: 'addNewStudentFunction', optional: false }
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

				name = 'isActiveButton';
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
			
			schoolIsOpen:function(){
					var nowDate=new Date(),
					beginThreshold, endThreshold, suppressFlag,
					twentyFourHours = (24*60*60*1000)
					
					var element=this.student.school;
					begin=element.dateOrderingBegin.replace(/-/g, '/');
					end=element.dateOrderingEnd.replace(/-/g, '/');
					;
					
					beginThreshold=new Date(begin); //this represents midnight of that day
					
					endThreshold=new Date(end);
					endThreshold.setTime(endThreshold.getTime() + (twentyFourHours-1000)); //one second before midnight
					
					openFlag=!(beginThreshold<nowDate && nowDate<endThreshold);
					return openFlag;
			},

			initDomElements: function() {
				this.displayParameters.myId.domObj = $(
					'#' + this.displayParameters.myId.divId
				);

				setTimeout(
					function() {
						this.displayParameters.lunchButton.domObj = $(
							'#' + this.displayParameters.lunchButton.divId
						);
						
						
						if (this.schoolIsOpen()){
							var initialControl='setUnavailable';
							var label="<div style='margin-top:1px;'>CLOSED</div>"
						}
						else{
							var initialControl='setToReady';
							var label="<div style='margin-top:1px;'>lunch</div>"
						}
						
						
						this.displayParameters.lunchButton.domObj.good_earth_store_tools_ui_button2(
							{
								ready: { classs: 'basicReady' },
								hover: { classs: 'basicHover' },
								clicked: { classs: 'basicReady' } /* basicActive */,
								unavailable: { classs: 'basicUnavailable' },
								accessFunction: this.displayParameters.lunchButton.handler,
								initialControl: initialControl, //initialControl:'setUnavailable'
								label: label
							}
						);

						this.displayParameters.isActiveButton.domObj = $(
							'#' + this.displayParameters.isActiveButton.divId
						);
						this.displayParameters.isActiveButton.domObj
							.on('click', this.callback('isActiveButtonHandler'))
							
					}.bind(this),
					1000
				);
			},

			change: function(thisDomObj, thisEvent) {
				var changedItem = $(thisEvent.target);
				
				var name = changedItem.attr('name');
				var value = changedItem.val();
				
				this.student[name] = value;
				
				this.student.newAddition = false; //stops being new when it gets typed on
				this.student.isActiveFlag=this.student.isActiveFlag?qtools.stringToType(this.student.isActiveFlag):true; //become involved when typed on
				
				if (name=='gradeLevelRefId'){
					this.student.gradeLevel=qtools.getByProperty(this.gradeLevels, 'refId', value);
				}
				
				var errorList = GoodEarthStore.Models.Student.validate(this.student);

				if (!errorList.length) {
					this.statusDomObj
						.html('')
						.removeClass('badStatus')
						.addClass('noStatus');
					changedItem.parent().children().each(function(inx, item) {
						$(item).removeClass('badInput');
					});
					this.student.doNotSave=false;
					if (!qtools.getByProperty(this.studentsToSaveList, 'refId', this.student.refId)){
						this.studentsToSaveList.push(this.student);
						$(window).bind('beforeunload.student', function(){ console.log("bind('beforeunload.student'"); return 'LUNCHES WERE SELECTED AND NOT ORDERED. ARE YOU SURE YOU WANT TO LEAVE THE STORE?';});
						this.lunchEditorHandler('setLunchButtonStatus', 'setUnavailable');
					}
					if (typeof this.addNewStudentFunction == 'function') {
						this.addNewStudentFunction();
					}
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
				switch (control) {
					case 'click':
						if (this.isAcceptingClicks()) {
							this.turnOffClicksForAwhile();
						} else {
							//turn off clicks for awhile and continue, default is 500ms
							return;
						}
						
						this.lunchEditorHandler(control, {student:this.student, studentDomObject:this.element}); //note: this is in school_admin/dashboard.js

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

			isActiveButtonHandler: function(event) {
				var control=event.type;

				var componentName = 'isActiveButton';
				switch (control) {
					case 'click':
						if (this.isAcceptingClicks()) {
							this.turnOffClicksForAwhile();
						} else {
							//turn off clicks for awhile and continue, default is 500ms
							return;
						}
						this.statusDomObj.html('isActiveButton clicked');
						var isActiveElement=this.element.find('[name="isActiveFlag"]');
	
						if (this.student.isActiveFlag===false){
							this.student.isActiveFlag=true;
							this.element.show();
							this.student.doNotSave=false;
						}
						else{
							this.student.isActiveFlag=false;
							this.element.hide();
							this.student.doNotSave=true;
						}

						isActiveElement.attr('value', this.student.isActiveFlag)
						

						this.change({}, {target:isActiveElement}); //this.change() wants a event/domObj-like object

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
