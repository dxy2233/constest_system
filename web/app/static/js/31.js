webpackJsonp([31],{"6W9V":function(n,e,t){"use strict";function s(n){t("sOK1")}Object.defineProperty(e,"__esModule",{value:!0});var o=t("YMwU"),i=t("SQ4B"),a=(o.a,{name:"reset",components:{pswField:o.a},data:function(){return{fieldLabel:"设置新支付密码",newPsw:"",step:0,loadingShow:!1,vcode:""}},methods:{acceptWord:function(n){switch(this.step){case 0:this.newPsw=n,this.fieldLabel="确认新支付密码",this.step=1;break;case 1:this.setNewPsw(n)}},setNewPsw:function(n){var e=this;if(this.newPsw!==n)return this.$vux.toast.show("两次密码输入不一致,请重新设置"),this.newPsw="",this.fieldLabel="设置新支付密码",void(this.step=0);this.loadingShow=!0;var t=localStorage.getItem("resetVcode");i.a.post("/pay/reset-pass",{pass:this.newPsw,repass:n,vcode:t},function(n){if(e.loadingShow=!1,e.$vux.toast.show(n.msg),0!==n.code)return e.newPsw="",e.fieldLabel="设置新支付密码",void(e.step=0);setTimeout(function(){e.$router.back()},1e3)})}},created:function(){var n=localStorage.getItem("resetVcode");if(!n)return void this.$router.back();this.vcode=n},destroyed:function(){localStorage.removeItem("resetVcode")}}),r=function(){var n=this,e=n.$createElement;return(n._self._c||e)("psw-field",{attrs:{title:"重置支付密码",label:n.fieldLabel,loadingShow:n.loadingShow},on:{iptWord:n.acceptWord}})},c=[],l={render:r,staticRenderFns:c},d=l,u=t("VU/8"),w=s,f=u(a,d,!1,w,"data-v-66a4002c",null);e.default=f.exports},sOK1:function(n,e,t){var s=t("wiLq");"string"==typeof s&&(s=[[n.i,s,""]]),s.locals&&(n.exports=s.locals);t("rjj0")("4633c41f",s,!0,{})},wiLq:function(n,e,t){e=n.exports=t("FZ+f")(!0),e.push([n.i,"\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n","",{version:3,sources:[],names:[],mappings:"",file:"reset.vue",sourceRoot:""}])}});
//# sourceMappingURL=31.js.map?v=ff122108fc6f847fc0e9