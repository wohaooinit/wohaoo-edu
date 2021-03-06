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
define("edu/wohaoo/mobile/MessageDialog", [
    "dojo/dom-class", "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "dojox/mobile/SimpleDialog", "dijit/registry",  "dojo/i18n!./nls/Messages",
     "dojo/domReady!"
], function(domClass, dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, SimpleDialog, dijitRegistry, messages) {
	return declare([SimpleDialog],{
		mblSimpleDialogTitleNode: null,
		mblSimpleDialogTextNode: null,
		mblSimpleDialogButtonNode: null,
		
		messages: messages,
		 
		postCreate: function(){
			var self = this;
			
			//showFavoritesButton
			dojoQuery(".mblSimpleDialogTitle", this.domNode).forEach(function(element){
				self.mblSimpleDialogTitleNode = element;
				self.mblSimpleDialogTitleNode.innerHTML = self.messages.Information;
			});
			
			dojoQuery(".mblSimpleDialogText", this.domNode).forEach(function(element){
				self.mblSimpleDialogTextNode = element;
			});
			
			dojoQuery(".mblSimpleDialogButton", this.domNode).forEach(function(element){
				self.mblSimpleDialogButtonNode = element;
				self.mblSimpleDialogTitleNode.innerHTML = self.messages.OK;
			});
			
			this.inherited(arguments);
		},
		
		show: function(message){
			this.mblSimpleDialogTextNode.innerHTML = message;
			this.inherited(arguments);
		}
	});
});