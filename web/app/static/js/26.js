webpackJsonp([26],{"6bvx":function(n,t,i){var e=i("UGSa");"string"==typeof e&&(e=[[n.i,e,""]]),e.locals&&(n.exports=e.locals);i("rjj0")("25e0cad1",e,!0,{})},UGSa:function(n,t,i){t=n.exports=i("FZ+f")(!0),t.push([n.i,"\n.invitation-record {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.invitation-record>.app-header {\n  border-bottom: 1px solid #c6d0da;\n}\n.invitation-record .record-main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  overflow: hidden;\n}\n.invitation-record .record-main .list {\n  min-height: 15px;\n}\n.invitation-record .record-main .list li {\n  padding: 15px;\n  border-bottom: 1px solid #e5e7e9;\n  font-size: 16px;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.invitation-record .record-main .list li p {\n  font-size: 12px;\n  color: #959da6;\n}\n.invitation-record .record-main .no-datas {\n  text-align: center;\n  padding-top: 55px;\n}\n.invitation-record .record-main .no-datas img {\n  width: 100px;\n}\n.invitation-record .record-main .no-datas p {\n  margin-top: 35px;\n  font-size: 16px;\n  color: #959da6;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/recommend/record.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,iCAAiC;CAClC;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,UAAU;EACV,QAAQ;EACR,SAAS;EACT,iBAAiB;CAClB;AACD;EACE,iBAAiB;CAClB;AACD;EACE,cAAc;EACd,iCAAiC;EACjC,gBAAgB;EAChB,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;EACvC,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;CAC7B;AACD;EACE,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,mBAAmB;EACnB,kBAAkB;CACnB;AACD;EACE,aAAa;CACd;AACD;EACE,iBAAiB;EACjB,gBAAgB;EAChB,eAAe;CAChB",file:"record.vue",sourcesContent:["\n.invitation-record {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.invitation-record>.app-header {\n  border-bottom: 1px solid #c6d0da;\n}\n.invitation-record .record-main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  overflow: hidden;\n}\n.invitation-record .record-main .list {\n  min-height: 15px;\n}\n.invitation-record .record-main .list li {\n  padding: 15px;\n  border-bottom: 1px solid #e5e7e9;\n  font-size: 16px;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.invitation-record .record-main .list li p {\n  font-size: 12px;\n  color: #959da6;\n}\n.invitation-record .record-main .no-datas {\n  text-align: center;\n  padding-top: 55px;\n}\n.invitation-record .record-main .no-datas img {\n  width: 100px;\n}\n.invitation-record .record-main .no-datas p {\n  margin-top: 35px;\n  font-size: 16px;\n  color: #959da6;\n}"],sourceRoot:""}])},hl4E:function(n,t,i){"use strict";function e(n){i("6bvx")}Object.defineProperty(t,"__esModule",{value:!0});var o=i("POHo"),a=i("SQ4B"),r=(o.a,{name:"index",components:{slide:o.a},data:function(){return{dataList:[],page:1,loadShow:!0,total:""}},methods:{handleBottom:function(){var n=this;if(""!==this.total&&this.dataList.length>=parseInt(this.total))return void this.$refs.my_scroller.finishInfinite(!0);a.a.post("/user/recommend",{page:this.page,page_size:10},function(t){if(n.loadShow&&(n.loadShow=!1),0!==t.code)return void n.$vux.toast.show(t.msg);n.dataList=n.dataList.concat(t.content.list),n.total=t.content.count,n.page++,n.$refs.my_scroller.finishInfinite(!1)})},getList:function(){var n=this;a.a.post("/user/recommend",{page:this.page,page_size:10},function(t){if(n.loadShow&&(n.loadShow=!1),0!==t.code)return void n.$vux.toast.show(t.msg);n.dataList=n.dataList.concat(t.content.list),n.dataList.length,parseInt(t.content.count),n.$refs.vueLoad.onBottomLoaded()})}},created:function(){}}),s=function(){var n=this,t=n.$createElement,i=n._self._c||t;return i("slide",[i("div",{staticClass:"invitation-record"},[i("app-header",[n._v("\n      邀请记录\n    ")]),n._v(" "),i("div",{staticClass:"record-main"},[i("scroller",{ref:"my_scroller",attrs:{"on-infinite":n.handleBottom}},[n.dataList.length||n.loadShow?n._e():i("div",{staticClass:"no-data"},[i("img",{attrs:{src:"/static/images/state-fail.png",alt:""}})]),n._v(" "),i("ul",{staticClass:"list"},n._l(n.dataList,function(t){return i("li",{key:t.id},[i("h4",[n._v(n._s(t.mobile))]),n._v(" "),i("p",[n._v("\n              "+n._s(t.createTime)+"\n            ")])])}))])],1)],1)])},d=[],A={render:s,staticRenderFns:d},c=A,l=i("VU/8"),p=e,C=l(r,c,!1,p,null,null);t.default=C.exports}});
//# sourceMappingURL=26.js.map?v=73401b2108ca2cc57de3