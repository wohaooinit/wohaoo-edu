define("edu/wohaoo/mobile/ItemPageView", [
    "dojo/query!css3", "dojo/_base/connect", "dojo/on", "dojo/_base/declare",  "dojox/mobile/ScrollableView",
     "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/_AppViewMixin", "dijit/registry", "dojo/data/ItemFileReadStore",
     "dojo/domReady!"
], function(dojoQuery, dojoConnect, on, declare, MobileScrollableView, _WidgetBase, domConstruct, _AppViewMixin, 
	dijitRegistry, ItemFileReadStore) {
	return declare([MobileScrollableView, _AppViewMixin],{
		headerNode: null,
		itemHeaderTemplateString:  '<img width=32 height=32 src="${icon}" alt="${name}" class="headerImage pull-left" />' + 
				'<div class="headerDetails"><div class="name">${short_name}</div>'  +
				'</div>',
		dataUrl:null,
		itemModel: '',
		itemPrefix: "",
		
		postCreate: function(){
			this.inherited(arguments);
		},
		
		startup: function(){
			this.inherited(arguments);
			/*var itemId = Sys.getActiveItemId();
			if(itemId){
				this.dataId = itemId;
			}*/
		},
		
		hideItems: function(){
			dojoQuery("." + this.itemClass, this.domNode).forEach(function(element){
				element.style.display = 'none';
			});
		},
		
		/**
		 storeItem => {
			model => 'curriculum' || 'module',
			id,
			icon,
			code,
			name,
			short_name
		 }
		 */
		refresh: function (){
			this.inherited(arguments);
			
			if(!this.dataId)
				return;
			var self = this;
			this.query = {id: this.itemPrefix + this.dataId.toString()};
			dojoQuery(".header", this.domNode).forEach(function(element){
				self.headerNode = element;
			});
			
			if(typeof (this.serviceUrl)  === 'function'){
				this.dataUrl = this.serviceUrl.call(this);
			}else{
				this.dataUrl = this.substitute(this.serviceUrl, this);
			}
			if(this.dataUrl === null)
				return;
			this.processDataParams();
			
			this._store = new ItemFileReadStore({url: this.dataUrl, hierarchical: true});
			
			if(!this._store || this._store === null || this.itemHeaderTemplateString === null || this.itemHeaderTemplateString === '')
				return;
			
			this._store.fetch({query: this.query, queryOptions: {deep: false}, onComplete: function(storeItems){
				for(var i in storeItems){
					var storeItem = storeItems[i];
					var icon = self._store.getValue(storeItem, "icon");
					if(icon && icon.indexOf("://") === -1){
						var httpScheme = Sys.getHttpScheme();
						var httpHost = Sys.getHttpHost();
						if(httpScheme && httpHost){
							icon = httpScheme + "://" + httpHost  + icon;
						}
					}
					storeItem.icon = icon;
					
					// Update the list item's content using our template for items
					self.headerNode.innerHTML = self.substitute(self.itemHeaderTemplateString, storeItem);
					
					self.itemName = self._store.getValue(storeItem, 'name');
					
					self.itemModel = self._store.getValue(storeItem, 'model');
				}
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug("error=" + error);
			}});
		}
	
	});
});