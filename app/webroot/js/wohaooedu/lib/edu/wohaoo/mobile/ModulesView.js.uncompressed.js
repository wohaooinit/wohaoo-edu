/**
 * SPECS
	The view shows the list of modules of a given curriculum.
	When the user clicks on the back button, the curriculum menu page is shown.
 */
define("edu/wohaoo/mobile/ModulesView", [
    "dojo/_base/array", "dijit/registry",  "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/ItemPageView", "dojox/mobile/ListItem",
     "dojo/domReady!"
], function(dojoArray, dijitRegistry, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, ItemPageView, MobileListItem) {
	return declare([ItemPageView],{
		listNode: null,
		
		courseId: null,
		paymentDest: null,
		paymentAmount: null,
		paymentMessage: null,
		paymentInput: null,
		
		/**
		 storeItem => {
			model => 'module',
			id,
			icon,
			name,
			passed
		 }
		 */
		 
		postCreate: function(){
			this.itemTemplateString =  '';
			this.itemClass = 'module';
			this.itemMoveTo = "modulePage";
			this.itemTransition = 'slide';
			this.inherited(arguments);
		},
		
		refresh: function (){
			this.inherited(arguments);
			if(!this._store || this._store === null)
				return;
			var self = this;
			dojoQuery(".itemList", this.domNode).forEach(function(element){
				self.listNode = element;
			});
				
			this.query = {id: this.itemPrefix + this.dataId, model: "curriculum"};
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(curItems){
				dojoArray.forEach(curItems, function(curItem, index){
					self.courseId = self._store.getValue(curItem, "course_id");
					self.paymentDest = self._store.getValue(curItem, "payment_dest");
					self.paymentAmount = self._store.getValue(curItem, "enroll_fees");
					self.paymentMessage = self._store.getValue(curItem, "payment_message");
					self.paymentInput = self._store.getValue(curItem, "payment_input");
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
			
			this.query = {id: "*", model: "module"};
			
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
					});
					
					var passed = self._store.getValue(storeItem, "passed");
					item.passed = 0;
					
					if(Sys.getUser()){
						if(passed)
							item.set('checked', 'true');
						item.passed = passed;
					}
					
					item.previous = self._store.getValue(storeItem, "previous");
					item.previous_passed = self._store.getValue(storeItem, "previous_passed");
					
					item.placeAt(self.listNode,"last");
					
					var name = self._store.getValue(storeItem, "name");
					
					item.labelNode.innerText = name;
					
					if(item.domNode._clickEvent)
						dojoConnect.disconnect(item.domNode._clickEvent);
					item.domNode._clickEvent = dojoConnect.connect(item.domNode, 'click', function(evt){
						var user = Sys.getUser();
						var itemWidget = dijitRegistry.byNode(this);
						if(!user){
							evt.stopPropagation();
							evt.preventDefault();
							dijitRegistry.byId('dlg_login_register').show();
							return;
						}else{
							if(!self.courseId){
								evt.stopPropagation();
								evt.preventDefault();
								
								dijitRegistry.byId('enroll').set('paymentDest', self.paymentDest);
								dijitRegistry.byId('enroll').set('paymentMessage', self.paymentMessage);
								dijitRegistry.byId('enroll').set('paymentAmount', self.paymentAmount);
								dijitRegistry.byId('enroll').set('paymentInput', self.paymentInput);
								
								dijitRegistry.byId('dlg_join_curriculum').show();
							}else{
								if(itemWidget.passed){
									//module is already pased
									//don't show it
									dijitRegistry.byId('dlg_message').show(self.messages.MODULE_IS_COMPLETED);
								}else{
									debugger;
									if(itemWidget.previous && !itemWidget.previous_passed){
										dijitRegistry.byId('dlg_message').show(
												self.messages.PREVIOUS_MODULE_MUST_BE_COMPLETED.format(
												itemWidget.previous) );
									}else{
										var moveToWidget = dijitRegistry.byId(self.itemMoveTo);
										moveToWidget.set('dataId', itemWidget.dataId);
										moveToWidget.refresh();
										self.moveTo(self.id, self.itemMoveTo, self.itemTransition);
									}
								}
							}
						}
					});
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
		},
		
		showView: function(dialog, viewName, transition){
			dijitRegistry.byId(dialog).hide();
			var moveToWidget = dijitRegistry.byId(viewName);
			moveToWidget.set('redirectTo', this.id);
			moveToWidget.set('dataId', this.dataId);
			moveToWidget.animate();
			this.moveTo(this.id, viewName, transition);
		}
	});
});