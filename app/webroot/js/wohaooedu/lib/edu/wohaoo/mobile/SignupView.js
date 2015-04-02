//>>built
require({cache:{"url:edu/wohaoo/mobile/templates/SignupView.html":'\x3cdiv class\x3d"signupContainer"\x3e\n\t\x3cdiv id\x3d"signupDialog" class\x3d"signupDialog"\x3e\n\t\t\x3cdiv class\x3d"signupHeaderPane"\x3e\n\t\t\t\x3cspan class\x3d"company-logo center"\x3e${logoText}\x3c/span\x3e\n\t\t\x3c/div\x3e\n\t\t\x3cdiv data-dojo-type\x3d"dojox/mobile/ContentPane" class\x3d"signupPane mblHidden" data-dojo-attach-point\x3d"signupPane"\x3e\n\t\t\t\x3cdiv class\x3d"firstNameInputPane"\x3e\n\t\t\t\t\x3cinput class\x3d\'firstNameInput\'  type\x3d\'text\'\n\t\t\t\t  data-dojo-props\x3d\'name:"data[Person][per_first_name]", value:"", textDir:"ltr"\'\n\t\t\t\t  placeholder\x3d"${firstNameText}" data-dojo-type\x3d"dojox/mobile/TextBox" data-dojo-attach-point\x3d"firstName"/\x3e\n\t\t\t\x3c/div\x3e\n\t\t\t\x3cdiv class\x3d"lastNameInputPane"\x3e\n\t\t\t\t\x3cinput class\x3d\'lastNameInput\'  type\x3d\'text\'\n\t\t\t\t  data-dojo-props\x3d\'name:"data[Person][per_last_name]", value:"", textDir:"ltr"\'\n\t\t\t\t  placeholder\x3d"${lastNameText}" data-dojo-type\x3d"dojox/mobile/TextBox" data-dojo-attach-point\x3d"lastName"/\x3e\n\t\t\t\x3c/div\x3e  \n\t\t\t\x3cdiv class\x3d"telInputPane"\x3e\n\t\t\t\t\x3cinput class\x3d\'telInput\'  type\x3d\'text\'\n\t\t\t\t  data-dojo-props\x3d\'name:"data[User][usr_telephone]", value:"", textDir:"ltr"\'\n\t\t\t\t  placeholder\x3d"${telephoneText}" data-dojo-type\x3d"dojox/mobile/TextBox" data-dojo-attach-point\x3d"telephone"/\x3e\n\t\t\t\x3c/div\x3e \n\t\t\t\x3cdiv class\x3d"passwordInputPane"\x3e\n\t\t\t\t\x3cinput class\x3d\'passwordInput\' placeholder\x3d"${passwordText}" \n\t\t\t\t\t\t\tdata-dojo-props\x3d\'name:"data[User][usr_password]", value:"", textDir:"ltr"\'\n\t\t\t\t\t\t\tdata-dojo-type\x3d"dojox/mobile/TextBox" type\x3d"text" data-dojo-attach-point\x3d"password"/\x3e\n\t\t\t\x3c/div\x3e\n\t\t\t\x3cdiv class\x3d"signupButtonPane"\x3e\n\t\t\t\t\x3cbutton  class\x3d\'signupButton\'\n\t\t\t\tdata-dojo-type\x3d"dojox/mobile/Button" data-dojo-attach-point\x3d"signupButton"\x3e${signupText}\x3c/button\x3e\n\t\t\t\x3c/div\x3e \n\t\t\x3c/div\x3e\n\t\x3c/div\x3e\n\x3c/div\x3e'}});
define("edu/wohaoo/mobile/SignupView","dijit/registry dojo/dom-class dojo/on dojo/_base/array dojo/keys dojo/_base/declare dojo/parser dojo/dom dojo/dom-geometry dojo/dom-construct dojo/request dojo/query!css3 dojo/dom-style dijit/_WidgetBase dijit/_AttachMixin dijit/_TemplatedMixin dijit/_WidgetsInTemplateMixin dojo/text!./templates/SignupView.html dojo/i18n!./nls/SignupView edu/wohaoo/mobile/_AppViewMixin dojox/mobile/ScrollableView dojox/mobile/ContentPane dojox/mobile/Button dojox/mobile/TextBox dojo/domReady!".split(" "),
function(f,b,k,t,u,l,v,w,x,y,m,g,z,A,B,n,p,q,c,r,s){return l([s,r,n,p],{templateString:q,messages:c,firstNameText:c.firstName,lastNameText:c.lastName,telephoneText:c.telephone,passwordText:c.password,logoText:__t("WOHAOO Education"),signupText:c.signup,delay:1E3,redirectTo:"",signupPane:null,firstName:null,lastName:null,telephone:null,password:null,signupButton:null,postCreate:function(){this.inherited(arguments);var a=this;k(this.signupButton.domNode,"click",function(e){var b=a.firstName.get("value"),
c=a.lastName.get("value"),d=a.telephone.get("value"),h=a.password.get("value");if(!b||!c||!d||!h)return f.byId("dlg_message").show(a.messages.INVALID_INPUT_PARAMETERS),e.stopPropagation(),e.preventDefault(),!1;a.__doSignup(b,c,d,h);e.stopPropagation();e.preventDefault();return!0})},startup:function(){this.inherited(arguments)},__doSignup:function(a,e,b,c){var d=this;m.post(this.serviceUrl,{data:{"data[User][usr_telephone]":b,"data[User][usr_password]":c,"data[Person][per_first_name]":a,"data[Person][per_last_name]":e},
handleAs:"json"}).then(function(a){a.userid?(a=f.byId(d.redirectTo),a.refresh(),a.animate(),d.moveTo(d.id,d.redirectTo)):f.byId("dlg_message").show(a.error)},function(a){d.alert(d.errors.err101)})},animate:function(){var a=this;b.contains(this.signupPane.domNode,"mblHidden")||b.add(this.signupPane.domNode,"mblHidden");g(".company-logo",this.domNode).forEach(function(a){b.contains(a,"center")||b.add(a,"center")});setTimeout(function(){b.contains(a.signupPane.domNode,"mblHidden")&&b.remove(a.signupPane.domNode,
"mblHidden");g(".company-logo",a.domNode).forEach(function(a){b.remove(a,"center")})},this.delay)},destroyDescendants:function(a){this.inherited(arguments)},destroy:function(){this.inherited(arguments)}})});
//@ sourceMappingURL=SignupView.js.map