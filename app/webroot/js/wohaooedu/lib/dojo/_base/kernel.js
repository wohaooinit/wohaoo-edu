//>>built
define("dojo/_base/kernel",["../has","./config","require","module"],function(a,d,f,c){var e;a={};var h={},b={config:d,global:this,dijit:a,dojox:h};a={dojo:["dojo",b],dijit:["dijit",a],dojox:["dojox",h]};c=f.map&&f.map[c.id.match(/[^\/]+/)[0]];for(e in c)a[e]?a[e][0]=c[e]:a[e]=[c[e],{}];for(e in a)c=a[e],c[1]._scopeName=c[0],d.noGlobals||(this[c[0]]=c[1]);b.scopeMap=a;b.baseUrl=b.config.baseUrl=f.baseUrl;b.isAsync=f.async;b.locale=d.locale;d="$Rev: f774568 $".match(/[0-9a-f]{7,}/);b.version={major:1,
minor:9,patch:2,flag:"",revision:d?d[0]:NaN,toString:function(){var a=b.version;return a.major+"."+a.minor+"."+a.patch+a.flag+" ("+a.revision+")"}};Function("d","d.eval \x3d function(){return d.global.eval ? d.global.eval(arguments[0]) : eval(arguments[0]);}")(b);b.exit=function(){};"undefined"!=typeof console||(console={});f="assert count debug dir dirxml error group groupEnd info profile profileEnd time timeEnd trace warn log".split(" ");var g;for(d=0;g=f[d++];)console[g]||function(){var a=g+"";
console[a]="log"in console?function(){var b=Array.apply({},arguments);b.unshift(a+":");console.log(b.join(" "))}:function(){};console[a]._fake=!0}();b.deprecated=b.experimental=function(){};b._hasResource={};return b});
//@ sourceMappingURL=kernel.js.map