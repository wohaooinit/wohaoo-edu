String.prototype.format = function () {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function (match, number) {
        return typeof args[number] != 'undefined'
      ? args[number].toString()
      : match
    });
};

define("edu/wohaoo/mobile/_AppViewMixin", [
   "dojo/query!css3", "dojo/on", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct", "dijit/registry", "dojox/mobile/Overlay", "dojo/i18n!./nls/Errors",
      "dojo/i18n!./nls/Messages", "dojo/domReady!"
], function(dojoQuery, on, dojoConnect, declare, _WidgetBase, domConstruct, dijitRegistry, 
	MobileOverlay, sysErrors, sysMessages) {
	return declare([_WidgetBase],{
		dataParams: null,
		serviceUrl: null,
		errors: sysErrors,
		messages: sysMessages,
		
		_setServiceUrlAttr: function(/*String*/ url){
		 	 if(typeof Sys !== 'undefined'){
		 	 	var httpScheme = Sys.getHttpScheme();
		 	 	var httpHost = Sys.getHttpHost();
		 	 	if(httpScheme && httpHost){
		 	 		url = httpScheme + "://" + httpHost  + url;
		 	 	}
		 	 }
			 this.serviceUrl = url;
		 },
		query: {},
		dataParams: [],
		dataId: false,
		busy: false,
		alert: function(str){
			if(typeof Sys !== 'undefined')
				Sys.message(str);
			else
				alert(str);
		},
		
		animate: function(){},
		
		postCreate: function(){
			var self = this;
			on(this.domNode, 'busy', function(e){
				console.debug("busy" + e.state);
				self.busy = e.state;
				var overlay = dijitRegistry.byId(this.id + "_overlay");
				if(self.busy && overlay){
					overlay.show();
					console.debug("showing overlay...");
				}else
				if(overlay){
					overlay.hide();
					console.debug("hiding overlay...");
				}
			});
			this.inherited(arguments);
		},
		
		processDataParams: function(){
			if(this.dataParams && this.dataParams.length > 0){
				var hasParams = this.dataUrl.indexOf("?") >= 0;
				if(hasParams){
					this.dataUrl = this.dataUrl + "&";
				}else{
					this.dataUrl = this.dataUrl + "?";
				}
				for(var iParam in this.dataParams){
					this.dataUrl += this.dataParams[iParam] + "&";
				}
			}
		},
		
		startup: function(){
			this.inherited(arguments);
			
			new MobileOverlay({
				"id": this.id + "_overlay",
				"class": 'overlay',
				"innerHTML": "<div class='waiting'><img src='/img/loading.gif'></div>"
			}).placeAt(this.domNode,"last");
			this.refresh();
		},
		
		moveTo: function(from, to, transition){
			if(typeof transition === 'undefined')
				transition = 'slide';
			dijitRegistry.byId(from).performTransition(to,1,transition);
		},
		
		startBusy: function(/*String*/ msg){
			on.emit(this.domNode, "busy", {message: msg, state: true});
		},
		
		endBusy: function(/*String*/ msg){
			on.emit(this.domNode, "busy", {message: msg, state: false});
		},
		
		refresh: function(){
		},
		
		createListItem: function(itemClass, id, caption, itemMoveTo, itemTransition){
		},
		
		//replaces url with full path url
		replaceUrl: function(url, pattern, replacement){
			return url.replace(pattern, replacement);
		},
		
		// Pushes data into a template - primitive
		substitute: function(template, obj) {
			return template.replace(/\$\{([^\s\:\}]+)(?:\:([^\s\:\}]+))?\}/g, function(match,key){
				if(typeof obj === 'function')
					return obj.call(this, key);
				return obj[key];
			});
		}
	
	});
});