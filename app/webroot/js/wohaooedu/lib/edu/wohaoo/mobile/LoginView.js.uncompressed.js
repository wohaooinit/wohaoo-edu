require({cache:{
'url:edu/wohaoo/mobile/templates/LoginView.html':"<div class=\"loginContainer\">\n\t<div id=\"loginDialog\" class=\"loginDialog\">\n\t\t<div class=\"loginHeaderPane\">\n\t\t\t<span class=\"company-logo center\">${logoText}</span>\n\t\t</div>\n\t\t<div data-dojo-type=\"dojox/mobile/ContentPane\" class=\"loginPane mblHidden\" data-dojo-attach-point=\"loginPane\">\n\t\t\t<div class=\"emailInputPane\">\n\t\t\t\t<input class='emailInput'  type='email'\n\t\t\t\t  data-dojo-props='name:\"data[User][usr_telephone]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${telephoneText}\" data-dojo-type=\"dojox/mobile/TextBox\" data-dojo-attach-point=\"telephone\"/>\n\t\t\t</div> \n\t\t\t<div class=\"passwordInputPane\">\n\t\t\t\t<input class='passwordInput' placeholder=\"${passwordText}\" \n\t\t\t\t\t\t\tdata-dojo-props='name:\"data[User][usr_password]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t\t\t\tdata-dojo-type=\"dojox/mobile/TextBox\" type=\"password\" data-dojo-attach-point=\"password\"/>\n\t\t\t</div>\n\t\t\t<div class=\"loginButtonPane\">\n\t\t\t\t<button  class='loginButton'\n\t\t\t\tdata-dojo-type=\"dojox/mobile/Button\" data-dojo-attach-point=\"loginButton\">${loginText}</button>\n\t\t\t</div> \n\t\t</div>\n\t</div>\n</div>"}});
define("edu/wohaoo/mobile/LoginView", [
    "dijit/registry", "dojo/dom-class", "dojo/on", "dojo/_base/array", "dojo/keys", "dojo/_base/declare", 
    "dojo/parser", "dojo/dom",  "dojo/dom-geometry", "dojo/dom-construct", 
    "dojo/request", "dojo/query!css3", "dojo/dom-style",
     "dijit/_WidgetBase" , "dijit/_AttachMixin", 
    "dijit/_TemplatedMixin", "dijit/_WidgetsInTemplateMixin",
    "dojo/text!./templates/LoginView.html",
    "dojo/i18n!./nls/LoginView", "edu/wohaoo/mobile/_AppViewMixin", "dojox/mobile/ScrollableView", 
    "dojox/mobile/ContentPane", "dojox/mobile/Button", "dojox/mobile/TextBox",
    "dojo/domReady!"
], function(dijitRegistry, dojoClass, on, array, keys, declare, parser, dom, dojoGeom, domConstruct,  
				dojoRequest, dojoQuery, domStyle,
				_WidgetBase, _AttachMixin, _TemplatedMixin, _WidgetsInTemplateMixin, 
				template, i18n, _AppViewMixin, MobileScrollableView) {
	return declare([MobileScrollableView,  _AppViewMixin, _TemplatedMixin, _WidgetsInTemplateMixin],{
		templateString: template,
		telephoneText: i18n.telephone,
		passwordText: i18n.password,
		logoText: __t("WOHAOO Education"),
		loginText: i18n.login,
		delay: 1500,
		redirectTo: "",
		
		loginPane: null,
		telephone: null,
		password: null,
		loginButton: null,
		
		postCreate: function(){
			this.inherited(arguments);
			var self = this;
			on(this.loginButton.domNode, 'click', function(e){
				console.debug("login clicked");
				var telephone = self.telephone.get('value');
				var password = self.password.get('value');
				self.__doLogin(telephone, password);
				e.stopPropagation();
				e.preventDefault(); 
			});
		},
		
		startup: function(){
			this.inherited(arguments);
		},
		
		__doLogin: function(telephone, password){
			var self = this;
			console.debug("authenticating user against url:" + this.serviceUrl);
			dojoRequest.post(this.serviceUrl, {
				data: {
					"data[User][usr_telephone]":  telephone,
					"data[User][usr_password]": password
				},
				handleAs: 'json'
			}).then(function(response){
				console.debug("auth request returned");
				if(!response.userid){
					console.debug("there was an error during login:" + response.toString());
					Sys.message(response.error);
				}
				else{
					console.debug("login was successfull");
					Sys.setUser(response.userid);
					var redirectToWidget = dijitRegistry.byId(self.redirectTo);
					redirectToWidget.refresh();
					self.moveTo(self.id, self.redirectTo);
				}
			}, function(error){
				self.alert(self.errors.err101);
			});
		},
		
		animate: function(){
			var self = this;
			if(!dojoClass.contains(this.loginPane.domNode, 'mblHidden')){
				dojoClass.add(this.loginPane.domNode, 'mblHidden');
			}
			
			dojoQuery(".company-logo", this.domNode).forEach(function(element){
				if(!dojoClass.contains(element, 'center')){
					dojoClass.add(element, 'center');
				}
			});
			setTimeout(function(){
				if(dojoClass.contains(self.loginPane.domNode, 'mblHidden')){
					dojoClass.remove(self.loginPane.domNode, 'mblHidden');
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