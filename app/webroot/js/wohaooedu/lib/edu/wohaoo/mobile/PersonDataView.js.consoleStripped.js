require({cache:{
'url:edu/wohaoo/mobile/templates/PersonDataView.html':"<div id=\"personDataViewContainer\">\n\t<div class=\"mblSimpleDialogTitle\">${SettingsHeaderText}</div>\n\t<div class=\"mblSimpleDialogText\">${FirstNameLabel}</div>\n\t<input data-dojo-type=\"dojox/mobile/TextBox\"\n\t\t   value=\"${FisrtName}\"\n\t\t   data-dojo-attach-point=\"FirstNameInput\"\n\t\t   style=\"width:90%;\">\n\t<div class=\"mblSimpleDialogText\">${LastNameLabel}</div>\n\t<input data-dojo-type=\"dojox/mobile/TextBox\"\n\t\t   value=\"${LastName}\"\n\t\t   data-dojo-attach-point=\"LastNameInput\"\n\t\t   style=\"width:90%;\">\n\t<div class=\"mblSimpleDialogText\">${BirthDateLabel}</div>\n\t<input data-dojo-type=\"dojox/mobile/TextBox\"\n\t\tvalue=\"${BirthDate}\"\n\t\tdata-dojo-props='readOnly: true'\n\t\t data-dojo-attach-point=\"BirthDateInput\"\n\t\t data-dojo-attach-event=\"onClick: onShowBirthDatePicker\"\n\t\t   style=\"width:90%;\">\n\t<div id=\"birthDateOpener\" \n\t\t data-dojo-attach-point=\"BirthDateOpener\"\n\t\tdata-dojo-type=\"dojox/mobile/Opener\"\n\t\tdata-dojo-props='lazy:true'\n\t\t\tdata-dojo-attach-event=\"onShow: onBirthDateShow, onHide: onBirthDateHide\">\n\t</div>\n\t<button data-dojo-type=\"dojox/mobile/Button\"\n\t\t\tclass=\"mblSimpleDialogButton\" \n\t\t\tdata-dojo-attach-event=\"onClick: onOk\" \n\t\t\tdata-dojo-attach-point=\"okButton\">${OKButton}</button>\n\t<button data-dojo-type=\"dojox/mobile/Button\"\n\t\t\tclass=\"mblSimpleDialogButton\" \n\t\t\tdata-dojo-attach-event=\"onClick: onCancel\"\n\t\t\tdata-dojo-attach-point=\"cancelButton\">${CancelButton}</button>\n</div>"}});
/** SPECS
	this view enables the user to setup personal prefrerences:
		-first name
		-last name
		-birth date
 */
