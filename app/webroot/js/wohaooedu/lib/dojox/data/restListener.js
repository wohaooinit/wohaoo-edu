//>>built
define("dojox/data/restListener",["dojo","dijit","dojox"],function(f,g,e){f.provide("dojox.data.restListener");e.data.restListener=function(a){var b=a.channel,d=e.rpc.JsonRest,c=d.getServiceAndId(b).service,d=e.json.ref.resolveJson(a.result,{defaultId:"put"==a.event&&b,index:e.rpc.Rest._index,idPrefix:c.servicePath.replace(/[^\/]*$/,""),idAttribute:d.getIdAttribute(c),schemas:d.schemas,loader:d._loader,assignAbsoluteIds:!0}),b=e.rpc.Rest._index&&e.rpc.Rest._index[b];a="on"+a.event.toLowerCase();c=
c&&c._store;if(b&&b[a])b[a](d);else if(c)switch(a){case "onpost":c.onNew(d);break;case "ondelete":c.onDelete(b)}}});
//@ sourceMappingURL=restListener.js.map