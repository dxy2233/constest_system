webpackJsonp([38],{Emg2:function(e,t,n){"use strict";function s(e){n("zdTU")}Object.defineProperty(t,"__esModule",{value:!0});var i=n("Dd8w"),a=n.n(i),r=n("mvHQ"),A=n.n(r),o=n("SQ4B"),p=n("Bfwr"),c=n("NYxO"),d=(p.a,a()({},Object(c.b)(["loginMsg"])),{name:"index",components:{Loading:p.a},data:function(){return{currencyList:[],showLoading:!1,listShow:!0}},methods:{openCollect:function(){this.$router.push({name:"collect",params:{address:this.walletData.address}})},getCurrencyList:function(){var e=this;o.a.post("/wallet/currency",{},function(t){if(e.listShow=!1,0!==t.code)return void e.$vux.toast.show(t.msg);e.currencyList=t.content,sessionStorage.setItem("currencyList",A()(e.currencyList))})},pageInt:function(){if(!this.loginMsg)return void this.$router.push({path:"/login"});this.currencyList.length||this.getCurrencyList()}},created:function(){this.pageInt()},activated:function(){this.pageInt()},computed:a()({},Object(c.b)(["loginMsg"]))}),l=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"assets"},[n("div",{staticClass:"assets-wrapper"},[n("div",{staticClass:"assets-content"},[n("dl",{staticClass:"currency"},[n("dt",[e._v("资产种类")]),e._v(" "),e._l(e.currencyList,function(t){return n("router-link",{key:t.code,attrs:{tag:"dd",to:"/assets/dts"+t.id}},[n("div",{staticClass:"left"},[n("img",{staticClass:"icon",attrs:{src:"/static/images/assets-icon/"+t.code+".jpg",alt:""}}),e._v(" "),n("span",[e._v(e._s(t.name))])]),e._v(" "),n("div",{staticClass:"right"},[e._v(e._s(t.positionAmount))])])})],2),e._v(" "),e.listShow?n("load-more",{attrs:{tip:"正在加载"}}):e._e()],1)]),e._v(" "),n("router-view")],1)},B=[],C={render:l,staticRenderFns:B},b=C,u=n("VU/8"),w=s,g=u(d,b,!1,w,null,null);t.default=g.exports},Sdkg:function(e,t,n){t=e.exports=n("FZ+f")(!0),t.push([e.i,"\n.assets {\n  position: absolute;\n  width: 100%;\n  top: 0;\n  bottom: 50px;\n  overflow: hidden;\n}\n.assets .assets-wrapper {\n  padding: 20px 15px;\n}\n.assets .assets-wrapper .title {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  font-size: 24px;\n  padding-bottom: 30px;\n}\n.assets .assets-wrapper .title img {\n  width: 48px;\n}\n.assets .assets-wrapper .currency dt {\n  font-size: 20px;\n  padding: 15px 0;\n  margin-bottom: 30px;\n}\n.assets .assets-wrapper .currency dd {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  font-size: 16px;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  padding: 25px 13px;\n  box-shadow: 0 0 12px 2px rgba(90,90,90,0.18);\n  margin-bottom: 20px;\n  border-radius: 5px;\n}\n.assets .assets-wrapper .currency dd .left {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.assets .assets-wrapper .currency dd .icon {\n  width: 45px;\n  height: 45px;\n  border-radius: 50%;\n  margin-right: 20px;\n}\n.assets .assets-wrapper .currency dd .right {\n  color: #b4b5bc;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/assets/index.vue"],names:[],mappings:";AACA;EACE,mBAAmB;EACnB,YAAY;EACZ,OAAO;EACP,aAAa;EACb,iBAAiB;CAClB;AACD;EACE,mBAAmB;CACpB;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;EACvC,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,gBAAgB;EAChB,qBAAqB;CACtB;AACD;EACE,YAAY;CACb;AACD;EACE,gBAAgB;EAChB,gBAAgB;EAChB,oBAAoB;CACrB;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;EACvC,gBAAgB;EAChB,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,mBAAmB;EACnB,6CAA6C;EAC7C,oBAAoB;EACpB,mBAAmB;CACpB;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;CAC7B;AACD;EACE,YAAY;EACZ,aAAa;EACb,mBAAmB;EACnB,mBAAmB;CACpB;AACD;EACE,eAAe;CAChB",file:"index.vue",sourcesContent:["\n.assets {\n  position: absolute;\n  width: 100%;\n  top: 0;\n  bottom: 50px;\n  overflow: hidden;\n}\n.assets .assets-wrapper {\n  padding: 20px 15px;\n}\n.assets .assets-wrapper .title {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  font-size: 24px;\n  padding-bottom: 30px;\n}\n.assets .assets-wrapper .title img {\n  width: 48px;\n}\n.assets .assets-wrapper .currency dt {\n  font-size: 20px;\n  padding: 15px 0;\n  margin-bottom: 30px;\n}\n.assets .assets-wrapper .currency dd {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  font-size: 16px;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  padding: 25px 13px;\n  box-shadow: 0 0 12px 2px rgba(90,90,90,0.18);\n  margin-bottom: 20px;\n  border-radius: 5px;\n}\n.assets .assets-wrapper .currency dd .left {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.assets .assets-wrapper .currency dd .icon {\n  width: 45px;\n  height: 45px;\n  border-radius: 50%;\n  margin-right: 20px;\n}\n.assets .assets-wrapper .currency dd .right {\n  color: #b4b5bc;\n}"],sourceRoot:""}])},zdTU:function(e,t,n){var s=n("Sdkg");"string"==typeof s&&(s=[[e.i,s,""]]),s.locals&&(e.exports=s.locals);n("rjj0")("c43cee70",s,!0,{})}});
//# sourceMappingURL=38.js.map?v=1c965da9f126fbb3a404