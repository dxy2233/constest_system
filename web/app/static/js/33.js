webpackJsonp([33],{"4psq":function(t,e,n){"use strict";function o(t){n("QrhB")}Object.defineProperty(e,"__esModule",{value:!0});var i=n("POHo"),s=n("SQ4B"),a=(i.a,{name:"index",components:{slide:i.a},data:function(){return{dataList:[],page:1,loadShow:!0,total:""}},methods:{handleBottom:function(){var t=this;if(""!==this.total&&this.dataList.length>=parseInt(this.total))return void this.$refs.my_scroller.finishInfinite(!0);s.a.post("/vote/logs",{page:this.page,page_size:10,type:0},function(e){if(t.loadShow&&(t.loadShow=!1),0!==e.code)return void t.$vux.toast.show(e.msg);t.dataList=t.dataList.concat(e.content.list),t.total=e.content.count,t.page++,t.$refs.my_scroller.finishInfinite(!1)})},getList:function(){var t=this;s.a.post("/vote/logs",{page:this.page,page_size:10,type:0},function(e){if(t.loadShow&&(t.loadShow=!1),0!==e.code)return void t.$vux.toast.show(e.msg);t.dataList=t.dataList.concat(e.content.list),t.dataList.length,parseInt(e.content.count),t.$refs.vueLoad.onBottomLoaded()})}},created:function(){}}),A=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("slide",[n("div",{staticClass:"vote-redeem"},[n("app-header",[t._v("\n      赎回记录\n    ")]),t._v(" "),n("div",{staticClass:"vote-content"},[n("scroller",{ref:"my_scroller",attrs:{"on-infinite":t.handleBottom}},[t.dataList.length||t.loadShow?t._e():n("div",{staticClass:"no-data"},[n("img",{attrs:{src:"/static/images/state-fail.png",alt:""}})]),t._v(" "),n("ul",{staticClass:"vote-list"},t._l(t.dataList,function(e){return n("li",[n("div",{staticClass:"top"},[n("h3",[t._v(t._s(e.name))]),t._v(" "),n("h4",[t._v(t._s(e.voteNumber+"票")+"\n                "),n("span",[t._v(t._s(e.statusStr))])])]),t._v(" "),n("div",{staticClass:"bottom"},[n("p",[t._v(t._s(e.typeStr))]),t._v(" "),n("p",[t._v(t._s(e.createTime))])])])}))])],1)],1)])},l=[],d={render:A,staticRenderFns:l},r=d,v=n("VU/8"),c=o,p=v(a,r,!1,c,null,null);e.default=p.exports},Icpo:function(t,e,n){e=t.exports=n("FZ+f")(!0),e.push([t.i,"\n.vote-redeem {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.vote-redeem .vote-content {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  overflow: hidden;\n}\n.vote-redeem .vote-content .vote-list {\n  min-height: 15px;\n}\n.vote-redeem .vote-content .vote-list li {\n  padding: 15px;\n  border-bottom: 1px solid #dbdbdb;\n}\n.vote-redeem .vote-content .vote-list li>div {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.vote-redeem .vote-content .vote-list li>div h3 {\n  font-size: 16px;\n}\n.vote-redeem .vote-content .vote-list li>div h4 {\n  font-size: 14px;\n}\n.vote-redeem .vote-content .vote-list li>div h4 span {\n  font-size: 12px;\n  color: #959da6;\n}\n.vote-redeem .vote-content .vote-list li>div p {\n  font-size: 12px;\n  color: #959da6;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/vote/redeem.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,UAAU;EACV,QAAQ;EACR,SAAS;EACT,iBAAiB;CAClB;AACD;EACE,iBAAiB;CAClB;AACD;EACE,cAAc;EACd,iCAAiC;CAClC;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;EACvC,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;CAC7B;AACD;EACE,gBAAgB;CACjB;AACD;EACE,gBAAgB;CACjB;AACD;EACE,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,gBAAgB;EAChB,eAAe;CAChB",file:"redeem.vue",sourcesContent:["\n.vote-redeem {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.vote-redeem .vote-content {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  overflow: hidden;\n}\n.vote-redeem .vote-content .vote-list {\n  min-height: 15px;\n}\n.vote-redeem .vote-content .vote-list li {\n  padding: 15px;\n  border-bottom: 1px solid #dbdbdb;\n}\n.vote-redeem .vote-content .vote-list li>div {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.vote-redeem .vote-content .vote-list li>div h3 {\n  font-size: 16px;\n}\n.vote-redeem .vote-content .vote-list li>div h4 {\n  font-size: 14px;\n}\n.vote-redeem .vote-content .vote-list li>div h4 span {\n  font-size: 12px;\n  color: #959da6;\n}\n.vote-redeem .vote-content .vote-list li>div p {\n  font-size: 12px;\n  color: #959da6;\n}"],sourceRoot:""}])},QrhB:function(t,e,n){var o=n("Icpo");"string"==typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);n("rjj0")("e364b5d2",o,!0,{})}});
//# sourceMappingURL=33.js.map?v=2c1570f4d748bd8d065e