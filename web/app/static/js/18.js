webpackJsonp([18],{AUYM:function(t,n,i){"use strict";function e(t){i("h7hX")}Object.defineProperty(n,"__esModule",{value:!0});var o=i("POHo"),s=i("SQ4B"),a=(o.a,{name:"index",components:{slide:o.a},data:function(){return{dataList:[],page:1,loadShow:!0,total:"",contributeTab:[{type:"all",name:"总票榜"},{type:"pay",name:"支付投票榜"}],currentContributeType:localStorage.getItem("currentContributeType")||"all",counttime:""}},methods:{selectTab:function(t){t!==this.currentContributeType&&(this.currentContributeType=t,localStorage.setItem("currentContributeType",t),this.page=1,this.dataList=[],this.total="",this.$refs.my_scroller.finishInfinite(!1))},handleBottom:function(){var t=this;if(""!==this.total&&this.dataList.length>=parseInt(this.total))return void this.$refs.my_scroller.finishInfinite(!0);s.a.post("/vote",{type:this.currentContributeType,page:this.page,page_size:10},function(n){if(0!==n.code)return void t.$vux.toast.show(n.msg);t.dataList=t.dataList.concat(n.content.list),t.total=n.content.count,t.page++,t.$refs.my_scroller.finishInfinite(!1)})}},created:function(){}}),A=function(){var t=this,n=t.$createElement,i=t._self._c||n;return i("slide",[i("div",{staticClass:"contribute-list"},[i("app-header",[t._v("\n      贡献榜\n      ")]),t._v(" "),i("div",{staticClass:"main"},[i("div",{staticClass:"top"},[i("ul",{staticClass:"list-tab"},t._l(t.contributeTab,function(n,e){return i("li",{class:{act:n.type===t.currentContributeType},on:{click:function(i){t.selectTab(n.type)}}},[t._v("\n            "+t._s(n.name)+"\n          ")])}))]),t._v(" "),i("div",{staticClass:"bottom"},[i("scroller",{ref:"my_scroller",attrs:{"on-infinite":t.handleBottom}},[i("ul",{staticClass:"list contribute-list-data"},t._l(t.dataList,function(n,e){return i("li",[i("div",{staticClass:"top"},[i("p",[i("span",{staticClass:"num"},[t._v(t._s(++e))]),t._v("\n                  "+t._s(n.mobile)+"\n                ")]),t._v(" "),i("p",{staticClass:"right"},[t._v("\n                  "+t._s(n.statusStr)+"\n                ")])]),t._v(" "),i("div",{staticClass:"bottom"},[i("p"),t._v(" "),i("p",{staticClass:"right"},[t._v("\n                  "+t._s(n.voteNumber)+"\n                  票")])])])}))])],1),t._v(" "),i("router-view")],1)],1)])},l=[],r={render:A,staticRenderFns:l},c=r,p=i("VU/8"),b=e,u=p(a,c,!1,b,null,null);n.default=u.exports},h7hX:function(t,n,i){var e=i("iz/c");"string"==typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);i("rjj0")("f7db265c",e,!0,{})},"iz/c":function(t,n,i){n=t.exports=i("FZ+f")(!0),n.push([t.i,"\n.contribute-list {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.contribute-list .main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  width: 100%;\n  overflow: hidden;\n}\n.contribute-list .main .contribute-list-data {\n  padding: 0 15px;\n  min-height: 15px;\n}\n.contribute-list .main .contribute-list-data li {\n  padding: 10px 0;\n  border-bottom: 1px solid #e5e7e9;\n}\n.contribute-list .main .contribute-list-data li > div {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n}\n.contribute-list .main .contribute-list-data li > div.top {\n  margin-bottom: 5px;\n}\n.contribute-list .main .contribute-list-data li > div .num {\n  margin-right: 20px;\n}\n.contribute-list .main .contribute-list-data li > div .right {\n  color: #959da6;\n}\n.contribute-list .main>.top .time {\n  text-align: center;\n  font-size: 12px;\n}\n.contribute-list .main>.top .time p {\n  display: inline-block;\n  padding: 5px 10px;\n  background: #f7f7f7;\n  color: #959da6;\n  border-radius: 3px;\n}\n.contribute-list .main>.top .list-tab {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: center;\n  -webkit-justify-content: center;\n          justify-content: center;\n  width: 100%;\n  margin: 5px auto;\n  font-size: 16px;\n  color: #757575;\n}\n.contribute-list .main>.top .list-tab li {\n  height: 35px;\n  line-height: 35px;\n  box-sizing: border-box;\n  margin: 0 10px;\n}\n.contribute-list .main>.top .list-tab li.act {\n  color: #2f4050;\n  border-bottom: 2px solid #ff4800;\n}\n.contribute-list .main>.bottom {\n  position: absolute;\n  bottom: 0;\n  top: 50px;\n  width: 100%;\n  overflow: hidden;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/home/contribute/index.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,UAAU;EACV,YAAY;EACZ,iBAAiB;CAClB;AACD;EACE,gBAAgB;EAChB,iBAAiB;CAClB;AACD;EACE,gBAAgB;EAChB,iCAAiC;CAClC;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;CACxC;AACD;EACE,mBAAmB;CACpB;AACD;EACE,mBAAmB;CACpB;AACD;EACE,eAAe;CAChB;AACD;EACE,mBAAmB;EACnB,gBAAgB;CACjB;AACD;EACE,sBAAsB;EACtB,kBAAkB;EAClB,oBAAoB;EACpB,eAAe;EACf,mBAAmB;CACpB;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,yBAAyB;EACzB,gCAAgC;UACxB,wBAAwB;EAChC,YAAY;EACZ,iBAAiB;EACjB,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,aAAa;EACb,kBAAkB;EAClB,uBAAuB;EACvB,eAAe;CAChB;AACD;EACE,eAAe;EACf,iCAAiC;CAClC;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,UAAU;EACV,YAAY;EACZ,iBAAiB;CAClB",file:"index.vue",sourcesContent:["\n.contribute-list {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.contribute-list .main {\n  position: absolute;\n  top: 50px;\n  bottom: 0;\n  width: 100%;\n  overflow: hidden;\n}\n.contribute-list .main .contribute-list-data {\n  padding: 0 15px;\n  min-height: 15px;\n}\n.contribute-list .main .contribute-list-data li {\n  padding: 10px 0;\n  border-bottom: 1px solid #e5e7e9;\n}\n.contribute-list .main .contribute-list-data li > div {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n}\n.contribute-list .main .contribute-list-data li > div.top {\n  margin-bottom: 5px;\n}\n.contribute-list .main .contribute-list-data li > div .num {\n  margin-right: 20px;\n}\n.contribute-list .main .contribute-list-data li > div .right {\n  color: #959da6;\n}\n.contribute-list .main>.top .time {\n  text-align: center;\n  font-size: 12px;\n}\n.contribute-list .main>.top .time p {\n  display: inline-block;\n  padding: 5px 10px;\n  background: #f7f7f7;\n  color: #959da6;\n  border-radius: 3px;\n}\n.contribute-list .main>.top .list-tab {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: center;\n  -webkit-justify-content: center;\n          justify-content: center;\n  width: 100%;\n  margin: 5px auto;\n  font-size: 16px;\n  color: #757575;\n}\n.contribute-list .main>.top .list-tab li {\n  height: 35px;\n  line-height: 35px;\n  box-sizing: border-box;\n  margin: 0 10px;\n}\n.contribute-list .main>.top .list-tab li.act {\n  color: #2f4050;\n  border-bottom: 2px solid #ff4800;\n}\n.contribute-list .main>.bottom {\n  position: absolute;\n  bottom: 0;\n  top: 50px;\n  width: 100%;\n  overflow: hidden;\n}"],sourceRoot:""}])}});
//# sourceMappingURL=18.js.map?v=35b713ab9ff7306640d3