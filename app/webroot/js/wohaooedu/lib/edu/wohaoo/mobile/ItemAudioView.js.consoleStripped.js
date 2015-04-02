/**
 *SPECS:
	The view shows the list of audios of the current curriculum. One audio under the other.
	When the user clicks on the back button, the curriculum menu page is shown.
 */
define("edu/wohaoo/mobile/ItemAudioView", [
    "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/ItemPageView", "dojox/mobile/ListItem",
    "dojox/mobile/compat", "dojox/mobile/Audio",
     "dojo/domReady!"
], function(dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, ItemPageView, MobileListItem, MobileCompat, MobileAudio) {
	return declare([ItemPageView],{
		listNode: null,
		
		/**
		 storeItem => {
			model => 'audio',
			id,
			mp3,
			ogg,
			wav,
			embed,
			text
		 }
		 */
		 
		postCreate: function(){
			this.itemTemplateString =  '<source src="${mp3}" type="audio/mpeg">' +
		'<source src="${ogg}" type="audio/ogg">' +
		'<source src="${wav}" type="audio/wav">' +
		'<p>${text}</p>';
			this.itemClass = 'audio';
			
			this.inherited(arguments);
		},
		
		refresh: function (){
			this.inherited(arguments);
			if(!this._store || this._store === null)
				return;
			this.query = {id: "*", model: "audio"};
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
						var audioItem = new MobileAudio({}).placeAt(item.domNode, 'first');
				
						// Update the audio item's content using our template for items
						audioItem.domNode.innerHTML = self.substitute(self.itemTemplateString, storeItem);
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