//>>built
require({cache:{"url:edu/wohaoo/mobile/templates/EnrollView.html":'\x3cdiv class\x3d"enrollContainer"\x3e\n\t\x3cdiv id\x3d"enrollDialog" class\x3d"enrollDialog"\x3e\n\t\t\x3cdiv class\x3d"enrollHeaderPane"\x3e\n\t\t\t\x3cspan class\x3d"company-logo center"\x3e${logoText}\x3c/span\x3e\n\t\t\x3c/div\x3e\n\t\t\x3cdiv data-dojo-type\x3d"dojox/mobile/ContentPane" class\x3d"enrollPane mblHidden" data-dojo-attach-point\x3d"enrollPane"\x3e\n\t\t\t\x3cdiv class\x3d"enrollDescPane"\x3e\n\t\t\t\t\x3cspan data-dojo-type\x3d"dojox/mobile/ContentPane" data-dojo-attach-point\x3d"enrollMessage" \n\t\t\t\tclass\x3d"text center"\x3e${descText}\x3c/span\x3e\n\t\t\t\x3c/div\x3e\n\t\t\t\x3cdiv class\x3d"amountInputPane"\x3e\n\t\t\t\t\x3cinput class\x3d\'amountInput\'  type\x3d\'text\'\n\t\t\t\t  data-dojo-props\x3d\'readOnly:true, name:"data[Payment][pay_amount]", value:"", textDir:"ltr"\'\n\t\t\t\t  placeholder\x3d"${amountText}" data-dojo-type\x3d"dojox/mobile/TextBox"\n\t\t\t\t  data-dojo-attach-point\x3d"amountInput"/\x3e\n\t\t\t\x3c/div\x3e\n\t\t\t\x3cdiv class\x3d"telephoneInputPane"\x3e\n\t\t\t\t\x3cinput class\x3d\'telephoneInput\'  type\x3d\'text\'\n\t\t\t\t  data-dojo-props\x3d\'readOnly:true, name:"data[Payment][pay_medium]", value:"", textDir:"ltr"\'\n\t\t\t\t  placeholder\x3d"${telephoneText}" data-dojo-type\x3d"dojox/mobile/TextBox"\n\t\t\t\t  data-dojo-attach-point\x3d"telephoneInput"/\x3e\n\t\t\t\x3c/div\x3e\n\t\t\t\x3cdiv class\x3d"transactionCodeInputPane"\x3e\n\t\t\t\t\x3cinput class\x3d\'transactionCodeInput\'  type\x3d\'text\'\n\t\t\t\t  data-dojo-props\x3d\'name:"data[Payment][pay_transaction_id]", value:"", textDir:"ltr"\'\n\t\t\t\t  placeholder\x3d"${transactionCodeText}" data-dojo-type\x3d"dojox/mobile/TextBox" data-dojo-attach-point\x3d"transactionCode"/\x3e\n\t\t\t\x3c/div\x3e\n\t\t\t\x3cdiv class\x3d"enrollButtonPane"\x3e\n\t\t\t\t\x3cbutton  class\x3d\'enrollButton mblBlueButton\'\n\t\t\t\tdata-dojo-type\x3d"dojox/mobile/Button" data-dojo-attach-point\x3d"enrollButton"\x3e${enrollText}\x3c/button\x3e\n\t\t\t\x3c/div\x3e \n\t\t\x3c/div\x3e\n\t\x3c/div\x3e\n\x3c/div\x3e'}});
define("edu/wohaoo/mobile/EnrollView","dijit/registry dojo/dom-class dojo/on dojo/_base/array dojo/keys dojo/_base/declare dojo/parser dojo/dom dojo/dom-geometry dojo/dom-construct dojo/request dojo/query!css3 dojo/dom-style dijit/_WidgetBase dijit/_AttachMixin dijit/_TemplatedMixin dijit/_WidgetsInTemplateMixin dojo/text!./templates/EnrollView.html dojo/i18n!./nls/EnrollView edu/wohaoo/mobile/_AppViewMixin dojox/mobile/ScrollableView dojox/mobile/ContentPane dojox/mobile/Button dojox/mobile/TextBox dojo/domReady!".split(" "),
function(f,c,g,s,t,h,u,v,w,x,k,d,y,z,A,l,m,n,e,p,q){return h([q,p,l,m],{templateString:n,dataId:0,transactionCodeText:"",logoText:e.logo,descText:"",amountText:"",telephoneText:"",enrollText:e.enroll,delay:1E3,redirectTo:"",enrollPane:null,transactionCode:null,enrollButton:null,enrollMessage:null,amountInput:null,telephoneInput:null,transactionCode:null,paymentDest:null,_setPaymentDestAttr:function(a){this.telephoneInput.domNode.placeholder=a},paymentMessage:null,_setPaymentMessageAttr:function(a){this.enrollMessage.containerNode.innerHTML=
a},paymentAmount:null,_setPaymentAmountAttr:function(a){this.amountInput.domNode.placeholder=a},paymentInput:null,_setPaymentInputAttr:function(a){this.transactionCode.domNode.placeholder=a},postCreate:function(){this.inherited(arguments);var a=this;g(this.enrollButton.domNode,"click",function(b){var r=a.transactionCode.get("value");a.__doEnroll(r);b.stopPropagation();b.preventDefault()})},startup:function(){this.inherited(arguments)},__doEnroll:function(a){var b=this;k.post(this.serviceUrl,{data:{"data[Curriculum][id]":b.dataId,
"data[Payment][pay_transaction_id]":a},handleAs:"json"}).then(function(a){!a.studentid||!a.courseid?Sys.message(a.error):(a=f.byId(b.redirectTo),a.refresh(),a.animate(),b.moveTo(b.id,b.redirectTo))},function(a){b.alert(b.errors.err101)})},animate:function(){var a=this;c.contains(this.enrollPane.domNode,"mblHidden")||c.add(this.enrollPane.domNode,"mblHidden");d(".company-logo",this.domNode).forEach(function(a){c.contains(a,"center")||c.add(a,"center")});setTimeout(function(){c.contains(a.enrollPane.domNode,
"mblHidden")&&c.remove(a.enrollPane.domNode,"mblHidden");d(".company-logo",a.domNode).forEach(function(a){c.remove(a,"center")})},this.delay)},destroyDescendants:function(a){this.inherited(arguments)},destroy:function(){this.inherited(arguments)}})});
//@ sourceMappingURL=EnrollView.js.map