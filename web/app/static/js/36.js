webpackJsonp([36],{LKJD:function(n,e,i){var t=i("ihZD");"string"==typeof t&&(t=[[n.i,t,""]]),t.locals&&(n.exports=t.locals);i("rjj0")("0dda4872",t,!0,{})},Ttkm:function(n,e,i){"use strict";function t(n){i("LKJD")}Object.defineProperty(e,"__esModule",{value:!0});var o=i("POHo"),A=(i("SQ4B"),i("wpcj")),r=i("V33R"),a=i.n(r),c=(o.a,A.a,{name:"index",components:{slide:o.a,Qrcode:A.a},data:function(){return{nodeInfo:{}}},created:function(){this.nodeInfo=JSON.parse(sessionStorage.getItem("myNodeInfo")),console.log(this.nodeInfo)},computed:{qrcodeData:function(){return window.location.href.split("#")[0]+"#/home/vote?nodeId="+this.nodeInfo.id+"&nodeName="+this.nodeInfo.name}},mounted:function(){var n=this,e=new a.a("#copy");e.on("success",function(e){n.$vux.toast.show("复制成功")}),e.on("error",function(e){n.$vux.toast.show("复制失败，你可以选择扫描以及二维码，或刷新重试")})}}),d=function(){var n=this,e=n.$createElement,i=n._self._c||e;return i("slide",[i("div",{staticClass:"invite"},[i("app-header"),n._v(" "),i("div",{staticClass:"invite-content"},[i("div",{staticClass:"avatar"},[i("img",{attrs:{src:n.nodeInfo.logo,alt:""}})]),n._v(" "),i("h6",[n._v(n._s(n.nodeInfo.name))]),n._v(" "),i("h1",[n._v("邀请您来为我投票")]),n._v(" "),i("qrcode",{staticClass:"qrcode-box",attrs:{value:n.qrcodeData,type:"img",size:165}}),n._v(" "),i("p",[n._v("投票链接二维码")]),n._v(" "),i("div",{staticClass:"copy-btn",attrs:{id:"copy","data-clipboard-text":n.qrcodeData}},[n._v("复制投票链接")])],1)],1)])},s=[],p={render:d,staticRenderFns:s},B=p,f=i("VU/8"),C=t,l=f(c,B,!1,C,null,null);e.default=l.exports},ihZD:function(n,e,i){e=n.exports=i("FZ+f")(!0),e.push([n.i,'\n.invite {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  background: #f3f0f3;\n  overflow: auto;\n}\n.invite .app-header {\n  background: #f3f0f3 !important;\n}\n.invite .invite-content {\n  margin: 20px;\n  margin-top: 95px;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n  -webkit-flex-direction: column;\n          flex-direction: column;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  background: url("/static/images/invite-bg.png") center no-repeat;\n  background-size: cover;\n  border-radius: 4px;\n}\n.invite .avatar {\n  width: 85px;\n  height: 85px;\n  border: 3px solid #e5cc6f;\n  border-radius: 50%;\n  margin-top: -45px;\n  overflow: hidden;\n}\n.invite .avatar img {\n  width: 100%;\n  height: 100%;\n}\n.invite h6 {\n  margin-top: 20px;\n  color: #ccaa74;\n  font-size: 16px;\n}\n.invite h1 {\n  margin-top: 35px;\n  margin-bottom: 50px;\n  color: #e5cc6f;\n  font-size: 28px;\n}\n.invite .qrcode-box {\n  border: 1px solid #fff;\n}\n.invite p {\n  margin-top: 15px;\n  font-size: 16px;\n  color: #d5d5d5;\n}\n.invite .copy-btn {\n  margin: 30px 0;\n  width: 255px;\n  text-align: center;\n  color: #0f0f0f;\n  line-height: 42px;\n  font-size: 18px;\n  background: #e5cc6f;\n  border-radius: 10px;\n}',"",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/node/invite.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;EACX,oBAAoB;EACpB,eAAe;CAChB;AACD;EACE,+BAA+B;CAChC;AACD;EACE,aAAa;EACb,iBAAiB;EACjB,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,6BAA6B;EAC7B,8BAA8B;EAC9B,+BAA+B;UACvB,uBAAuB;EAC/B,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,iEAAiE;EACjE,uBAAuB;EACvB,mBAAmB;CACpB;AACD;EACE,YAAY;EACZ,aAAa;EACb,0BAA0B;EAC1B,mBAAmB;EACnB,kBAAkB;EAClB,iBAAiB;CAClB;AACD;EACE,YAAY;EACZ,aAAa;CACd;AACD;EACE,iBAAiB;EACjB,eAAe;EACf,gBAAgB;CACjB;AACD;EACE,iBAAiB;EACjB,oBAAoB;EACpB,eAAe;EACf,gBAAgB;CACjB;AACD;EACE,uBAAuB;CACxB;AACD;EACE,iBAAiB;EACjB,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,eAAe;EACf,aAAa;EACb,mBAAmB;EACnB,eAAe;EACf,kBAAkB;EAClB,gBAAgB;EAChB,oBAAoB;EACpB,oBAAoB;CACrB",file:"invite.vue",sourcesContent:['\n.invite {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  background: #f3f0f3;\n  overflow: auto;\n}\n.invite .app-header {\n  background: #f3f0f3 !important;\n}\n.invite .invite-content {\n  margin: 20px;\n  margin-top: 95px;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n  -webkit-flex-direction: column;\n          flex-direction: column;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  background: url("/static/images/invite-bg.png") center no-repeat;\n  background-size: cover;\n  border-radius: 4px;\n}\n.invite .avatar {\n  width: 85px;\n  height: 85px;\n  border: 3px solid #e5cc6f;\n  border-radius: 50%;\n  margin-top: -45px;\n  overflow: hidden;\n}\n.invite .avatar img {\n  width: 100%;\n  height: 100%;\n}\n.invite h6 {\n  margin-top: 20px;\n  color: #ccaa74;\n  font-size: 16px;\n}\n.invite h1 {\n  margin-top: 35px;\n  margin-bottom: 50px;\n  color: #e5cc6f;\n  font-size: 28px;\n}\n.invite .qrcode-box {\n  border: 1px solid #fff;\n}\n.invite p {\n  margin-top: 15px;\n  font-size: 16px;\n  color: #d5d5d5;\n}\n.invite .copy-btn {\n  margin: 30px 0;\n  width: 255px;\n  text-align: center;\n  color: #0f0f0f;\n  line-height: 42px;\n  font-size: 18px;\n  background: #e5cc6f;\n  border-radius: 10px;\n}'],sourceRoot:""}])}});
//# sourceMappingURL=36.js.map?v=2bab136a15859b02b10b