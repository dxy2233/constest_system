webpackJsonp([32],{"5fmV":function(t,n,o){"use strict";function i(t){o("SoI0")}Object.defineProperty(n,"__esModule",{value:!0});var e=o("mvHQ"),s=o.n(e),a=o("POHo"),c=o("SQ4B"),r=(a.a,{name:"index",components:{slide:a.a},data:function(){return{dataList:[],page:1,loadShow:!0,total:""}},methods:{handleBottom:function(t){var n=this;if(""!==this.total&&this.dataList.length>=parseInt(this.total))return void this.$refs.my_scroller.finishInfinite(!0);c.a.post("/notice",{page:this.page,page_size:10},function(t){if(n.loadShow&&(n.loadShow=!1),0!==t.code)return void n.$vux.toast.show(t.msg);n.dataList=n.dataList.concat(t.content.list),n.total=t.content.count,n.page++,n.$refs.my_scroller.finishInfinite(!1)})},getList:function(){var t=this;c.a.post("/notice",{page:this.page,page_size:10},function(n){if(t.loadShow&&(t.loadShow=!1),0!==n.code)return void t.$vux.toast.show(n.msg);t.dataList=t.dataList.concat(n.content.list),t.dataList.length<parseInt(n.content.count)?t.$refs.vueLoad.onBottomLoaded():t.$refs.vueLoad.onBottomLoaded(!1)})},lookDetails:function(t){this.getData(t.id)},getData:function(t){var n=this;c.a.post("/notice/info",{id:t},function(o){if(console.log(o),0!==o.code)return void n.$vux.toast.show(o.msg);0==o.content.type?window.location.href=o.content.url:(localStorage.setItem("noticeInfo",s()(o.content)),n.$router.push({path:"/home/notice/dts"+t}))})}},created:function(){}}),l=function(){var t=this,n=t.$createElement,o=t._self._c||n;return o("slide",[o("div",{staticClass:"notice-list"},[o("app-header",[t._v("\n      全部公告\n    ")]),t._v(" "),o("div",{staticClass:"main"},[o("scroller",{ref:"my_scroller",attrs:{"on-infinite":t.handleBottom}},[o("ul",{staticClass:"list"},t._l(t.dataList,function(n,i){return o("li",{style:{backgroundImage:"url("+n.image+")"},on:{click:function(o){t.lookDetails(n)}}})}))]),t._v(" "),o("router-view")],1)],1)])},d=[],A={render:l,staticRenderFns:d},u=A,p=o("VU/8"),f=i,h=p(r,u,!1,f,null,null);n.default=h.exports},Jvwc:function(t,n,o){n=t.exports=o("FZ+f")(!0),n.push([t.i,"\n.notice-list {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.notice-list .main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  width: 100%;\n  overflow: hidden;\n}\n.notice-list .main .list {\n  padding: 20px 15px 0 15px;\n}\n.notice-list .main .list li {\n  margin-bottom: 25px;\n  background-size: cover;\n  background-position: center center;\n  border-radius: 4px;\n  width: 100%;\n  height: 135px;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/home/noticeList/index.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,UAAU;EACV,YAAY;EACZ,iBAAiB;CAClB;AACD;EACE,0BAA0B;CAC3B;AACD;EACE,oBAAoB;EACpB,uBAAuB;EACvB,mCAAmC;EACnC,mBAAmB;EACnB,YAAY;EACZ,cAAc;CACf",file:"index.vue",sourcesContent:["\n.notice-list {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.notice-list .main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  width: 100%;\n  overflow: hidden;\n}\n.notice-list .main .list {\n  padding: 20px 15px 0 15px;\n}\n.notice-list .main .list li {\n  margin-bottom: 25px;\n  background-size: cover;\n  background-position: center center;\n  border-radius: 4px;\n  width: 100%;\n  height: 135px;\n}"],sourceRoot:""}])},SoI0:function(t,n,o){var i=o("Jvwc");"string"==typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);o("rjj0")("eadc79d4",i,!0,{})}});
//# sourceMappingURL=32.js.map?v=5c1c6f47e5412efca648