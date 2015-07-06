//>>built
define("dojo/Deferred",["./has","./_base/lang","./errors/CancelError","./promise/Promise","require"],function(t,u,q,r,n){var s=Object.freeze||function(){},l=function(b,a,e,c,d){for(d=0;d<b.length;d++)p(b[d],a,e,c)},p=function(b,a,e,c){c=b[a];var d=b.deferred;if(c)try{var f=c(e);0===a?"undefined"!==typeof f&&g(d,a,f):f&&"function"===typeof f.then?(b.cancel=f.cancel,f.then(m(d,1),m(d,2),m(d,0))):g(d,1,f)}catch(h){g(d,2,h)}else g(d,a,e)},m=function(b,a){return function(e){g(b,a,e)}},g=function(b,a,e){if(!b.isCanceled())switch(a){case 0:b.progress(e);
break;case 1:b.resolve(e);break;case 2:b.reject(e)}},k=function(b){var a=this.promise=new r,e=this,c,d,f=!1,h=[];this.isResolved=a.isResolved=function(){return 1===c};this.isRejected=a.isRejected=function(){return 2===c};this.isFulfilled=a.isFulfilled=function(){return!!c};this.isCanceled=a.isCanceled=function(){return f};this.progress=function(d,b){if(c){if(!0===b)throw Error("This deferred has already been fulfilled.");return a}l(h,0,d,null,e);return a};this.resolve=function(b,f){if(c){if(!0===
f)throw Error("This deferred has already been fulfilled.");return a}l(h,c=1,d=b,null,e);h=null;return a};var g=this.reject=function(b,f){if(c){if(!0===f)throw Error("This deferred has already been fulfilled.");return a}l(h,c=2,d=b,void 0,e);h=null;return a};this.then=a.then=function(b,e,f){var g=[f,b,e];g.cancel=a.cancel;g.deferred=new k(function(a){return g.cancel&&g.cancel(a)});c&&!h?p(g,c,d,void 0):h.push(g);return g.deferred.promise};this.cancel=a.cancel=function(a,e){if(c){if(!0===e)throw Error("This deferred has already been fulfilled.");
}else{if(b){var h=b(a);a="undefined"===typeof h?a:h}f=!0;if(c){if(2===c&&d===a)return a}else return"undefined"===typeof a&&(a=new q),g(a),a}};s(a)};k.prototype.toString=function(){return"[object Deferred]"};n&&n(k);return k});
//@ sourceMappingURL=Deferred.js.map