require({cache:{
'url:edu/wohaoo/mobile/templates/LanguageDialogView.html':"<div id=\"languageDialogContainer\">\n\t<div class=\"mblSimpleDialogTitle\">${LanguageDialogTitle}</div>\n\t<ul data-dojo-type=\"dojox.mobile.EdgeToEdgeList\"  data-dojo-attach-point=\"optionsList\" data-dojo-props='select:\"single\"'>\n\t\t<!--<li data-dojo-type=\"dojox.mobile.ListItem\" data-dojo-props='checkClass:\"mblDomButtonSilverCircleGreenButton\", uncheckClass:\"mblDomButtonSilverCircleGrayButton\"'>\n\t\t\tSample Item \n\t\t</li>-->\n\t</ul>\n\t<button data-dojo-type=\"dojox.mobile.Button\" \n\t\tclass=\"mblSimpleDialogButton\" \n\t\tdata-dojo-props='select:\"single\"'\n\t\tdata-dojo-attach-point=\"okButton\" \n\t\tstyle=\"width:60%;\">${OKButton}</button>\n</div>"}});
/** SPECS
	this view enables the user to setup personal prefrerences:
		-first name
		-last name
		-birth date
		-language
 */
define("edu/wohaoo/mobile/LanguageDialogView", [
    "dojo/dom-class", "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/_AppViewMixin",  "dojox/mobile/SimpleDialog", "dijit/registry",  
    "dojo/i18n!./nls/Messages",   "dojo/text!./templates/LanguageDialogView.html", "dojo/data/ItemFileReadStore",
    "dijit/_TemplatedMixin",  "dijit/_WidgetsInTemplateMixin", "dojox/mobile/ListItem",
    "dojox/mobile/EdgeToEdgeList", "dojox/mobile/Button" ,
     "dojo/domReady!"
], function(domClass, dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, _AppViewMixin, SimpleDialog, dijitRegistry, messages, Template, ItemFileReadStore,
	_TemplatedMixin, _WidgetsInTemplateMixin, MobileListItem) {
	return declare([SimpleDialog, _TemplatedMixin, _WidgetsInTemplateMixin, _AppViewMixin],{
		templateString: Template,
		messages: messages,
		okButton: null,
		optionsList: null,
		serviceUrl: "",
		
		LanguageDialogTitle: messages.LANGUAGE_SELECTION,
		OKButton: messages.OK,
		
		messages: messages,
		
		callback: null,
		
		languageCode: null,
		
		languageDictionary: null,
		
		langItems: null,
		
		_setLanguageCodeAttr: function(v){
			if(v!== null && v){
				this._set('LanguageCode', v);
				this.langItems[v].set('checked', 'true');
			}else{
				this._set('LanguageCode', '');
			}
		},
		
		map: function(code){
			if(code !== null && code)
				return this.languageDictionary[code];
			return '';
		},
		 
		postCreate: function(){
			var self = this;
			this.itemTemplateString =  "";
			this.itemClass = 'langItem';
			
			this.languageDictionary = [];
			
			this.inherited(arguments);
			
			if(this.okButton){
				if(this.okButton.domNode._clickEvent)
					dojoConnect.disconnect(this.okButton.domNode._clickEvent);
				this.okButton.domNode._clickEvent = dojoConnect.connect(this.okButton.domNode, 'click', function(evt){
					self.callback.call(self, self.LanguageCode);
					self.hide();
				});
			}
		},
		
		hideItems: function(){
			dojoQuery("." + this.itemClass, this.optionsList.domNode).forEach(function(element){
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
			var self = this;
			this.query = {id: "*", model: "language"};
			
			if(typeof (this.serviceUrl)  === 'function'){
				this.dataUrl = this.serviceUrl.call(this);
			}else{
				this.dataUrl = this.substitute(this.serviceUrl, this);
			}
			if(this.dataUrl === null)
				return;
			
			this._store = new ItemFileReadStore({url: this.dataUrl, hierarchical: true});
			
			if(!this._store || this._store === null )
				return;
			
			this._store.fetch({query: this.query, queryOptions: {deep: false}, 
				onBegin: function(){
					self.hideItems();
				},
				onComplete: function(storeItems){
				self.langItems = [];
				
				for(var i in storeItems){
					var storeItem = storeItems[i];
					var code = self._store.getValue(storeItem, "code");
					var name = self._store.getValue(storeItem, "name");
					
					self.languageDictionary[code] = name;
					
					var item = new MobileListItem({
								"code": code,
								'label': name,
								'class' : self.itemClass
							});
							
					self.langItems[code] = item;
					
					if(code === self.LanguageCode)
						item.set('checked', 'true');
					
					item.placeAt(self.optionsList.domNode,"last");
					
					storeItem.code = code;
					
					// Update the list item's content using our template for items
					//item.labelNode.innerHTML = item.labelNode.innerHTML+
					//		 self.substitute(self.itemTemplateString, storeItem);
							 
					
					if(item.domNode._clickEvent)
						dojoConnect.disconnect(item.domNode._clickEvent);
					item.domNode._clickEvent = dojoConnect.connect(item.domNode, 'click', function(evt){
						var itemWidget = dijitRegistry.byNode(this);
						self.LanguageCode = itemWidget.get('code');
					});
					
				}
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug("error=" + error);
			}});
			SimpleDialog.prototype.refresh.call(this, arguments);
		},
		
		show: function(cb){
			this.callback = cb;
			this.inherited(arguments);
		}
	});
});