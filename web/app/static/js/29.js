webpackJsonp([29],{QWgB:function(n,t,e){t=n.exports=e("FZ+f")(!0),t.push([n.i,"\n.notice-details {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n}\n.notice-details .content-box {\n  padding: 20px 15px;\n}\n.notice-details .content-box .title {\n  margin-bottom: 20px;\n  font-weight: bold;\n  font-size: 20px;\n}\n.notice-details .content-box .detail {\n  line-height: 1.5em;\n}\n.notice-details .content-box img {\n  max-width: 100%;\n}","",{version:3,sources:["/Users/wangqingai/Desktop/contest_system/web/app/contest-app/src/views/home/noticeDetails/index.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;EACX,eAAe;CAChB;AACD;EACE,mBAAmB;CACpB;AACD;EACE,oBAAoB;EACpB,kBAAkB;EAClB,gBAAgB;CACjB;AACD;EACE,mBAAmB;CACpB;AACD;EACE,gBAAgB;CACjB",file:"index.vue",sourcesContent:["\n.notice-details {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n}\n.notice-details .content-box {\n  padding: 20px 15px;\n}\n.notice-details .content-box .title {\n  margin-bottom: 20px;\n  font-weight: bold;\n  font-size: 20px;\n}\n.notice-details .content-box .detail {\n  line-height: 1.5em;\n}\n.notice-details .content-box img {\n  max-width: 100%;\n}"],sourceRoot:""}])},hFjw:function(n,t,e){var o=e("QWgB");"string"==typeof o&&(o=[[n.i,o,""]]),o.locals&&(n.exports=o.locals);e("rjj0")("7bacd3eb",o,!0,{})},k1c5:function(n,t,e){"use strict";function o(n){e("hFjw")}Object.defineProperty(t,"__esModule",{value:!0});var i=e("POHo"),s=e("SQ4B"),a=(i.a,{name:"index",components:{slide:i.a},data:function(){return{noticeInfo:{}}},methods:{getData:function(){var n=this;s.a.post("/notice/info",{id:this.$route.params.id},function(t){if(0!==t.code)return void n.$vux.toast.show(t.msg);0==t.content.type?(n.$router.back(),window.location.href=t.content.url):n.noticeInfo=t.content})}},created:function(){this.noticeInfo=JSON.parse(localStorage.getItem("noticeInfo"))}}),c=function(){var n=this,t=n.$createElement,e=n._self._c||t;return e("slide",[e("div",{staticClass:"notice-details"},[e("app-header",[1==n.noticeInfo.type?e("span",[n._v(n._s(n.noticeInfo.title))]):n._e()]),n._v(" "),1==n.noticeInfo.type?e("div",{staticClass:"h-main"},[e("div",{staticClass:"content-box"},[e("div",{staticClass:"detail",domProps:{innerHTML:n._s(n.noticeInfo.detail)}},[n._v("egwrgwgvgwegr")])])]):n._e()],1)])},A=[],d={render:c,staticRenderFns:A},l=d,r=e("VU/8"),p=o,f=r(a,l,!1,p,null,null);t.default=f.exports}});
//# sourceMappingURL=29.js.map?v=9896d8cd1b7c17c5c844