webpackJsonp([29],{"5k9G":function(n,t,e){t=n.exports=e("FZ+f")(!0),t.push([n.i,"\n.record {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.record>.app-header {\n  border-bottom: 1px solid #c6d0da;\n}\n.record .record-main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  overflow: hidden;\n}\n.record .record-main .list {\n  min-height: 15px;\n}\n.record .record-main .list li {\n  padding: 15px;\n  border-bottom: 1px solid #e5e7e9;\n  font-size: 16px;\n}\n.record .record-main .list li p {\n  margin-top: 5px;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  font-size: 12px;\n  color: #959da6;\n}\n.record .record-main .no-data {\n  text-align: center;\n  padding-top: 55px;\n}\n.record .record-main .no-data img {\n  width: 100px;\n}\n.record .record-main .no-data p {\n  margin-top: 35px;\n  font-size: 16px;\n  color: #959da6;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/recommend/record.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,iCAAiC;CAClC;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,UAAU;EACV,QAAQ;EACR,SAAS;EACT,iBAAiB;CAClB;AACD;EACE,iBAAiB;CAClB;AACD;EACE,cAAc;EACd,iCAAiC;EACjC,gBAAgB;CACjB;AACD;EACE,gBAAgB;EAChB,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;EACvC,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,mBAAmB;EACnB,kBAAkB;CACnB;AACD;EACE,aAAa;CACd;AACD;EACE,iBAAiB;EACjB,gBAAgB;EAChB,eAAe;CAChB",file:"record.vue",sourcesContent:["\n.record {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.record>.app-header {\n  border-bottom: 1px solid #c6d0da;\n}\n.record .record-main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  overflow: hidden;\n}\n.record .record-main .list {\n  min-height: 15px;\n}\n.record .record-main .list li {\n  padding: 15px;\n  border-bottom: 1px solid #e5e7e9;\n  font-size: 16px;\n}\n.record .record-main .list li p {\n  margin-top: 5px;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  font-size: 12px;\n  color: #959da6;\n}\n.record .record-main .no-data {\n  text-align: center;\n  padding-top: 55px;\n}\n.record .record-main .no-data img {\n  width: 100px;\n}\n.record .record-main .no-data p {\n  margin-top: 35px;\n  font-size: 16px;\n  color: #959da6;\n}"],sourceRoot:""}])},hl4E:function(n,t,e){"use strict";function o(n){e("pE26")}Object.defineProperty(t,"__esModule",{value:!0});var i=e("POHo"),r=e("SQ4B"),a=(i.a,{name:"index",components:{slide:i.a},data:function(){return{dataList:[],page:1,loadShow:!0,total:""}},methods:{handleBottom:function(){var n=this;if(""!==this.total&&this.dataList.length>=parseInt(this.total))return void this.$refs.my_scroller.finishInfinite(!0);r.a.post("/user/recommend",{page:this.page,page_size:10},function(t){if(n.loadShow&&(n.loadShow=!1),0!==t.code)return void n.$vux.toast.show(t.msg);n.dataList=n.dataList.concat(t.content.list),n.total=t.content.count,n.page++,n.$refs.my_scroller.finishInfinite(!1)})},getList:function(){var n=this;r.a.post("/user/recommend",{page:this.page,page_size:10},function(t){if(n.loadShow&&(n.loadShow=!1),0!==t.code)return void n.$vux.toast.show(t.msg);n.dataList=n.dataList.concat(t.content.list),n.dataList.length,parseInt(t.content.count),n.$refs.vueLoad.onBottomLoaded()})}},created:function(){}}),s=function(){var n=this,t=n.$createElement,e=n._self._c||t;return e("slide",[e("div",{staticClass:"record"},[e("app-header",[n._v("\n      推荐记录\n    ")]),n._v(" "),e("div",{staticClass:"record-main"},[e("scroller",{ref:"my_scroller",attrs:{"on-infinite":n.handleBottom}},[n.dataList.length||n.loadShow?n._e():e("div",{staticClass:"no-data"},[e("img",{attrs:{src:"/static/images/state-fail.png",alt:""}})]),n._v(" "),e("ul",{staticClass:"list"},n._l(n.dataList,function(t){return e("li",{key:t.id},[e("h4",[n._v(n._s(t.mobile))]),n._v(" "),e("p",[e("span",[n._v(n._s(t.typeName))]),n._v(" "),e("span",[n._v(n._s(t.createTime))])])])}))])],1)],1)])},A=[],d={render:s,staticRenderFns:A},c=d,l=e("VU/8"),p=o,C=l(a,c,!1,p,null,null);t.default=C.exports},pE26:function(n,t,e){var o=e("5k9G");"string"==typeof o&&(o=[[n.i,o,""]]),o.locals&&(n.exports=o.locals);e("rjj0")("715d9e7a",o,!0,{})}});
//# sourceMappingURL=29.js.map?v=e54076f1a5fa112db0bc