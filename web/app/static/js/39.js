webpackJsonp([39],{P7UP:function(t,e,n){"use strict";function o(t){n("dni5")}Object.defineProperty(e,"__esModule",{value:!0});var i=n("POHo"),r=n("SQ4B"),A=(i.a,{name:"index",components:{slide:i.a},data:function(){return{tabList:[{type:"1",name:"获取记录"},{type:"0",name:"使用记录"}],currentType:localStorage.getItem("voteVoucherType")||"1",dataList:[],page:1,loadShow:!0,currencyInfo:{},voucherInfo:0,total:""}},methods:{selectedTab:function(t){t.type!==this.currentType&&(this.currentType=t.type,localStorage.setItem("voteVoucherType",t.type),this.page=1,this.dataList=[],this.total="",this.$refs.my_scroller.finishInfinite(!1))},handleBottom:function(){var t=this;if(""!==this.total&&this.dataList.length>=parseInt(this.total))return void this.$refs.my_scroller.finishInfinite(!0);r.a.post("/vote/voucher",{type:this.currentType,page:this.page,page_size:10},function(e){if(t.loadShow&&(t.loadShow=!1),0!==e.code)return void t.$vux.toast.show(e.msg);t.dataList=t.dataList.concat(e.content.list),t.total=e.content.count,t.page++,t.$refs.my_scroller.finishInfinite(!1)})},getVoucherInfo:function(){var t=this;r.a.post("/vote/voucher-info",{},function(e){if(0!==e.code)return void t.$vux.toast.show(e.msg);t.voucherInfo=e.content.count})},getList:function(){var t=this;r.a.post("/vote/voucher",{type:this.currentType,page:this.page,page_size:10},function(e){if(t.loadShow&&(t.loadShow=!1),0!==e.code)return void t.$vux.toast.show(e.msg);t.dataList=t.dataList.concat(e.content.list),t.dataList.length,parseInt(e.content.count),t.$refs.vueLoad.onBottomLoaded()})}},created:function(){this.getVoucherInfo()}}),s=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("slide",[n("div",{staticClass:"vote-voucher"},[n("app-header",[t._v("\n      我的投票券\n    ")]),t._v(" "),n("div",{staticClass:"vote-voucher-content"},[n("div",{staticClass:"brief-card-box"},[n("div",{staticClass:"brief-card"},[n("div",{staticClass:"left"},[n("h3",[t._v(t._s(t.voucherInfo)+"票")]),t._v(" "),n("h5",[t._v("活动截止后失效")])]),t._v(" "),n("div",{staticClass:"right"},[n("router-link",{attrs:{tag:"button",to:"/home/vote"}},[t._v("立即投票")])],1)])]),t._v(" "),n("ul",{staticClass:"tab-list"},t._l(t.tabList,function(e){return n("li",{key:e.name,class:{act:e.type===t.currentType},on:{click:function(n){t.selectedTab(e)}}},[t._v(t._s(e.name)+"\n        ")])})),t._v(" "),n("div",{staticClass:"list-box"},[n("scroller",{ref:"my_scroller",attrs:{"on-infinite":t.handleBottom}},[n("ul",{staticClass:"list"},t._l(t.dataList,function(e){return n("li",[n("p",[n("span",{},[t._v(t._s("1"===t.currentType?e.mobile:e.name))]),t._v(" "),n("span",{})]),t._v(" "),n("p",[n("span",{staticClass:"small"},[t._v(t._s(e.createTime))]),t._v(" "),n("span",{staticClass:"light"},[t._v(t._s("1"===t.currentType?e.voucherNum:e.amount))])])])}))])],1)])],1)])},c=[],a={render:s,staticRenderFns:c},l=a,v=n("VU/8"),u=o,p=v(A,l,!1,u,null,null);e.default=p.exports},"R0+3":function(t,e,n){e=t.exports=n("FZ+f")(!0),e.push([t.i,"\n.vote-voucher {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.vote-voucher .vote-voucher-content {\n  position: absolute;\n  top: 57px;\n  bottom: 0;\n  left: 0;\n  right: 0;\n}\n.vote-voucher .vote-voucher-content .brief-card-box {\n  height: 100px;\n  padding: 0 15px;\n}\n.vote-voucher .vote-voucher-content .brief-card {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  padding: 15px 20px;\n  color: #fff;\n  background: -webkit-linear-gradient(left, #ffaa4f, #ffb357); /* Safari 5.1 - 6.0 */ /* Opera 11.1 - 12.0 */ /* Firefox 3.6 - 15 */\n  background: linear-gradient(to right, #ffaa4f, #ffb357); /* 标准的语法 */\n  border-radius: 10px;\n  box-shadow: 0 4px 15px 4px RGBA(240, 208, 172, 0.5);\n}\n.vote-voucher .vote-voucher-content .brief-card h3 {\n  font-size: 24px;\n  margin-bottom: 5px;\n}\n.vote-voucher .vote-voucher-content .brief-card button {\n  line-height: 35px;\n  background: #fff;\n  color: #ff4800;\n  border-radius: 20px;\n  width: 80px;\n  border: 0;\n}\n.vote-voucher .vote-voucher-content .tab-list {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-justify-content: space-around;\n          justify-content: space-around;\n  box-sizing: border-box;\n  margin: 0 15px;\n  border-bottom: 1px solid #e5e7e9;\n}\n.vote-voucher .vote-voucher-content .tab-list li {\n  line-height: 35px;\n  height: 35px;\n  box-sizing: border-box;\n  font-size: 16px;\n}\n.vote-voucher .vote-voucher-content .tab-list li.act {\n  border-bottom: 2px solid #ff4800;\n}\n.vote-voucher .vote-voucher-content .list-box {\n  position: absolute;\n  top: 135px;\n  bottom: 0;\n  width: 100%;\n  overflow: hidden;\n}\n.vote-voucher .vote-voucher-content .list-box .list {\n  margin: 0 15px;\n  min-height: 15px;\n}\n.vote-voucher .vote-voucher-content .list-box li {\n  border-bottom: 1px solid #e5e7e9;\n  padding: 10px 0;\n}\n.vote-voucher .vote-voucher-content .list-box li p {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  line-height: 20px;\n}\n.vote-voucher .vote-voucher-content .list-box li .small {\n  font-size: 10px;\n  color: #959da6;\n}\n.vote-voucher .vote-voucher-content .list-box li .light {\n  color: #757575;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/voteVoucher/index.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,UAAU;EACV,QAAQ;EACR,SAAS;CACV;AACD;EACE,cAAc;EACd,gBAAgB;CACjB;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;EACvC,mBAAmB;EACnB,YAAY;EACZ,4DAA4D,CAAC,sBAAsB,CAAC,uBAAuB,CAAC,sBAAsB;EAClI,wDAAwD,CAAC,WAAW;EACpE,oBAAoB;EACpB,oDAAoD;CACrD;AACD;EACE,gBAAgB;EAChB,mBAAmB;CACpB;AACD;EACE,kBAAkB;EAClB,iBAAiB;EACjB,eAAe;EACf,oBAAoB;EACpB,YAAY;EACZ,UAAU;CACX;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,sCAAsC;UAC9B,8BAA8B;EACtC,uBAAuB;EACvB,eAAe;EACf,iCAAiC;CAClC;AACD;EACE,kBAAkB;EAClB,aAAa;EACb,uBAAuB;EACvB,gBAAgB;CACjB;AACD;EACE,iCAAiC;CAClC;AACD;EACE,mBAAmB;EACnB,WAAW;EACX,UAAU;EACV,YAAY;EACZ,iBAAiB;CAClB;AACD;EACE,eAAe;EACf,iBAAiB;CAClB;AACD;EACE,iCAAiC;EACjC,gBAAgB;CACjB;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;EACvC,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,kBAAkB;CACnB;AACD;EACE,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,eAAe;CAChB",file:"index.vue",sourcesContent:["\n.vote-voucher {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.vote-voucher .vote-voucher-content {\n  position: absolute;\n  top: 57px;\n  bottom: 0;\n  left: 0;\n  right: 0;\n}\n.vote-voucher .vote-voucher-content .brief-card-box {\n  height: 100px;\n  padding: 0 15px;\n}\n.vote-voucher .vote-voucher-content .brief-card {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  padding: 15px 20px;\n  color: #fff;\n  background: -webkit-linear-gradient(left, #ffaa4f, #ffb357); /* Safari 5.1 - 6.0 */ /* Opera 11.1 - 12.0 */ /* Firefox 3.6 - 15 */\n  background: linear-gradient(to right, #ffaa4f, #ffb357); /* 标准的语法 */\n  border-radius: 10px;\n  box-shadow: 0 4px 15px 4px RGBA(240, 208, 172, 0.5);\n}\n.vote-voucher .vote-voucher-content .brief-card h3 {\n  font-size: 24px;\n  margin-bottom: 5px;\n}\n.vote-voucher .vote-voucher-content .brief-card button {\n  line-height: 35px;\n  background: #fff;\n  color: #ff4800;\n  border-radius: 20px;\n  width: 80px;\n  border: 0;\n}\n.vote-voucher .vote-voucher-content .tab-list {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-justify-content: space-around;\n          justify-content: space-around;\n  box-sizing: border-box;\n  margin: 0 15px;\n  border-bottom: 1px solid #e5e7e9;\n}\n.vote-voucher .vote-voucher-content .tab-list li {\n  line-height: 35px;\n  height: 35px;\n  box-sizing: border-box;\n  font-size: 16px;\n}\n.vote-voucher .vote-voucher-content .tab-list li.act {\n  border-bottom: 2px solid #ff4800;\n}\n.vote-voucher .vote-voucher-content .list-box {\n  position: absolute;\n  top: 135px;\n  bottom: 0;\n  width: 100%;\n  overflow: hidden;\n}\n.vote-voucher .vote-voucher-content .list-box .list {\n  margin: 0 15px;\n  min-height: 15px;\n}\n.vote-voucher .vote-voucher-content .list-box li {\n  border-bottom: 1px solid #e5e7e9;\n  padding: 10px 0;\n}\n.vote-voucher .vote-voucher-content .list-box li p {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  line-height: 20px;\n}\n.vote-voucher .vote-voucher-content .list-box li .small {\n  font-size: 10px;\n  color: #959da6;\n}\n.vote-voucher .vote-voucher-content .list-box li .light {\n  color: #757575;\n}"],sourceRoot:""}])},dni5:function(t,e,n){var o=n("R0+3");"string"==typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);n("rjj0")("69f8525a",o,!0,{})}});
//# sourceMappingURL=39.js.map?v=f164eb44094552b05a5f