/** SPECS
	Translation mixin
 */
define("edu/wohaoo/mobile/_T9nMixin", [
     "dojo/request", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dijit/registry",  
     "dojo/domReady!"
], function(dojoRequest, declare, _WidgetBase, 
	 dijitRegistry, messages) {
	 
	var _T9nMixin = declare([_WidgetBase],{
		ServiceUrl: "/translations/translate/",
		
		postCreate: function(){
			
			var self = this;
			
			var httpScheme = Sys.getHttpScheme();
			var httpHost = Sys.getHttpHost();
			if(httpScheme && httpHost){
				this.ServiceUrl = httpScheme + "://" + httpHost  + this.ServiceUrl;
			}
			this.inherited(arguments);
		},
		
		translate : function(trans_lang, orig_text, orig_lang){
			
			if(!orig_lang || typeof orig_lang === 'undefined')
				orig_lang = 'en';
			if(!trans_lang || typeof trans_lang === 'undefined')
				trans_lang = dojo.locale;
			if(orig_lang ===  trans_lang)
				return orig_text;
			var self = this;
			var translation = orig_text;
			
			if(typeof TRANSLATIONS_DB === 'object'  && typeof CryptoJS === 'object'){
				var guid = CryptoJS.MD5(orig_text);
				if(typeof TRANSLATIONS_DB[guid] === 'object'  && typeof TRANSLATIONS_DB[guid][trans_lang]  !== 'undefined'){
					translation = TRANSLATIONS_DB[guid][trans_lang];
					return translation;
				}
			}
			
			// The parameters to pass to xhrGet, the url, how to handle it, and the callbacks.
			var xhrArgs = {
				url: this.ServiceUrl,
				sync: true,
				handleAs: "json",
				preventCache: false,
				postData:  {
					 "data[Translation][t9n_orig_lang]":  orig_lang,
					"data[Translation][t9n_orig_text]": orig_text,
					"data[Translation][t9n_trans_lang]": trans_lang
				},
				load: function(data){
					
					 if(!data.trans_text){
						translation = orig_text;
					}
					else{
						translation =  data.trans_text;
					}
				},
				error: function(error){
				}
			};

			// Call the asynchronous xhrGet
			var deferred = dojo.xhrPost(xhrArgs);
			return translation;
		}
	});
	_T9nMixin.prototype._S = new _T9nMixin();
	window.__t = function(orig_text, trans_lang, orig_lang){
		return _T9nMixin.prototype.translate.call(_T9nMixin.prototype._S, trans_lang, orig_text, orig_lang);
	};
	return _T9nMixin;
});