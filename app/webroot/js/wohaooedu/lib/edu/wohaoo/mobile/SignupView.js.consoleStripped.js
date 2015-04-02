require({cache:{
'url:edu/wohaoo/mobile/templates/SignupView.html':"<div class=\"signupContainer\">\n\t<div id=\"signupDialog\" class=\"signupDialog\">\n\t\t<div class=\"signupHeaderPane\">\n\t\t\t<span class=\"company-logo center\">${logoText}</span>\n\t\t</div>\n\t\t<div data-dojo-type=\"dojox/mobile/ContentPane\" class=\"signupPane mblHidden\" data-dojo-attach-point=\"signupPane\">\n\t\t\t<div class=\"firstNameInputPane\">\n\t\t\t\t<input class='firstNameInput'  type='text'\n\t\t\t\t  data-dojo-props='name:\"data[Person][per_first_name]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${firstNameText}\" data-dojo-type=\"dojox/mobile/TextBox\" data-dojo-attach-point=\"firstName\"/>\n\t\t\t</div>\n\t\t\t<div class=\"lastNameInputPane\">\n\t\t\t\t<input class='lastNameInput'  type='text'\n\t\t\t\t  data-dojo-props='name:\"data[Person][per_last_name]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${lastNameText}\" data-dojo-type=\"dojox/mobile/TextBox\" data-dojo-attach-point=\"lastName\"/>\n\t\t\t</div>  \n\t\t\t<div class=\"telInputPane\">\n\t\t\t\t<input class='telInput'  type='text'\n\t\t\t\t  data-dojo-props='name:\"data[User][usr_telephone]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${telephoneText}\" data-dojo-type=\"dojox/mobile/TextBox\" data-dojo-attach-point=\"telephone\"/>\n\t\t\t</div> \n\t\t\t<div class=\"passwordInputPane\">\n\t\t\t\t<input class='passwordInput' placeholder=\"${passwordText}\" \n\t\t\t\t\t\t\tdata-dojo-props='name:\"data[User][usr_password]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t\t\t\tdata-dojo-type=\"dojox/mobile/TextBox\" type=\"text\" data-dojo-attach-point=\"password\"/>\n\t\t\t</div>\n\t\t\t<div class=\"signupButtonPane\">\n\t\t\t\t<button  class='signupButton'\n\t\t\t\tdata-dojo-type=\"dojox/mobile/Button\" data-dojo-attach-point=\"signupButton\">${signupText}</button>\n\t\t\t</div> \n\t\t</div>\n\t</div>\n</div>"}});
define("edu/wohaoo/mobile/SignupView", [
    "dijit/registry", "dojo/dom-class", "dojo/on", "dojo/_base/array", "dojo/keys", "dojo/_base/declare", 
    "dojo/parser", "dojo/dom",  "dojo/dom-geometry", "dojo/dom-construct", 
    "dojo/request", "dojo/query!css3", "dojo/dom-style",
     "dijit/_WidgetBase" , "dijit/_AttachMixin", 
    "dijit/_TemplatedMixin", "dijit/_WidgetsInTemplateMixin",
    "dojo/text!./templates/SignupView.html",
    "dojo/i18n!./nls/SignupView", "edu/wohaoo/mobile/_AppViewMixin", "dojox/mobile/ScrollableView", 
    "dojox/mobile/ContentPane", "dojox/mobile/Button", "dojox/mobile/TextBox",
    "dojo/domReady!"
], function(dijitRegistry, dojoClass, on, array, keys, declare, parser, dom, dojoGeom, domConstruct,  
				dojoRequest, dojoQuery, domStyle,
				_WidgetBase, _AttachMixin, _TemplatedMixin, _WidgetsInTemplateMixin, 
				template, i18n, _AppViewMixin, MobileScrollableView) {
	return declare([MobileScrollableView,  _AppViewMixin, _TemplatedMixin, _WidgetsInTemplateMixin],{
		templateString: template,
		messages: i18n,
		firstNameText: i18n.firstName,
		lastNameText: i18n.lastName,
		telephoneText: i18n.telephone,
		passwordText: i18n.password,
		logoText: __t("WOHAOO Education"),
		signupText: i18n.signup,
		delay: 1000,
		redirectTo: "",
		signupPane: null,
		firstName: null,
		lastName: null,
		telephone: null,
		password: null,
		signupButton: null,
		
		postCreate: function(){
			this.inherited(arguments);
			var self = this;
			on(this.signupButton.domNode, 'click', function(e){
				0 && console.debug("signup clicked");
				var firstName = self.firstName.get('value');
				var lastName = self.lastName.get('value');
				var telephone = self.telephone.get('value');
				var password = self.password.get('value');
				
				if(!firstName || !lastName || !telephone || !password){
					dijitRegistry.byId('dlg_message').show(self.messages.INVALID_INPUT_PARAMETERS);
					e.stopPropagation();
					e.preventDefault(); 
					return false;
				}
				
				self.__doSignup(firstName, lastName, telephone, password);
				e.stopPropagation();
				e.preventDefault(); 
				return true;
			});
		},
		
		startup: function(){
			this.inherited(arguments);
		},
		
		__doSignup: function(firstName, lastName, telephone, password){
			var self = this;
			0 && console.debug("creating new user");
			dojoRequest.post(this.serviceUrl, {
				data: {
					"data[User][usr_telephone]":  telephone,
					"data[User][usr_password]": password,
					"data[Person][per_first_name]":  firstName,
					"data[Person][per_last_name]":  lastName
				},
				handleAs: 'json'
			}).then(function(response){
				0 && console.debug("signup request returned");
				if(!response.userid){
					0 && console.debug("there was an error during signup:" + response.error);
					dijitRegistry.byId('dlg_message').show(response.error);
				}
				else{
					0 && console.debug("signup was successfull");
					var redirectToWidget = dijitRegistry.byId(self.redirectTo);
					redirectToWidget.refresh();
					redirectToWidget.animate();
					self.moveTo(self.id, self.redirectTo);
				}
			}, function(error){
				self.alert(self.errors.err101);
				0 && console.debug("error occurred:" + error.toString());
			});
		},
		
		animate: function(){
			var self = this;
			if(!dojoClass.contains(this.signupPane.domNode, 'mblHidden')){
				dojoClass.add(this.signupPane.domNode, 'mblHidden');
			}
			
			dojoQuery(".company-logo", this.domNode).forEach(function(element){
				if(!dojoClass.contains(element, 'center')){
					dojoClass.add(element, 'center');
				}
			});
			setTimeout(function(){
				if(dojoClass.contains(self.signupPane.domNode, 'mblHidden')){
					dojoClass.remove(self.signupPane.domNode, 'mblHidden');
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