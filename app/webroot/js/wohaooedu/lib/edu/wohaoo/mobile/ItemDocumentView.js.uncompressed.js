/**
 *SPECS:
	The view shows the list of documents of the current curriculum. One document under the other.
	When the user clicks on the back button, the curriculum menu page is shown.
  */
define("edu/wohaoo/mobile/ItemDocumentView", [
    "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/ItemPageView", "dojox/mobile/ListItem",
     "dojo/domReady!"
], function(dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, ItemPageView, MobileListItem) {
	return declare([ItemPageView],{
		listNode: null,
		
		/**
		 storeItem => {
			model => 'document',
			source
		 }
		 */
		 
		postCreate: function(){
			this.itemTemplateString =  '<iframe id="viewer" src ="${source}" ' +
    	     			'width="320" height="240"' + 
    	     			'allowfullscreen webkitallowfullscreen></iframe>';
			this.itemClass = 'audio';
			
			this.inherited(arguments);
		},
		
		refresh: function (){
			this.inherited(arguments);
			if(!this._store || this._store === null)
				return;
			this.query = {id: "*", model: "document"};
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
					
					var pdf = self._store.getValue(storeItem, "pdf");
					
					var embed = self._store.getValue(storeItem, "embed");

					if(pdf){
						 storeItem.source = '/ViewerJS/#' + pdf;
						// Update the audio item's content using our template for items
						item.labelNode.innerHTML = self.substitute(self.itemTemplateString, storeItem);
					}else{				
						// Update the audio item's content using our template for items
						item.labelNode.innerHTML = embed;
					}
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
		}
	
	});
});