/**
 * SPECS:
	The view shows a four button menubar (2 cols and 2 rows): info, documents, videos, and audios.
	When the user clicks on the info button, the [item] info view is shown.
	When the user clicks on the documents button, the [item]  documents view is shown.
	When the user clicks on the videos button, the [item]  videos view is shown.
	When the user clicks on the audios button, the [item]  audios view is shown.
	At the bottom of the view there is a toolbar containing only one button: the "List" (modules, exams) button.
	When the user clicks on the "List" button the list of modules of the current [item]  is shown.
 
 */
define("edu/wohaoo/mobile/ItemMenuPageView", [
     "dojo/dom-class", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct", "dijit/registry",  "edu/wohaoo/mobile/ItemPageView", "dojo/i18n!./nls/Messages",
     "dojo/domReady!"
], function(domClass, dojoQuery, dojoConnect, declare, _WidgetBase, domConstruct, dijitRegistry, 
		ItemPageView, sysMessages) {
	return declare([ItemPageView],{
		addFavoriteButton: null,
		sysMessages: sysMessages,
		
		postCreate: function(){
			this.inherited(arguments);
		},
		refresh: function (){
			this.inherited(arguments);
			var self = this;
			dojoQuery(".iconMenu", this.domNode).forEach(function(element){
				if(element._clickEvent)
					dojoConnect.disconnect(element._clickEvent);
				element._clickEvent = dojoConnect.connect(element, "click", function(evt){
					var menuItem = dijitRegistry.byNode(this);
					var moveToWidget = dijitRegistry.byId(menuItem.moveTo);
					moveToWidget.set('dataId', self.dataId);
					moveToWidget.set('model', self.itemModel);
					moveToWidget.refresh();
					self.moveTo(self.id, menuItem.moveTo, menuItem.transition);
					evt.stopPropagation();
					evt.preventDefault();
				});
			});
			
			dojoQuery(".nextButton", this.domNode).forEach(function(element){
				if(element._clickEvent)
					dojoConnect.disconnect(element._clickEvent);
				element._clickEvent = dojoConnect.connect(element, "click", function(evt){
					var menuItem = dijitRegistry.byNode(this);
					var moveToWidget = dijitRegistry.byId(menuItem.moveTo);
					moveToWidget.set('dataId', self.dataId);
					moveToWidget.refresh();
					self.moveTo(self.id, menuItem.moveTo, menuItem.transition);
					evt.stopPropagation();
					evt.preventDefault();
				});
			});
			
			//addFavoriteButton
			dojoQuery(".addFavoriteButton", this.domNode).forEach(function(button){
				if(!self.itemModel || self.itemModel !== 'curriculum') return;
				
				self.addFavoriteButton = button;
				
				var favoriteStr =  "";
				favoriteStr = Sys.getCurriculumFavorites();	
				
				var dataId = self.dataId;
				var regex = dataId + '($|\,)';
				var is_favorite = favoriteStr.match(regex);
				if(is_favorite){
					domClass.add(self.addFavoriteButton, 'favorite');
				}else
				if(domClass.contains(self.addFavoriteButton, "favorite")){
					domClass.remove(self.addFavoriteButton, 'favorite');
				}
				
				if(self.addFavoriteButton._clickEvent)
					self.addFavoriteButton._clickEvent.remove();
				self.addFavoriteButton._clickEvent = null;
				self.addFavoriteButton._clickEvent = dojoConnect.connect(self.addFavoriteButton, "click", function(evt){
					var favoriteStr = Sys.getCurriculumFavorites();
					var favorites = favoriteStr.split(',');
					var notempty= function(elt){ return elt !== "";};
					favorites = favorites.filter(notempty);
					if(domClass.contains(self.addFavoriteButton, "favorite")){
						domClass.remove(self.addFavoriteButton, 'favorite');
						//remove current curriculum from favorites list;
						var notself = function(elt){ return elt !== self.dataId.toString();};
						favorites = favorites.filter(notself);
						favoriteStr = favorites.join(',');
						Sys.setCurriculumFavorites(favoriteStr);
						Sys.message(self.sysMessages.FavoriteRemoved.format(self.itemName));
					}else{
						domClass.add(self.addFavoriteButton, 'favorite');
						//Set curriculum favorite date
						Sys.setCurriculumFavoriteDate(dataId.toString(), new Date().getTime().toString());
						//add current curriculum from favorites list;
						favorites[favorites.length] = self.dataId.toString();
						favoriteStr = favorites.join(',');
						Sys.setCurriculumFavorites(favoriteStr);
						Sys.message(self.sysMessages.FavoriteAdded.format(self.itemName));
					}
					evt.stopPropagation();
					evt.preventDefault();
				});
			});
		}
	
	});
});