require({cache:{
'url:edu/wohaoo/mobile/templates/LogoutView.html':"<div class=\"logoutContainer\">\n\t<div id=\"logoutDialog\" class=\"logoutDialog\">\n\t\t<div class=\"logoutHeaderPane\">\n\t\t\t<span class=\"company-logo center\">${logoText}</span>\n\t\t</div>\n\t\t<div data-dojo-type=\"dojox/mobile/ContentPane\" class=\"logoutPane mblHidden\" data-dojo-attach-point=\"logoutPane\">\n\t\t\t<div class=\"logoutButtonPane\">\n\t\t\t\t<button  class='logoutButton'\n\t\t\t\tdata-dojo-type=\"dojox/mobile/Button\" data-dojo-attach-point=\"logoutButton\">${logoutText}</button>\n\t\t\t</div> \n\t\t</div>\n\t</div>\n</div>"}});
define("edu/wohaoo/mobile/LogoutView", [
    "dijit/registry", "dojo/dom-class", "dojo/on", "dojo/_base/array", "dojo/keys", "dojo/_base/declare", 
    "dojo/parser", "dojo/dom",  "dojo/dom-geometry", "dojo/dom-construct", 
    "dojo/request", "dojo/query!css3", "dojo/dom-style",
     "dijit/_WidgetBase" , "dijit/_AttachMixin", 
    "dijit/_TemplatedMixin", "dijit/_WidgetsInTemplateMixin",
    "dojo/text!./templates/LogoutView.html",
    "dojo/i18n!./nls/LogoutView", "edu/wohaoo/mobile/_AppViewMixin", "dojox/mobile/ScrollableView", 
    "dojox/mobile/ContentPane", "dojox/mobile/Button", "dojox/mobile/TextBox",
    "dojo/domReady!"
], function(dijitRegistry, dojoClass, on, array, keys, declare, parser, dom, dojoGeom, domConstruct,  
				dojoRequest, dojoQuery, domStyle,
				_WidgetBase, _AttachMixin, _TemplatedMixin, _WidgetsInTemplateMixin, 
				template, i18n, _AppViewMixin, MobileScrollableView) {
	return declare([MobileScrollableView,  _AppViewMixin, _TemplatedMixin, _WidgetsInTemplateMixin],{
		templateString: template,
		logoText: "WOHAOO Education",
		logoutText: i18n.logout,
		delay: 100,
		redirectTo: "",
		
		logoutPane: null,
		logoutButton: null,
		
		postCreate: function(){
			this.inherited(arguments);
			var self = this;
			on(this.logoutButton.domNode, 'click', function(e){
				0 && console.debug("logout clicked");
				var userid = Sys.getUser();
				self.__doLogout(userid);
				e.stopPropagation();
				e.preventDefault(); 
			});
		},
		
		startup: function(){
			this.inherited(arguments);
		},
		
		__doLogout: function(userid){
			var self = this;
			0 && console.debug("logging user out");
			
			function logOut(){
				Sys.setUser("");
				var redirectToWidget = dijitRegistry.byId(self.redirectTo);
				redirectToWidget.refresh();
				self.moveTo(self.id, self.redirectTo);
			}
			
			dojoRequest.post(this.serviceUrl, {
				data: {
					"data[User][id]":  userid
				},
				handleAs: 'json'
			}).then(function(response){
				0 && console.debug("logout request returned");
				if(response.error){
					0 && console.debug("there was an error during  logout:" + response.error);
					Sys.message(response.error);
				}
				else{
					0 && console.debug("logout was successfull");
					logOut();
				}
			}, function(error){
				self.alert(self.errors.err101);
			});
		},
		
		animate: function(){
			var self = this;
			if(!dojoClass.contains(this.logoutPane.domNode, 'mblHidden')){
				dojoClass.add(this.logoutPane.domNode, 'mblHidden');
			}
			
			dojoQuery(".company-logo", this.domNode).forEach(function(element){
				if(!dojoClass.contains(element, 'center')){
					dojoClass.add(element, 'center');
				}
			});
			setTimeout(function(){
				if(dojoClass.contains(self.logoutPane.domNode, 'mblHidden')){
					dojoClass.remove(self.logoutPane.domNode, 'mblHidden');
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