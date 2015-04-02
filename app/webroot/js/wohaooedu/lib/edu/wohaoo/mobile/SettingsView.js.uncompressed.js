require({cache:{
'url:edu/wohaoo/mobile/templates/SettingsView.html':"<div id=\"settingsViewContainer\">\n\t<h2 class=\"settingsGeneralHeader\" data-dojo-type=\"dojox.mobile.EdgeToEdgeCategory\" data-dojo-props='label:\"General\"'></h2>\n\t<ul data-dojo-type=\"dojox.mobile.EdgeToEdgeList\">\n\t\t<li class=\"settingsPersonalDataItem propertyListItem\" data-dojo-type=\"dojox.mobile.ListItem\" \n\t\t\t\tdata-dojo-props='label:\"Personal Details\", variableHeight:true'\n\t\t\t\tdata-dojo-attach-point=\"personalDataItem\">\n\t\t\t\t<div class=\"personalDetailsData\" data-dojo-attach-point=\"personalDetailsData\"></div>\n\t\t</li>\n\t\t<li class=\"settingsLanguageItem propertyListItem\"  data-dojo-type=\"dojox.mobile.ListItem\" \n\t\t\t\tdata-dojo-props='label:\"Language\", variableHeight:true'\n\t\t\t\tdata-dojo-attach-point=\"languageItem\">\n\t\t\t\t<div class=\"languageData\" data-dojo-attach-point=\"languageData\"></div>\n\t\t</li>\n\t</ul>\n</div>"}});
/** SPECS
	this view enables the user to setup personal prefrerences:
		-first name
		-last name
		-birth date
		-language
 */
