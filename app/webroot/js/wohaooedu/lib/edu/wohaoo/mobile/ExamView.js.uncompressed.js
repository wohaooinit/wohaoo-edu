/** SPECS
	The view shows the exam of the given module.
	The server will check if the user has already completed the exam.
	If the user has already completed the exam, the view shows the users score along with the exam comment (passed, failed).
		The user is then asked wether he/she would want to retake the exam. If yes, a new exam session is created and
		the first question of the exam is runned.
	If the user has not already  already completed the exam, the first question of the exam session is returned and
	the list of possible answers (options) is displayed.
	When the user clicks on the "Next Question" Button, the view is refreshed.
 */
define("edu/wohaoo/mobile/ExamView", [
    "dojo/dom-class", "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/ItemPageView", "dojox/mobile/ListItem", "dijit/registry",
    "dojo/i18n!./nls/Messages",
     "dojo/domReady!"
], function(domClass, dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, ItemPageView, MobileListItem, dijitRegistry, messages) {
	return declare([ItemPageView],{
		listNode: null,
		sessionId:  0,
		questionId: 0,
		optionId: 0,
		forceNew: 0,
		completed: false,
		messages: messages,
		
		/**
		 examItem => {
			model => 'exam',
			id,
			score,
			passed,
			completed
		 }
		 */
		 
		 /**
		 questionItem => {
			model => 'question',
			id,
			text
		 }
		 */
		 
		  /**
		 optionItem => {
			model => 'option',
			id,
			text
		 }
		 */
		questionNode: null,
		examDetailsNode: null,
		examScoreNode: null,
		examCommentNode: null,
		examNextButtonNode: null,
		examRetakeButtonNode: null,
		 
		postCreate: function(){
			this.itemTemplateString =  '';
			this.itemClass = 'question';
			var self = this;
			
			//showFavoritesButton
			dojoQuery(".questionCategory", this.domNode).forEach(function(element){
				self.questionNode = element;
			});
			
			dojoQuery(".examNextButton", this.domNode).forEach(function(element){
				self.examNextButtonNode = element;
			});
			
			dojoQuery(".examRetakeButton", this.domNode).forEach(function(element){
				self.examRetakeButtonNode = element;
			});
			
			dojoQuery(".examDetails", this.domNode).forEach(function(element){
				self.examDetailsNode = element;
			});
			
			
			dojoQuery("span.examScore", self.examDetailsNode ).forEach(function(element){
				self.examScoreNode = element;
			});
			
			
			dojoQuery("span.examComment", self.examDetailsNode ).forEach(function(element){
				self.examCommentNode = element;
			});
			
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
			
			self.completed  = false;
			self.forceNew = 0;
			self.sessionid = 0;
			self.questionId = 0;
			self.optionId = 0;
			
			this.query = {id: "*", model: "exam"};
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(examItems){
				dojoArray.forEach(examItems, function(examItem, index){
					self.sessionId = self._store.getValue(examItem, "uniqueid");
					var text = self._store.getValue(examItem, "text");
					self.completed = self._store.getValue(examItem, "completed");
					
					domClass.remove(self.questionNode, 'mblHidden');
					domClass.remove(self.examNextButtonNode , 'mblHidden');
							
					debugger;
					if(self.completed){
						domClass.add(self.questionNode, 'mblHidden');
						domClass.add(self.examNextButtonNode , 'mblHidden');
							
						if(domClass.contains(self.examDetailsNode, "mblHidden")){
							var score = self._store.getValue(examItem, "score");
							self.examScoreNode.innerText = score.toString();
							var passed = self._store.getValue(examItem, "passed");
							if(passed)
								self.examCommentNode.innerText = "Passed";
							else
								self.examCommentNode.innerText = "Failed";
							domClass.remove(self.examDetailsNode, 'mblHidden');
						}
					}else{
						if(!domClass.contains(self.examDetailsNode, "mblHidden")){
							domClass.add(self.examDetailsNode, 'mblHidden');
						}
					}
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
			
			this.query = {id: "*", model: "question"};
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(questionItems){
				dojoArray.forEach(questionItems, function(questionItem, index){
					self.questionId = self._store.getValue(questionItem, "uniqueid");
					var text = self._store.getValue(questionItem, "text");
					self.questionNode.innerHTML = text;
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
			
			
			this.query = {id: "*", model: "option"};
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(optionItems){
				dojoArray.forEach(optionItems, function(optionItem, index){
					// Create a new list item, inject into list
					var item = new MobileListItem({
						"class": self.itemClass,
						"dataId": self._store.getValue(optionItem, "uniqueid"),
						'variableHeight': true
					});
					
					item.placeAt(self.listNode,"first");
					
					var text = self._store.getValue(optionItem, "text");
					
					item.labelNode.innerHTML = text;
					
					if(item._checkEvent){
						item._checkEvent.remove();
						item._checkEvent = null;
					}
					item._checkEvent = dojoConnect.connect(item.domNode, 'click', 
						function(e){
							var itemWidget = dijitRegistry.byNode(this);
							self.optionId = itemWidget.dataId;
						});
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
			
			//next question button
			if(self.examNextButtonNode._clickEvent){
				self.examNextButtonNode._clickEvent.remove();
				self.examNextButtonNode._clickEvent = null;
			}
			
			self.examNextButtonNode._clickEvent = dojoConnect.connect(self.examNextButtonNode, 'click', function(e){
				if(!self.completed){
					domClass.remove(self.questionNode, 'mblHidden');
					domClass.remove(self.examNextButtonNode , 'mblHidden');
					if(self.optionId){
						self.examNextButtonNode._clickEvent.remove();
						self.examNextButtonNode._clickEvent = null;
						self.refresh();
					}
				}else{
					dijitRegistry.byId('dlg_message').show(self.messages.EXAM_IS_COMPLETED);
				}
			});
			
			//retake  button
			if(self.examRetakeButtonNode._clickEvent){
				self.examRetakeButtonNode._clickEvent.remove();
				self.examRetakeButtonNode._clickEvent = null;
			}
			self.examRetakeButtonNode._clickEvent = dojoConnect.connect(self.examRetakeButtonNode, 'click', function(e){
				dijitRegistry.byId('dlg_confirm').show(self.messages.CONFIRM_EXAM_RETAKE, function(){
								domClass.remove(self.questionNode, 'mblHidden');
								domClass.remove(self.examNextButtonNode , 'mblHidden');
								self.forceNew = 1;
								
								self.examRetakeButtonNode._clickEvent.remove();
								self.examRetakeButtonNode._clickEvent = null;
				
								self.refresh();
								});
			});
		}
	
	});
});