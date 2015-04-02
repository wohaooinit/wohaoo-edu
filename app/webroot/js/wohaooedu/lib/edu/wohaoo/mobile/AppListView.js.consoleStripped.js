define("edu/wohaoo/mobile/AppListView", [
     "dojo/_base/array", "dojo/on", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare", 
      "dojox/mobile/ScrollableView",  "dijit/_WidgetBase", 
    "dojo/dom-construct", "dijit/registry", "edu/wohaoo/mobile/_AppViewMixin",
     "dojo/data/ItemFileReadStore", "dojo/domReady!"
], function(dojoArray, on, dojoQuery, dojoConnect, declare, MobileScrollableView, 
                _WidgetBase, domConstruct,  dijitRegistry,
		_AppViewMixin, ItemFileReadStore) {
	return declare([MobileScrollableView, _AppViewMixin],{
		//no Scrollable Cover
		noCover: true,
		
		// Create a template string for items:
		itemTemplateString: '',

		listNode: null,
		
		itemClass: '',
		
		_store: null,
		
		dataId: null,
		
		q: "",
		
		l: "",
		
		minId: 0,
		maxId: 0,
		
		dataUrl: null,
		
		autoScroll: true,
		
		postCreate: function(){
			this.inherited(arguments);
			
			var self = this;
			on(this, 'beforescroll', function (evt){
				self.__beforescroll(evt);
			});
			
			dojoQuery(".itemList", this.domNode).forEach(function(element){
				self.listNode = element;
			});
		},
		
		hideItems: function(){
			dojoQuery("." + this.itemClass, this.domNode).forEach(function(element){
				element.style.display = 'none';
			});
		},
		
		__beforescroll: function(evt){
			if(!this.autoScroll)
				return false;
			if(!evt.afterBottom && !evt.beforeTop)
				return false;
			if(evt.afterBottomHeight > 40)
				return false;
			if(evt.beforeTopHeight > 40)
				return false;
			if(this.busy)
				return false;
			var up = false, down = false;
			up = evt.beforeTopHeight > 0;
			down = evt.afterBottomHeight > 0;
			0 && console.debug("up=" + up + ",down=" + down);
			this.dataParams = [];
			if(down && this.maxId > 0){
				this.dataParams[this.dataParams.length] = ['max_id=' + this.maxId];
			}else
			if(up && this.minId > 0){
				this.dataParams[this.dataParams.length] = ['min_id=' + this.minId];
			}
			0 && console.debug("refresh:minId=" + this.minId + ",maxId=" + this.maxId);
			if(this.dataParams.length > 0){
				this.refresh();
			}
			return true;
		},
		
		updateItemList: function(){
			if(typeof (this.serviceUrl)  === 'function'){
				this.dataUrl = this.serviceUrl.call(this);
			}else{
				this.dataUrl = this.substitute(this.serviceUrl, this);
			}
			if(this.dataUrl === null)
				return;
			this.processDataParams();
			this._store = new ItemFileReadStore({url: this.dataUrl});
			
			if(!this._store || this._store === null || this.itemTemplateString === null || this.itemTemplateString === '')
				return;
			//self MUST be defined right next to the fetch call!!!
			var self = this;
			self._store.fetch({query: this.query, 
			onBegin: function(){
				self.hideItems();
				self.minId = 0;
				self.maxId = 0;
			},
			onComplete: function(storeItems){
				dojoArray.forEach(storeItems, function(storeItem, index){
					var iStore = storeItem._S; //get the item store
					var id = iStore.getValue(storeItem, "uniqueid");
					var uniqueid = iStore.getValue(storeItem, "uniqueid");
					var caption = iStore.getValue(storeItem, "name");
					
					// Create a new list item, inject into list
					var item = self.createListItem(self.itemClass,
						id,
						caption,
						self.itemMoveTo,
						self.itemTransition);
					
					if(!item || item === null)
						throw new Exception(__t("New Item is Null"));
					
					var icon = iStore.getValue(storeItem, "icon");
					if(icon.indexOf("//") === -1){
						var httpScheme = Sys.getHttpScheme();
						var httpHost = Sys.getHttpHost();
						if(httpScheme && httpHost){
							icon = httpScheme + "://" + httpHost  + icon;
						}
					}
					storeItem.icon = icon;
					item.set('icon', icon);
					
					item.placeAt(self.listNode,"last");
					
					// Update the list item's content using our template for items
					self.updateItemContent(item, self.itemTemplateString, storeItem);
					
					if(typeof item.moveTo !== 'undefined'){
						if(item.domNode._clickEvent)
							dojoConnect.disconnect(item.domNode._clickEvent);
						item.domNode._clickEvent = dojoConnect.connect(item.domNode, 'click', function(evt){
							var itemWidget = dijitRegistry.byNode(this);
							var moveToWidget = dijitRegistry.byId(itemWidget.moveTo);
							moveToWidget.set('dataId', itemWidget.dataId);
							moveToWidget.refresh();
							self.moveTo(self.id, item.moveTo, itemWidget.transition);
						});
					}
					
					//uniaueid === ranking
					0 && console.debug("uniqueid=" + uniqueid);
					if(uniqueid > self.maxId)
						self.maxId = uniqueid;
					if(!self.minId || uniqueid < self.minId)
						self.minId = uniqueid;
					0 && console.debug("minId=" + self.minId + ",maxId=" + self.maxId);
				});
			}, onError: function(error){
				self.alert(self.errors.err102);//Internal client error
				0 && console.debug("error=" + error);
				throw error;
			}});
		},
		
		updateItemContent: function(item, itemTemplateString, storeItem){
			item.labelNode.innerHTML = this.substitute(self.itemTemplateString, storeItem);
		},
		
		refresh: function(){
			this.updateItemList();
			this.inherited(arguments);
		}
	});
});