define("edu/wohaoo/mobile/PersonDataView", [
    "dojo/dom-class", "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct", "dijit/_TemplatedMixin",  "dijit/_WidgetsInTemplateMixin", "edu/wohaoo/mobile/_AppViewMixin", 
     "dojox/mobile/SimpleDialog", "dijit/registry",  
    "dojo/i18n!./nls/Messages",   "dojo/text!./templates/PersonDataView.html", "dojo/data/ItemFileReadStore",
    "dojo/date/stamp", "dojox/mobile/Heading",
    "dojox/mobile/ToolBarButton" , "dojox/mobile/DatePicker" , 
    "dojox/mobile/Button",  "dojox/mobile/TextBox","dojox/mobile/Opener",
     "dojo/domReady!"
], function(domClass, dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, _TemplatedMixin, _WidgetsInTemplateMixin, _AppViewMixin, SimpleDialog, 
	dijitRegistry, messages, template, ItemFileReadStore, stamp, MobileHeading,
	MobileToolBarButton, MobileDatePicker) {
	return declare([SimpleDialog, _TemplatedMixin, _WidgetsInTemplateMixin, _AppViewMixin],{
		templateString: template,
		messages: messages,
		SettingsHeaderText: messages.PERSONAL_DETAILS,
		FirstNameLabel: messages.FIRST_NAME,
		LastNameLabel: messages.LAST_NAME,
		BirthDateLabel:  messages.BIRTH_DATE,
		OKButton: messages.OK,
		CancelButton: messages.CANCEL,
		
		FirstNameInput: null,
		LastNameInput: null,
		BirthDateInput: null,
		BirthDatePicker: null,
		BirthDateOpener: null,
		datePicker: null,
		okButton: null,
		cancelButton: null,
		serviceUrl: "",
		
		messages: messages,
		
		callback: null,
		
		FisrtName: null,
		LastName: null,
		BirthDate: null,
		
		_setFirstNameAttr: function(n){
			this._set('FirstName', n);
			this.FirstNameInput.set('value', n);
		},
		_setLastNameAttr: function(n){
			this._set('LastName', n);
			this.LastNameInput.set('value',  n);
		},
		
		_setBirthDateAttr: function(d){
			if(d !== null){
				this._set('BirthDate', d);
				this.BirthDateInput.set('value', d);
			}else
				this._set('BirthDate', '');
		},
		 
		postCreate: function(){
			var self = this;
			this.itemTemplateString =  "";
			
			this.inherited(arguments);
			
			//this.datePicker.set("value", stamp.toISOString(new Date(), {selector: "date"}));
			if(this.BirthDateInput){
				if(this.BirthDateInput.domNode._clickEvent)
					dojoConnect.disconnect(this.BirthDateInput.domNode._clickEvent);
				this.BirthDateInput.domNode._clickEvent = dojoConnect.connect(this.BirthDateInput.domNode, 'click', function(toto){
					self.onShowBirthDatePicker(toto);
				});
			}
		},
		
		hideItems: function(){
			dojoQuery("." + this.itemClass, this.domNode).forEach(function(element){
				element.style.display = 'none';
			});
		},
		
		/**
		 storeItem => {
			model => 'language',
			id,
			code,
			name
		 }
		 */
		refresh: function (){
			SimpleDialog.prototype.refresh.call(this, arguments);
		},
		
		onShowBirthDatePicker: function(evt){
			var self = this;
			domConstruct.empty(this.BirthDateOpener.containerNode);
			
			var heading = MobileHeading({
				'label': __t("Date Picker")
			}).placeAt(this.BirthDateOpener.containerNode, 'first');
			
			var button1 = new MobileToolBarButton({
				"label" : __t('Done'),
				"onClick":  function(e){
					self.onBirthDateDone();
				},
				'class':"mblColorBlue",
				'style' : 'position:absolute;width:45px;right:0;'
			}).placeAt(heading.domNode, 'last');
			
			var button2 = new MobileToolBarButton({
				"label" : __t('Cancel'),
				"onClick": function(e){
					self.onBirthDateCancel();
				},
				'class':"mblColorBlue",
				'style' : "position:absolute;width:45px;left:0;"
			}).placeAt(heading.domNode, 'last');
			
			this.datePicker = new MobileDatePicker({
				value: '2000-01-01'
			}).placeAt(this.BirthDateOpener.containerNode, 'last');
			
			this.BirthDateOpener.startup();
			this.BirthDateOpener.show(this.BirthDateInput.domNode, ['below-centered','above-centered','after','before']);
		},
		
		onBirthDateShow: function(){
			this.datePicker.set('value', this.BirthDateInput.get('value'));
		},
		onBirthDateHide: function(node, v){
			if(!v)
				return;
		},
		onBirthDateDone: function(){
			this.BirthDateOpener.hide(true);
			this.set('BirthDate', this.datePicker.get("value"));
		},
		onBirthDateCancel: function(){
			this.BirthDateOpener.hide(false);
		},
		onOk: function(){
			this.callback.call(this, this.FirstName, this.LastName, this.BirthDate);
			this.hide();
		},
		
		onCancel: function(){
			this.hide();
		},
		
		show: function(cb){
			this.callback = cb;
			this.inherited(arguments);
		}
	});
});