webpackJsonp([17],{"On+8":function(t,n,e){"use strict";function o(t){e("tg2E")}Object.defineProperty(n,"__esModule",{value:!0});var i=e("POHo"),s=e("SQ4B"),a=e("/kga"),A=(i.a,a.a,{name:"index",components:{slide:i.a,XDialog:a.a},data:function(){return{dataList:[],page:1,loadShow:!0,show:!1,btnLoading:!1,redeemId:"",total:""}},methods:{handRedeem:function(){var t=this;this.btnLoading=!0,s.a.post("/vote/revoke-vote",{id:this.redeemId},function(n){t.btnLoading=!1,0===n.code&&(t.show=!1,t.redeemId=""),t.$vux.toast.show(n.msg),t.cleanUpData()})},cleanUpData:function(){this.dataList=[],this.page=1,this.loadShow=!0,this.getList()},clickRedeem:function(t){this.redeemId=t,this.show=!0},handleBottom:function(){var t=this;if(""!==this.total&&this.dataList.length>=parseInt(this.total))return void this.$refs.my_scroller.finishInfinite(!0);s.a.post("/vote/logs",{page:this.page,page_size:10,type:1},function(n){if(t.loadShow&&(t.loadShow=!1),0!==n.code)return void t.$vux.toast.show(n.msg);t.dataList=t.dataList.concat(n.content.list),t.total=n.content.count,t.page++,t.$refs.my_scroller.finishInfinite(!1)})},getList:function(){var t=this;s.a.post("/vote/logs",{page:this.page,page_size:10,type:1},function(n){if(t.loadShow&&(t.loadShow=!1),0!==n.code)return void t.$vux.toast.show(n.msg);t.dataList=t.dataList.concat(n.content.list),t.dataList.length,parseInt(n.content.count),t.$refs.vueLoad.onBottomLoaded()})}},created:function(){}}),l=function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("slide",[e("div",{staticClass:"vote"},[e("app-header",[t._v("\n      我的投票\n      "),e("router-link",{attrs:{slot:"right",tag:"span",to:"/personal/vote/redeem"},on:{click:function(t){}},slot:"right"},[t._v("赎回记录")])],1),t._v(" "),e("div",{staticClass:"vote-content"},[e("scroller",{ref:"my_scroller",attrs:{"on-infinite":t.handleBottom}},[t.dataList.length||t.loadShow?t._e():e("div",{staticClass:"no-data"},[e("img",{attrs:{src:"/static/images/state-fail.png",alt:""}})]),t._v(" "),e("ul",{staticClass:"vote-list"},t._l(t.dataList,function(n){return e("li",[e("div",{staticClass:"top"},[e("div",{staticClass:"left"},[e("span",{staticClass:"name"},[t._v(t._s(n.name))]),t._v(" "),e("span",{staticClass:"sign"},[t._v(t._s(n.typeName.slice(0,2)))])]),t._v(" "),e("div",{staticClass:"right"},[t._v(t._s(n.voteNumber+"票"))])]),t._v(" "),e("div",{staticClass:"bottom"},[e("div",{staticClass:"left"},[e("p",[t._v(t._s("方式："+n.typeStr))]),t._v(" "),e("p",[t._v(t._s(n.createTime))])]),t._v(" "),"普通投票"===n.typeStr?e("div",{staticClass:"right"},[n.isRevoke?e("x-button",{staticClass:"redeem",attrs:{type:"warn"},nativeOn:{click:function(e){t.clickRedeem(n.id)}}},[t._v("赎回")]):e("p",[t._v("本次竞选活动结束后才能赎回")])],1):t._e()])])}))])],1),t._v(" "),e("x-dialog",{staticClass:"redeem-dialog",model:{value:t.show,callback:function(n){t.show=n},expression:"show"}},[e("div",{staticClass:"dlg-box"},[e("div",{staticClass:"title"},[t._v("\n          确认赎回投票？\n          "),e("span",{staticClass:"icon-close close",on:{click:function(n){t.show=!1}}})]),t._v(" "),e("div",{staticClass:"hint"},[t._v("确认赎回后我们将于72小时将资产返还到 你的账户")]),t._v(" "),e("x-button",{staticClass:"redeem-btn",attrs:{type:"warn","show-loading":t.btnLoading},nativeOn:{click:function(n){return t.handRedeem(n)}}},[t._v("确定")])],1)]),t._v(" "),e("router-view")],1)])},d=[],r={render:l,staticRenderFns:d},c=r,p=e("VU/8"),v=o,C=p(A,c,!1,v,null,null);n.default=C.exports},"WLu+":function(t,n,e){n=t.exports=e("FZ+f")(!0),n.push([t.i,"\n.vote {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.vote .vote-content {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  overflow: hidden;\n}\n.vote .vote-content .vote-list {\n  min-height: 30px;\n}\n.vote .vote-content .vote-list li {\n  padding: 15px;\n  border-bottom: 1px solid #dbdbdb;\n}\n.vote .vote-content .vote-list li>div {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.vote .vote-content .vote-list li>div button.redeem {\n  border-radius: 20px;\n  line-height: 32px;\n  width: 85px;\n  font-size: 14px;\n}\n.vote .vote-content .vote-list li>div .name {\n  font-weight: bold;\n  font-size: 16px;\n}\n.vote .vote-content .vote-list li>div .sign {\n  display: inline-block;\n  color: #ff6a2f;\n  border-radius: 15px;\n  border: 1px solid #ffa344;\n  background: #fff8f2;\n  font-size: 20px;\n  padding: 5px 10px;\n  margin-left: -10px;\n  transform: scale(0.5);\n  -ms-transform: scale(0.5); /* IE 9 */\n  -webkit-transform: scale(0.5); /* Safari and Chrome */\n}\n.vote .vote-content .vote-list li .bottom {\n  margin-top: 5px;\n}\n.vote .vote-content .vote-list li .bottom .left {\n  line-height: 18px;\n  color: #b4b5bc;\n}\n.vote .redeem-dialog .dlg-box {\n  padding: 25px 15px;\n}\n.vote .redeem-dialog .title {\n  font-size: 20px;\n}\n.vote .redeem-dialog .title .close {\n  float: right;\n}\n.vote .redeem-dialog .hint {\n  line-height: 20px;\n  padding: 30px 0;\n  color: #b4b5bc;\n  text-align: center;\n}\n.vote .redeem-dialog .redeem-btn {\n  line-height: 40px;\n  font-size: 14px;\n}\n.vote .no-data {\n  text-align: center;\n  padding-top: 55px;\n}\n.vote .no-data img {\n  width: 100px;\n}\n.vote .no-data p {\n  margin-top: 35px;\n  font-size: 16px;\n  color: #b4b5bc;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/vote/index.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,UAAU;EACV,QAAQ;EACR,SAAS;EACT,iBAAiB;CAClB;AACD;EACE,iBAAiB;CAClB;AACD;EACE,cAAc;EACd,iCAAiC;CAClC;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;EACvC,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;CAC7B;AACD;EACE,oBAAoB;EACpB,kBAAkB;EAClB,YAAY;EACZ,gBAAgB;CACjB;AACD;EACE,kBAAkB;EAClB,gBAAgB;CACjB;AACD;EACE,sBAAsB;EACtB,eAAe;EACf,oBAAoB;EACpB,0BAA0B;EAC1B,oBAAoB;EACpB,gBAAgB;EAChB,kBAAkB;EAClB,mBAAmB;EACnB,sBAAsB;EACtB,0BAA0B,CAAC,UAAU;EACrC,8BAA8B,CAAC,uBAAuB;CACvD;AACD;EACE,gBAAgB;CACjB;AACD;EACE,kBAAkB;EAClB,eAAe;CAChB;AACD;EACE,mBAAmB;CACpB;AACD;EACE,gBAAgB;CACjB;AACD;EACE,aAAa;CACd;AACD;EACE,kBAAkB;EAClB,gBAAgB;EAChB,eAAe;EACf,mBAAmB;CACpB;AACD;EACE,kBAAkB;EAClB,gBAAgB;CACjB;AACD;EACE,mBAAmB;EACnB,kBAAkB;CACnB;AACD;EACE,aAAa;CACd;AACD;EACE,iBAAiB;EACjB,gBAAgB;EAChB,eAAe;CAChB",file:"index.vue",sourcesContent:["\n.vote {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.vote .vote-content {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  overflow: hidden;\n}\n.vote .vote-content .vote-list {\n  min-height: 30px;\n}\n.vote .vote-content .vote-list li {\n  padding: 15px;\n  border-bottom: 1px solid #dbdbdb;\n}\n.vote .vote-content .vote-list li>div {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.vote .vote-content .vote-list li>div button.redeem {\n  border-radius: 20px;\n  line-height: 32px;\n  width: 85px;\n  font-size: 14px;\n}\n.vote .vote-content .vote-list li>div .name {\n  font-weight: bold;\n  font-size: 16px;\n}\n.vote .vote-content .vote-list li>div .sign {\n  display: inline-block;\n  color: #ff6a2f;\n  border-radius: 15px;\n  border: 1px solid #ffa344;\n  background: #fff8f2;\n  font-size: 20px;\n  padding: 5px 10px;\n  margin-left: -10px;\n  transform: scale(0.5);\n  -ms-transform: scale(0.5); /* IE 9 */\n  -webkit-transform: scale(0.5); /* Safari and Chrome */\n}\n.vote .vote-content .vote-list li .bottom {\n  margin-top: 5px;\n}\n.vote .vote-content .vote-list li .bottom .left {\n  line-height: 18px;\n  color: #b4b5bc;\n}\n.vote .redeem-dialog .dlg-box {\n  padding: 25px 15px;\n}\n.vote .redeem-dialog .title {\n  font-size: 20px;\n}\n.vote .redeem-dialog .title .close {\n  float: right;\n}\n.vote .redeem-dialog .hint {\n  line-height: 20px;\n  padding: 30px 0;\n  color: #b4b5bc;\n  text-align: center;\n}\n.vote .redeem-dialog .redeem-btn {\n  line-height: 40px;\n  font-size: 14px;\n}\n.vote .no-data {\n  text-align: center;\n  padding-top: 55px;\n}\n.vote .no-data img {\n  width: 100px;\n}\n.vote .no-data p {\n  margin-top: 35px;\n  font-size: 16px;\n  color: #b4b5bc;\n}"],sourceRoot:""}])},tg2E:function(t,n,e){var o=e("WLu+");"string"==typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);e("rjj0")("771a7082",o,!0,{})}});
//# sourceMappingURL=17.js.map?v=9a25c312a85ce5b8f576