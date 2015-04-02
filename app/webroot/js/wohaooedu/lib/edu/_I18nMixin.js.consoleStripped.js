define("edu/_I18nMixin", ["dijit/_WidgetBase", "dojo/_base/declare", 
     "dojo/i18n!./wohaoo/nls/Messages", "dojo/domReady!"
], function(_WidgetBase, declare, sysI18n) {
	return declare([_WidgetBase],{
		i18n: sysI18n,
		
		_translate: function(/*String*/ labelStr){
		 	if(this.i18n[labelStr]){
		 		return this.i18n[labelStr];
		 	}else
		 		return  labelStr;
		 }
	});
});
		