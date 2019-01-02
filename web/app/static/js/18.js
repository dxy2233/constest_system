webpackJsonp([18],{j4P5:function(n,t,e){t=n.exports=e("FZ+f")(!0),t.push([n.i,"\n.node-submit {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n  line-height: 1.2em;\n}\n.node-submit .ps {\n  color: #ff4800;\n  font-size: 12px;\n  margin-top: 5px;\n}\n.node-submit .title-img {\n  width: 100%;\n}\n.node-submit .wrapper {\n  padding: 0 15px;\n}\n.node-submit .wrapper .tip {\n  padding: 10px 0;\n  color: #ff4800;\n  font-size: 12px;\n}\n.node-submit .wrapper .address-btn {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  margin-top: 5px;\n  margin-bottom: 15px;\n}\n.node-submit .wrapper .address-btn li {\n  width: 48%;\n  padding: 10px 0;\n  border-radius: 10px;\n  font-size: 16px;\n  text-align: center;\n  color: #fff;\n  line-height: 1.25rem;\n  box-shadow: 0px 2px 6px 0px rgba(255,181,67,0.5);\n}\n.node-submit .wrapper .address-btn .grt {\n  background: #ff4800;\n}\n.node-submit .wrapper .address-btn .tt {\n  background: #ffb543;\n}\n.node-submit .wrapper .condition {\n  font-size: 12px;\n  margin-bottom: 20px;\n}\n.node-submit .wrapper .condition .normal:before {\n  content: '';\n  margin-right: 5px;\n  display: inline-block;\n  width: 8px;\n  height: 8px;\n  background: #959da6;\n}\n.node-submit .wrapper .condition .added {\n  color: #ff4800;\n}\n.node-submit .wrapper .form-item {\n  margin-bottom: 15px;\n}\n.node-submit .wrapper .form-item :-moz-placeholder {\n/* Mozilla Firefox 4 to 18 */\n  color: #959da6;\n}\n.node-submit .wrapper .form-item ::-moz-placeholder {\n/* Mozilla Firefox 19+ */\n  color: #959da6;\n}\n.node-submit .wrapper .form-item input:-ms-input-placeholder {\n  color: #959da6;\n}\n.node-submit .wrapper .form-item input::-webkit-input-placeholder {\n  color: #959da6;\n}\n.node-submit .wrapper .form-item .label {\n  margin-bottom: 5px;\n  font-weight: bold;\n}\n.node-submit .wrapper .form-item .label span {\n  font-weight: normal;\n  margin-top: 5px;\n  color: #959da6;\n}\n.node-submit .wrapper .form-item .label .must {\n  color: #f52e00;\n}\n.node-submit .wrapper .form-item input {\n  width: 100%;\n  line-height: 42px;\n  border-radius: 10px;\n  box-sizing: border-box;\n  padding: 0 10px;\n  border: 1px solid #c6d0da;\n}\n.node-submit .wrapper .sbm-btn-box {\n  padding: 25px 0;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/node/submit.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;EACX,eAAe;EACf,mBAAmB;CACpB;AACD;EACE,eAAe;EACf,gBAAgB;EAChB,gBAAgB;CACjB;AACD;EACE,YAAY;CACb;AACD;EACE,gBAAgB;CACjB;AACD;EACE,gBAAgB;EAChB,eAAe;EACf,gBAAgB;CACjB;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;EACvC,gBAAgB;EAChB,oBAAoB;CACrB;AACD;EACE,WAAW;EACX,gBAAgB;EAChB,oBAAoB;EACpB,gBAAgB;EAChB,mBAAmB;EACnB,YAAY;EACZ,qBAAqB;EACrB,iDAAiD;CAClD;AACD;EACE,oBAAoB;CACrB;AACD;EACE,oBAAoB;CACrB;AACD;EACE,gBAAgB;EAChB,oBAAoB;CACrB;AACD;EACE,YAAY;EACZ,kBAAkB;EAClB,sBAAsB;EACtB,WAAW;EACX,YAAY;EACZ,oBAAoB;CACrB;AACD;EACE,eAAe;CAChB;AACD;EACE,oBAAoB;CACrB;AACD;AACA,6BAA6B;EAC3B,eAAe;CAChB;AACD;AACA,yBAAyB;EACvB,eAAe;CAChB;AACD;EACE,eAAe;CAChB;AACD;EACE,eAAe;CAChB;AACD;EACE,mBAAmB;EACnB,kBAAkB;CACnB;AACD;EACE,oBAAoB;EACpB,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,eAAe;CAChB;AACD;EACE,YAAY;EACZ,kBAAkB;EAClB,oBAAoB;EACpB,uBAAuB;EACvB,gBAAgB;EAChB,0BAA0B;CAC3B;AACD;EACE,gBAAgB;CACjB",file:"submit.vue",sourcesContent:["\n.node-submit {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n  line-height: 1.2em;\n}\n.node-submit .ps {\n  color: #ff4800;\n  font-size: 12px;\n  margin-top: 5px;\n}\n.node-submit .title-img {\n  width: 100%;\n}\n.node-submit .wrapper {\n  padding: 0 15px;\n}\n.node-submit .wrapper .tip {\n  padding: 10px 0;\n  color: #ff4800;\n  font-size: 12px;\n}\n.node-submit .wrapper .address-btn {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  margin-top: 5px;\n  margin-bottom: 15px;\n}\n.node-submit .wrapper .address-btn li {\n  width: 48%;\n  padding: 10px 0;\n  border-radius: 10px;\n  font-size: 16px;\n  text-align: center;\n  color: #fff;\n  line-height: 1.25rem;\n  box-shadow: 0px 2px 6px 0px rgba(255,181,67,0.5);\n}\n.node-submit .wrapper .address-btn .grt {\n  background: #ff4800;\n}\n.node-submit .wrapper .address-btn .tt {\n  background: #ffb543;\n}\n.node-submit .wrapper .condition {\n  font-size: 12px;\n  margin-bottom: 20px;\n}\n.node-submit .wrapper .condition .normal:before {\n  content: '';\n  margin-right: 5px;\n  display: inline-block;\n  width: 8px;\n  height: 8px;\n  background: #959da6;\n}\n.node-submit .wrapper .condition .added {\n  color: #ff4800;\n}\n.node-submit .wrapper .form-item {\n  margin-bottom: 15px;\n}\n.node-submit .wrapper .form-item :-moz-placeholder {\n/* Mozilla Firefox 4 to 18 */\n  color: #959da6;\n}\n.node-submit .wrapper .form-item ::-moz-placeholder {\n/* Mozilla Firefox 19+ */\n  color: #959da6;\n}\n.node-submit .wrapper .form-item input:-ms-input-placeholder {\n  color: #959da6;\n}\n.node-submit .wrapper .form-item input::-webkit-input-placeholder {\n  color: #959da6;\n}\n.node-submit .wrapper .form-item .label {\n  margin-bottom: 5px;\n  font-weight: bold;\n}\n.node-submit .wrapper .form-item .label span {\n  font-weight: normal;\n  margin-top: 5px;\n  color: #959da6;\n}\n.node-submit .wrapper .form-item .label .must {\n  color: #f52e00;\n}\n.node-submit .wrapper .form-item input {\n  width: 100%;\n  line-height: 42px;\n  border-radius: 10px;\n  box-sizing: border-box;\n  padding: 0 10px;\n  border: 1px solid #c6d0da;\n}\n.node-submit .wrapper .sbm-btn-box {\n  padding: 25px 0;\n}"],sourceRoot:""}])},vb1I:function(n,t,e){var o=e("j4P5");"string"==typeof o&&(o=[[n.i,o,""]]),o.locals&&(n.exports=o.locals);e("rjj0")("560ef104",o,!0,{})},vp5z:function(n,t,e){"use strict";function o(n){e("vb1I")}Object.defineProperty(t,"__esModule",{value:!0});var i=e("POHo"),r=e("SQ4B"),s=e("M1N4"),a=e("l1VX"),m=(i.a,s.a,{name:"index",components:{slide:i.a,sel:s.a},data:function(){return{conditionDts:["超级节点：48250GRT+8750TT","高级节点：19300GRT+3500TT","中级节点：7720GRT+1400TT","动力节点：1930GRT+350TT"],form:{type_id:"",weixin:"",grt_address:"",grt_num:"",tt_address:"",tt_num:"",bpt_address:"",bpt_num:"",recommend_mobile:""},nodeSelData:[],btnLoading:!1,recommend_name:""}},methods:{getNodeSel:function(){var n=this;r.a.post("/node/type-list",{},function(t){if(0!==t.code)return void n.$vux.toast.show(t.msg);n.nodeSelData=t.content;var e=n.nodeSelData[0];n.form.type_id=e.id,n.form.grt_num=e.grt,n.form.tt_num=e.tt,n.form.bpt_num=e.bpt})},changeNode:function(n){this.form.type_id=n.id,this.form.grt_num=n.grt,this.form.tt_num=n.tt,this.form.bpt_num=n.bpt},submitFrom:function(){if(!this.form.weixin)return void this.$vux.toast.show("微信号必填");var n=Number(this.form.grt_num),t=Number(this.form.tt_num),e=Number(this.form.bpt_num);return n+t+e<=0?void this.$vux.toast.show("请输入数量"):n&&!this.form.grt_address?void this.$vux.toast.show("请输入贵人通钱包地址"):t&&!this.form.tt_address?void this.$vux.toast.show("请输入茶通钱包地址"):e&&!this.form.bpt_address?void this.$vux.toast.show("请输入美食通钱包地址"):void this.applyNode()},applyNode:function(){var n=this;this.btnLoading=!0,r.a.post("/node/apply",this.form,function(t){if(n.btnLoading=!1,0!==t.code)return void n.$vux.toast.show(t.msg);n.$vux.toast.show({text:t.msg,type:"success"}),setTimeout(function(){n.$router.push({path:"/personal"})},1500)})},getRecommendMsg:function(){var n=this;this.form.recommend_mobile&&r.a.post("/node/recommend-mobile",{mobile:this.form.recommend_mobile},function(t){if(0!==t.code)return void n.$vux.toast.show(t.msg);n.recommend_name=t.content.realname})}},created:function(){this.getNodeSel()},watch:{"form.grt_num":function(n){var t=Object(a.d)(n);this.form.grt_num=t},"form.tt_num":function(n){var t=Object(a.d)(n);this.form.tt_num=t},"form.bpt_num":function(n){var t=Object(a.d)(n);this.form.bpt_num=t}},computed:{isShowRecommend:function(){return"1"!==this.form.type_id}}}),d=function(){var n=this,t=n.$createElement,e=n._self._c||t;return e("slide",[e("div",{staticClass:"node-submit"},[e("app-header",[n._v("\n      申请节点\n    ")]),n._v(" "),e("div",{staticClass:"h-main"},[e("img",{staticClass:"title-img",attrs:{src:"/static/images/sbm-node-top.png",alt:""}}),n._v(" "),e("div",{staticClass:"wrapper"},[e("div",{staticClass:"tip"},[n._v("\n          提示：尊敬的客户您好！转积分时请认准公司公众号中唯一指定的钱包地址，并且请您在转积分的时候确认扫描二维码出来的钱包地址是否与我们标注的钱包地址一致。\n        ")]),n._v(" "),e("ul",{staticClass:"address-btn"},[e("router-link",{staticClass:"grt",attrs:{tag:"li",to:{path:"/personal/applynode/address",query:{name:"GRT"}}}},[n._v("\n            官方GRT钱包\n            "),e("br"),n._v("\n            收款地址\n          ")]),n._v(" "),e("router-link",{staticClass:"tt",attrs:{tag:"li",to:{path:"/personal/applynode/address",query:{name:"TT"}}}},[n._v("\n            官方TT钱包\n            "),e("br"),n._v("\n            收款地址\n          ")])],1),n._v(" "),e("dl",{staticClass:"condition"},[e("dt",[n._v("\n            报名节点条件：\n            "),e("br"),n._v("\n            节点竞选以“贵人通+茶通”总个数为准，成为各节点条件分别为：\n          ")]),n._v(" "),n._l(n.conditionDts,function(t){return e("dd",{staticClass:"normal"},[n._v(n._s(t))])}),n._v(" "),e("dd",{staticClass:"added"},[n._v("\n            （如您现在有美食通可按照原条件要求的三通数量转入）\n          ")])],2),n._v(" "),e("div",{staticClass:"form"},[e("div",{staticClass:"form-item"},[e("div",{staticClass:"label"},[n._v("\n              您的微信\n              "),e("span",{staticClass:"must"},[n._v("*")])]),n._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:n.form.weixin,expression:"form.weixin"}],attrs:{type:"text",placeholder:"输入您的微信"},domProps:{value:n.form.weixin},on:{input:function(t){t.target.composing||n.$set(n.form,"weixin",t.target.value)}}})]),n._v(" "),e("div",{directives:[{name:"show",rawName:"v-show",value:n.isShowRecommend,expression:"isShowRecommend"}],staticClass:"form-item"},[e("div",{staticClass:"label"},[n._v("\n              推荐人手机号\n              "),e("span",[n._v(n._s(n.recommend_name))])]),n._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:n.form.recommend_mobile,expression:"form.recommend_mobile"}],attrs:{onkeyup:"(this.v=function(){this.value=this.value.replace(/[^0-9-]+/,'');}).call(this)",maxlength:"11",type:"text",placeholder:"输入推荐人手机号"},domProps:{value:n.form.recommend_mobile},on:{blur:n.getRecommendMsg,input:function(t){t.target.composing||n.$set(n.form,"recommend_mobile",t.target.value)}}}),n._v(" "),e("p",{staticClass:"ps"},[n._v("\n              请正确填写节点推荐人手机号，节点推荐人将获得奖励，不填或填写错误提交后将不可修改\n            ")])]),n._v(" "),e("div",{staticClass:"form-item"},[e("div",{staticClass:"label"},[n._v("\n              节点类型\n              "),e("span",{staticClass:"must"},[n._v("*")])]),n._v(" "),e("sel",{attrs:{dataList:n.nodeSelData,placeholder:"请选择节点类型",select:n.form.type_id,value:"id",label:"name"},on:{changeSel:n.changeNode}})],1),n._v(" "),e("div",{staticClass:"form-item"},[e("div",{staticClass:"label"},[n._v("\n              确认已转入贵人通数量\n              ")]),n._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:n.form.grt_num,expression:"form.grt_num"}],attrs:{type:"text",placeholder:"输入数量"},domProps:{value:n.form.grt_num},on:{input:function(t){t.target.composing||n.$set(n.form,"grt_num",t.target.value)}}})]),n._v(" "),e("div",{staticClass:"form-item"},[e("div",{staticClass:"label"},[n._v("\n              申请贵人通钱包地址\n              ")]),n._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:n.form.grt_address,expression:"form.grt_address"}],attrs:{type:"text",placeholder:"输入或粘贴钱包地址"},domProps:{value:n.form.grt_address},on:{input:function(t){t.target.composing||n.$set(n.form,"grt_address",t.target.value)}}})]),n._v(" "),e("div",{staticClass:"form-item"},[e("div",{staticClass:"label"},[n._v("\n              确认已转入茶通数量\n            ")]),n._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:n.form.tt_num,expression:"form.tt_num"}],attrs:{type:"text",placeholder:"输入数量"},domProps:{value:n.form.tt_num},on:{input:function(t){t.target.composing||n.$set(n.form,"tt_num",t.target.value)}}})]),n._v(" "),e("div",{staticClass:"form-item"},[e("div",{staticClass:"label"},[n._v("\n              申请茶通钱包地址\n            ")]),n._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:n.form.tt_address,expression:"form.tt_address"}],attrs:{type:"text",placeholder:"输入或粘贴钱包地址"},domProps:{value:n.form.tt_address},on:{input:function(t){t.target.composing||n.$set(n.form,"tt_address",t.target.value)}}})]),n._v(" "),e("div",{staticClass:"form-item"},[e("div",{staticClass:"label"},[n._v("\n              确认已转入美食通数量\n            ")]),n._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:n.form.bpt_num,expression:"form.bpt_num"}],attrs:{type:"text",placeholder:"输入数量"},domProps:{value:n.form.bpt_num},on:{input:function(t){t.target.composing||n.$set(n.form,"bpt_num",t.target.value)}}})]),n._v(" "),e("div",{staticClass:"form-item"},[e("div",{staticClass:"label"},[n._v("\n              申请美食通钱包地址\n            ")]),n._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:n.form.bpt_address,expression:"form.bpt_address"}],attrs:{type:"text",placeholder:"输入或粘贴钱包地址"},domProps:{value:n.form.bpt_address},on:{input:function(t){t.target.composing||n.$set(n.form,"bpt_address",t.target.value)}}})])]),n._v(" "),e("div",{staticClass:"sbm-btn-box"},[e("x-button",{staticClass:"base-btn",attrs:{type:"warn",disabled:n.btnLoading,"show-loading":n.btnLoading},nativeOn:{click:function(t){return n.submitFrom(t)}}},[n._v("下一步\n          ")])],1)])]),n._v(" "),e("router-view")],1)])},p=[],l={render:d,staticRenderFns:p},A=l,u=e("VU/8"),c=o,f=u(m,A,!1,c,null,null);t.default=f.exports}});
//# sourceMappingURL=18.js.map?v=ce95df83a8d965c337fa