webpackJsonp([40],{"7Rqt":function(n,t,e){var o=e("acmk");"string"==typeof o&&(o=[[n.i,o,""]]),o.locals&&(n.exports=o.locals);e("rjj0")("07cea9a0",o,!0,{})},"a8D/":function(n,t,e){"use strict";function o(n){e("7Rqt")}Object.defineProperty(t,"__esModule",{value:!0});var a=e("POHo"),A=(e("SQ4B"),e("wpcj")),i=(a.a,A.a,{name:"index",components:{slide:a.a,Qrcode:A.a},data:function(){return{applyUrl:"http://uaq5pzd9vm1kxhdk.mikecrm.com/6aNQyf2"}}}),p=function(){var n=this,t=n.$createElement,e=n._self._c||t;return e("slide",[e("div",{staticClass:"apply-node"},[e("app-header",[n._v("\n      申请成为节点\n      "),e("router-link",{attrs:{slot:"right",tag:"span",to:"/personal/applynode/rules"},slot:"right"},[n._v("规则说明")])],1),n._v(" "),e("div",{staticClass:"h-main"},[e("div",{staticClass:"top"},[e("img",{attrs:{src:"/static/images/apply/top.png",alt:""}})]),n._v(" "),e("div",{staticClass:"center"},[e("img",{staticClass:"icon",attrs:{src:"/static/images/apply/icon.png",alt:""}}),n._v(" "),e("div",{staticClass:"center-box"},[e("div",{staticClass:"info"},[n._v("\n            目前开放动力节点，中级节点，高\n            级节点，超级节点申请，名额有限，\n            先到先得。成功申请节点即刻享受\n            等值茅台珍藏酒品，超过12项\n            身份及实体权益,共同参与生态建设\n            和权益分红。\n          ")]),n._v(" "),e("div",{staticClass:"tel"},[e("img",{staticClass:"left-img",attrs:{src:"/static/images/apply/left.png",alt:""}}),n._v(" "),e("div",{staticClass:"text"},[e("p",[n._v("财富热线")]),n._v(" "),e("h4",[n._v("18586823227")])]),n._v(" "),e("img",{staticClass:"right-img",attrs:{src:"/static/images/apply/right.png",alt:""}})]),n._v(" "),e("div",{staticClass:"contact"},[e("a",{attrs:{href:n.applyUrl}},[e("div",{staticClass:"button"},[e("h5",[n._v("立即申请")]),n._v(" "),e("p",[n._v("即刻报名，独享专属权益")])])])]),n._v(" "),e("div",{staticClass:"qr-code"},[e("qrcode",{attrs:{value:n.applyUrl,type:"img",size:110}}),n._v(" "),e("p",[n._v("扫码填写申请表")])],1)])]),n._v(" "),e("div",{staticClass:"bottom"},[e("img",{attrs:{src:"/static/images/apply/bottom.png",alt:""}})])]),n._v(" "),e("router-view")],1)])},c=[],r={render:p,staticRenderFns:c},l=r,d=e("VU/8"),s=o,C=d(i,l,!1,s,null,null);t.default=C.exports},acmk:function(n,t,e){t=n.exports=e("FZ+f")(!0),t.push([n.i,'\n.apply-node {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.apply-node > .h-main {\n  width: 100%;\n  height: 100%;\n  overflow: auto;\n}\n.apply-node > .h-main .top {\n  background: #ffca12;\n  padding-bottom: 40px;\n}\n.apply-node > .h-main .top img {\n  width: 100%;\n}\n.apply-node > .h-main .center {\n  background: #ffca12;\n  position: relative;\n}\n.apply-node > .h-main .center .icon {\n  display: block;\n  width: 100px;\n  position: absolute;\n  top: -40px;\n  left: 50%;\n  margin-left: -50px;\n  z-index: 2;\n}\n.apply-node > .h-main .center .center-box {\n  position: relative;\n  bottom: -15px;\n  margin-left: 12%;\n  margin-right: 12%;\n  background: #fff;\n  border-radius: 2px;\n  padding-top: 50px;\n}\n.apply-node > .h-main .center .center-box .info {\n  color: #421e88;\n  font-size: 16px;\n  font-weight: bold;\n  padding: 15px;\n}\n.apply-node > .h-main .center .center-box .tel {\n  background: #673dc7;\n  position: relative;\n}\n.apply-node > .h-main .center .center-box .tel img {\n  position: absolute;\n  top: 15px;\n  width: 35px;\n}\n.apply-node > .h-main .center .center-box .tel .left-img {\n  left: -35px;\n}\n.apply-node > .h-main .center .center-box .tel .right-img {\n  right: -35px;\n}\n.apply-node > .h-main .center .center-box .tel h4 {\n  font-size: 20px;\n}\n.apply-node > .h-main .center .center-box .tel .text {\n  padding: 4px;\n  text-align: center;\n  color: #fff;\n  font-weight: bold;\n}\n.apply-node > .h-main .center .center-box .contact {\n  padding: 20px 0;\n}\n.apply-node > .h-main .center .center-box .contact .button {\n  background: url("/static/images/apply/button.png");\n  background-size: 100% 100%;\n  color: #fff;\n  text-align: center;\n  width: 190px;\n  margin: 0 auto;\n  padding: 5px 0;\n}\n.apply-node > .h-main .center .center-box .contact .button h5 {\n  font-size: 16px;\n  font-weight: bold;\n}\n.apply-node > .h-main .center .center-box .contact .button p {\n  font-size: 12px;\n}\n.apply-node > .h-main .center .center-box .qr-code {\n  text-align: center;\n  padding-bottom: 20px;\n}\n.apply-node > .h-main .bottom {\n  background: #421e88;\n  padding: 50px 0;\n}\n.apply-node > .h-main .bottom img {\n  display: block;\n  margin: 0 auto;\n  width: 80%;\n}',"",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/node/applynode.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,YAAY;EACZ,aAAa;EACb,eAAe;CAChB;AACD;EACE,oBAAoB;EACpB,qBAAqB;CACtB;AACD;EACE,YAAY;CACb;AACD;EACE,oBAAoB;EACpB,mBAAmB;CACpB;AACD;EACE,eAAe;EACf,aAAa;EACb,mBAAmB;EACnB,WAAW;EACX,UAAU;EACV,mBAAmB;EACnB,WAAW;CACZ;AACD;EACE,mBAAmB;EACnB,cAAc;EACd,iBAAiB;EACjB,kBAAkB;EAClB,iBAAiB;EACjB,mBAAmB;EACnB,kBAAkB;CACnB;AACD;EACE,eAAe;EACf,gBAAgB;EAChB,kBAAkB;EAClB,cAAc;CACf;AACD;EACE,oBAAoB;EACpB,mBAAmB;CACpB;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,YAAY;CACb;AACD;EACE,YAAY;CACb;AACD;EACE,aAAa;CACd;AACD;EACE,gBAAgB;CACjB;AACD;EACE,aAAa;EACb,mBAAmB;EACnB,YAAY;EACZ,kBAAkB;CACnB;AACD;EACE,gBAAgB;CACjB;AACD;EACE,mDAAmD;EACnD,2BAA2B;EAC3B,YAAY;EACZ,mBAAmB;EACnB,aAAa;EACb,eAAe;EACf,eAAe;CAChB;AACD;EACE,gBAAgB;EAChB,kBAAkB;CACnB;AACD;EACE,gBAAgB;CACjB;AACD;EACE,mBAAmB;EACnB,qBAAqB;CACtB;AACD;EACE,oBAAoB;EACpB,gBAAgB;CACjB;AACD;EACE,eAAe;EACf,eAAe;EACf,WAAW;CACZ",file:"applynode.vue",sourcesContent:['\n.apply-node {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.apply-node > .h-main {\n  width: 100%;\n  height: 100%;\n  overflow: auto;\n}\n.apply-node > .h-main .top {\n  background: #ffca12;\n  padding-bottom: 40px;\n}\n.apply-node > .h-main .top img {\n  width: 100%;\n}\n.apply-node > .h-main .center {\n  background: #ffca12;\n  position: relative;\n}\n.apply-node > .h-main .center .icon {\n  display: block;\n  width: 100px;\n  position: absolute;\n  top: -40px;\n  left: 50%;\n  margin-left: -50px;\n  z-index: 2;\n}\n.apply-node > .h-main .center .center-box {\n  position: relative;\n  bottom: -15px;\n  margin-left: 12%;\n  margin-right: 12%;\n  background: #fff;\n  border-radius: 2px;\n  padding-top: 50px;\n}\n.apply-node > .h-main .center .center-box .info {\n  color: #421e88;\n  font-size: 16px;\n  font-weight: bold;\n  padding: 15px;\n}\n.apply-node > .h-main .center .center-box .tel {\n  background: #673dc7;\n  position: relative;\n}\n.apply-node > .h-main .center .center-box .tel img {\n  position: absolute;\n  top: 15px;\n  width: 35px;\n}\n.apply-node > .h-main .center .center-box .tel .left-img {\n  left: -35px;\n}\n.apply-node > .h-main .center .center-box .tel .right-img {\n  right: -35px;\n}\n.apply-node > .h-main .center .center-box .tel h4 {\n  font-size: 20px;\n}\n.apply-node > .h-main .center .center-box .tel .text {\n  padding: 4px;\n  text-align: center;\n  color: #fff;\n  font-weight: bold;\n}\n.apply-node > .h-main .center .center-box .contact {\n  padding: 20px 0;\n}\n.apply-node > .h-main .center .center-box .contact .button {\n  background: url("/static/images/apply/button.png");\n  background-size: 100% 100%;\n  color: #fff;\n  text-align: center;\n  width: 190px;\n  margin: 0 auto;\n  padding: 5px 0;\n}\n.apply-node > .h-main .center .center-box .contact .button h5 {\n  font-size: 16px;\n  font-weight: bold;\n}\n.apply-node > .h-main .center .center-box .contact .button p {\n  font-size: 12px;\n}\n.apply-node > .h-main .center .center-box .qr-code {\n  text-align: center;\n  padding-bottom: 20px;\n}\n.apply-node > .h-main .bottom {\n  background: #421e88;\n  padding: 50px 0;\n}\n.apply-node > .h-main .bottom img {\n  display: block;\n  margin: 0 auto;\n  width: 80%;\n}'],sourceRoot:""}])}});
//# sourceMappingURL=40.js.map?v=eb61dc6e9f7513d9d985