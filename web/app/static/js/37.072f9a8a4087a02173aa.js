webpackJsonp([37],{"7sKZ":function(n,e,s){e=n.exports=s("FZ+f")(!0),e.push([n.i,"\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n","",{version:3,sources:[],names:[],mappings:"",file:"revise.vue",sourceRoot:""}])},RJDA:function(n,e,s){"use strict";function t(n){s("ZNcO")}Object.defineProperty(e,"__esModule",{value:!0});var i=s("YMwU"),o=s("SQ4B"),a=(i.a,{name:"reset",components:{pswField:i.a},data:function(){return{newPsw:"",step:0,loadingShow:!1,vcode:"",oldPsw:"",fieldDisabled:!1}},methods:{acceptWord:function(n){switch(this.step){case 0:this.validatePsw(n);break;case 1:this.newPsw=n,this.step=2;break;case 2:this.setNewPsw(n)}},validatePsw:function(n){var e=this;this.loadingShow=!0,this.fieldDisabled=!0,o.a.post("/pay/validate-pass",{pass:n},function(s){return e.fieldDisabled=!1,e.loadingShow=!1,0!==s.code?void e.$vux.toast.show(s.msg):s.content?(e.step=1,void(e.oldPsw=n)):void e.$vux.toast.show("校验失败")})},setNewPsw:function(n){var e=this;if(this.fieldDisabled=!0,this.newPsw!==n)return this.$vux.toast.show("两次密码输入不一致,请重新设置"),this.newPsw="",this.step=0,void(this.fieldDisabled=!1);this.loadingShow=!0,o.a.post("/pay/update-pass",{pass:this.newPsw,repass:n,oldpass:this.oldPsw},function(n){e.loadingShow=!1,e.$vux.toast.show(n.msg),0===n.code&&setTimeout(function(){e.$router.back()},1e3)})}},created:function(){},destroyed:function(){},computed:{fieldLabel:function(){switch(this.step){case 0:return"输入原支付密码";case 1:return"设置新支付密码";case 2:return"确认新支付密码"}}}}),d=function(){var n=this,e=n.$createElement;return(n._self._c||e)("psw-field",{attrs:{title:"修改支付密码",label:n.fieldLabel,loadingShow:n.loadingShow,fieldDisabled:n.fieldDisabled},on:{iptWord:n.acceptWord}})},l=[],c={render:d,staticRenderFns:l},r=c,u=s("VU/8"),w=t,h=u(a,r,!1,w,"data-v-08cd7e48",null);e.default=h.exports},ZNcO:function(n,e,s){var t=s("7sKZ");"string"==typeof t&&(t=[[n.i,t,""]]),t.locals&&(n.exports=t.locals);s("rjj0")("1ff9e22e",t,!0,{})}});
//# sourceMappingURL=37.072f9a8a4087a02173aa.js.map