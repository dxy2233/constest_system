webpackJsonp([2],{ZTmh:function(n,e,s){var t=s("hXPx");"string"==typeof t&&(t=[[n.i,t,""]]),t.locals&&(n.exports=t.locals);s("rjj0")("31eee9ab",t,!0,{})},hXPx:function(n,e,s){e=n.exports=s("FZ+f")(!0),e.push([n.i,"\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n","",{version:3,sources:[],names:[],mappings:"",file:"set.vue",sourceRoot:""}])},u2FT:function(n,e,s){"use strict";function t(n){s("ZTmh")}Object.defineProperty(e,"__esModule",{value:!0});var i=s("Dd8w"),a=s.n(i),o=s("YMwU"),r=s("SQ4B"),w=s("NYxO"),c=(o.a,a()({acceptWord:function(n){switch(this.step){case 0:this.newPsw=n,this.fieldLabel="确认支付密码",this.step=1;break;case 1:this.setNewPsw(n)}},setNewPsw:function(n){var e=this;if(this.newPsw!==n)return this.$vux.toast.show("两次密码输入不一致,请重新设置"),this.newPsw="",this.fieldLabel="设置支付密码",void(this.step=0);this.loadingShow=!0,r.a.post("/pay/create-pass",{pass:this.newPsw,repass:n},function(n){if(e.loadingShow=!1,e.$vux.toast.show(n.msg),0!==n.code)return e.newPsw="",e.fieldLabel="设置支付密码",void(e.step=0);e.setPayPsw("1"),localStorage.setItem("payPsw","1"),setTimeout(function(){e.$router.back()},1e3)})}},Object(w.c)({setPayPsw:"PAY_PSW"})),{name:"reset",components:{pswField:o.a},data:function(){return{fieldLabel:"设置支付密码",newPsw:"",step:0,loadingShow:!1,hiddenHeader:!0}},methods:a()({acceptWord:function(n){switch(this.step){case 0:this.newPsw=n,this.fieldLabel="确认支付密码",this.step=1;break;case 1:this.setNewPsw(n)}},setNewPsw:function(n){var e=this;if(this.newPsw!==n)return this.$vux.toast.show("两次密码输入不一致,请重新设置"),this.newPsw="",this.fieldLabel="设置支付密码",void(this.step=0);this.loadingShow=!0,r.a.post("/pay/create-pass",{pass:this.newPsw,repass:n},function(n){if(e.loadingShow=!1,e.$vux.toast.show(n.msg),0!==n.code)return e.newPsw="",e.fieldLabel="设置支付密码",void(e.step=0);e.setPayPsw("1"),localStorage.setItem("payPsw","1"),setTimeout(function(){e.$router.back()},1e3)})}},Object(w.c)({setPayPsw:"PAY_PSW"})),created:function(){},destroyed:function(){}}),h=function(){var n=this,e=n.$createElement;return(n._self._c||e)("psw-field",{attrs:{title:"",label:n.fieldLabel,loadingShow:n.loadingShow,hiddenHeader:n.hiddenHeader},on:{iptWord:n.acceptWord}})},d=[],l={render:h,staticRenderFns:d},u=l,p=s("VU/8"),f=t,P=p(c,u,!1,f,"data-v-a7699286",null);e.default=P.exports}});
//# sourceMappingURL=2.js.map?v=fa6db3fe9d1e8fb7e9e6