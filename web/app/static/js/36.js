webpackJsonp([36],{FctN:function(n,e,t){"use strict";function o(n){t("z0Kh")}Object.defineProperty(e,"__esModule",{value:!0});var i=t("POHo"),d=t("SQ4B"),A=(i.a,{name:"index",components:{slide:i.a},data:function(){return{nodeInfo:{},replaceStr:function(n){return n?n.replace(/\r\n/g,"<br/>").replace(/\n/g,"<br/>").replace(/\s/g,"&nbsp"):""}}},computed:{bgStyle:function(){var n=this.nodeInfo.typeId;return n||(n=1),{backgroundImage:"url(/static/images/personal-node/bg_"+n+".jpg)"}}},methods:{goVoting:function(){this.$router.push({path:"/home/node/dts"+this.$route.params.id+"/voting"})},getData:function(){var n=this;d.a.post("/node/info",{id:this.$route.params.id},function(e){if(0!==e.code)return void n.$vux.toast.show(e.msg);n.nodeInfo=e.content})}},created:function(){this.getData()}}),a=function(){var n=this,e=n.$createElement,t=n._self._c||e;return t("slide",[t("div",{staticClass:"node-details"},[t("app-header",[t("span",{attrs:{slot:"right"},on:{click:n.goVoting},slot:"right"},[n._v("投票明细")])]),n._v(" "),n.nodeInfo?t("div",{staticClass:"node-details-content"},[t("div",{staticClass:"top",style:n.bgStyle},[t("div",{staticClass:"img"},[t("img",{attrs:{src:n.nodeInfo.logo||"/static/images/node-avatar-default.jpg",alt:""}})]),n._v(" "),t("p",{staticClass:"name"},[n._v(n._s(n.nodeInfo.name))]),n._v(" "),n.nodeInfo.isTenure?t("span",{staticClass:"sign right-sign"},[n._v("任职")]):n._e(),n._v(" "),n.nodeInfo.typeName?t("span",{staticClass:"sign left-sign",domProps:{innerHTML:n._s(n.nodeInfo.typeName)}},[n._v("超级节点")]):n._e()]),n._v(" "),t("div",{staticClass:"bottom"},[t("div",{staticClass:"nav"},[t("div",[t("h2",[n._v(n._s(n.nodeInfo.voteNumber))]),n._v(" "),t("p",[n._v("票")])]),n._v(" "),t("div",[t("h4",[n._v(n._s(n.nodeInfo.peopleNumber))]),n._v(" "),t("p",[n._v("人支持")])])]),n._v(" "),t("dl",[t("dt",[n._v("简介")]),n._v(" "),t("dd",{domProps:{innerHTML:n._s(n.replaceStr(n.nodeInfo.desc))}})]),n._v(" "),t("dl",[t("dt",[n._v("建设方案")]),n._v(" "),t("dd",{domProps:{innerHTML:n._s(n.replaceStr(n.nodeInfo.scheme))}})])])]):n._e(),n._v(" "),n.nodeInfo.isVote?t("div",{staticClass:"btn-box"},[t("router-link",{attrs:{tag:"button",to:{path:"/home/vote",query:{nodeId:n.nodeInfo.id,nodeName:n.nodeInfo.name}}}},[n._v("支持TA\n      ")])],1):n._e(),n._v(" "),t("router-view")],1)])},s=[],l={render:a,staticRenderFns:s},r=l,p=t("VU/8"),c=o,B=p(A,r,!1,c,null,null);e.default=B.exports},nscM:function(n,e,t){e=n.exports=t("FZ+f")(!0),e.push([n.i,'\n.node-details {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n  background: #f7f7f7;\n}\n.node-details > .app-header {\n  background: #f7f7f7;\n}\n.node-details .node-details-content {\n  margin: 20px;\n  margin-top: 60px;\n  background: #fff;\n  border-radius: 20px;\n  overflow: auto;\n  margin-bottom: 75px;\n}\n.node-details .node-details-content .top {\n  position: relative;\n  height: 200px;\n  background-image: url("/static/images/personal-node/bg_1.jpg");\n  color: #fff;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: center;\n  -webkit-justify-content: center;\n          justify-content: center;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n  -webkit-flex-direction: column;\n          flex-direction: column;\n}\n.node-details .node-details-content .top .name {\n  font-size: 20px;\n  margin-top: 20px;\n}\n.node-details .node-details-content .top .img {\n  width: 85px;\n  height: 85px;\n  border-radius: 50%;\n  overflow: hidden;\n}\n.node-details .node-details-content .top .img img {\n  width: 100%;\n  height: 100%;\n}\n.node-details .node-details-content .top .sign {\n  position: absolute;\n  top: 20px;\n  border-radius: 10px;\n  font-size: 12px;\n  background: rgba(150,150,150,0.3);\n  padding: 5px 10px;\n}\n.node-details .node-details-content .top .left-sign {\n  left: 20px;\n}\n.node-details .node-details-content .top .right-sign {\n  right: 20px;\n}\n.node-details .node-details-content .bottom {\n  padding: 30px 25px;\n  min-height: 250px;\n}\n.node-details .node-details-content .bottom .nav {\n  text-align: center;\n  padding-bottom: 20px;\n}\n.node-details .node-details-content .bottom .nav > div {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: end;\n  -webkit-align-items: flex-end;\n          align-items: flex-end;\n  -webkit-box-pack: center;\n  -webkit-justify-content: center;\n          justify-content: center;\n  margin-bottom: 15px;\n}\n.node-details .node-details-content .bottom .nav h2 {\n  font-size: 24px;\n}\n.node-details .node-details-content .bottom .nav h4 {\n  font-size: 16px;\n}\n.node-details .node-details-content .bottom .nav p {\n  margin-left: 10px;\n  color: #959da6;\n}\n.node-details .node-details-content .bottom dl {\n  margin-bottom: 45px;\n}\n.node-details .node-details-content .bottom dt {\n  margin-bottom: 25px;\n  font-size: 20px;\n  font-weight: bold;\n}\n.node-details .node-details-content .bottom dd {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  font-size: 14px;\n  line-height: 1.5em;\n}\n.node-details .btn-box {\n  position: fixed;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  padding: 15px 20px;\n  background-image: -webkit-linear-gradient(top, rgba(243,240,243,0) 0%, #f3f0f3 100%);\n  background-image: linear-gradient(top, rgba(243,240,243,0) 0%, #f3f0f3 100%);\n}\n.node-details .btn-box button {\n  display: block;\n  width: 100%;\n  line-height: 42px;\n  color: #fff;\n  border: 0;\n  background: #ff4800;\n  font-size: 18px;\n  border-radius: 10px;\n}',"",{version:3,sources:["/Users/wangqingai/Desktop/contest_system/web/app/contest-app/src/views/home/nodeDetails/index.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;EACX,eAAe;EACf,oBAAoB;CACrB;AACD;EACE,oBAAoB;CACrB;AACD;EACE,aAAa;EACb,iBAAiB;EACjB,iBAAiB;EACjB,oBAAoB;EACpB,eAAe;EACf,oBAAoB;CACrB;AACD;EACE,mBAAmB;EACnB,cAAc;EACd,+DAA+D;EAC/D,YAAY;EACZ,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,yBAAyB;EACzB,gCAAgC;UACxB,wBAAwB;EAChC,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,6BAA6B;EAC7B,8BAA8B;EAC9B,+BAA+B;UACvB,uBAAuB;CAChC;AACD;EACE,gBAAgB;EAChB,iBAAiB;CAClB;AACD;EACE,YAAY;EACZ,aAAa;EACb,mBAAmB;EACnB,iBAAiB;CAClB;AACD;EACE,YAAY;EACZ,aAAa;CACd;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,oBAAoB;EACpB,gBAAgB;EAChB,kCAAkC;EAClC,kBAAkB;CACnB;AACD;EACE,WAAW;CACZ;AACD;EACE,YAAY;CACb;AACD;EACE,mBAAmB;EACnB,kBAAkB;CACnB;AACD;EACE,mBAAmB;EACnB,qBAAqB;CACtB;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,uBAAuB;EACvB,8BAA8B;UACtB,sBAAsB;EAC9B,yBAAyB;EACzB,gCAAgC;UACxB,wBAAwB;EAChC,oBAAoB;CACrB;AACD;EACE,gBAAgB;CACjB;AACD;EACE,gBAAgB;CACjB;AACD;EACE,kBAAkB;EAClB,eAAe;CAChB;AACD;EACE,oBAAoB;CACrB;AACD;EACE,oBAAoB;EACpB,gBAAgB;EAChB,kBAAkB;CACnB;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,gBAAgB;EAChB,mBAAmB;CACpB;AACD;EACE,gBAAgB;EAChB,UAAU;EACV,QAAQ;EACR,SAAS;EACT,mBAAmB;EACnB,qFAAqF;EACrF,6EAA6E;CAC9E;AACD;EACE,eAAe;EACf,YAAY;EACZ,kBAAkB;EAClB,YAAY;EACZ,UAAU;EACV,oBAAoB;EACpB,gBAAgB;EAChB,oBAAoB;CACrB",file:"index.vue",sourcesContent:['\n.node-details {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n  background: #f7f7f7;\n}\n.node-details > .app-header {\n  background: #f7f7f7;\n}\n.node-details .node-details-content {\n  margin: 20px;\n  margin-top: 60px;\n  background: #fff;\n  border-radius: 20px;\n  overflow: auto;\n  margin-bottom: 75px;\n}\n.node-details .node-details-content .top {\n  position: relative;\n  height: 200px;\n  background-image: url("/static/images/personal-node/bg_1.jpg");\n  color: #fff;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: center;\n  -webkit-justify-content: center;\n          justify-content: center;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n  -webkit-flex-direction: column;\n          flex-direction: column;\n}\n.node-details .node-details-content .top .name {\n  font-size: 20px;\n  margin-top: 20px;\n}\n.node-details .node-details-content .top .img {\n  width: 85px;\n  height: 85px;\n  border-radius: 50%;\n  overflow: hidden;\n}\n.node-details .node-details-content .top .img img {\n  width: 100%;\n  height: 100%;\n}\n.node-details .node-details-content .top .sign {\n  position: absolute;\n  top: 20px;\n  border-radius: 10px;\n  font-size: 12px;\n  background: rgba(150,150,150,0.3);\n  padding: 5px 10px;\n}\n.node-details .node-details-content .top .left-sign {\n  left: 20px;\n}\n.node-details .node-details-content .top .right-sign {\n  right: 20px;\n}\n.node-details .node-details-content .bottom {\n  padding: 30px 25px;\n  min-height: 250px;\n}\n.node-details .node-details-content .bottom .nav {\n  text-align: center;\n  padding-bottom: 20px;\n}\n.node-details .node-details-content .bottom .nav > div {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: end;\n  -webkit-align-items: flex-end;\n          align-items: flex-end;\n  -webkit-box-pack: center;\n  -webkit-justify-content: center;\n          justify-content: center;\n  margin-bottom: 15px;\n}\n.node-details .node-details-content .bottom .nav h2 {\n  font-size: 24px;\n}\n.node-details .node-details-content .bottom .nav h4 {\n  font-size: 16px;\n}\n.node-details .node-details-content .bottom .nav p {\n  margin-left: 10px;\n  color: #959da6;\n}\n.node-details .node-details-content .bottom dl {\n  margin-bottom: 45px;\n}\n.node-details .node-details-content .bottom dt {\n  margin-bottom: 25px;\n  font-size: 20px;\n  font-weight: bold;\n}\n.node-details .node-details-content .bottom dd {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  font-size: 14px;\n  line-height: 1.5em;\n}\n.node-details .btn-box {\n  position: fixed;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  padding: 15px 20px;\n  background-image: -webkit-linear-gradient(top, rgba(243,240,243,0) 0%, #f3f0f3 100%);\n  background-image: linear-gradient(top, rgba(243,240,243,0) 0%, #f3f0f3 100%);\n}\n.node-details .btn-box button {\n  display: block;\n  width: 100%;\n  line-height: 42px;\n  color: #fff;\n  border: 0;\n  background: #ff4800;\n  font-size: 18px;\n  border-radius: 10px;\n}'],sourceRoot:""}])},z0Kh:function(n,e,t){var o=t("nscM");"string"==typeof o&&(o=[[n.i,o,""]]),o.locals&&(n.exports=o.locals);t("rjj0")("2bb30daa",o,!0,{})}});
//# sourceMappingURL=36.js.map?v=953e17ad99dbaaa8eb11