/**
 *<!-- SPECS:
	The view shows the list of attributes of the current [item].
	When the user clicks on the back button, the [item] menu page is shown.
*/
define("edu/wohaoo/mobile/ItemInfoView", [
    "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/ItemPageView", "dojox/mobile/ListItem",
     "dojo/domReady!"
], function(dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, ItemPageView, MobileListItem) {
	return declare([ItemPageView],{
		listNode: null,
		postCreate: function(){
			this.itemTemplateString = '<div class="attributeName">${name}</div>' +
				'<div class="attributeValue">${value}</div>' ;
			this.itemClass = 'attribute';
			this.inherited(arguments);
		},
		
		refresh: function (){
			this.inherited(arguments);
			if(!this._store || this._store === null)
				return;
			this.query = {id: "*", model: "attribute"};
			var self = this;
			
			dojoQuery(".itemList", this.domNode).forEach(function(element){
				self.listNode = element;
			});
			/**
			 storeItem => {
			 	model => 'attribute',
			 	id,
			 	name,
			 	value
			 }
			 */
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
				
					// Update the list item's content using our template for items
					item.labelNode.innerHTML = self.substitute(self.itemTemplateString, storeItem);
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
		}
	
	});
});