//>>built
require({cache:{"url:dojox/calc/templates/Grapher.html":'\x3cdiv\x3e\n\x3cdiv data-dojo-attach-point\x3d"chartsParent" class\x3d"dojoxCalcChartHolder"\x3e\x3c/div\x3e\n\x3cdiv data-dojo-attach-point\x3d"outerDiv"\x3e\n\x3cdiv data-dojo-type\x3d"dijit.form.DropDownButton" data-dojo-attach-point\x3d"windowOptions" class\x3d"dojoxCalcDropDownForWindowOptions" title\x3d"Window Options"\x3e\n\t\x3cdiv\x3eWindow Options\x3c/div\x3e\n\t\x3cdiv data-dojo-type\x3d"dijit.TooltipDialog" data-dojo-attach-point\x3d"windowOptionsInside" class\x3d"dojoxCalcTooltipDialogForWindowOptions" title\x3d""\x3e\n\t\t\x3ctable class\x3d"dojoxCalcGraphOptionTable"\x3e\n\t\t\t\x3ctr\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\tWidth:\n\t\t\t\t\x3c/td\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\t\x3cinput data-dojo-type\x3d"dijit.form.TextBox" data-dojo-attach-point\x3d"graphWidth" class\x3d"dojoxCalcGraphWidth" value\x3d"500" /\x3e\n\t\t\t\t\x3c/td\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\tHeight:\n\t\t\t\t\x3c/td\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\t\x3cinput data-dojo-type\x3d"dijit.form.TextBox" data-dojo-attach-point\x3d"graphHeight" class\x3d"dojoxCalcGraphHeight" value\x3d"500" /\x3e\n\t\t\t\t\x3c/td\x3e\n\t\t\t\x3c/tr\x3e\n\t\t\t\x3ctr\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\tX \x3e\x3d\n\t\t\t\t\x3c/td\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\t\x3cinput data-dojo-type\x3d"dijit.form.TextBox" data-dojo-attach-point\x3d"graphMinX" class\x3d"dojoxCalcGraphMinX" value\x3d"-10" /\x3e\n\t\t\t\t\x3c/td\x3e\n\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\tX \x3c\x3d\n\t\t\t\t\x3c/td\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\t\x3cinput data-dojo-type\x3d"dijit.form.TextBox" data-dojo-attach-point\x3d"graphMaxX" class\x3d"dojoxCalcGraphMaxX" value\x3d"10" /\x3e\n\t\t\t\t\x3c/td\x3e\n\t\t\t\x3c/tr\x3e\n\t\t\t\x3ctr\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\tY \x3e\x3d\n\t\t\t\t\x3c/td\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\t\x3cinput data-dojo-type\x3d"dijit.form.TextBox" data-dojo-attach-point\x3d"graphMinY" class\x3d"dojoxCalcGraphMinY" value\x3d"-10" /\x3e\n\t\t\t\t\x3c/td\x3e\n\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\tY \x3c\x3d\n\t\t\t\t\x3c/td\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\t\x3cinput data-dojo-type\x3d"dijit.form.TextBox" data-dojo-attach-point\x3d"graphMaxY" class\x3d"dojoxCalcGraphMaxY" value\x3d"10" /\x3e\n\t\t\t\t\x3c/td\x3e\n\t\t\t\x3c/tr\x3e\n\t\t\x3c/table\x3e\n\t\x3c/div\x3e\n\x3c/div\x3e\n\n\x3cBR\x3e\n\n\x3cdiv class\x3d"dojoxCalcGrapherFuncOuterDiv"\x3e\n\t\x3ctable class\x3d"dojoxCalcGrapherFuncTable" data-dojo-attach-point\x3d"graphTable"\x3e\n\t\x3c/table\x3e\n\x3c/div\x3e\n\n\x3cdiv data-dojo-type\x3d"dijit.form.DropDownButton" data-dojo-attach-point\x3d\'addFuncButton\' class\x3d"dojoxCalcDropDownAddingFunction"\x3e\n\t\x3cdiv\x3eAdd Function\x3c/div\x3e\n\t\x3cdiv data-dojo-type\x3d"dijit.TooltipDialog" data-dojo-attach-point\x3d"addFuncInside" class\x3d"dojoxCalcTooltipDialogAddingFunction" title\x3d""\x3e\n\t\t\x3ctable class\x3d"dojoxCalcGrapherModeTable"\x3e\n\t\t\t\x3ctr\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\tMode:\n\t\t\t\t\x3c/td\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\t\x3cselect data-dojo-type\x3d"dijit.form.Select" data-dojo-attach-point\x3d"funcMode" class\x3d"dojoxCalcFunctionModeSelector"\x3e\n\t\t\t\t\t\t\x3coption value\x3d"y\x3d" selected\x3d"selected"\x3ey\x3d\x3c/option\x3e\n\t\t\t\t\t\t\x3coption value\x3d"x\x3d"\x3ex\x3d\x3c/option\x3e\n\t\t\t\t\t\x3c/select\x3e\n\t\t\t\t\x3c/td\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\x3c/tr\x3e\n\t\n\t\t\t\x3ctr\x3e\n\t\t\t\t\x3ctd\x3e\n\t\t\t\t\t\x3cinput data-dojo-type\x3d"dijit.form.Button" data-dojo-attach-point\x3d"createFunc" class\x3d"dojoxCalcAddFunctionButton" label\x3d"Create" /\x3e\n\t\t\t\t\x3c/td\x3e\n\t\t\t\x3c/tr\x3e\n\t\t\x3c/table\x3e\n\t\x3c/div\x3e\n\x3c/div\x3e\n\x3cBR\x3e\n\x3cBR\x3e\n\x3ctable class\x3d"dijitInline dojoxCalcGrapherLayout"\x3e\n\t\x3ctr\x3e\n\t\t\x3ctd class\x3d"dojoxCalcGrapherButtonContainer"\x3e\n\t\t\t\x3cinput data-dojo-type\x3d"dijit.form.Button" class\x3d"dojoxCalcGrapherButton" data-dojo-attach-point\x3d\'selectAllButton\' label\x3d"Select All" /\x3e\n\t\t\x3c/td\x3e\n\t\t\x3ctd class\x3d"dojoxCalcGrapherButtonContainer"\x3e\n\t\t\t\x3cinput data-dojo-type\x3d"dijit.form.Button" class\x3d"dojoxCalcGrapherButton" data-dojo-attach-point\x3d\'deselectAllButton\' label\x3d"Deselect All" /\x3e\n\t\t\x3c/td\x3e\n\t\x3c/tr\x3e\n\t\x3ctr\x3e\n\t\t\x3ctd class\x3d"dojoxCalcGrapherButtonContainer"\x3e\n\t\t\t\x3cinput data-dojo-type\x3d"dijit.form.Button" class\x3d"dojoxCalcGrapherButton" data-dojo-attach-point\x3d\'drawButton\'label\x3d"Draw Selected" /\x3e\n\t\t\x3c/td\x3e\n\t\t\x3ctd class\x3d"dojoxCalcGrapherButtonContainer"\x3e\n\t\t\t\x3cinput data-dojo-type\x3d"dijit.form.Button" class\x3d"dojoxCalcGrapherButton" data-dojo-attach-point\x3d\'eraseButton\' label\x3d"Erase Selected" /\x3e\n\t\t\x3c/td\x3e\n\t\x3c/tr\x3e\n\t\x3ctr\x3e\n\t\t\x3ctd class\x3d"dojoxCalcGrapherButtonContainer"\x3e\n\t\t\t\x3cinput data-dojo-type\x3d"dijit.form.Button" class\x3d"dojoxCalcGrapherButton" data-dojo-attach-point\x3d\'deleteButton\' label\x3d"Delete Selected" /\x3e\n\t\t\x3c/td\x3e\n\t\t\x3ctd class\x3d"dojoxCalcGrapherButtonContainer"\x3e\n\t\t\t\x3cinput data-dojo-type\x3d"dijit.form.Button" class\x3d"dojoxCalcGrapherButton" data-dojo-attach-point\x3d\'closeButton\' label\x3d"Close" /\x3e\n\t\t\x3c/td\x3e\n\t\x3c/tr\x3e\n\x3c/table\x3e\n\x3c/div\x3e\n\x3c/div\x3e\n'}});
define("dojox/calc/Grapher","dojo/_base/declare dojo/_base/lang dojo/_base/window dojo/dom-construct dojo/dom-class dojo/dom-style dijit/_WidgetBase dijit/_WidgetsInTemplateMixin dijit/_TemplatedMixin dojox/math/_base dijit/registry dijit/form/DropDownButton dijit/TooltipDialog dijit/form/TextBox dijit/form/CheckBox dijit/ColorPalette dojox/charting/Chart dojox/charting/axis2d/Default dojox/charting/plot2d/Default dojox/charting/plot2d/Lines dojox/charting/themes/Tufte dojo/colors dojo/text!./templates/Grapher.html dojox/calc/_Executor dijit/form/Button dijit/form/Select".split(" "),
function(q,k,x,m,p,r,y,z,A,K,B,C,D,E,F,G,L,M,N,O,P,Q,H,s){var u=1E-15/9,I=Math.log(2),J={graphNumber:0,fOfX:!0,color:{stroke:"black"}};q=q("dojox.calc.Grapher",[y,A,z],{templateString:H,addXYAxes:function(a){return a.addAxis("x",{max:parseInt(this.graphMaxX.get("value")),min:parseInt(this.graphMinX.get("value")),majorLabels:!0,minorLabels:!0,minorTicks:!1,microTicks:!1,htmlLabels:!0,labelFunc:function(a){return a},maxLabelSize:30,fixUpper:"major",fixLower:"major",majorTick:{length:3}}).addAxis("y",
{max:parseInt(this.graphMaxY.get("value")),min:parseInt(this.graphMinY.get("value")),labelFunc:function(a){return a},maxLabelSize:50,vertical:!0,microTicks:!1,minorTicks:!0,majorTick:{stroke:"black",length:3}})},selectAll:function(){for(var a=0;a<this.rowCount;a++)this.array[a][this.checkboxIndex].set("checked",!0)},deselectAll:function(){for(var a=0;a<this.rowCount;a++)this.array[a][this.checkboxIndex].set("checked",!1)},drawOne:function(a){},onDraw:function(){},erase:function(a){for(var d=0,c="Series "+
this.array[a][this.funcNumberIndex]+"_"+d;c in this.array[a][this.chartIndex].runs;)this.array[a][this.chartIndex].removeSeries(c),d++,c="Series "+this.array[a][this.funcNumberIndex]+"_"+d;this.array[a][this.chartIndex].render();this.setStatus(a,"Hidden")},onErase:function(){for(var a=0;a<this.rowCount;a++)this.array[a][this.checkboxIndex].get("checked")&&this.erase(a)},onDelete:function(){for(var a=0;a<this.rowCount;a++)if(this.array[a][this.checkboxIndex].get("checked")){this.erase(a);for(var d=
0;d<this.functionRef;d++)this.array[a][d]&&this.array[a][d].destroy&&this.array[a][d].destroy();this.graphTable.deleteRow(a);this.array.splice(a,1);this.rowCount--;a--}},checkboxIndex:0,functionMode:1,expressionIndex:2,colorIndex:3,dropDownIndex:4,tooltipIndex:5,colorBoxFieldsetIndex:6,statusIndex:7,chartIndex:8,funcNumberIndex:9,evaluatedExpression:10,functionRef:11,createFunction:function(){var a=this.graphTable.insertRow(-1);this.array[a.rowIndex]=[];var d=a.insertCell(-1),c=m.create("div");d.appendChild(c);
var f=new F({},c);this.array[a.rowIndex][this.checkboxIndex]=f;p.add(c,"dojoxCalcCheckBox");d=a.insertCell(-1);f=this.funcMode.get("value");c=x.doc.createTextNode(f);d.appendChild(c);this.array[a.rowIndex][this.functionMode]=f;d=a.insertCell(-1);c=m.create("div");d.appendChild(c);f=new E({},c);this.array[a.rowIndex][this.expressionIndex]=f;p.add(c,"dojoxCalcExpressionBox");c=m.create("div");f=new G({changedColor:this.changedColor},c);p.add(c,"dojoxCalcColorPalette");this.array[a.rowIndex][this.colorIndex]=
f;var c=m.create("div"),g=new D({content:f},c);this.array[a.rowIndex][this.tooltipIndex]=g;p.add(c,"dojoxCalcContainerOfColor");d=a.insertCell(-1);c=m.create("div");d.appendChild(c);var b=m.create("fieldset");r.set(b,{backgroundColor:"black",width:"1em",height:"1em",display:"inline"});this.array[a.rowIndex][this.colorBoxFieldsetIndex]=b;d=new C({label:"Color ",dropDown:g},c);d.containerNode.appendChild(b);this.array[a.rowIndex][this.dropDownIndex]=d;p.add(c,"dojoxCalcDropDownForColor");d=a.insertCell(-1);
c=m.create("fieldset");c.innerHTML="Hidden";this.array[a.rowIndex][this.statusIndex]=c;p.add(c,"dojoxCalcStatusBox");d.appendChild(c);c=m.create("div");r.set(c,{position:"absolute",left:"0px",top:"0px"});this.chartsParent.appendChild(c);this.array[a.rowIndex][this.chartNodeIndex]=c;p.add(c,"dojoxCalcChart");c=(new dojox.charting.Chart(c)).setTheme(dojox.charting.themes.Tufte).addPlot("default",{type:"Lines",shadow:{dx:1,dy:1,width:2,color:[0,0,0,0.3]}});this.addXYAxes(c);this.array[a.rowIndex][this.chartIndex]=
c;f.set("chart",c);f.set("colorBox",b);f.set("onChange",k.hitch(f,"changedColor"));this.array[a.rowIndex][this.funcNumberIndex]=this.funcNumber++;this.rowCount++},setStatus:function(a,d){this.array[a][this.statusIndex].innerHTML=d},changedColor:function(){for(var a=this.get("chart"),d=this.get("colorBox"),c=0;c<a.series.length;c++)a.series[c].stroke&&a.series[c].stroke.color&&(a.series[c].stroke.color=this.get("value"),a.dirty=!0);a.render();r.set(d,{backgroundColor:this.get("value")})},makeDirty:function(){this.dirty=
!0},checkDirty1:function(){setTimeout(k.hitch(this,"checkDirty"),0)},checkDirty:function(){if(this.dirty){for(var a=0;a<this.rowCount;a++)this.array[a][this.chartIndex].removeAxis("x"),this.array[a][this.chartIndex].removeAxis("y"),this.addXYAxes(this.array[a][this.chartIndex]);this.onDraw()}this.dirty=!1},postCreate:function(){this.inherited(arguments);this.createFunc.set("onClick",k.hitch(this,"createFunction"));this.selectAllButton.set("onClick",k.hitch(this,"selectAll"));this.deselectAllButton.set("onClick",
k.hitch(this,"deselectAll"));this.drawButton.set("onClick",k.hitch(this,"onDraw"));this.eraseButton.set("onClick",k.hitch(this,"onErase"));this.deleteButton.set("onClick",k.hitch(this,"onDelete"));this.dirty=!1;this.graphWidth.set("onChange",k.hitch(this,"makeDirty"));this.graphHeight.set("onChange",k.hitch(this,"makeDirty"));this.graphMaxX.set("onChange",k.hitch(this,"makeDirty"));this.graphMinX.set("onChange",k.hitch(this,"makeDirty"));this.graphMaxY.set("onChange",k.hitch(this,"makeDirty"));this.graphMinY.set("onChange",
k.hitch(this,"makeDirty"));this.windowOptionsInside.set("onClose",k.hitch(this,"checkDirty1"));this.rowCount=this.funcNumber=0;this.array=[]},startup:function(){this.inherited(arguments);var a=B.getEnclosingWidget(this.domNode.parentNode);a&&"function"==typeof a.close?this.closeButton.set("onClick",k.hitch(a,"close")):r.set(this.closeButton.domNode,{display:"none"});this.createFunction();this.array[0][this.checkboxIndex].set("checked",!0);this.onDraw();this.erase(0);this.array[0][this.expressionIndex].value=
""}});return k.mixin(s,{draw:function(a,d,c){c=k.mixin({},J,c);a.fullGeometry();d=!0==c.fOfX?s.generatePoints(d,"x","y",a.axes.x.scaler.bounds.span,a.axes.x.scaler.bounds.lower,a.axes.x.scaler.bounds.upper,a.axes.y.scaler.bounds.lower,a.axes.y.scaler.bounds.upper):s.generatePoints(d,"y","x",a.axes.y.scaler.bounds.span,a.axes.y.scaler.bounds.lower,a.axes.y.scaler.bounds.upper,a.axes.x.scaler.bounds.lower,a.axes.x.scaler.bounds.upper);var f=0;if(0<d.length)for(;f<d.length;f++)0<d[f].length&&a.addSeries("Series "+
c.graphNumber+"_"+f,d[f],c.color);for(var g="Series "+c.graphNumber+"_"+f;g in a.runs;)a.removeSeries(g),f++,g="Series "+c.graphNumber+"_"+f;a.render();return d},generatePoints:function(a,d,c,f,g,b,k,m){function p(a,b,e,h){for(;b<=e;){var f={};f[d]=(b[d]+e[d])/2;f[c]=a(f[d]);if(h==f[c]||f[d]==e[d]||f[d]==b[d])return f;var g=!0;h<f[c]&&(g=!1);f[c]<e[c]?g?b=f:e=f:f[c]<b[c]&&(g?e=f:b=f)}return NaN}function r(a,b,e){for(var f=[[],[]],h;b[d]<=e[d];){var g=(b[d]+e[d])/2;h={};h[d]=g;h[c]=a(g);var g=h[d],
k=void 0,k=-1<g&&1>g?0>g?g>=-u?-g:g/Math.ceil(g/u):u:Math.abs(g)*u,g=g+k,k={};k[d]=g;k[c]=a(g);if(Math.abs(k[c])>=Math.abs(h[c]))f[0].push(h),b=k;else{f[1].unshift(h);if(e[d]==h[d])break;e=h}}return f}function q(a,b){var c=!1,d=!1;a<b&&(c=!0);0<b&&(d=!0);return{inc:c,pos:d}}function t(a,b){return(b[c]-a[c])/(b[d]-a[d])}f=1<<Math.ceil(Math.log(f)/I);var n=(b-g)/f;b=[];var h=0,e,l;b[h]=[];for(var v=g,s=0;s<=f;v+=n,s++){g={};g[d]=v;g[c]=a({_name:d,_value:v,_graphing:!0});if(null==g[d]||null==g[c])return{};
if(!isNaN(g[c])&&!isNaN(g[d]))if(b[h].push(g),3==b[h].length)e=q(t(b[h][b[h].length-3],b[h][b[h].length-2]),t(b[h][b[h].length-2],b[h][b[h].length-1]));else if(!(4>b[h].length)&&(l=q(t(b[h][b[h].length-3],b[h][b[h].length-2]),t(b[h][b[h].length-2],b[h][b[h].length-1])),e.inc!=l.inc||e.pos!=l.pos)){var w=r(a,b[h][b[h].length-3],b[h][b[h].length-1]);g=b[h].pop();b[h].pop();for(e=0;e<w[0].length;e++)b[h].push(w[0][e]);for(e=1;e<w.length;e++)b[++h]=w.pop();b[h].push(g);e=l}}for(;1<b.length;){for(e=0;e<
b[1].length;e++)b[0][b[0].length-1][d]!=b[1][e][d]&&b[0].push(b[1][e]);b.splice(1,1)}b=b[0];f=0;n=[[]];for(e=0;e<b.length;e++)if(isNaN(b[e][c])||isNaN(b[e][d])){for(;isNaN(b[e][c])||isNaN(b[e][d]);)b.splice(e,1);n[++f]=[];e--}else if(b[e][c]>m||b[e][c]<k){0<e&&(b[e-1].y!=k&&b[e-1].y!=m)&&(l=t(b[e-1],b[e]),1E200<l?l=1E200:-1E200>l&&(l=-1E200),h=b[e][c]>m?m:k,g=b[e][c]-l*b[e][d],l=(h-g)/l,g={},g[d]=l,g[c]=a(l),g[c]!=h&&(g=p(a,b[e-1],b[e],h)),n[f].push(g),n[++f]=[]);for(;e<b.length&&(b[e][c]>m||b[e][c]<
k);)e++;if(e>=b.length){0==n[f].length&&n.splice(f,1);break}0<e&&(b[e].y!=k&&b[e].y!=m)&&(l=t(b[e-1],b[e]),1E200<l?l=1E200:-1E200>l&&(l=-1E200),h=b[e-1][c]>m?m:k,g=b[e][c]-l*b[e][d],l=(h-g)/l,g={},g[d]=l,g[c]=a(l),g[c]!=h&&(g=p(a,b[e-1],b[e],h)),n[f].push(g),n[f].push(b[e]))}else n[f].push(b[e]);return n},Grapher:q})});
//@ sourceMappingURL=Grapher.js.map