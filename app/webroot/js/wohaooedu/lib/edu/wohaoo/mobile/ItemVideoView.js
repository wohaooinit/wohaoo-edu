//>>built
define("edu/wohaoo/mobile/ItemVideoView","dojo/_base/array dojo/query!css3 dojo/_base/connect dojo/_base/declare dijit/_WidgetBase dojo/dom-construct edu/wohaoo/mobile/ItemPageView dojox/mobile/ListItem dojox/mobile/compat dojox/mobile/Video dojo/domReady!".split(" "),function(c,f,m,g,n,p,h,k,q,l){return g([h],{listNode:null,postCreate:function(){this.itemTemplateString='\x3csource src\x3d"${mp4}" type\x3d"video/mp4"\x3e\x3csource src\x3d"${ogv}" type\x3d"video/ogg"\x3e\x3csource src\x3d"${webm}" type\x3d"video/webm"\x3e\x3cp\x3e${text}\x3c/p\x3e';
this.itemClass="video";this.inherited(arguments)},refresh:function(){this.inherited(arguments);if(this._store&&null!==this._store){this.query={id:"*",model:"video"};var a=this;f(".itemList",this.domNode).forEach(function(b){a.listNode=b});this._store.fetch({query:this.query,queryOptions:{deep:!0},onBegin:function(){a.hideItems()},onComplete:function(b){c.forEach(b,function(b,c){var d=(new k({"class":a.itemClass,dataId:a._store.getValue(b,"uniqueid"),variableHeight:!0})).placeAt(a.listNode,"first"),
e=a._store.getValue(b,"embed");e?d.domNode.innerHTML=e:(new l({width:320,height:240})).placeAt(d.domNode,"first").domNode.innerHTML=a.substitute(a.itemTemplateString,b)})},onError:function(b){a.alert(a.errors.err102)}})}}})});
//@ sourceMappingURL=ItemVideoView.js.map