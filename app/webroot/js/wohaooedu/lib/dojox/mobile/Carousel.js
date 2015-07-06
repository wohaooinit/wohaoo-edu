//>>built
define("dojox/mobile/Carousel","dojo/_base/array dojo/_base/connect dojo/_base/declare dojo/_base/event dojo/_base/lang dojo/sniff dojo/dom-class dojo/dom-construct dojo/dom-style dijit/registry dijit/_Contained dijit/_Container dijit/_WidgetBase ./lazyLoadUtils ./CarouselItem ./PageIndicator ./SwapView require dojo/has!dojo-bidi?dojox/mobile/bidi/Carousel".split(" "),function(h,t,r,u,v,p,q,c,n,s,k,w,x,y,C,z,l,A,B){k=r(p("dojo-bidi")?"dojox.mobile.NonBidiCarousel":"dojox.mobile.Carousel",[x,w,k],
{numVisible:2,itemWidth:0,title:"",pageIndicator:!0,navButton:!1,height:"",selectable:!0,baseClass:"mblCarousel",buildRendering:function(){this.containerNode=c.create("div",{className:"mblCarouselPages"});this.inherited(arguments);var a;if(this.srcNodeRef){a=0;for(len=this.srcNodeRef.childNodes.length;a<len;a++)this.containerNode.appendChild(this.srcNodeRef.firstChild)}this.headerNode=c.create("div",{className:"mblCarouselHeaderBar"},this.domNode);this.navButton&&(this.btnContainerNode=c.create("div",
{className:"mblCarouselBtnContainer"},this.headerNode),n.set(this.btnContainerNode,"float","right"),this.prevBtnNode=c.create("button",{className:"mblCarouselBtn",title:"Previous",innerHTML:"\x26lt;"},this.btnContainerNode),this.nextBtnNode=c.create("button",{className:"mblCarouselBtn",title:"Next",innerHTML:"\x26gt;"},this.btnContainerNode),this._prevHandle=this.connect(this.prevBtnNode,"onclick","onPrevBtnClick"),this._nextHandle=this.connect(this.nextBtnNode,"onclick","onNextBtnClick"));this.pageIndicator&&
(this.title||(this.title="\x26nbsp;"),this.piw=new z,this.headerNode.appendChild(this.piw.domNode));this.titleNode=c.create("div",{className:"mblCarouselTitle"},this.headerNode);this.domNode.appendChild(this.containerNode);this.subscribe("/dojox/mobile/viewChanged","handleViewChanged");this.connect(this.domNode,"onclick","_onClick");this.connect(this.domNode,"onkeydown","_onClick");this._dragstartHandle=this.connect(this.domNode,"ondragstart",u.stop);this.selectedItemIndex=-1;this.items=[]},startup:function(){if(!this._started){var a;
"inherit"===this.height?this.domNode.offsetParent&&(a=this.domNode.offsetParent.offsetHeight+"px"):this.height&&(a=this.height);a&&(this.domNode.style.height=a);if(this.store){if(!this.setStore)throw Error("Use StoreCarousel or DataCarousel instead of Carousel.");a=this.store;this.store=null;this.setStore(a,this.query,this.queryOptions)}else this.resizeItems();this.inherited(arguments);this.currentView=h.filter(this.getChildren(),function(a){return a.isVisible()})[0]}},resizeItems:function(){var a=
0,b,d=this.domNode.offsetHeight-(this.headerNode?this.headerNode.offsetHeight:0),m=10>p("ie")?5/this.numVisible-1:5/this.numVisible,f,e;h.forEach(this.getChildren(),function(g){if(g instanceof l){g.lazy||(g._instantiated=!0);g=g.containerNode.childNodes;b=0;for(len=g.length;b<len;b++)f=g[b],1===f.nodeType&&(e=this.items[a]||{},n.set(f,{width:e.width||90/this.numVisible+"%",height:e.height||d+"px",margin:"0 "+(e.margin||m+"%")}),q.add(f,"mblCarouselSlot"),a++)}},this);this.piw&&(this.piw.refId=this.containerNode.firstChild,
this.piw.reset())},resize:function(){if(this.itemWidth){var a=Math.floor(this.domNode.offsetWidth/this.itemWidth);a!==this.numVisible&&(this.selectedItemIndex=this.getIndexByItemWidget(this.selectedItem),this.numVisible=a,0<this.items.length&&(this.onComplete(this.items),this.select(this.selectedItemIndex)))}},fillPages:function(){h.forEach(this.getChildren(),function(a,b){var d="",m;for(m=0;m<this.numVisible;m++){var f,e="",g;f=b*this.numVisible+m;var c={};f<this.items.length?(c=this.items[f],(f=
this.store.getValue(c,"type"))?(e=this.store.getValue(c,"props"),g=this.store.getValue(c,"mixins")):(f="dojox.mobile.CarouselItem",h.forEach(["alt","src","headerText","footerText"],function(a){var b=this.store.getValue(c,a);void 0!==b&&(e&&(e+=","),e+=a+':"'+b+'"')},this))):(f="dojox.mobile.CarouselItem",e='src:"'+A.toUrl("dojo/resources/blank.gif")+'", className:"mblCarouselItemBlank"');d+='\x3cdiv data-dojo-type\x3d"'+f+'"';e&&(d+=" data-dojo-props\x3d'"+e+"'");g&&(d+=" data-dojo-mixins\x3d'"+g+
"'");d+="\x3e\x3c/div\x3e"}a.containerNode.innerHTML=d},this)},onComplete:function(a){h.forEach(this.getChildren(),function(a){a instanceof l&&a.destroyRecursive()});this.selectedItem=null;this.items=a;var b=Math.ceil(a.length/this.numVisible),d=this.domNode.offsetHeight-this.headerNode.offsetHeight;pg=Math.floor((-1===this.selectedItemIndex?0:this.selectedItemIndex)/this.numVisible);for(a=0;a<b;a++){var c=new l({height:d+"px",lazy:!0});this.addChild(c);a===pg?(c.show(),this.currentView=c):c.hide()}this.fillPages();
this.resizeItems();d=this.getChildren();b=pg+1>b-1?b-1:pg+1;for(a=0>pg-1?0:pg-1;a<=b;a++)this.instantiateView(d[a])},onError:function(){},onUpdate:function(){},onDelete:function(){},onSet:function(a,b,c,h){},onNew:function(a,b){},onStoreClose:function(a){},getParentView:function(a){for(a=s.getEnclosingWidget(a);a;a=a.getParent())if(a.getParent()instanceof l)return a;return null},getIndexByItemWidget:function(a){if(!a)return-1;var b=a.getParent();return h.indexOf(this.getChildren(),b)*this.numVisible+
h.indexOf(b.getChildren(),a)},getItemWidgetByIndex:function(a){return-1===a?null:this.getChildren()[Math.floor(a/this.numVisible)].getChildren()[a%this.numVisible]},onPrevBtnClick:function(){this.currentView&&this.currentView.goTo(-1)},onNextBtnClick:function(){this.currentView&&this.currentView.goTo(1)},_onClick:function(a){if(!1!==this.onClick(a)){if(a&&"keydown"===a.type)if(39===a.keyCode)this.onNextBtnClick();else if(37===a.keyCode)this.onPrevBtnClick();else if(13!==a.keyCode)return;for(a=s.getEnclosingWidget(a.target);;a=
a.getParent()){if(!a)return;if(a.getParent()instanceof l)break}this.select(a);var b=this.getIndexByItemWidget(a);t.publish("/dojox/mobile/carouselSelect",[this,a,this.items[b],b])}},select:function(a){"number"===typeof a&&(a=this.getItemWidgetByIndex(a));this.selectable&&(this.selectedItem&&(this.selectedItem.set("selected",!1),q.remove(this.selectedItem.domNode,"mblCarouselSlotSelected")),a&&(a.set("selected",!0),q.add(a.domNode,"mblCarouselSlotSelected")),this.selectedItem=a)},onClick:function(){},
instantiateView:function(a){if(a&&!a._instantiated){var b="none"===n.get(a.domNode,"display");b&&n.set(a.domNode,{visibility:"hidden",display:""});y.instantiateLazyWidgets(a.containerNode,null,function(c){b&&n.set(a.domNode,{visibility:"visible",display:"none"})});a._instantiated=!0}},handleViewChanged:function(a){a.getParent()===this&&(this.currentView.nextView(this.currentView.domNode)===a?this.instantiateView(a.nextView(a.domNode)):this.instantiateView(a.previousView(a.domNode)),this.currentView=
a)},_setTitleAttr:function(a){this.titleNode.innerHTML=this._cv?this._cv(a):a;this._set("title",a)}});k.ChildSwapViewProperties={lazy:!1};v.extend(l,k.ChildSwapViewProperties);return p("dojo-bidi")?r("dojox.mobile.Carousel",[k,B]):k});
//@ sourceMappingURL=Carousel.js.map