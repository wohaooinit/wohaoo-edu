/**
	SPECS:
	The view shows the list of videos of the current curriculum. One video under the other.
	When the user clicks on the back button, the curriculum menu page is shown.
   */
define("edu/wohaoo/mobile/ItemVideoView", [
    "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/ItemPageView", "dojox/mobile/ListItem",
    "dojox/mobile/compat", "dojox/mobile/Video",
     "dojo/domReady!"
], function(dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, ItemPageView, MobileListItem, MobileCompat, MobileVideo) {
	return declare([ItemPageView],{
		listNode: null,
		
		/**
		 storeItem => {
			model => 'video',
			id,
			mp4,
			ogv,
			webm,
			embed,
			text
		 }
		 */
		 
		postCreate: function(){
			this.itemTemplateString =  '<source src="${mp4}" type="video/mp4">'+
		'<source src="${ogv}" type="video/ogg">'+
		'<source src="${webm}" type="video/webm">'+
		'<p>${text}</p>';
			this.itemClass = 'video';
			
			this.inherited(arguments);
		},
		
		refresh: function (){
			this.inherited(arguments);
			if(!this._store || this._store === null)
				return;
			this.query = {id: "*", model: "video"};
			var self = this;
			
			dojoQuery(".itemList", this.domNode).forEach(function(element){
				self.listNode = element;
			});
			
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(storeItems){
				dojoArray.forEach(storeItems, function(storeItem, index){
					// Create a new list item, inject into list
					var item = new MobileListItem({
						"class": self.itemClass,
						"dataId": self._store.getValue(storeItem, "uniqueid"),
						'variableHeight': true
					}).placeAt(self.listNode,"first");
					
					var embed = self._store.getValue(storeItem, "embed");
					if(!embed){
						var videoItem = new MobileVideo({
							"width":  320,
							"height" : 240
						}).placeAt(item.domNode, 'first');
						// Update the video item's content using our template for items
						videoItem.domNode.innerHTML = self.substitute(self.itemTemplateString, storeItem);
					}else{
						item.domNode.innerHTML = embed;
					}
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				0 && console.debug('error=' + error.toString());
			}});
		}
	
	});
});