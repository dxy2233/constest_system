webpackJsonp([17],{"1Eoy":function(t,n,e){"use strict";function i(t){e("7kAW")}function s(t){e("7Lh0")}Object.defineProperty(n,"__esModule",{value:!0});var a=e("POHo"),o=e("SQ4B"),A=e("jijt"),l={name:"index",data:function(){return{isLoading:!1,noMore:!1,err:!1}},methods:{loadMore:function(){this.isMoreLoading=!0,this.isLoading=!0,this.$emit("loadMore")}},computed:{scrollDisabled:function(){return!!this.noMore||this.isLoading}},mounted:function(){var t=this;this.$on("finishInfinite",function(n){t.isLoading=!1,n&&(t.noMore=n)}),this.$on("errInfinite",function(){t.isLoading=!1,t.noMore=!0,t.err=!0})}},d=function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("div",{staticClass:"load-more"},[e("div",{directives:[{name:"infinite-scroll",rawName:"v-infinite-scroll",value:t.loadMore,expression:"loadMore"}],attrs:{"infinite-scroll-disabled":"scrollDisabled","infinite-scroll-distance":"10"}},[t._t("default")],2),t._v(" "),t.isLoading?e("div",{staticClass:"loading-box"},[e("mt-spinner",{staticClass:"loading-more",attrs:{type:"fading-circle",size:20}}),t._v(" "),e("span",{staticClass:"loading-more-txt"},[t._v("加载中...")])],1):t._e(),t._v(" "),t.noMore?e("div",{staticClass:"no-more loading-box"},[t.err?e("p",[t._v("出错了~")]):e("p",[t._v("没有更多了~")])]):t._e()])},r=[],p={render:d,staticRenderFns:r},c=p,g=e("VU/8"),C=i,B=g(l,c,!1,C,"data-v-9db44820",null),f=B.exports,b=(a.a,A.a,{name:"index",components:{slide:a.a,InlineLoading:A.a,MLoad:f},data:function(){return{tabList:[{type:"1",name:"获取明细"},{type:"0",name:"领取记录"}],currentType:localStorage.getItem("currencyDetailType")||"1",dataList:[],page:1,currencyInfo:{},dtsId:this.$route.params.id,total:"",refreshLoad:!1,data1:{page:1,dataList:[],total:""},data0:{page:1,dataList:[],total:""}}},methods:{goFrozen:function(){this.$router.push({path:"/assets/dts"+this.dtsId+"/frozen"})},selectedTab:function(t){t.type!==this.currentType&&(this.currentType=t.type,localStorage.setItem("currencyDetailType",t.type))},handleBottom1:function(){var t=this;o.a.post("/wallet/currency-detail",{id:this.$route.params.id,page:this.data1.page,type:"1",page_size:10},function(n){if(0!==n.code)return void t.$vux.toast.show(n.msg);t.data1.dataList=t.data1.dataList.concat(n.content.list),t.data1.total=n.content.count,t.data1.page++;var e=t.data1.dataList.length>=n.content.count;t.$refs.my_scroller1.$emit("finishInfinite",e)})},handleBottom0:function(){var t=this;o.a.post("/wallet/currency-auditing",{id:this.$route.params.id,page:this.data0.page,page_size:10},function(n){if(0!==n.code)return void t.$vux.toast.show(n.msg);t.data0.dataList=t.data0.dataList.concat(n.content.list),t.data0.total=n.content.count,t.data0.page++;var e=t.data0.dataList.length>=n.content.count;t.$refs.my_scroller0.$emit("finishInfinite",e)})},getCurrencyInfo:function(){var t=this;o.a.post("/wallet/currency-info",{id:this.$route.params.id},function(n){if(0!==n.code)return void t.$vux.toast.show(n.msg);t.currencyInfo=n.content})}},created:function(){this.getCurrencyInfo()},destroyed:function(){localStorage.removeItem("currentType")}}),u=function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("slide",[e("div",{staticClass:"assets-details-gdt"},[e("app-header"),t._v(" "),e("div",{staticClass:"assets-details-main"},[e("div",{staticClass:"brief"},[e("div",{staticClass:"top"},[e("p",[t._v(t._s(t.currencyInfo.name))]),t._v(" "),e("h3",[t._v(t._s(t.currencyInfo.positionAmount))])])]),t._v(" "),e("ul",{staticClass:"tab-list"},t._l(t.tabList,function(n){return e("li",{key:n.name,class:{act:n.type===t.currentType},on:{click:function(e){t.selectedTab(n)}}},[t._v(t._s(n.name)+"\n        ")])})),t._v(" "),e("m-load",{directives:[{name:"show",rawName:"v-show",value:"1"===t.currentType,expression:"currentType==='1'"}],ref:"my_scroller1",staticClass:"detail",on:{loadMore:t.handleBottom1}},[e("ul",{staticClass:"detail-list"},t._l(t.data1.dataList,function(n){return e("li",[e("p",[e("span",{staticClass:"remark"},[t._v(t._s(n.remark))]),t._v(" "),e("span",{staticClass:"status"},[t._v(t._s(n.statusStr))])]),t._v(" "),e("p",[e("span",{staticClass:"time"},[t._v(t._s(n.effectTime))]),t._v(" "),e("span",{staticClass:"amount"},[t._v(t._s(n.amount))])])])}))]),t._v(" "),e("m-load",{directives:[{name:"show",rawName:"v-show",value:"0"===t.currentType,expression:"currentType==='0'"}],ref:"my_scroller0",staticClass:"detail",on:{loadMore:t.handleBottom0}},[e("ul",{staticClass:"detail-list"},t._l(t.data0.dataList,function(n){return e("router-link",{key:n.id,attrs:{tag:"li",to:t.$route.path+"/"+n.id}},[e("p",[e("span",{staticClass:"remark"},[t._v(t._s(n.remark))]),t._v(" "),e("span",{staticClass:"status"},[t._v(t._s(n.statusStr))])]),t._v(" "),e("p",[e("span",{staticClass:"time"},[t._v(t._s(n.createTime))]),t._v(" "),e("span",{staticClass:"amount"},[t._v(t._s(n.amount))])])])}))]),t._v(" "),e("div",{staticClass:"handle-btn"},[parseInt(t.currencyInfo.withdrawStatus)?e("router-link",{attrs:{tag:"button",to:{path:"/assets/dts"+t.dtsId+"/transfer",query:{name:"gdt"}}}},[e("span",[t._v("领取")])]):t._e()],1)],1),t._v(" "),e("router-view")],1)])},x=[],m={render:u,staticRenderFns:x},h=m,E=e("VU/8"),v=s,k=E(b,h,!1,v,null,null);n.default=k.exports},"6fe5":function(t,n,e){n=t.exports=e("FZ+f")(!0),n.push([t.i,'\n.assets-details-gdt {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.assets-details-gdt .refresh-btn {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.assets-details-gdt .refresh-btn span {\n  margin-left: 5px;\n}\n.assets-details-gdt .assets-details-main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  width: 100%;\n}\n.assets-details-gdt .brief {\n  border-radius: 10px;\n  background: #ffb760;\n  overflow: hidden;\n  color: #fff;\n  box-shadow: 0 4px 15px 4px RGBA(240, 208, 172, 0.5);\n  margin: 10px 15px;\n}\n.assets-details-gdt .brief > .top {\n  padding: 30px 0;\n  background: url("/static/images/assets-bg1.png") center no-repeat;\n  background-size: cover;\n  text-align: center;\n}\n.assets-details-gdt .brief > .top p {\n  font-size: 20px;\n  height: 20px;\n}\n.assets-details-gdt .brief > .top h3 {\n  margin-top: 10px;\n  font-weight: 700;\n  font-size: 36px;\n  height: 36px;\n}\n.assets-details-gdt .brief > .bottom {\n  padding: 10px 0;\n  margin-left: -1px;\n  overflow: hidden;\n}\n.assets-details-gdt .brief > .bottom li {\n  width: 50%;\n  box-sizing: border-box;\n  border-left: 1px solid #fff;\n  text-align: center;\n  float: left;\n}\n.assets-details-gdt .brief > .bottom li h4 {\n  margin-top: 10px;\n  font-size: 20px;\n  height: 20px;\n}\n.assets-details-gdt .tab-list {\n  box-sizing: border-box;\n  line-height: 50px;\n  height: 50px;\n  margin-left: 15px;\n}\n.assets-details-gdt .tab-list li {\n  padding: 0 3px;\n  display: inline-block;\n  box-sizing: border-box;\n  margin-right: 30px;\n  font-size: 18px;\n  color: #959da6;\n}\n.assets-details-gdt .tab-list li.act {\n  color: #ff4800;\n}\n.assets-details-gdt .detail {\n  position: absolute;\n  top: 195px;\n  bottom: 0;\n  padding-bottom: 60px;\n  width: 100%;\n  overflow: scroll;\n}\n.assets-details-gdt .detail .detail-list {\n  padding-left: 15px;\n  min-height: 20px;\n}\n.assets-details-gdt .detail .detail-list li {\n  padding: 15px 15px 15px 0;\n  border-bottom: 1px solid #e5e7e9;\n}\n.assets-details-gdt .detail .detail-list li p {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  line-height: 20px;\n}\n.assets-details-gdt .detail .detail-list li .remark {\n  font-weight: bold;\n}\n.assets-details-gdt .detail .detail-list li .time {\n  font-size: 10px;\n  color: #959da6;\n}\n.assets-details-gdt .detail .detail-list li .amount {\n  font-size: 12px;\n  color: #757575;\n}\n.assets-details-gdt .detail .detail-list li .status {\n  font-size: 12px;\n}\n.assets-details-gdt .handle-btn {\n  position: absolute;\n  left: 0;\n  bottom: 0;\n  right: 0;\n  background: #fff;\n  padding: 10px 15px;\n}\n.assets-details-gdt .handle-btn button {\n  text-align: center;\n  border: none;\n  font-size: 18px;\n  box-sizing: border-box;\n  width: 100%;\n  line-height: 40px;\n  color: #fff;\n  background: #ff4800;\n  border-radius: 10px;\n}',"",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/assets/assetsDetails/gdt.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;CAC7B;AACD;EACE,iBAAiB;CAClB;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,UAAU;EACV,YAAY;CACb;AACD;EACE,oBAAoB;EACpB,oBAAoB;EACpB,iBAAiB;EACjB,YAAY;EACZ,oDAAoD;EACpD,kBAAkB;CACnB;AACD;EACE,gBAAgB;EAChB,kEAAkE;EAClE,uBAAuB;EACvB,mBAAmB;CACpB;AACD;EACE,gBAAgB;EAChB,aAAa;CACd;AACD;EACE,iBAAiB;EACjB,iBAAiB;EACjB,gBAAgB;EAChB,aAAa;CACd;AACD;EACE,gBAAgB;EAChB,kBAAkB;EAClB,iBAAiB;CAClB;AACD;EACE,WAAW;EACX,uBAAuB;EACvB,4BAA4B;EAC5B,mBAAmB;EACnB,YAAY;CACb;AACD;EACE,iBAAiB;EACjB,gBAAgB;EAChB,aAAa;CACd;AACD;EACE,uBAAuB;EACvB,kBAAkB;EAClB,aAAa;EACb,kBAAkB;CACnB;AACD;EACE,eAAe;EACf,sBAAsB;EACtB,uBAAuB;EACvB,mBAAmB;EACnB,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,eAAe;CAChB;AACD;EACE,mBAAmB;EACnB,WAAW;EACX,UAAU;EACV,qBAAqB;EACrB,YAAY;EACZ,iBAAiB;CAClB;AACD;EACE,mBAAmB;EACnB,iBAAiB;CAClB;AACD;EACE,0BAA0B;EAC1B,iCAAiC;CAClC;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;EACvC,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,kBAAkB;CACnB;AACD;EACE,kBAAkB;CACnB;AACD;EACE,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,gBAAgB;CACjB;AACD;EACE,mBAAmB;EACnB,QAAQ;EACR,UAAU;EACV,SAAS;EACT,iBAAiB;EACjB,mBAAmB;CACpB;AACD;EACE,mBAAmB;EACnB,aAAa;EACb,gBAAgB;EAChB,uBAAuB;EACvB,YAAY;EACZ,kBAAkB;EAClB,YAAY;EACZ,oBAAoB;EACpB,oBAAoB;CACrB",file:"gdt.vue",sourcesContent:['\n.assets-details-gdt {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.assets-details-gdt .refresh-btn {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.assets-details-gdt .refresh-btn span {\n  margin-left: 5px;\n}\n.assets-details-gdt .assets-details-main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  width: 100%;\n}\n.assets-details-gdt .brief {\n  border-radius: 10px;\n  background: #ffb760;\n  overflow: hidden;\n  color: #fff;\n  box-shadow: 0 4px 15px 4px RGBA(240, 208, 172, 0.5);\n  margin: 10px 15px;\n}\n.assets-details-gdt .brief > .top {\n  padding: 30px 0;\n  background: url("/static/images/assets-bg1.png") center no-repeat;\n  background-size: cover;\n  text-align: center;\n}\n.assets-details-gdt .brief > .top p {\n  font-size: 20px;\n  height: 20px;\n}\n.assets-details-gdt .brief > .top h3 {\n  margin-top: 10px;\n  font-weight: 700;\n  font-size: 36px;\n  height: 36px;\n}\n.assets-details-gdt .brief > .bottom {\n  padding: 10px 0;\n  margin-left: -1px;\n  overflow: hidden;\n}\n.assets-details-gdt .brief > .bottom li {\n  width: 50%;\n  box-sizing: border-box;\n  border-left: 1px solid #fff;\n  text-align: center;\n  float: left;\n}\n.assets-details-gdt .brief > .bottom li h4 {\n  margin-top: 10px;\n  font-size: 20px;\n  height: 20px;\n}\n.assets-details-gdt .tab-list {\n  box-sizing: border-box;\n  line-height: 50px;\n  height: 50px;\n  margin-left: 15px;\n}\n.assets-details-gdt .tab-list li {\n  padding: 0 3px;\n  display: inline-block;\n  box-sizing: border-box;\n  margin-right: 30px;\n  font-size: 18px;\n  color: #959da6;\n}\n.assets-details-gdt .tab-list li.act {\n  color: #ff4800;\n}\n.assets-details-gdt .detail {\n  position: absolute;\n  top: 195px;\n  bottom: 0;\n  padding-bottom: 60px;\n  width: 100%;\n  overflow: scroll;\n}\n.assets-details-gdt .detail .detail-list {\n  padding-left: 15px;\n  min-height: 20px;\n}\n.assets-details-gdt .detail .detail-list li {\n  padding: 15px 15px 15px 0;\n  border-bottom: 1px solid #e5e7e9;\n}\n.assets-details-gdt .detail .detail-list li p {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  line-height: 20px;\n}\n.assets-details-gdt .detail .detail-list li .remark {\n  font-weight: bold;\n}\n.assets-details-gdt .detail .detail-list li .time {\n  font-size: 10px;\n  color: #959da6;\n}\n.assets-details-gdt .detail .detail-list li .amount {\n  font-size: 12px;\n  color: #757575;\n}\n.assets-details-gdt .detail .detail-list li .status {\n  font-size: 12px;\n}\n.assets-details-gdt .handle-btn {\n  position: absolute;\n  left: 0;\n  bottom: 0;\n  right: 0;\n  background: #fff;\n  padding: 10px 15px;\n}\n.assets-details-gdt .handle-btn button {\n  text-align: center;\n  border: none;\n  font-size: 18px;\n  box-sizing: border-box;\n  width: 100%;\n  line-height: 40px;\n  color: #fff;\n  background: #ff4800;\n  border-radius: 10px;\n}'],sourceRoot:""}])},"7Lh0":function(t,n,e){var i=e("6fe5");"string"==typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);e("rjj0")("b0ae01a8",i,!0,{})},"7kAW":function(t,n,e){var i=e("jASl");"string"==typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);e("rjj0")("17cd3738",i,!0,{})},jASl:function(t,n,e){n=t.exports=e("FZ+f")(!0),n.push([t.i,"\n.loading-box[data-v-9db44820] {\n  padding: 5px 0;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: center;\n  -webkit-justify-content: center;\n          justify-content: center;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.loading-box span[data-v-9db44820] {\n/*color $color-theme*/\n  margin-left: 10px;\n}\n.no-more[data-v-9db44820] {\n  color: #757575;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/components/mLoad/index.vue"],names:[],mappings:";AACA;EACE,eAAe;EACf,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,yBAAyB;EACzB,gCAAgC;UACxB,wBAAwB;EAChC,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;CAC7B;AACD;AACA,sBAAsB;EACpB,kBAAkB;CACnB;AACD;EACE,eAAe;CAChB",file:"index.vue",sourcesContent:["\n.loading-box[data-v-9db44820] {\n  padding: 5px 0;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: center;\n  -webkit-justify-content: center;\n          justify-content: center;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.loading-box span[data-v-9db44820] {\n/*color $color-theme*/\n  margin-left: 10px;\n}\n.no-more[data-v-9db44820] {\n  color: #757575;\n}"],sourceRoot:""}])}});
//# sourceMappingURL=17.js.map?v=d1aca05a7fef595521be