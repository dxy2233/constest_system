webpackJsonp([20],{"0pc8":function(n,t,e){t=n.exports=e("FZ+f")(!0),t.push([n.i,'\n.interests {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n  background: #f3f0f3;\n}\n.interests > .vux-header {\n  background: none;\n  border: none;\n}\n.interests .interests-content {\n  margin: 20px;\n  margin-top: 60px;\n  background: #fff;\n  border-radius: 20px;\n  overflow: hidden;\n}\n.interests .interests-content .top {\n  position: relative;\n  height: 200px;\n  background-image: url("/static/images/personal-node/bg_1.jpg");\n  color: #fff;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: center;\n  -webkit-justify-content: center;\n          justify-content: center;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n  -webkit-flex-direction: column;\n          flex-direction: column;\n}\n.interests .interests-content .top .name {\n  font-size: 20px;\n  margin-top: 20px;\n}\n.interests .interests-content .top .img {\n  width: 90px;\n}\n.interests .interests-content .top .sign {\n  position: absolute;\n  top: 20px;\n  right: 20px;\n  border-radius: 10px;\n  font-size: 12px;\n  background: rgba(150,150,150,0.3);\n  padding: 5px 10px;\n}\n.interests .interests-content .bottom {\n  padding: 30px 25px;\n  min-height: 250px;\n}\n.interests .interests-content .bottom dt {\n  margin-bottom: 30px;\n  font-size: 26px;\n}\n.interests .interests-content .bottom dd {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  margin-bottom: 25px;\n}\n.interests .interests-content .bottom dd .spot {\n  width: 8px;\n  height: 8px;\n  margin-right: 15px;\n  background: #dfdde0;\n  border-radius: 50%;\n}\n.interests .interests-content .bottom dd h3 {\n  font-size: 18px;\n}\n.interests .interests-content .bottom dd p {\n  margin-top: 5px;\n  color: #b4b5bc;\n}',"",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/node/interests.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;EACX,eAAe;EACf,oBAAoB;CACrB;AACD;EACE,iBAAiB;EACjB,aAAa;CACd;AACD;EACE,aAAa;EACb,iBAAiB;EACjB,iBAAiB;EACjB,oBAAoB;EACpB,iBAAiB;CAClB;AACD;EACE,mBAAmB;EACnB,cAAc;EACd,+DAA+D;EAC/D,YAAY;EACZ,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,yBAAyB;EACzB,gCAAgC;UACxB,wBAAwB;EAChC,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,6BAA6B;EAC7B,8BAA8B;EAC9B,+BAA+B;UACvB,uBAAuB;CAChC;AACD;EACE,gBAAgB;EAChB,iBAAiB;CAClB;AACD;EACE,YAAY;CACb;AACD;EACE,mBAAmB;EACnB,UAAU;EACV,YAAY;EACZ,oBAAoB;EACpB,gBAAgB;EAChB,kCAAkC;EAClC,kBAAkB;CACnB;AACD;EACE,mBAAmB;EACnB,kBAAkB;CACnB;AACD;EACE,oBAAoB;EACpB,gBAAgB;CACjB;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,oBAAoB;CACrB;AACD;EACE,WAAW;EACX,YAAY;EACZ,mBAAmB;EACnB,oBAAoB;EACpB,mBAAmB;CACpB;AACD;EACE,gBAAgB;CACjB;AACD;EACE,gBAAgB;EAChB,eAAe;CAChB",file:"interests.vue",sourcesContent:['\n.interests {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n  background: #f3f0f3;\n}\n.interests > .vux-header {\n  background: none;\n  border: none;\n}\n.interests .interests-content {\n  margin: 20px;\n  margin-top: 60px;\n  background: #fff;\n  border-radius: 20px;\n  overflow: hidden;\n}\n.interests .interests-content .top {\n  position: relative;\n  height: 200px;\n  background-image: url("/static/images/personal-node/bg_1.jpg");\n  color: #fff;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: center;\n  -webkit-justify-content: center;\n          justify-content: center;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n  -webkit-flex-direction: column;\n          flex-direction: column;\n}\n.interests .interests-content .top .name {\n  font-size: 20px;\n  margin-top: 20px;\n}\n.interests .interests-content .top .img {\n  width: 90px;\n}\n.interests .interests-content .top .sign {\n  position: absolute;\n  top: 20px;\n  right: 20px;\n  border-radius: 10px;\n  font-size: 12px;\n  background: rgba(150,150,150,0.3);\n  padding: 5px 10px;\n}\n.interests .interests-content .bottom {\n  padding: 30px 25px;\n  min-height: 250px;\n}\n.interests .interests-content .bottom dt {\n  margin-bottom: 30px;\n  font-size: 26px;\n}\n.interests .interests-content .bottom dd {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  margin-bottom: 25px;\n}\n.interests .interests-content .bottom dd .spot {\n  width: 8px;\n  height: 8px;\n  margin-right: 15px;\n  background: #dfdde0;\n  border-radius: 50%;\n}\n.interests .interests-content .bottom dd h3 {\n  font-size: 18px;\n}\n.interests .interests-content .bottom dd p {\n  margin-top: 5px;\n  color: #b4b5bc;\n}'],sourceRoot:""}])},"8FCs":function(n,t,e){var i=e("0pc8");"string"==typeof i&&(i=[[n.i,i,""]]),i.locals&&(n.exports=i.locals);e("rjj0")("e7d0c8ec",i,!0,{})},KeeA:function(n,t,e){"use strict";function i(n){e("8FCs")}Object.defineProperty(t,"__esModule",{value:!0});var o=e("POHo"),s=e("SQ4B"),A=(o.a,{name:"index",components:{slide:o.a},data:function(){return{nodeInfo:{},interestsList:[]}},created:function(){this.nodeInfo=JSON.parse(sessionStorage.getItem("myNodeInfo")),this.getInterests()},computed:{bgStyle:function(){var n=this.nodeInfo.typeId;return n||(n=1),{backgroundImage:"url(/static/images/personal-node/bg_"+n+".jpg)"}}},methods:{getInterests:function(){var n=this;s.a.post("/user/node-rule-info",{},function(t){if(0!==t.code)return void n.$vux.toast.show(t.msg);n.interestsList=t.content.rules})}}}),r=function(){var n=this,t=n.$createElement,e=n._self._c||t;return e("slide",[e("div",{staticClass:"interests"},[e("app-header",[n._v("\n      当前权益\n    ")]),n._v(" "),e("div",{staticClass:"interests-content"},[e("div",{staticClass:"top",style:n.bgStyle},[e("img",{staticClass:"img",attrs:{src:"/static/images/personal-node/icon_"+n.nodeInfo.typeId+".png",alt:""}}),n._v(" "),e("p",{staticClass:"name"},[n._v(n._s(n.nodeInfo.name))]),n._v(" "),n.nodeInfo.isTenure?n._e():e("span",{staticClass:"sign"},[n._v("任职")])]),n._v(" "),e("div",{staticClass:"bottom"},[e("dl",[e("dt",[n._v("节点权益")]),n._v(" "),n._l(n.interestsList,function(t){return e("dd",[e("div",{staticClass:"spot"}),n._v(" "),e("div",{staticClass:"text"},[e("h3",[n._v(n._s(t.name))]),n._v(" "),e("p",[n._v(n._s(t.content))])])])})],2)])])],1)])},a=[],p={render:r,staticRenderFns:a},c=p,d=e("VU/8"),B=i,C=d(A,c,!1,B,null,null);t.default=C.exports}});
//# sourceMappingURL=20.js.map?v=5b8ce7a294657c7ce96f