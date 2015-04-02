require({cache:{
'url:edu/wohaoo/mobile/templates/EnrollView.html':"<div class=\"enrollContainer\">\n\t<div id=\"enrollDialog\" class=\"enrollDialog\">\n\t\t<div class=\"enrollHeaderPane\">\n\t\t\t<span class=\"company-logo center\">${logoText}</span>\n\t\t</div>\n\t\t<div data-dojo-type=\"dojox/mobile/ContentPane\" class=\"enrollPane mblHidden\" data-dojo-attach-point=\"enrollPane\">\n\t\t\t<div class=\"enrollDescPane\">\n\t\t\t\t<span data-dojo-type=\"dojox/mobile/ContentPane\" data-dojo-attach-point=\"enrollMessage\" \n\t\t\t\tclass=\"text center\">${descText}</span>\n\t\t\t</div>\n\t\t\t<div class=\"amountInputPane\">\n\t\t\t\t<input class='amountInput'  type='text'\n\t\t\t\t  data-dojo-props='readOnly:true, name:\"data[Payment][pay_amount]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${amountText}\" data-dojo-type=\"dojox/mobile/TextBox\"\n\t\t\t\t  data-dojo-attach-point=\"amountInput\"/>\n\t\t\t</div>\n\t\t\t<div class=\"telephoneInputPane\">\n\t\t\t\t<input class='telephoneInput'  type='text'\n\t\t\t\t  data-dojo-props='readOnly:true, name:\"data[Payment][pay_medium]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${telephoneText}\" data-dojo-type=\"dojox/mobile/TextBox\"\n\t\t\t\t  data-dojo-attach-point=\"telephoneInput\"/>\n\t\t\t</div>\n\t\t\t<div class=\"transactionCodeInputPane\">\n\t\t\t\t<input class='transactionCodeInput'  type='text'\n\t\t\t\t  data-dojo-props='name:\"data[Payment][pay_transaction_id]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${transactionCodeText}\" data-dojo-type=\"dojox/mobile/TextBox\" data-dojo-attach-point=\"transactionCode\"/>\n\t\t\t</div>\n\t\t\t<div class=\"enrollButtonPane\">\n\t\t\t\t<button  class='enrollButton mblBlueButton'\n\t\t\t\tdata-dojo-type=\"dojox/mobile/Button\" data-dojo-attach-point=\"enrollButton\">${enrollText}</button>\n\t\t\t</div> \n\t\t</div>\n\t</div>\n</div>"}});
define("edu/wohaoo/mobile/EnrollView", [
    "dijit/registry", "dojo/dom-class", "dojo/on", "dojo/_base/array", "dojo/keys", "dojo/_base/declare", 
    "dojo/parser", "dojo/dom",  "dojo/dom-geometry", "dojo/dom-construct", 
    "dojo/request", "dojo/query!css3", "dojo/dom-style",
     "dijit/_WidgetBase" , "dijit/_AttachMixin", 
    "dijit/_TemplatedMixin", "dijit/_WidgetsInTemplateMixin",
    "dojo/text!./templates/EnrollView.html",
    "dojo/i18n!./nls/EnrollView", "edu/wohaoo/mobile/_AppViewMixin", "dojox/mobile/ScrollableView", 
    "dojox/mobile/ContentPane", "dojox/mobile/Button", "dojox/mobile/TextBox",
    "dojo/domReady!"
], function(dijitRegistry, dojoClass, on, array, keys, declare, parser, dom, dojoGeom, domConstruct,  
				dojoRequest, dojoQuery, domStyle,
				_WidgetBase, _AttachMixin, _TemplatedMixin, _WidgetsInTemplateMixin, 
				template, i18n, _AppViewMixin, MobileScrollableView) {
	return declare([MobileScrollableView,  _AppViewMixin, _TemplatedMixin, _WidgetsInTemplateMixin],{
		templateString: template,
	
		dataId: 0,
		transactionCodeText: "",
		logoText: i18n.logo,
		descText: "",
		amountText: "",
		telephoneText: "",
		enrollText: i18n.enroll,
		delay: 1000,
		redirectTo: "",
		
		enrollPane: null,
		transactionCode: null,
		enrollButton: null,
		
		enrollMessage: null,
		amountInput: null,
		telephoneInput: null,
		transactionCode: null,
		
		//payment
		paymentDest: null,
		_setPaymentDestAttr: function(d){
			this.telephoneInput.domNode.placeholder = d;
		},
		paymentMessage: null,
		_setPaymentMessageAttr: function(m){
			this.enrollMessage.containerNode.innerHTML = m;
		}, 
		paymentAmount: null,
		_setPaymentAmountAttr: function(a){
			this.amountInput.domNode.placeholder = a;
		}, 
		
		paymentInput: null,
		_setPaymentInputAttr: function(d){
			this.transactionCode.domNode.placeholder = d;
		}, 
		
		postCreate: function(){
			this.inherited(arguments);
			var self = this;
			on(this.enrollButton.domNode, 'click', function(e){
				console.debug("enroll clicked");
				var transactionCode = self.transactionCode.get('value');
				self.__doEnroll(transactionCode);
				e.stopPropagation();
				e.preventDefault(); 
			});
		},
		
		startup: function(){
			this.inherited(arguments);
		},
		
		__doEnroll: function(transactionCode){
			var self = this;
			console.debug("creating new student enrollment");
			dojoRequest.post(this.serviceUrl, {
				data: {
					"data[Curriculum][id]":  self.dataId,
					"data[Payment][pay_transaction_id]":  transactionCode
				},
				handleAs: 'json'
			}).then(function(response){
				console.debug("enroll request returned");
				if(!response.studentid || !response.courseid ){
					console.debug("there was an error during enrollment:" + response.error);
					Sys.message(response.error);
				}
				else{
					console.debug("enrollment was successfull");
					var redirectToWidget = dijitRegistry.byId(self.redirectTo);
					redirectToWidget.refresh();
					redirectToWidget.animate();
					self.moveTo(self.id, self.redirectTo);
				}
			}, function(error){
				self.alert(self.errors.err101);
				console.debug("error occurred:" + error.toString());
			});
		},
		
		animate: function(){
			var self = this;
			if(!dojoClass.contains(this.enrollPane.domNode, 'mblHidden')){
				dojoClass.add(this.enrollPane.domNode, 'mblHidden');
			}
			
			dojoQuery(".company-logo", this.domNode).forEach(function(element){
				if(!dojoClass.contains(element, 'center')){
					dojoClass.add(element, 'center');
				}
			});
			setTimeout(function(){
				if(dojoClass.contains(self.enrollPane.domNode, 'mblHidden')){
					dojoClass.remove(self.enrollPane.domNode, 'mblHidden');
				}
				
				dojoQuery(".company-logo", self.domNode).forEach(function(element){
					dojoClass.remove(element, 'center');
				});
			}, this.delay);
		},
		
		destroyDescendants: function(/*Boolean*/ preserveDom){
			this.inherited(arguments);
		},

		destroy: function(){
			this.inherited(arguments);
		}
	});

});