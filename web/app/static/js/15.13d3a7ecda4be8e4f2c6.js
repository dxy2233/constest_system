webpackJsonp([15],{d2tS:function(n,t,e){var o=e("oncc");"string"==typeof o&&(o=[[n.i,o,""]]),o.locals&&(n.exports=o.locals);e("rjj0")("3f7eab19",o,!0,{})},oncc:function(n,t,e){t=n.exports=e("FZ+f")(!0),t.push([n.i,"\n.personal-set {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.personal-set .weui-cells {\n  margin: 0;\n}\n.personal-set .weui-cells .weui-cell {\n  padding: 15px 15px;\n}\n.personal-set .btn-box {\n  position: absolute;\n  left: 15px;\n  right: 15px;\n  bottom: 50px;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/set/index.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,UAAU;CACX;AACD;EACE,mBAAmB;CACpB;AACD;EACE,mBAAmB;EACnB,WAAW;EACX,YAAY;EACZ,aAAa;CACd",file:"index.vue",sourcesContent:["\n.personal-set {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.personal-set .weui-cells {\n  margin: 0;\n}\n.personal-set .weui-cells .weui-cell {\n  padding: 15px 15px;\n}\n.personal-set .btn-box {\n  position: absolute;\n  left: 15px;\n  right: 15px;\n  bottom: 50px;\n}"],sourceRoot:""}])},wGDG:function(n,t,e){"use strict";function o(n){e("d2tS")}Object.defineProperty(t,"__esModule",{value:!0});var s=e("Dd8w"),i=e.n(s),l=e("POHo"),a=e("SQ4B"),r=e("rHil"),p=e("1DHf"),c=e("32ER"),A=e("NYxO"),u=e("l1VX"),d=(l.a,r.a,p.a,c.a,i()({},Object(A.b)(["loginMsg","identifyMsg"])),{name:"index",components:{slide:l.a,Group:r.a,Cell:p.a,CellBox:c.a},data:function(){return{hidMobile:u.c,btnLoading:!1}},methods:{loginOut:function(){var n=this;this.btnLoading=!0,a.a.post("/login/logout",{},function(t){n.btnLoading=!1,n.$vux.toast.show(t.msg),0===t.code&&(Object(u.b)(),n.$router.replace({path:"/home"}))})}},computed:i()({},Object(A.b)(["loginMsg","identifyMsg"]))}),g=function(){var n=this,t=n.$createElement,e=n._self._c||t;return e("slide",[e("div",{staticClass:"personal-set"},[e("app-header",[n._v("\n      设置\n    ")]),n._v(" "),e("div",{staticClass:"h-main"},[e("group",[e("cell",{attrs:{title:"当前账号",value:n.hidMobile(n.loginMsg?n.loginMsg.mobile:"")}}),n._v(" "),e("cell",{attrs:{title:"关于","is-link":"",link:"/personal/set/about"}})],1),n._v(" "),e("div",{staticClass:"btn-box"},[e("x-button",{staticClass:"login-out",attrs:{type:"warn","show-loading":n.btnLoading},nativeOn:{click:function(t){return n.loginOut(t)}}},[n._v("退出登录")])],1)],1),n._v(" "),e("router-view")],1)])},b=[],C={render:g,staticRenderFns:b},f=C,x=e("VU/8"),v=o,m=x(d,f,!1,v,null,null);t.default=m.exports}});
//# sourceMappingURL=15.13d3a7ecda4be8e4f2c6.js.map