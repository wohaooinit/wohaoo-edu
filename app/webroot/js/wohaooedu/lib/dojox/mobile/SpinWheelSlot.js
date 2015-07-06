//>>built
define("dojox/mobile/SpinWheelSlot","dojo/_base/kernel dojo/_base/array dojo/_base/declare dojo/_base/window dojo/dom-class dojo/dom-construct dojo/has dojo/has!dojo-bidi?dojox/mobile/bidi/SpinWheelSlot dojo/touch dojo/on dijit/_Contained dijit/_WidgetBase ./scrollable ./common".split(" "),function(m,n,r,s,h,l,k,t,p,q,u,v,w){m=r(k("dojo-bidi")?"dojox.mobile.NonBidiSpinWheelSlot":"dojox.mobile.SpinWheelSlot",[v,u,w],{items:[],labels:[],labelFrom:0,labelTo:0,zeroPad:0,value:"",step:1,tabIndex:"0",_setTabIndexAttr:"",
baseClass:"mblSpinWheelSlot",maxSpeed:500,minItems:15,centerPos:0,scrollBar:!1,constraint:!1,propagatable:!1,androidWorkaroud:!1,buildRendering:function(){this.inherited(arguments);this.initLabels();var a,c;if(0<this.labels.length){this.items=[];for(a=0;a<this.labels.length;a++)this.items.push([a,this.labels[a]])}this.containerNode=l.create("div",{className:"mblSpinWheelSlotContainer"});this.containerNode.style.height=2*(s.global.innerHeight||s.doc.documentElement.clientHeight)+"px";this.panelNodes=
[];for(var b=0;3>b;b++){this.panelNodes[b]=l.create("div",{className:"mblSpinWheelSlotPanel"});var d=this.items.length;if(0<d){var e=Math.ceil(this.minItems/d);for(c=0;c<e;c++)for(a=0;a<d;a++)l.create("div",{className:"mblSpinWheelSlotLabel",name:this.items[a][0],"data-mobile-val":this.items[a][1],innerHTML:this._cv?this._cv(this.items[a][1]):this.items[a][1]},this.panelNodes[b])}this.containerNode.appendChild(this.panelNodes[b])}this.domNode.appendChild(this.containerNode);this.touchNode=l.create("div",
{className:"mblSpinWheelSlotTouch"},this.domNode);this.setSelectable(this.domNode,!1);""===this.value&&0<this.items.length&&(this.value=this.items[0][1]);this._initialValue=this.value;if(k("windows-theme")){var f=this,g=this.containerNode;this.own(q(f.touchNode,p.press,function(a){var b=a.pageY;a=f.getParent().getChildren();for(var c=0,d=a.length;c<d;c++){var e=a[c].containerNode;g!==e?(h.remove(e,"mblSelectedSlot"),e.selected=!1):h.add(g,"mblSelectedSlot")}var k=q(f.touchNode,p.move,function(a){5>
Math.abs(a.pageY-b)||(k.remove(),l.remove(),g.selected=!0,(a=f.getCenterItem())&&h.remove(a,"mblSelectedSlotItem"))}),l=q(f.touchNode,p.release,function(){l.remove();k.remove();g.selected?h.remove(g,"mblSelectedSlot"):h.add(g,"mblSelectedSlot");g.selected=!g.selected})}));this.on("flickAnimationEnd",function(){var a=f.getCenterItem();f.previousCenterItem&&h.remove(f.previousCenterItem,"mblSelectedSlotItem");h.add(a,"mblSelectedSlotItem");f.previousCenterItem=a})}},startup:function(){this._started||
(this.inherited(arguments),this.noResize=!0,0<this.items.length&&(this.init(),this.centerPos=this.getParent().centerPos,this._itemHeight=this.panelNodes[1].childNodes[0].offsetHeight,this.adjust(),this.connect(this.domNode,"onkeydown","_onKeyDown")),k("windows-theme")&&(this.previousCenterItem=this.getCenterItem())&&h.add(this.previousCenterItem,"mblSelectedSlotItem"))},initLabels:function(){if(this.labelFrom!==this.labelTo)for(var a=this.labels=[],c=this.zeroPad&&Array(this.zeroPad).join("0"),b=
this.labelFrom;b<=this.labelTo;b+=this.step)a.push(this.zeroPad?(c+b).slice(-this.zeroPad):b+"")},adjust:function(){for(var a=this.panelNodes[1].childNodes,c,b=0,d=a.length;b<d;b++){var e=a[b];if(e.offsetTop<=this.centerPos&&this.centerPos<e.offsetTop+e.offsetHeight){c=this.centerPos-(e.offsetTop+Math.round(e.offsetHeight/2));break}}a=this.panelNodes[0].offsetHeight;this.panelNodes[0].style.top=-a+c+"px";this.panelNodes[1].style.top=c+"px";this.panelNodes[2].style.top=a+c+"px"},setInitialValue:function(){this.set("value",
this._initialValue)},_onKeyDown:function(a){a&&"keydown"===a.type&&(40===a.keyCode?this.spin(-1):38===a.keyCode&&this.spin(1))},_getCenterPanel:function(){for(var a=this.getPos(),c=0,b=this.panelNodes.length;c<b;c++){var d=a.y+this.panelNodes[c].offsetTop;if(d<=this.centerPos&&this.centerPos<d+this.panelNodes[c].offsetHeight)return this.panelNodes[c]}return null},setColor:function(a,c){n.forEach(this.panelNodes,function(b){n.forEach(b.childNodes,function(b,e){h.toggle(b,c||"mblSpinWheelSlotLabelBlue",
b.innerHTML===a)},this)},this)},disableValues:function(a){n.forEach(this.panelNodes,function(c){for(var b=0;b<c.childNodes.length;b++)h.toggle(c.childNodes[b],"mblSpinWheelSlotLabelGray",b>=a)})},getCenterItem:function(){var a=this.getPos(),c=this._getCenterPanel();if(c)for(var a=a.y+c.offsetTop,c=c.childNodes,b=0,d=c.length;b<d;b++)if(a+c[b].offsetTop<=this.centerPos&&this.centerPos<a+c[b].offsetTop+c[b].offsetHeight)return c[b];return null},_getKeyAttr:function(){if(!this._started){if(this.items)for(var a=
0;a<this.items.length;a++)if(this.items[a][1]==this.value)return this.items[a][0];return null}return(a=this.getCenterItem())&&a.getAttribute("name")},_getValueAttr:function(){if(!this._started)return this.value;if(0<this.items.length){var a=this.getCenterItem();return a&&a.getAttribute("data-mobile-val")}return this._initialValue},_setValueAttr:function(a){0<this.items.length&&this._spinToValue(a,!0)},_spinToValue:function(a,c){var b,d,e=this.get("value");if(e){if(e!=a){this._pendingValue=void 0;
c&&this._set("value",a);for(var f=this.items.length,g=0;g<f&&!(this.items[g][1]===String(e)&&(b=g),this.items[g][1]===String(a)&&(d=g),void 0!==b&&void 0!==d);g++);b=d-(b||0);this.spin(0<b?b<f-b?-b:f-b:-b<f+b?-b:-(f+b))}}else this._pendingValue=a},onFlickAnimationStart:function(a){this._onFlickAnimationStartCalled=!0;this.inherited(arguments)},onFlickAnimationEnd:function(a){this._onFlickAnimationStartCalled=this._duringSlideTo=!1;this.inherited(arguments)},spin:function(a){if(this._started&&!this._duringSlideTo){var c=
this.getPos();c.y+=a*this._itemHeight;this.slideTo(c,1)}},getSpeed:function(){var a=0,c=this._time.length,b=(new Date).getTime()-this.startTime-this._time[c-1];2<=c&&200>b&&(a=this.calcSpeed(this._posY[c-1]-this._posY[0<=c-6?c-6:0],this._time[c-1]-this._time[0<=c-6?c-6:0]));return{x:0,y:a}},calcSpeed:function(a,c){var b=this.inherited(arguments);if(!b)return 0;var d=Math.abs(b),e=b;d>this.maxSpeed&&(e=this.maxSpeed*(b/d));return e},adjustDestination:function(a,c,b){c=this._itemHeight;b=a.y+Math.round(c/
2);a.y=b-(0<=b?b%c:b%c+c);return!0},resize:function(a){a=this.panelNodes[1].childNodes;0<a.length&&!k("windows-theme")&&(this._itemHeight=a[0].offsetHeight,this.centerPos=this.getParent().centerPos,this.panelNodes[0].style.top||this.adjust());this._pendingValue&&this.set("value",this._pendingValue)},slideTo:function(a,c,b){this._duringSlideTo=!0;var d=this.getPos(),e=d.y+this.panelNodes[1].offsetTop,f=e+this.panelNodes[1].offsetHeight,g=this.domNode.parentNode.offsetHeight;d.y<a.y?f>g&&(d=this.panelNodes[2],
d.style.top=this.panelNodes[0].offsetTop-this.panelNodes[0].offsetHeight+"px",this.panelNodes[2]=this.panelNodes[1],this.panelNodes[1]=this.panelNodes[0],this.panelNodes[0]=d):d.y>a.y&&0>e&&(d=this.panelNodes[0],d.style.top=this.panelNodes[2].offsetTop+this.panelNodes[2].offsetHeight+"px",this.panelNodes[0]=this.panelNodes[1],this.panelNodes[1]=this.panelNodes[2],this.panelNodes[2]=d);this.getParent()._duringStartup?c=0:40>Math.abs(this._speed.y)&&(c=0.2);this.inherited(arguments,[a,c,b]);if(this.getParent()._duringStartup&&
!this._onFlickAnimationStartCalled)this.onFlickAnimationEnd();else this._onFlickAnimationStartCalled||(this._duringSlideTo=!1)}});return k("dojo-bidi")?r("dojox.mobile.SpinWheelSlot",[m,t]):m});
//@ sourceMappingURL=SpinWheelSlot.js.map