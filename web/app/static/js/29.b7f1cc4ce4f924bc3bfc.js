webpackJsonp([29],{DDM9:function(n,t,e){t=n.exports=e("FZ+f")(!0),t.push([n.i,"\n.notice-details {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n}\n.notice-details .content-box {\n  padding: 20px 15px;\n}\n.notice-details .content-box .title {\n  margin-bottom: 20px;\n  font-weight: bold;\n  font-size: 20px;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/home/noticeDetails/index.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;EACX,eAAe;CAChB;AACD;EACE,mBAAmB;CACpB;AACD;EACE,oBAAoB;EACpB,kBAAkB;EAClB,gBAAgB;CACjB",file:"index.vue",sourcesContent:["\n.notice-details {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n}\n.notice-details .content-box {\n  padding: 20px 15px;\n}\n.notice-details .content-box .title {\n  margin-bottom: 20px;\n  font-weight: bold;\n  font-size: 20px;\n}"],sourceRoot:""}])},k1c5:function(n,t,e){"use strict";function o(n){e("zGId")}Object.defineProperty(t,"__esModule",{value:!0});var i=e("POHo"),s=e("SQ4B"),a=(i.a,{name:"index",components:{slide:i.a},data:function(){return{noticeInfo:{}}},methods:{getData:function(){var n=this;s.a.post("/notice/info",{id:this.$route.params.id},function(t){if(0!==t.code)return void n.$vux.toast.show(t.msg);n.noticeInfo=t.content})}},created:function(){this.getData()}}),c=function(){var n=this,t=n.$createElement,e=n._self._c||t;return e("slide",[e("div",{staticClass:"notice-details"},[e("app-header",[n._v("\n      "+n._s(n.noticeInfo.title)+"\n    ")]),n._v(" "),e("div",{staticClass:"h-main"},[e("div",{staticClass:"content-box"},[e("div",{staticClass:"detail",domProps:{innerHTML:n._s(n.noticeInfo.detail)}},[n._v("egwrgwgvgwegr")])])])],1)])},d=[],A={render:c,staticRenderFns:d},r=A,l=e("VU/8"),f=o,p=l(a,r,!1,f,null,null);t.default=p.exports},zGId:function(n,t,e){var o=e("DDM9");"string"==typeof o&&(o=[[n.i,o,""]]),o.locals&&(n.exports=o.locals);e("rjj0")("bb4419f2",o,!0,{})}});
//# sourceMappingURL=29.b7f1cc4ce4f924bc3bfc.js.map