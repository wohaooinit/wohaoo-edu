/**
 *The view shows the list of curriculums as icons with labels.
    When the user clicks on an icon, the curriculum menu page is shown
    The top bar has a search box. When the user enters a search term and clicks
    on the search button, only the curriculums corresponding to the search term are shown.
    When the user clicks on the favorites button a keyword ":favorites" is added at the end of the search term
    When the user clicks on the 'Show Menu' button a toolbar is presented.
    If the user is connected, then the user bar (User Menu) is shown, else
    	the default home bar (Home Menu) is shown
    */
define("edu/wohaoo/mobile/CurriculumsView", [
   "dojo/dom-class", "dojo/keys", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/AppListView", "dijit/registry", "dojox/mobile/IconItem",
     "dojo/domReady!"
], function(domClass, dojoKeys, dojoQuery, dojoConnect, declare, _WidgetBase, 
		domConstruct, AppListView, dijitRegistry, MobileIconItem) {
	return declare([AppListView],{
		loggedin: false,
		
		/**
		 storeItem => {
			model => 'curriculum',
			id,
			icon,
			code,
			lang,
			desc,
			name,
			value,
			created,
			module_count,
			course_id,
			course_created,
			course_graduated,
			course_module_count
		 }
		 */
		searchButtonNode: null,
		searchInputNode: null,
		showFavoritesButtonNode: null,
		showHomeMenuButtonNode: null,
		homeMenuNode: null,
		userMenuNode: null,
		
		postCreate: function(){
			this.itemTemplateString = '<div class="box"></div>';
			this.itemClass = 'curriculum';
			this.itemMoveTo = 'curriculumPage';
			this.itemTransition = 'slide';
			
			this.inherited(arguments);
		},
		
		startup: function(){
			var self = this;
			dojoQuery('button[type="submit"].icon', this.domNode).forEach(function(element){
				self.searchButtonNode = element;
			});
			
			dojoQuery("input#search-query", this.domNode).forEach(function(element){
				self.searchInputNode = element;
			});
			
			//showFavoritesButton
			dojoQuery("span.showFavoritesButton", this.domNode).forEach(function(element){
				var widget = dijitRegistry.byNode(element);
				self.showFavoritesButtonNode = widget.domNode;
			});
			
			//showHomeMenuButtonNode
			dojoQuery("span.showHomeMenuButton", this.domNode).forEach(function(element){
				var widget = dijitRegistry.byNode(element);
				self.showHomeMenuButtonNode = widget.domNode;
			});
			
			//homeMenu
			dojoQuery(".homeMenu", this.domNode).forEach(function(element){
				var widget = dijitRegistry.byNode(element);
				self.homeMenuNode = widget.domNode;
			});
			
			//userMenu
			dojoQuery(".userMenu", this.domNode).forEach(function(element){
				var widget = dijitRegistry.byNode(element);
				self.userMenuNode = widget.domNode;
			});
			
			this._attachEvents();
			this.inherited(arguments);
		},
		
		createListItem: function(itemClass, id, caption, itemMoveTo, itemTransition){
			var item = new MobileIconItem({
				"class": itemClass,
				"dataId": id,
				"moveTo": itemMoveTo,
				"rightText": "",
				"transition": itemTransition,
				'label': caption
			});
			return item;
		},
		
		updateItemContent: function(item, itemTemplateString, storeItem){
		},
		
		refresh: function(){
			//hide tad bars
			domClass.add(this.homeMenuNode, 'mblHidden');
			domClass.add(this.userMenuNode, 'mblHidden');
			if(this.searchInputNode)
				this.q = this.searchInputNode.value;
			this.inherited(arguments);
		},
		
		_attachEvents: function (){
			var self = this;
			if(self.searchInputNode._keyupEvent){
				self.searchInputNode._keyupEvent.remove();
				self.searchInputNode._keyupEvent = null;
			}
			self.searchInputNode._keyupEvent = dojoConnect.connect(self.searchInputNode, "keyup", function(e){
				if(e.keyCode === dojoKeys.ENTER){
					self.q = self.searchInputNode.value;
					if(self.q !== ''){
						domClass.add(self.searchButtonNode, 'nav-search-active');
						domClass.remove(self.searchButtonNode, 'nav-search');
					}else{
						domClass.remove(self.searchButtonNode, 'nav-search-active');
						domClass.add(self.searchButtonNode, 'nav-search');
					}
					self.refresh();
				}
			});
			
			//show clear button icon
			if(self.searchButtonNode._clickEvent){
				self.searchButtonNode._clickEvent.remove();
				self.searchButtonNode._clickEvent = null;
			}
			self.searchButtonNode._clickEvent = dojoConnect.connect(self.searchButtonNode, 'click', function(e){
				if(domClass.contains(self.searchButtonNode, "nav-search-active")){
					self.q = "";
					self.l = "";
					self.searchInputNode.value = "";
					self.refresh();
					domClass.remove(self.searchButtonNode, 'nav-search-active');
					domClass.add(self.searchButtonNode, 'nav-search');
				}else{
					self.q = self.searchInputNode.value;
					self.l = "";
					if(self.q !== ''){
						self.refresh();
						domClass.add(self.searchButtonNode, 'nav-search-active');
						domClass.remove(self.searchButtonNode, 'nav-search');
					}
				}
			});
			
			//showFavoritesButton
			if(self.showFavoritesButtonNode._clickEvent){
				self.showFavoritesButtonNode._clickEvent.remove();
				self.showFavoritesButtonNode._clickEvent = null;
			}
			
			self.showFavoritesButtonNode._clickEvent = 
			dojoConnect.connect(self.showFavoritesButtonNode, 'click', function(e){
				if(self.searchInputNode.value.indexOf(":favorites") < 0)
					self.searchInputNode.value = self.searchInputNode.value + ":favorites";
				
				var favorites = Sys.getCurriculumFavorites(); //restrict search to favorites list
				if(!favorites)
					favorites = "0"; //default
				self.l = favorites;
				
				if(!domClass.contains(self.searchButtonNode, "nav-search-active")){
					domClass.add(self.searchButtonNode, 'nav-search-active');
					domClass.remove(self.searchButtonNode, 'nav-search');
				}
				
				self.refresh();
			});
			
			//showHomeMenuButtonNode
			if(self.showHomeMenuButtonNode._clickEvent){
				self.showHomeMenuButtonNode._clickEvent.remove();
				self.showHomeMenuButtonNode._clickEvent = null;
			}
			self.showHomeMenuButtonNode._clickEvent = 
			dojoConnect.connect(self.showHomeMenuButtonNode, 'click', function(e){
				if(!Sys.getUser()){
					if(domClass.contains(self.homeMenuNode, "mblHidden")){
						domClass.remove(self.homeMenuNode, 'mblHidden');
					}else{
						domClass.add(self.homeMenuNode, 'mblHidden');
					}
				}else{
					//user is logged in
					if(domClass.contains(self.userMenuNode, "mblHidden")){
						domClass.remove(self.userMenuNode, 'mblHidden');
					}else{
						domClass.add(self.userMenuNode, 'mblHidden');
					}
				}
			});
			
			dojoQuery(".tabBarButton", this.domNode).forEach(function(element){
				var button = dijitRegistry.byNode(element);
				button.set('onClick', function(evt){
					var moveToWidget = dijitRegistry.byId(this.moveTo);
					moveToWidget.refresh();
					moveToWidget.animate();
					self.moveTo(self.id, this.moveTo, this.transition);
					evt.stopPropagation();
					evt.preventDefault();
				});
			});
			
		}
	
	});
});