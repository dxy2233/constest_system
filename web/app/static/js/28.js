webpackJsonp([28],{"2zFH":function(n,t,e){var s=e("B/qp");"string"==typeof s&&(s=[[n.i,s,""]]),s.locals&&(n.exports=s.locals);e("rjj0")("07b397ea",s,!0,{})},"B/qp":function(n,t,e){t=n.exports=e("FZ+f")(!0),t.push([n.i,"\n.personal-set {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.personal-set .weui-cells {\n  margin: 0;\n}\n.personal-set .weui-cells .weui-cell {\n  padding: 15px 15px;\n}\n.personal-set .btn-box {\n  position: absolute;\n  left: 15px;\n  right: 15px;\n  bottom: 50px;\n}\n.personal-set .link-list {\n  margin-top: 0;\n}\n.personal-set .link-list .right-text {\n  color: #959da6;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/set/index.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,UAAU;CACX;AACD;EACE,mBAAmB;CACpB;AACD;EACE,mBAAmB;EACnB,WAAW;EACX,YAAY;EACZ,aAAa;CACd;AACD;EACE,cAAc;CACf;AACD;EACE,eAAe;CAChB",file:"index.vue",sourcesContent:["\n.personal-set {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.personal-set .weui-cells {\n  margin: 0;\n}\n.personal-set .weui-cells .weui-cell {\n  padding: 15px 15px;\n}\n.personal-set .btn-box {\n  position: absolute;\n  left: 15px;\n  right: 15px;\n  bottom: 50px;\n}\n.personal-set .link-list {\n  margin-top: 0;\n}\n.personal-set .link-list .right-text {\n  color: #959da6;\n}"],sourceRoot:""}])},wGDG:function(n,t,e){"use strict";function s(n){e("2zFH")}Object.defineProperty(t,"__esModule",{value:!0});var o=e("Dd8w"),i=e.n(o),a=e("POHo"),l=e("SQ4B"),r=e("rHil"),p=e("1DHf"),A=e("32ER"),c=e("NYxO"),u=e("l1VX"),g=(a.a,r.a,p.a,A.a,i()({},Object(c.b)(["loginMsg","identifyMsg"])),{name:"index",components:{slide:a.a,Group:r.a,Cell:p.a,CellBox:A.a},data:function(){return{hidMobile:u.c,btnLoading:!1}},methods:{loginOut:function(){var n=this;this.btnLoading=!0,l.a.post("/login/logout",{},function(t){n.btnLoading=!1,n.$vux.toast.show(t.msg),0===t.code&&(Object(u.b)(),n.$router.replace({path:"/home"}))})}},computed:i()({},Object(c.b)(["loginMsg","identifyMsg"]))}),C=function(){var n=this,t=n.$createElement,e=n._self._c||t;return e("slide",[e("div",{staticClass:"personal-set"},[e("app-header",[n._v("\n      设置\n    ")]),n._v(" "),e("div",{staticClass:"h-main"},[e("ul",{staticClass:"link-list"},[e("li",[e("span",{staticClass:"text"},[n._v("当前账号")]),n._v(" "),e("span",{staticClass:"right-text"},[n._v(n._s(n.hidMobile(n.loginMsg?n.loginMsg.mobile:"")))])]),n._v(" "),e("router-link",{attrs:{tag:"li",to:"/personal/set/about"}},[e("span",{staticClass:"text"},[n._v("关于")]),n._v(" "),e("svg",{staticClass:"vux-x-icon vux-x-icon-ios-arrow-right",attrs:{type:"ios-arrow-right",xmlns:"http://www.w3.org/2000/svg",width:"24",height:"24",viewBox:"0 0 512 512"}},[e("path",{attrs:{d:"M160 115.4L180.7 96 352 256 180.7 416 160 396.7 310.5 256z"}})])])],1),n._v(" "),e("div",{staticClass:"btn-box"},[e("x-button",{staticClass:"login-out",attrs:{type:"warn","show-loading":n.btnLoading},nativeOn:{click:function(t){return n.loginOut(t)}}},[n._v("退出登录")])],1)]),n._v(" "),e("router-view")],1)])},d=[],x={render:C,staticRenderFns:d},b=x,v=e("VU/8"),f=s,h=v(g,b,!1,f,null,null);t.default=h.exports}});
//# sourceMappingURL=28.js.map?v=3a37e5af31847929dab0