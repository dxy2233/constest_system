webpackJsonp([34],{B6Qn:function(t,n,e){"use strict";function i(t){e("rb4h")}Object.defineProperty(n,"__esModule",{value:!0});var s=e("POHo"),a=e("SQ4B"),o=e("jijt"),l=(s.a,o.a,{name:"index",components:{slide:s.a,InlineLoading:o.a},data:function(){return{tabList:[{type:"1",name:"收入明细"},{type:"0",name:"支出记录"}],currentType:sessionStorage.getItem("currencyDetailType")||"1",dataList:[],page:1,loadShow:!0,currencyInfo:{},dtsId:this.$route.params.id,total:"",refreshLoad:!1}},methods:{refreshData:function(){var t=this;this.refreshLoad=!0,a.a.post("/wallet/recharge-refresh",{id:this.$route.params.id},function(n){if(n.content.isRefresh=!0,t.refreshLoad=!1,0!==n.code)return void t.$vux.toast.show(n.msg);n.content.isRefresh&&(t.getCurrencyInfo(),t.page=1,t.dataList=[],t.total="",t.$refs.my_scroller.finishInfinite(!1))})},goFrozen:function(){this.$router.push({path:"/assets/dts"+this.dtsId+"/frozen"})},selectedTab:function(t){t.type!==this.currentType&&(this.currentType=t.type,sessionStorage.setItem("currencyDetailType",t.type),this.page=1,this.dataList=[],this.total="",this.$refs.my_scroller.finishInfinite(!1))},handleBottom:function(){var t=this;if(""!==this.total&&this.dataList.length>=parseInt(this.total))return void this.$refs.my_scroller.finishInfinite(!0);a.a.post("/wallet/currency-detail",{id:this.$route.params.id,type:this.currentType,page:this.page,page_size:10},function(n){if(t.loadShow&&(t.loadShow=!1),0!==n.code)return void t.$vux.toast.show(n.msg);t.dataList=t.dataList.concat(n.content.list),t.total=n.content.count,t.page++,t.$refs.my_scroller.finishInfinite(!1)})},getList:function(){var t=this;a.a.post("/wallet/currency-detail",{id:this.$route.params.id,type:this.currentType,page:this.page,page_size:10},function(n){if(t.loadShow&&(t.loadShow=!1),0!==n.code)return void t.$vux.toast.show(n.msg);t.dataList=t.dataList.concat(n.content.list),t.dataList.length<parseInt(n.content.count)?t.$refs.vueLoad.onBottomLoaded():t.$refs.vueLoad.onBottomLoaded(!1)})},getCurrencyInfo:function(){var t=this;a.a.post("/wallet/currency-info",{id:this.$route.params.id},function(n){if(0!==n.code)return void t.$vux.toast.show(n.msg);t.currencyInfo=n.content})}},created:function(){this.getCurrencyInfo()},destroyed:function(){sessionStorage.removeItem("currentType")}}),A=function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("slide",[e("div",{staticClass:"assets-details"},[e("app-header",[e("div",{staticClass:"refresh-btn",attrs:{slot:"right"},on:{click:t.refreshData},slot:"right"},[e("inline-loading",{directives:[{name:"show",rawName:"v-show",value:t.refreshLoad,expression:"refreshLoad"}]}),t._v(" "),e("span",[t._v("刷新数据")])],1)]),t._v(" "),e("div",{staticClass:"assets-details-main"},[e("div",{staticClass:"brief"},[e("div",{staticClass:"top"},[e("p",[t._v(t._s(t.currencyInfo.name))]),t._v(" "),e("h3",[t._v(t._s(t.currencyInfo.positionAmount))])]),t._v(" "),e("ul",{staticClass:"bottom"},[e("li",[e("p",[t._v("可用")]),t._v(" "),e("h4",[t._v(t._s(t.currencyInfo.useAmount))])]),t._v(" "),e("li",{on:{click:t.goFrozen}},[e("p",[t._v("锁仓")]),t._v(" "),e("h4",[t._v(t._s(t.currencyInfo.frozenAmount))])])])]),t._v(" "),e("ul",{staticClass:"tab-list"},t._l(t.tabList,function(n){return e("li",{key:n.name,class:{act:n.type===t.currentType},on:{click:function(e){t.selectedTab(n)}}},[t._v(t._s(n.name)+"\n        ")])})),t._v(" "),e("div",{staticClass:"detail"},[e("scroller",{ref:"my_scroller",attrs:{"on-infinite":t.handleBottom}},[e("ul",{staticClass:"detail-list"},t._l(t.dataList,function(n){return e("li",[e("p",[e("span",{staticClass:"remark"},[t._v(t._s(n.remark))]),t._v(" "),e("span",{staticClass:"status"},[t._v(t._s(n.statusStr))])]),t._v(" "),e("p",[e("span",{staticClass:"time"},[t._v(t._s(n.effectTime))]),t._v(" "),e("span",{staticClass:"amount"},[t._v(t._s(n.amount))])])])}))])],1),t._v(" "),e("div",{staticClass:"handle-btn"},[e("ul",[parseInt(t.currencyInfo.rechargeStatus)?e("router-link",{attrs:{tag:"li",to:{path:"/assets/dts"+t.dtsId+"/collect",query:{name:t.currencyInfo.name}}}},[e("img",{attrs:{src:"/static/images/collect.png",alt:""}}),t._v(" "),e("span",[t._v("收款")])]):t._e(),t._v(" "),parseInt(t.currencyInfo.withdrawStatus)?e("router-link",{attrs:{tag:"li",to:"/assets/dts"+t.dtsId+"/transfer"}},[e("img",{attrs:{src:"/static/images/transfer.png",alt:""}}),t._v(" "),e("span",[t._v("转账")])]):t._e()],1)])]),t._v(" "),e("router-view")],1)])},r=[],d={render:A,staticRenderFns:r},p=d,c=e("VU/8"),B=i,f=c(l,p,!1,B,null,null);n.default=f.exports},PsCr:function(t,n,e){n=t.exports=e("FZ+f")(!0),n.push([t.i,'\n.assets-details {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n/*.handle-btn\n    position absolute\n    left $space-box\n    bottom $space-box\n    right $space-box\n    ul\n      border-radius 10px\n      overflow hidden\n      background $color-theme\n      line-height 48px\n      height 48px\n      li\n        width 50%\n        float left\n        text-align center\n        font-size $font-size-medium-x\n        color white\n    .line\n      position absolute\n      top 0\n      left 50%\n      height 48px\n      width 1px\n      background white*/\n}\n.assets-details .refresh-btn {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.assets-details .refresh-btn span {\n  margin-left: 5px;\n}\n.assets-details .assets-details-main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  width: 100%;\n}\n.assets-details .brief {\n  border-radius: 10px;\n  background: #ffb760;\n  overflow: hidden;\n  color: #fff;\n  box-shadow: 0 4px 15px 4px RGBA(240, 208, 172, 0.5);\n  margin: 10px 15px;\n}\n.assets-details .brief > .top {\n  padding: 15px 0;\n  background: url("/static/images/assets-bg1.png") center no-repeat;\n  background-size: cover;\n  text-align: center;\n}\n.assets-details .brief > .top p {\n  font-size: $fs$font-size-large;\n  height: $fs$font-size-large;\n}\n.assets-details .brief > .top h3 {\n  margin-top: 10px;\n  font-weight: 700;\n  font-size: 36px;\n  height: 36px;\n}\n.assets-details .brief > .bottom {\n  padding: 10px 0;\n  margin-left: -1px;\n  overflow: hidden;\n}\n.assets-details .brief > .bottom li {\n  width: 50%;\n  box-sizing: border-box;\n  border-left: 1px solid #fff;\n  text-align: center;\n  float: left;\n}\n.assets-details .brief > .bottom li h4 {\n  margin-top: 10px;\n  font-size: 20px;\n  height: 20px;\n}\n.assets-details .tab-list {\n  box-sizing: border-box;\n  line-height: 50px;\n  height: 50px;\n  margin-left: 15px;\n}\n.assets-details .tab-list li {\n  padding: 0 3px;\n  display: inline-block;\n  box-sizing: border-box;\n  margin-right: 30px;\n  font-size: 18px;\n  color: #959da6;\n}\n.assets-details .tab-list li.act {\n  color: #ff4800;\n}\n.assets-details .detail {\n  position: absolute;\n  top: 225px;\n  bottom: 0;\n  padding-bottom: 60px;\n  width: 100%;\n  overflow: hidden;\n}\n.assets-details .detail .detail-list {\n  padding-left: 15px;\n  min-height: 20px;\n}\n.assets-details .detail .detail-list li {\n  padding: 15px 15px 15px 0;\n  border-bottom: 1px solid #dbdbdb;\n}\n.assets-details .detail .detail-list li p {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  line-height: 20px;\n}\n.assets-details .detail .detail-list li .remark {\n  font-weight: bold;\n}\n.assets-details .detail .detail-list li .time {\n  font-size: 10px;\n  color: #959da6;\n}\n.assets-details .detail .detail-list li .amount {\n  font-size: 12px;\n  color: #757575;\n}\n.assets-details .detail .detail-list li .status {\n  font-size: 12px;\n}\n.assets-details .handle-btn {\n  position: absolute;\n  left: 0;\n  bottom: 0;\n  right: 0;\n}\n.assets-details .handle-btn ul {\n  overflow: hidden;\n  line-height: 60px;\n  height: 60px;\n  margin-left: -1px;\n  box-sizing: border-box;\n}\n.assets-details .handle-btn ul li {\n  width: 50%;\n  float: left;\n  text-align: center;\n  border-top: 1px solid #c6d0da;\n  font-size: 18px;\n  box-sizing: border-box;\n  border-left: 1px solid #c6d0da;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  -webkit-box-pack: center;\n  -webkit-justify-content: center;\n          justify-content: center;\n}\n.assets-details .handle-btn ul li img {\n  margin-right: 5px;\n  width: 25px;\n}',"",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/assets/assetsDetails/index.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;EACX,eAAe;AACjB;;;;;;;;;;;;;;;;;;;;;;;wBAuBwB;CACvB;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;CAC7B;AACD;EACE,iBAAiB;CAClB;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,UAAU;EACV,YAAY;CACb;AACD;EACE,oBAAoB;EACpB,oBAAoB;EACpB,iBAAiB;EACjB,YAAY;EACZ,oDAAoD;EACpD,kBAAkB;CACnB;AACD;EACE,gBAAgB;EAChB,kEAAkE;EAClE,uBAAuB;EACvB,mBAAmB;CACpB;AACD;EACE,+BAA+B;EAC/B,4BAA4B;CAC7B;AACD;EACE,iBAAiB;EACjB,iBAAiB;EACjB,gBAAgB;EAChB,aAAa;CACd;AACD;EACE,gBAAgB;EAChB,kBAAkB;EAClB,iBAAiB;CAClB;AACD;EACE,WAAW;EACX,uBAAuB;EACvB,4BAA4B;EAC5B,mBAAmB;EACnB,YAAY;CACb;AACD;EACE,iBAAiB;EACjB,gBAAgB;EAChB,aAAa;CACd;AACD;EACE,uBAAuB;EACvB,kBAAkB;EAClB,aAAa;EACb,kBAAkB;CACnB;AACD;EACE,eAAe;EACf,sBAAsB;EACtB,uBAAuB;EACvB,mBAAmB;EACnB,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,eAAe;CAChB;AACD;EACE,mBAAmB;EACnB,WAAW;EACX,UAAU;EACV,qBAAqB;EACrB,YAAY;EACZ,iBAAiB;CAClB;AACD;EACE,mBAAmB;EACnB,iBAAiB;CAClB;AACD;EACE,0BAA0B;EAC1B,iCAAiC;CAClC;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;EACvC,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,kBAAkB;CACnB;AACD;EACE,kBAAkB;CACnB;AACD;EACE,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,gBAAgB;CACjB;AACD;EACE,mBAAmB;EACnB,QAAQ;EACR,UAAU;EACV,SAAS;CACV;AACD;EACE,iBAAiB;EACjB,kBAAkB;EAClB,aAAa;EACb,kBAAkB;EAClB,uBAAuB;CACxB;AACD;EACE,WAAW;EACX,YAAY;EACZ,mBAAmB;EACnB,8BAA8B;EAC9B,gBAAgB;EAChB,uBAAuB;EACvB,+BAA+B;EAC/B,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,yBAAyB;EACzB,gCAAgC;UACxB,wBAAwB;CACjC;AACD;EACE,kBAAkB;EAClB,YAAY;CACb",file:"index.vue",sourcesContent:['\n.assets-details {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n/*.handle-btn\n    position absolute\n    left $space-box\n    bottom $space-box\n    right $space-box\n    ul\n      border-radius 10px\n      overflow hidden\n      background $color-theme\n      line-height 48px\n      height 48px\n      li\n        width 50%\n        float left\n        text-align center\n        font-size $font-size-medium-x\n        color white\n    .line\n      position absolute\n      top 0\n      left 50%\n      height 48px\n      width 1px\n      background white*/\n}\n.assets-details .refresh-btn {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.assets-details .refresh-btn span {\n  margin-left: 5px;\n}\n.assets-details .assets-details-main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  width: 100%;\n}\n.assets-details .brief {\n  border-radius: 10px;\n  background: #ffb760;\n  overflow: hidden;\n  color: #fff;\n  box-shadow: 0 4px 15px 4px RGBA(240, 208, 172, 0.5);\n  margin: 10px 15px;\n}\n.assets-details .brief > .top {\n  padding: 15px 0;\n  background: url("/static/images/assets-bg1.png") center no-repeat;\n  background-size: cover;\n  text-align: center;\n}\n.assets-details .brief > .top p {\n  font-size: $fs$font-size-large;\n  height: $fs$font-size-large;\n}\n.assets-details .brief > .top h3 {\n  margin-top: 10px;\n  font-weight: 700;\n  font-size: 36px;\n  height: 36px;\n}\n.assets-details .brief > .bottom {\n  padding: 10px 0;\n  margin-left: -1px;\n  overflow: hidden;\n}\n.assets-details .brief > .bottom li {\n  width: 50%;\n  box-sizing: border-box;\n  border-left: 1px solid #fff;\n  text-align: center;\n  float: left;\n}\n.assets-details .brief > .bottom li h4 {\n  margin-top: 10px;\n  font-size: 20px;\n  height: 20px;\n}\n.assets-details .tab-list {\n  box-sizing: border-box;\n  line-height: 50px;\n  height: 50px;\n  margin-left: 15px;\n}\n.assets-details .tab-list li {\n  padding: 0 3px;\n  display: inline-block;\n  box-sizing: border-box;\n  margin-right: 30px;\n  font-size: 18px;\n  color: #959da6;\n}\n.assets-details .tab-list li.act {\n  color: #ff4800;\n}\n.assets-details .detail {\n  position: absolute;\n  top: 225px;\n  bottom: 0;\n  padding-bottom: 60px;\n  width: 100%;\n  overflow: hidden;\n}\n.assets-details .detail .detail-list {\n  padding-left: 15px;\n  min-height: 20px;\n}\n.assets-details .detail .detail-list li {\n  padding: 15px 15px 15px 0;\n  border-bottom: 1px solid #dbdbdb;\n}\n.assets-details .detail .detail-list li p {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  line-height: 20px;\n}\n.assets-details .detail .detail-list li .remark {\n  font-weight: bold;\n}\n.assets-details .detail .detail-list li .time {\n  font-size: 10px;\n  color: #959da6;\n}\n.assets-details .detail .detail-list li .amount {\n  font-size: 12px;\n  color: #757575;\n}\n.assets-details .detail .detail-list li .status {\n  font-size: 12px;\n}\n.assets-details .handle-btn {\n  position: absolute;\n  left: 0;\n  bottom: 0;\n  right: 0;\n}\n.assets-details .handle-btn ul {\n  overflow: hidden;\n  line-height: 60px;\n  height: 60px;\n  margin-left: -1px;\n  box-sizing: border-box;\n}\n.assets-details .handle-btn ul li {\n  width: 50%;\n  float: left;\n  text-align: center;\n  border-top: 1px solid #c6d0da;\n  font-size: 18px;\n  box-sizing: border-box;\n  border-left: 1px solid #c6d0da;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  -webkit-box-pack: center;\n  -webkit-justify-content: center;\n          justify-content: center;\n}\n.assets-details .handle-btn ul li img {\n  margin-right: 5px;\n  width: 25px;\n}'],sourceRoot:""}])},rb4h:function(t,n,e){var i=e("PsCr");"string"==typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);e("rjj0")("8747cdde",i,!0,{})}});
//# sourceMappingURL=34.js.map?v=f8ce572adf9190e6aa91