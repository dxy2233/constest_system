webpackJsonp([37],{LU3f:function(n,t,e){var s=e("dP0X");"string"==typeof s&&(s=[[n.i,s,""]]),s.locals&&(n.exports=s.locals);e("rjj0")("eb8f1f02",s,!0,{})},dP0X:function(n,t,e){t=n.exports=e("FZ+f")(!0),t.push([n.i,"\n.assets-frozen {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.assets-frozen .main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  width: 100%;\n  overflow: hidden;\n}\n.assets-frozen .main .list {\n  min-height: 15px;\n}\n.assets-frozen .main .list li {\n  padding: 20px 15px;\n  border-bottom: 1px solid #e5e7e9;\n}\n.assets-frozen .main .list p {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  line-height: 20px;\n}\n.assets-frozen .main .list .remark {\n  font-weight: bold;\n}\n.assets-frozen .main .list .time {\n  font-size: 10px;\n  color: #959da6;\n}\n.assets-frozen .main .list .amount {\n  font-size: 12px;\n  color: #757575;\n}\n.assets-frozen .main .list .status {\n  font-size: 12px;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/assets/frozen/index.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,UAAU;EACV,YAAY;EACZ,iBAAiB;CAClB;AACD;EACE,iBAAiB;CAClB;AACD;EACE,mBAAmB;EACnB,iCAAiC;CAClC;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;EACvC,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,kBAAkB;CACnB;AACD;EACE,kBAAkB;CACnB;AACD;EACE,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,gBAAgB;CACjB",file:"index.vue",sourcesContent:["\n.assets-frozen {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.assets-frozen .main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  width: 100%;\n  overflow: hidden;\n}\n.assets-frozen .main .list {\n  min-height: 15px;\n}\n.assets-frozen .main .list li {\n  padding: 20px 15px;\n  border-bottom: 1px solid #e5e7e9;\n}\n.assets-frozen .main .list p {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  line-height: 20px;\n}\n.assets-frozen .main .list .remark {\n  font-weight: bold;\n}\n.assets-frozen .main .list .time {\n  font-size: 10px;\n  color: #959da6;\n}\n.assets-frozen .main .list .amount {\n  font-size: 12px;\n  color: #757575;\n}\n.assets-frozen .main .list .status {\n  font-size: 12px;\n}"],sourceRoot:""}])},fSs5:function(n,t,e){"use strict";function s(n){e("LU3f")}Object.defineProperty(t,"__esModule",{value:!0});var i=e("POHo"),o=e("SQ4B"),a=(i.a,{name:"index",components:{slide:i.a},data:function(){return{dataList:[],page:1,loadShow:!0,total:""}},methods:{handleBottom:function(){var n=this;if(""!==this.total&&this.dataList.length>=parseInt(this.total))return void this.$refs.my_scroller.finishInfinite(!0);o.a.post("/wallet/currency-frozen",{id:this.$route.params.id,page:this.page,page_size:10},function(t){if(n.loadShow&&(n.loadShow=!1),0!==t.code)return void n.$vux.toast.show(t.msg);n.dataList=n.dataList.concat(t.content.list),n.total=t.content.count,n.page++,n.$refs.my_scroller.finishInfinite(!1)})},getList:function(){var n=this;o.a.post("/wallet/currency-frozen",{id:this.$route.params.id,page:this.page,page_size:10},function(t){if(n.loadShow&&(n.loadShow=!1),0!==t.code)return void n.$vux.toast.show(t.msg);n.dataList=n.dataList.concat(t.content.list),n.dataList.length<parseInt(t.content.count)?n.$refs.vueLoad.onBottomLoaded():n.$refs.vueLoad.onBottomLoaded(!1)})}},created:function(){}}),A=function(){var n=this,t=n.$createElement,e=n._self._c||t;return e("slide",[e("div",{staticClass:"assets-frozen"},[e("app-header",[n._v("\n      锁仓记录\n    ")]),n._v(" "),e("div",{staticClass:"main"},[e("scroller",{ref:"my_scroller",attrs:{"on-infinite":n.handleBottom}},[e("ul",{staticClass:"list"},n._l(n.dataList,function(t){return e("li",[e("p",[e("span",{staticClass:"remark"},[n._v(n._s(t.remark))]),n._v(" "),e("span",{staticClass:"status"})]),n._v(" "),e("p",[e("span",{staticClass:"time"},[n._v(n._s(t.createTime))]),n._v(" "),e("span",{staticClass:"amount"},[n._v(n._s(t.amount))])])])}))])],1)],1)])},r=[],l={render:A,staticRenderFns:r},f=l,c=e("VU/8"),p=s,d=c(a,f,!1,p,null,null);t.default=d.exports}});
//# sourceMappingURL=37.js.map?v=0414536fa4b00f1a7184