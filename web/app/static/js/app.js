webpackJsonp([41],{"+zJ7":function(t,e){},"1DHf":function(t,e,n){"use strict";function i(t){n("7sYg")}var o=n("kbG3"),s=n("0FxO"),a=n("2IIR"),r=n("wmxo"),l=n("vLYD"),c=(o.a,Object(a.a)(),{name:"cell",components:{InlineDesc:o.a},props:Object(a.a)(),created:function(){},beforeMount:function(){this.hasTitleSlot=!!this.$slots.title,this.$slots.value},computed:{labelStyles:function(){return Object(r.a)({width:Object(l.a)(this,"labelWidth"),textAlign:Object(l.a)(this,"labelAlign"),marginRight:Object(l.a)(this,"labelMarginRight")})},valueClass:function(){return{"vux-cell-primary":"content"===this.primary||"left"===this.valueAlign,"vux-cell-align-left":"left"===this.valueAlign,"vux-cell-arrow-transition":!!this.arrowDirection,"vux-cell-arrow-up":"up"===this.arrowDirection,"vux-cell-arrow-down":"down"===this.arrowDirection}},labelClass:function(){return{"vux-cell-justify":"justify"===this.$parent.labelAlign||"justify"===this.$parent.$parent.labelAlign}},style:function(){if(this.alignItems)return{alignItems:this.alignItems}}},methods:{onClick:function(){!this.disabled&&Object(s.a)(this.link,this.$router)}},data:function(){return{hasTitleSlot:!0,hasMounted:!1}}}),u=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"weui-cell",class:{"vux-tap-active":t.isLink||!!t.link,"weui-cell_access":t.isLink||!!t.link,"vux-cell-no-border-intent":!t.borderIntent,"vux-cell-disabled":t.disabled},style:t.style,on:{click:t.onClick}},[n("div",{staticClass:"weui-cell__hd"},[t._t("icon")],2),t._v(" "),n("div",{staticClass:"vux-cell-bd",class:{"vux-cell-primary":"title"===t.primary&&"left"!==t.valueAlign}},[n("p",[t.title||t.hasTitleSlot?n("label",{staticClass:"vux-label",class:t.labelClass,style:t.labelStyles},[t._t("title",[t._v(t._s(t.title))])],2):t._e(),t._v(" "),t._t("after-title")],2),t._v(" "),n("inline-desc",[t._t("inline-desc",[t._v(t._s(t.inlineDesc))])],2)],1),t._v(" "),n("div",{staticClass:"weui-cell__ft",class:t.valueClass},[t._t("value"),t._v(" "),t._t("default",[t._v(t._s(t.value))]),t._v(" "),t.isLoading?n("i",{staticClass:"weui-loading"}):t._e()],2),t._v(" "),t._t("child")],2)},d=[],h={render:u,staticRenderFns:d},p=h,f=n("VU/8"),m=i,g=f(c,p,!1,m,null,null);e.a=g.exports},"2lnh":function(t,e){},"2sLL":function(t,e,n){"use strict";function i(t){n("RMDY")}var o=n("0FxO"),s=(Boolean,Boolean,Boolean,String,String,Boolean,String,Object,Array,{name:"x-button",props:{type:{default:"default"},disabled:Boolean,mini:Boolean,plain:Boolean,text:String,actionType:String,showLoading:Boolean,link:[String,Object],gradients:{type:Array,validator:function(t){return 2===t.length}}},methods:{onClick:function(){!this.disabled&&Object(o.a)(this.link,this.$router)}},computed:{noBorder:function(){return Array.isArray(this.gradients)},buttonStyle:function(){if(this.gradients)return{background:"linear-gradient(90deg, "+this.gradients[0]+", "+this.gradients[1]+")",color:"#FFFFFF"}},classes:function(){return[{"weui-btn_disabled":!this.plain&&this.disabled,"weui-btn_plain-disabled":this.plain&&this.disabled,"weui-btn_mini":this.mini,"vux-x-button-no-border":this.noBorder},this.plain?"":"weui-btn_"+this.type,this.plain?"weui-btn_plain-"+this.type:"",this.showLoading?"weui-btn_loading":""]}}}),a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("button",{staticClass:"weui-btn",class:t.classes,style:t.buttonStyle,attrs:{disabled:t.disabled,type:t.actionType},on:{click:t.onClick}},[t.showLoading?n("i",{staticClass:"weui-loading"}):t._e(),t._v(" "),t._t("default",[t._v(t._s(t.text))])],2)},r=[],l={render:a,staticRenderFns:r},c=l,u=n("VU/8"),d=i,h=u(s,c,!1,d,null,null);e.a=h.exports},"3k2l":function(t,e){},"4oK9":function(t,e){},"6FzQ":function(t,e){},"7cyf":function(t,e){},"7sYg":function(t,e){},"8Pnd":function(t,e,n){"use strict";n.d(e,"d",function(){return i}),n.d(e,"a",function(){return o}),n.d(e,"c",function(){return s}),n.d(e,"b",function(){return a});var i=!1,o={url:i?"http://app.contest_system.local":""},s="#E74A2B",a="#FF6B3E"},Fgsu:function(t,e){},IcnI:function(t,e,n){"use strict";var i={};n.d(i,"openToast",function(){return l});var o={};n.d(o,"waitCursor",function(){return c}),n.d(o,"toastObj",function(){return u}),n.d(o,"loginMsg",function(){return d}),n.d(o,"identifyMsg",function(){return h}),n.d(o,"payPsw",function(){return p});var s,a=n("7+uW"),r=n("NYxO"),l=function(t,e){t.commit,t.state;({}).show=e.show||!0},c=function(t){return t.waitCursor},u=function(t){return t.toastObj},d=function(t){return t.loginMsg},h=function(t){return t.identifyMsg},p=function(t){return t.payPsw},f={waitCursor:!1,toastObj:{show:!1,time:1e3,type:"text",width:"7.6em"},loginMsg:JSON.parse(sessionStorage.getItem("loginMsg"))||null,identifyMsg:JSON.parse(sessionStorage.getItem("identifyMsg"))||null,payPsw:sessionStorage.getItem("payPsw")||"0"},m=f,g=n("bOdI"),v=n.n(g),b=(s={},v()(s,"WAIT_CURSOR",function(t,e){t.waitCursor=e}),v()(s,"TOAST_OBJ",function(t,e){t.toastObj=e}),v()(s,"LOGIN_MSG",function(t,e){t.loginMsg=e}),v()(s,"IDENTIFY_MSG",function(t,e){t.identifyMsg=e}),v()(s,"PAY_PSW",function(t,e){t.payPsw=e}),s),_=b,w=n("sax8"),x=n.n(w),y=n("8Pnd");a.a.use(r.a);e.a=new r.a.Store({actions:i,getters:o,state:m,mutations:_,strict:y.d,plugins:y.d?[x()()]:[]})},NHnr:function(t,e,n){"use strict";function i(t){n("Fgsu")}function o(t){n("iASr")}function s(t){n("4oK9")}function a(t){n("Xn0V")}function r(t){n("owug")}function l(t){n("j/tg")}function c(t){n("6FzQ")}function u(t){n("+zJ7")}Object.defineProperty(e,"__esModule",{value:!0});var d=n("7+uW"),h=n("v5o6"),p=n.n(h),f=n("YaEn"),m=n("Dd8w"),g=n.n(m),v=n("rLAy"),b=n("piuB"),_=(b.b,String,{mounted:function(){},name:"tabbar",mixins:[b.b],props:{iconClass:String}}),w=function(){var t=this,e=t.$createElement;return(t._self._c||e)("div",{staticClass:"weui-tabbar"},[t._t("default")],2)},x=[],y={render:w,staticRenderFns:x},S=y,k=n("VU/8"),C=i,P=k(_,S,!1,C,null,null),O=P.exports,T=(String,Number,{name:"badge",props:{text:[String,Number]}}),M=function(){var t=this,e=t.$createElement;return(t._self._c||e)("span",{class:["vux-badge",{"vux-badge-dot":void 0===t.text,"vux-badge-single":void 0!==t.text&&1===t.text.toString().length}],domProps:{textContent:t._s(t.text)}})},$=[],B={render:M,staticRenderFns:$},j=B,F=n("VU/8"),I=o,A=F(T,j,!1,I,null,null),L=A.exports,N=(b.a,Boolean,String,String,Object,String,{name:"tabbar-item",components:{Badge:L},mounted:function(){this.$slots.icon||(this.simple=!0),this.$slots["icon-active"]&&(this.hasActiveIcon=!0)},mixins:[b.a],props:{showDot:{type:Boolean,default:!1},badge:String,link:[String,Object],iconClass:String},computed:{isActive:function(){return this.$parent.index===this.currentIndex}},data:function(){return{simple:!1,hasActiveIcon:!1}}}),R=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("a",{staticClass:"weui-tabbar__item",class:{"weui-bar__item_on":t.isActive,"vux-tabbar-simple":t.simple},attrs:{href:"javascript:;"},on:{click:function(e){t.onItemClick(!0)}}},[t.simple?t._e():n("div",{staticClass:"weui-tabbar__icon",class:[t.iconClass||t.$parent.iconClass,{"vux-reddot":t.showDot}]},[t.simple||t.hasActiveIcon&&t.isActive?t._e():t._t("icon"),t._v(" "),!t.simple&&t.hasActiveIcon&&t.isActive?t._t("icon-active"):t._e(),t._v(" "),t.badge?n("sup",[n("badge",{attrs:{text:t.badge}})],1):t._e()],2),t._v(" "),n("p",{staticClass:"weui-tabbar__label"},[t._t("label")],2)])},E=[],D={render:R,staticRenderFns:E},V=D,U=n("VU/8"),W=U(N,V,!1,null,null,null),H=W.exports,Y=n("rHil"),G=n("1DHf"),J=(Y.a,G.a,{name:"index",components:{Tabbar:O,TabbarItem:H,Group:Y.a,Cell:G.a},data:function(){return{tabList:f.b,currentPath:""}},methods:{obtainPath:function(t){var e=t.split("/");this.currentPath="/"+e[1]}},created:function(){this.obtainPath(this.$route.path)},watch:{$route:function(t){this.obtainPath(t.path)}}}),z=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("ul",{staticClass:"app-footer"},t._l(t.tabList,function(e,i){return n("router-link",{key:e.path,staticClass:"footer-item",class:{act:t.currentPath===e.path},attrs:{to:e.path,tag:"li"}},[n("span",{staticClass:"icon",class:e.icon}),t._v(" "),n("p",[t._v(t._s(e.name))])])}))},Q=[],K={render:z,staticRenderFns:Q},q=K,X=n("VU/8"),Z=s,tt=X(J,q,!1,Z,null,null),et=tt.exports,nt=n("NYxO"),it=(n("SQ4B"),v.a,g()({},Object(nt.b)(["loginMsg","identifyMsg","payPsw"])),{components:{Toast:v.a,appFooter:et},methods:{},name:"app",watch:{$route:function(t){this.loginMsg&&!parseInt(this.payPsw)&&this.$router.push({path:"/setpsw"})}},computed:g()({},Object(nt.b)(["loginMsg","identifyMsg","payPsw"]))}),ot=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{attrs:{id:"app"}},[n("keep-alive",[n("router-view")],1),t._v(" "),n("app-footer")],1)},st=[],at={render:ot,staticRenderFns:st},rt=at,lt=n("VU/8"),ct=a,ut=lt(it,rt,!1,ct,null,null),dt=ut.exports,ht=n("IcnI"),pt=n("3BeM"),ft=n("oWtu"),mt=n("BEQ0"),gt=n.n(mt),vt=(Object,String,String,Object,{name:"x-header",props:{leftOptions:Object,title:String,transition:String,rightOptions:{type:Object,default:function(){return{showMore:!1}}}},beforeMount:function(){this.$slots["overwrite-title"]&&(this.shouldOverWriteTitle=!0)},computed:{_leftOptions:function(){return gt()({showBack:!0,preventGoBack:!1},this.leftOptions||{})}},methods:{onClickBack:function(){this._leftOptions.preventGoBack?this.$emit("on-click-back"):this.$router?this.$router.back():window.history.back()}},data:function(){return{shouldOverWriteTitle:!1}}}),bt=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"vux-header"},[n("div",{staticClass:"vux-header-left"},[t._t("overwrite-left",[n("transition",{attrs:{name:t.transition}},[n("a",{directives:[{name:"show",rawName:"v-show",value:t._leftOptions.showBack,expression:"_leftOptions.showBack"}],staticClass:"vux-header-back",on:{click:[function(e){if(!("button"in e)&&t._k(e.keyCode,"preventDefault",void 0,e.key,void 0))return null},t.onClickBack]}},[t._v(t._s(void 0===t._leftOptions.backText?"返回":t._leftOptions.backText))])]),t._v(" "),n("transition",{attrs:{name:t.transition}},[n("div",{directives:[{name:"show",rawName:"v-show",value:t._leftOptions.showBack,expression:"_leftOptions.showBack"}],staticClass:"left-arrow",on:{click:t.onClickBack}})])]),t._v(" "),t._t("left")],2),t._v(" "),t.shouldOverWriteTitle?t._e():n("h1",{staticClass:"vux-header-title",on:{click:function(e){t.$emit("on-click-title")}}},[t._t("default",[n("transition",{attrs:{name:t.transition}},[n("span",{directives:[{name:"show",rawName:"v-show",value:t.title,expression:"title"}]},[t._v(t._s(t.title))])])])],2),t._v(" "),t.shouldOverWriteTitle?n("div",{staticClass:"vux-header-title-area"},[t._t("overwrite-title")],2):t._e(),t._v(" "),n("div",{staticClass:"vux-header-right"},[t.rightOptions.showMore?n("a",{staticClass:"vux-header-more",on:{click:[function(e){if(!("button"in e)&&t._k(e.keyCode,"preventDefault",void 0,e.key,void 0))return null},function(e){t.$emit("on-click-more")}]}}):t._e(),t._v(" "),t._t("right")],2)])},_t=[],wt={render:bt,staticRenderFns:_t},xt=wt,yt=n("VU/8"),St=r,kt=yt(vt,xt,!1,St,null,null),Ct=kt.exports,Pt=n("2sLL"),Ot=(Boolean,String,{name:"load-more",props:{showLoading:{type:Boolean,default:!0},tip:String}}),Tt=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"vux-loadmore weui-loadmore",class:{"weui-loadmore_line":!t.showLoading,"weui-loadmore_dot":!t.showLoading&&!t.tip}},[t.showLoading?n("i",{staticClass:"weui-loading"}):t._e(),t._v(" "),n("span",{directives:[{name:"show",rawName:"v-show",value:t.tip||!t.showLoading,expression:"tip || !showLoading"}],staticClass:"weui-loadmore__tips"},[t._v(t._s(t.tip))])])},Mt=[],$t={render:Tt,staticRenderFns:Mt},Bt=$t,jt=n("VU/8"),Ft=l,It=jt(Ot,Bt,!1,Ft,null,null),At=(It.exports,{name:"index",data:function(){return{}},methods:{backPage:function(){this.$router.back()}}}),Lt=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"app-header"},[n("div",{staticClass:"center"},[t._t("default")],2),t._v(" "),n("div",{staticClass:"left"},[t._t("left",[n("span",{staticClass:"icon-back",on:{click:t.backPage}})])],2),t._v(" "),n("div",{staticClass:"right"},[t._t("right")],2)])},Nt=[],Rt={render:Lt,staticRenderFns:Nt},Et=Rt,Dt=n("VU/8"),Vt=c,Ut=Dt(At,Et,!1,Vt,"data-v-9e47aed4",null),Wt=Ut.exports,Ht=n("jijt"),Yt=(Ht.a,{name:"index",components:{InlineLoading:Ht.a}}),Gt=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"load-more"},[n("inline-loading"),n("span",{staticStyle:{"vertical-align":"middle",display:"inline-block","font-size":"14px"}},[t._v("  加载中")])],1)},Jt=[],zt={render:Gt,staticRenderFns:Jt},Qt=zt,Kt=n("VU/8"),qt=u,Xt=Kt(Yt,Qt,!1,qt,"data-v-4e94c588",null),Zt=Xt.exports,te=n("POcy"),ee=n.n(te);n("7cyf");d.a.use(ee.a),d.a.component("icon",ft.a),d.a.component("x-header",Ct),d.a.component("x-button",Pt.a),d.a.component("load-more",Zt),d.a.component("app-header",Wt),d.a.use(pt.a,{time:1500,type:"text",width:"12em"}),p.a.attach(document.body),d.a.config.productionTip=!1,new d.a({router:f.a,store:ht.a,render:function(t){return t(dt)}}).$mount("#app-box")},RMDY:function(t,e){},SQ4B:function(t,e,n){"use strict";var i=n("mvHQ"),o=n.n(i),s=n("//Fk"),a=n.n(s),r=n("mtWM"),l=n.n(r),c=n("mw3O"),u=n.n(c),d=n("8Pnd"),h=l.a.create({baseURL:d.a.url,timeout:5e3});h.defaults.headers.post["Content-Type"]="application/x-www-form-urlencoded;charset=UTF-8",h.interceptors.request.use(function(t){var e=JSON.parse(sessionStorage.getItem("loginMsg"));return t.headers.Authorization=e?"Bearer "+e.accessToken:"","post"===t.method&&(t.data=u.a.stringify(t.data)),t},function(t){console.log(t),a.a.reject(t)}),l.a.interceptors.response.use(function(t){return t},function(t){if(console.log(t),t&&t.response){switch(t.response.status){case 400:t.msg="请求错误";break;case 401:t.msg="未授权，请登录";break;case 403:t.msg="拒绝访问";break;case 404:t.msg="请求地址出错: "+t.response.config.url;break;case 408:t.msg="请求超时";break;case 500:t.msg="服务器内部错误";break;case 501:t.msg="服务未实现";break;case 502:t.msg="网关错误";break;case 503:t.msg="服务不可用";break;case 504:t.msg="网关超时";break;case 505:t.err="HTTP版本不受支持"}alert(t.msg)}return console.log(t),a.a.reject(t)});var p=h,f=n("l1VX"),m=n("YaEn"),g=(n("IcnI"),n("3BeM"),{get:function(t,e){p({method:"get",url:t}).then(function(t){e(t.data)}).catch(function(t){t.msg&&console.log(t.msg)})},post:function(t,e,n){v(function(){p({method:"post",url:t,data:e}).then(function(t){-1===t.data.code&&(Object(f.b)(),m.a.push({path:"/login"})),n(t.data)}).catch(function(t){t.msg&&console.log(t.msg)})})}});window.refreshLock=!1;var v=function t(e){if(window.refreshLock)return void setTimeout(function(){t(e)},500);var n=JSON.parse(sessionStorage.getItem("loginMsg"));if(n){var i=parseInt(Date.parse(new Date)/1e3);n.expireTime-i>0?n.expireTime-i>1800?e():(window.refreshLock=!0,b(e)):(Object(f.b)(),e())}else e()},b=function(t){p({method:"post",url:"/login/refresh-token",data:{refreshToken:loginMsg.refreshToken}}).then(function(e){e=e.data,0===e.code?sessionStorage.setItem("loginMsg",o()(e.content)):Object(f.b)(),window.refreshLock=!1,t()}).catch(function(t){window.refreshLock=!1,t.msg&&console.log(t.msg)})};e.a=g},Xn0V:function(t,e){},YaEn:function(t,e,n){"use strict";n.d(e,"b",function(){return a});var i=n("7+uW"),o=n("/ocq");i.a.use(o.a);var s=[{path:"/",redirect:"/home"},{path:"/login",component:function(){return n.e(4).then(n.bind(null,"T+/8"))}},{path:"/setpsw",component:function(){return Promise.all([n.e(0),n.e(2)]).then(n.bind(null,"u2FT"))}}],a=[{path:"/home",name:"首页",icon:"icon-home",component:function(){return n.e(5).then(n.bind(null,"KR8f"))},children:[{path:"node",component:function(){return Promise.all([n.e(0),n.e(11)]).then(n.bind(null,"kzui"))},children:[{path:"dts:id",component:function(){return Promise.all([n.e(0),n.e(32)]).then(n.bind(null,"FctN"))},children:[{path:"voting",component:function(){return Promise.all([n.e(0),n.e(33)]).then(n.bind(null,"HP3t"))}}]},{path:"rule",component:function(){return Promise.all([n.e(0),n.e(30)]).then(n.bind(null,"cq1P"))}}]},{path:"notice",component:function(){return Promise.all([n.e(0),n.e(16)]).then(n.bind(null,"5fmV"))},children:[{path:"dts:id",component:function(){return Promise.all([n.e(0),n.e(19)]).then(n.bind(null,"k1c5"))}}]},{path:"contribute",component:function(){return Promise.all([n.e(0),n.e(22)]).then(n.bind(null,"AUYM"))},children:[{path:"rule",component:function(){return Promise.all([n.e(0),n.e(18)]).then(n.bind(null,"jau4"))}}]},{path:"vote",component:function(){return Promise.all([n.e(0),n.e(8)]).then(n.bind(null,"eN6/"))}}]},{path:"/assets",name:"资产",icon:"icon-money",component:function(){return Promise.all([n.e(0),n.e(34)]).then(n.bind(null,"Emg2"))},children:[{path:"dts:id",component:function(){return Promise.all([n.e(0),n.e(29)]).then(n.bind(null,"B6Qn"))},children:[{path:"frozen",component:function(){return Promise.all([n.e(0),n.e(14)]).then(n.bind(null,"fSs5"))}},{path:"collect",name:"collect",component:function(){return Promise.all([n.e(0),n.e(7)]).then(n.bind(null,"KA6T"))}},{path:"transfer",component:function(){return Promise.all([n.e(3),n.e(0)]).then(n.bind(null,"Z662"))}}]}]},{path:"/personal",name:"我的",icon:"icon-my",component:function(){return Promise.all([n.e(0),n.e(10)]).then(n.bind(null,"uTKz"))},children:[{path:"node",component:function(){return Promise.all([n.e(0),n.e(15)]).then(n.bind(null,"K7ca"))},hidden:!0,children:[{path:"fail",component:function(){return Promise.all([n.e(0),n.e(37)]).then(n.bind(null,"cu1F"))}},{path:"wait",component:function(){return Promise.all([n.e(0),n.e(24)]).then(n.bind(null,"J800"))}},{path:"interests",component:function(){return Promise.all([n.e(0),n.e(28)]).then(n.bind(null,"KeeA"))}}]},{path:"applynode",component:function(){return Promise.all([n.e(0),n.e(31)]).then(n.bind(null,"a8D/"))},children:[{path:"rules",component:function(){return Promise.all([n.e(0),n.e(23)]).then(n.bind(null,"JWya"))}}]},{path:"identify",component:function(){return Promise.all([n.e(0),n.e(1)]).then(n.bind(null,"fdMA"))},children:[{path:"fail",component:function(){return Promise.all([n.e(0),n.e(21)]).then(n.bind(null,"OY+r"))}},{path:"wait",component:function(){return Promise.all([n.e(0),n.e(27)]).then(n.bind(null,"0ZRt"))}},{path:"success",component:function(){return Promise.all([n.e(0),n.e(12)]).then(n.bind(null,"lpXf"))}},{path:"submit",component:function(){return Promise.all([n.e(0),n.e(9)]).then(n.bind(null,"dWZw"))}}]},{path:"psw",component:function(){return Promise.all([n.e(0),n.e(1)]).then(n.bind(null,"fdMA"))},children:[{path:"index",component:function(){return Promise.all([n.e(0),n.e(39)]).then(n.bind(null,"hox6"))},children:[{path:"reset",component:function(){return Promise.all([n.e(0),n.e(36)]).then(n.bind(null,"6W9V"))}},{path:"revise",component:function(){return Promise.all([n.e(0),n.e(38)]).then(n.bind(null,"RJDA"))}}]},{path:"set",component:function(){return Promise.all([n.e(0),n.e(2)]).then(n.bind(null,"u2FT"))}}]},{path:"voucher",component:function(){return Promise.all([n.e(0),n.e(20)]).then(n.bind(null,"P7UP"))}},{path:"vote",component:function(){return Promise.all([n.e(0),n.e(17)]).then(n.bind(null,"On+8"))},children:[{path:"redeem",component:function(){return Promise.all([n.e(0),n.e(35)]).then(n.bind(null,"4psq"))}}]},{path:"rcmd",component:function(){return Promise.all([n.e(0),n.e(6)]).then(n.bind(null,"GjcV"))},children:[{path:"record",component:function(){return Promise.all([n.e(0),n.e(25)]).then(n.bind(null,"hl4E"))}}]},{path:"set",component:function(){return Promise.all([n.e(0),n.e(13)]).then(n.bind(null,"wGDG"))},children:[{path:"about",component:function(){return Promise.all([n.e(0),n.e(26)]).then(n.bind(null,"KFns"))}}]}]}];e.a=new o.a({routes:s.concat(a)})},"c9+y":function(t,e){},iASr:function(t,e){},"j/tg":function(t,e){},jijt:function(t,e,n){"use strict";function i(t){n("c9+y")}var o={name:"inline-loading"},s=function(){var t=this,e=t.$createElement;return(t._self._c||e)("i",{staticClass:"weui-loading"})},a=[],r={render:s,staticRenderFns:a},l=r,c=n("VU/8"),u=i,d=c(o,l,!1,u,null,null);e.a=d.exports},kbG3:function(t,e,n){"use strict";function i(t){n("3k2l")}var o={name:"inline-desc"},s=function(){var t=this,e=t.$createElement;return(t._self._c||e)("span",{staticClass:"vux-label-desc"},[t._t("default")],2)},a=[],r={render:s,staticRenderFns:a},l=r,c=n("VU/8"),u=i,d=c(o,l,!1,u,null,null);e.a=d.exports},kllH:function(t,e){},l1VX:function(t,e,n){"use strict";n.d(e,"b",function(){return o}),n.d(e,"c",function(){return s}),n.d(e,"a",function(){return a});var i=(n("YaEn"),n("IcnI")),o=function(){sessionStorage.clear(),i.a.commit("LOGIN_MSG",null),i.a.commit("IDENTIFY_MSG",null)},s=function(t){return t?t.substr(0,4)+"****"+t.substr(-4):""},a=function(t){var e=document.location.toString(),n=e.split("?");if(n.length>1){for(var i=n[1].split("&"),o=void 0,s=0;s<i.length;s++)if(null!=(o=i[s].split("="))&&o[0]==t)return o[1];return""}return""}},oWtu:function(t,e,n){"use strict";function i(t){n("2lnh")}var o=(String,Boolean,{name:"icon",props:{type:String,isMsg:Boolean},computed:{className:function(){return"weui-icon weui_icon_"+this.type+" weui-icon-"+this.type.replace(/_/g,"-")}}}),s=function(){var t=this,e=t.$createElement;return(t._self._c||e)("i",{class:[t.className,t.isMsg?"weui-icon_msg":""]})},a=[],r={render:s,staticRenderFns:a},l=r,c=n("VU/8"),u=i,d=c(o,l,!1,u,null,null);e.a=d.exports},owug:function(t,e){},qHQD:function(t,e){},rHil:function(t,e,n){"use strict";function i(t){n("qHQD")}var o=n("wmxo"),s=(o.a,String,String,String,String,String,String,Number,String,String,{name:"group",methods:{cleanStyle:o.a},props:{title:String,titleColor:String,labelWidth:String,labelAlign:String,labelMarginRight:String,gutter:[String,Number],footerTitle:String,footerTitleColor:String}}),a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[t.title?n("div",{staticClass:"weui-cells__title",style:t.cleanStyle({color:t.titleColor}),domProps:{innerHTML:t._s(t.title)}}):t._e(),t._v(" "),t._t("title"),t._v(" "),n("div",{staticClass:"weui-cells",class:{"vux-no-group-title":!t.title},style:t.cleanStyle({marginTop:"number"==typeof t.gutter?t.gutter+"px":t.gutter})},[t._t("after-title"),t._v(" "),t._t("default")],2),t._v(" "),t.footerTitle?n("div",{staticClass:"weui-cells__title vux-group-footer-title",style:t.cleanStyle({color:t.footerTitleColor}),domProps:{innerHTML:t._s(t.footerTitle)}}):t._e()],2)},r=[],l={render:a,staticRenderFns:r},c=l,u=n("VU/8"),d=i,h=u(s,c,!1,d,null,null);e.a=h.exports},rLAy:function(t,e,n){"use strict";function i(t){n("kllH")}var o=n("xNvf"),s=(o.a,Boolean,Number,String,String,String,Boolean,String,String,{name:"toast",mixins:[o.a],props:{value:Boolean,time:{type:Number,default:2e3},type:{type:String,default:"success"},transition:String,width:{type:String,default:"7.6em"},isShowMask:{type:Boolean,default:!1},text:String,position:String},data:function(){return{show:!1}},created:function(){this.value&&(this.show=!0)},computed:{currentTransition:function(){return this.transition?this.transition:"top"===this.position?"vux-slide-from-top":"bottom"===this.position?"vux-slide-from-bottom":"vux-fade"},toastClass:function(){return{"weui-toast_forbidden":"warn"===this.type,"weui-toast_cancel":"cancel"===this.type,"weui-toast_success":"success"===this.type,"weui-toast_text":"text"===this.type,"vux-toast-top":"top"===this.position,"vux-toast-bottom":"bottom"===this.position,"vux-toast-middle":"middle"===this.position}},style:function(){if("text"===this.type&&"auto"===this.width)return{padding:"10px"}}},watch:{show:function(t){var e=this;t&&(this.$emit("input",!0),this.$emit("on-show"),this.fixSafariOverflowScrolling("auto"),clearTimeout(this.timeout),this.timeout=setTimeout(function(){e.show=!1,e.$emit("input",!1),e.$emit("on-hide"),e.fixSafariOverflowScrolling("touch")},this.time))},value:function(t){this.show=t}}}),a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"vux-toast"},[n("div",{directives:[{name:"show",rawName:"v-show",value:t.isShowMask&&t.show,expression:"isShowMask && show"}],staticClass:"weui-mask_transparent"}),t._v(" "),n("transition",{attrs:{name:t.currentTransition}},[n("div",{directives:[{name:"show",rawName:"v-show",value:t.show,expression:"show"}],staticClass:"weui-toast",class:t.toastClass,style:{width:t.width}},[n("i",{directives:[{name:"show",rawName:"v-show",value:"text"!==t.type,expression:"type !== 'text'"}],staticClass:"weui-icon-success-no-circle weui-icon_toast"}),t._v(" "),t.text?n("p",{staticClass:"weui-toast__content",style:t.style,domProps:{innerHTML:t._s(t.text)}}):n("p",{staticClass:"weui-toast__content",style:t.style},[t._t("default")],2)])])],1)},r=[],l={render:a,staticRenderFns:r},c=l,u=n("VU/8"),d=i,h=u(s,c,!1,d,null,null);e.a=h.exports}},["NHnr"]);
//# sourceMappingURL=app.js.map?v=aad63ba59081ade36c8b