define("edu/wohaoo/mobile/SettingsView", [
    "dojo/request","dojo/dom-class", "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/_AppViewMixin", "dojox/mobile/ScrollableView", "dijit/registry",  
    "dijit/_TemplatedMixin",  "dijit/_WidgetsInTemplateMixin",
    "dojo/i18n!./nls/Messages",   "dojo/text!./templates/SettingsView.html","dojo/data/ItemFileReadStore",
     "dojo/domReady!"
], function(dojoRequest, domClass, dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, _AppViewMixin, MobileScrollableView, dijitRegistry, 
	_TemplatedMixin, _WidgetsInTemplateMixin, messages, SettingsViewTemplate, ItemFileReadStore) {
	return declare([MobileScrollableView, _TemplatedMixin, _WidgetsInTemplateMixin, _AppViewMixin],{
		templateString: SettingsViewTemplate,
		messages: messages,
		personalDataItem: null,
		languageItem: null,
		
		messages: messages,
		
		FirstName: "",
		LastName: "",
		BirthDate: '',
		LanguageCode: '',
		
		_setFirstNameAttr : function(n){
			this._set('FirstName', n);
			this.updatePersonDetails();
		},
		
		_setLastNameAttr : function(n){
			this._set('LastName', n);
			this.updatePersonDetails();
		},
		_setLanguageCodeAttr : function(n){
			if(n !== null)
				this._set('LanguageCode', n);
			else
				this._set('LanguageCode', '');
			this.updateLanguageData();
		},
		
		_setBirthDateAttr : function(n){
			if(n !== null){
				this._set('BirthDate', n);
			}else
				this._set('BirthDate', '');
			this.updatePersonDetails();
		},
		
		updatePersonDetails: function(){
			this.personalDataItem.labelNode.innerHTML = 
				this.FirstName + ' ' + this.LastName + ', ' +  this.messages.BORN + ' ' + this.BirthDate;
		},
		
		updateLanguageData: function(){
			dlg_language_select = dijitRegistry.byId('dlg_language_select');
			if(!dlg_language_select) return;
			var langName = '';
			if(this.LanguageCode !== null)
				 langName = dlg_language_select.map(this.LanguageCode);
			this.languageItem.labelNode.innerHTML = langName;
		},
		 
		postCreate: function(){
			var self = this;
			this.inherited(arguments);
			
			this.personalDetailsData.innerHTML = __t('Personal Details');
			this.languageData.innerHTML  = __t('Preferred Language');
			
			if(this.personalDataItem){
				if(this.personalDataItem.domNode._clickEvent)
					dojoConnect.disconnect(this.personalDataItem.domNode._clickEvent);
				this.personalDataItem.domNode._clickEvent = 
				dojoConnect.connect(this.personalDataItem.domNode, 'click', function(evt){
					var dlg_person_data = dijitRegistry.byId('dlg_person_data');
					if(!dlg_person_data) return false;
					
					dlg_person_data.set('FirstName', self.FirstName);
					dlg_person_data.set('LastName', self.LastName);
					dlg_person_data.set('BirthDate', self.BirthDate);
					
					dlg_person_data.show(
						function (first_name, last_name, birth_date){
							self.set('FirstName', first_name);
							self.set('LastName', last_name);
							self.set('BirthDate', birth_date);
							self.updateData();
						}
					);
					return true;
				});
			}
			
			if(this.languageItem){
				if(this.languageItem.domNode._clickEvent)
					dojoConnect.disconnect(this.languageItem.domNode._clickEvent);
				this.languageItem.domNode._clickEvent = dojoConnect.connect(this.languageItem.domNode, 'click', function(evt){
					var dlg_language_select = dijitRegistry.byId('dlg_language_select');
					if(!dlg_language_select) return false;
					
					dlg_language_select.set('LanguageCode', self.LanguageCode);
					
					dlg_language_select.show(
						function (code){
							self.set('LanguageCode', code);
							self.updateData();
						}
					);
					return true;
				});
			}
		},
		
		updateData: function(){
			var self = this;
			console.debug("updatign settings data against url:" + this.serviceUrl);
			dojoRequest.post(this.serviceUrl, {
				data: {
					"data[Person][per_first_name]":  this.FirstName,
					"data[Person][per_last_name]": this.LastName,
					"data[Person][per_birth_date]": this.BirthDate,
					"data[User][usr_lang_code]": this.LanguageCode
				},
				handleAs: 'json'
			}).then(function(response){
				console.debug("auth request returned");
				if(!response.personid){
					console.debug("there was an error during update:" + response.toString());
					Sys.message(response.error);
				}
				else{
					console.debug("update was successfull");
				}
			}, function(error){
				self.alert(self.errors.err101);
			});
		},
		
		/**
		 storeItem => {
			model => 'user',
			id,
			first_name,
			last_name,
			birth_date,
			language_code
		 }
		 */
		refresh: function (){
			this.inherited(arguments);
		
			var self = this;
			this.query = {id: '*', model: 'user'};
			dojoQuery(".header", this.domNode).forEach(function(element){
				self.headerNode = element;
			});
			if(!Sys.getUser())
				return; //user is not authenticated
				
			if(typeof (this.serviceUrl)  === 'function'){
				this.dataUrl = this.serviceUrl.call(this);
			}else{
				this.dataUrl = this.substitute(this.serviceUrl, this);
			}
			if(this.dataUrl === null)
				return;
			this.processDataParams();
			
			this._store = new ItemFileReadStore({url: this.dataUrl, hierarchical: true});
			
			if(!this._store || this._store === null)
				return;
			
			this._store.fetch({query: this.query, queryOptions: {deep: false}, onComplete: function(storeItems){
				for(var i in storeItems){
					var storeItem = storeItems[i];
					
					self.set('FirstName', self._store.getValue(storeItem, 'first_name'));
					
					self.set('LastName', self._store.getValue(storeItem, 'last_name'));
					
					self.set('BirthDate', self._store.getValue(storeItem, 'birth_date'));
					
					self.set('LanguageCode', self._store.getValue(storeItem, 'language_code'));
				}
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug("error=" + error);
			}});
		}
	});
});