webpackJsonp([11,18],{"1jYV":function(e,t,n){t=e.exports=n("FZ+f")(!0),t.push([e.i,"\n.address-submit {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n}\n.address-submit .app-header {\n  border-bottom: 1px solid #e5e7e9;\n  background: #f7f7f7;\n}\n.address-submit .form {\n  padding: 20px 15px;\n}\n.address-submit .form-item {\n  margin-bottom: 15px;\n}\n.address-submit .form-item :-moz-placeholder {\n/* Mozilla Firefox 4 to 18 */\n  color: #959da6;\n}\n.address-submit .form-item ::-moz-placeholder {\n/* Mozilla Firefox 19+ */\n  color: #959da6;\n}\n.address-submit .form-item input:-ms-input-placeholder {\n  color: #959da6;\n}\n.address-submit .form-item input::-webkit-input-placeholder {\n  color: #959da6;\n}\n.address-submit .form-item .label {\n  margin-bottom: 5px;\n  font-weight: bold;\n}\n.address-submit .form-item .label span {\n  font-weight: normal;\n  margin-top: 5px;\n  color: #959da6;\n}\n.address-submit .form-item .label .must {\n  color: #f52e00;\n}\n.address-submit .form-item input {\n  width: 100%;\n  line-height: 42px;\n  border-radius: 10px;\n  box-sizing: border-box;\n  padding: 0 10px;\n  border: 1px solid #c6d0da;\n}\n.address-submit .sbm-btn-box {\n  padding: 25px 0;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/address/submit.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;EACX,eAAe;CAChB;AACD;EACE,iCAAiC;EACjC,oBAAoB;CACrB;AACD;EACE,mBAAmB;CACpB;AACD;EACE,oBAAoB;CACrB;AACD;AACA,6BAA6B;EAC3B,eAAe;CAChB;AACD;AACA,yBAAyB;EACvB,eAAe;CAChB;AACD;EACE,eAAe;CAChB;AACD;EACE,eAAe;CAChB;AACD;EACE,mBAAmB;EACnB,kBAAkB;CACnB;AACD;EACE,oBAAoB;EACpB,gBAAgB;EAChB,eAAe;CAChB;AACD;EACE,eAAe;CAChB;AACD;EACE,YAAY;EACZ,kBAAkB;EAClB,oBAAoB;EACpB,uBAAuB;EACvB,gBAAgB;EAChB,0BAA0B;CAC3B;AACD;EACE,gBAAgB;CACjB",file:"submit.vue",sourcesContent:["\n.address-submit {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n}\n.address-submit .app-header {\n  border-bottom: 1px solid #e5e7e9;\n  background: #f7f7f7;\n}\n.address-submit .form {\n  padding: 20px 15px;\n}\n.address-submit .form-item {\n  margin-bottom: 15px;\n}\n.address-submit .form-item :-moz-placeholder {\n/* Mozilla Firefox 4 to 18 */\n  color: #959da6;\n}\n.address-submit .form-item ::-moz-placeholder {\n/* Mozilla Firefox 19+ */\n  color: #959da6;\n}\n.address-submit .form-item input:-ms-input-placeholder {\n  color: #959da6;\n}\n.address-submit .form-item input::-webkit-input-placeholder {\n  color: #959da6;\n}\n.address-submit .form-item .label {\n  margin-bottom: 5px;\n  font-weight: bold;\n}\n.address-submit .form-item .label span {\n  font-weight: normal;\n  margin-top: 5px;\n  color: #959da6;\n}\n.address-submit .form-item .label .must {\n  color: #f52e00;\n}\n.address-submit .form-item input {\n  width: 100%;\n  line-height: 42px;\n  border-radius: 10px;\n  box-sizing: border-box;\n  padding: 0 10px;\n  border: 1px solid #c6d0da;\n}\n.address-submit .sbm-btn-box {\n  padding: 25px 0;\n}"],sourceRoot:""}])},"5kbj":function(e,t,n){"use strict";function i(e){n("bNJR")}Object.defineProperty(t,"__esModule",{value:!0});var s=n("POHo"),o=n("SQ4B"),a=n("ksDW"),r=(s.a,a.default,{name:"index",components:{slide:s.a,submitTemp:a.default},data:function(){return{loading:!0,submitShow:!1,addressList:[]}},methods:{getAddressList:function(){var e=this;o.a.post("/user/address-list",{},function(t){if(0!==t.code)return e.loading=!1,void e.$vux.toast.show(t.msg);e.addressList=t.content.list,e.loading=!1})}},created:function(){this.getAddressList()},activated:function(){},watch:{$route:function(e,t){"/personal/address"===e.path&&this.getAddressList()}}}),d=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("slide",[n("div",{staticClass:"receive-address"},[n("load-more",{directives:[{name:"show",rawName:"v-show",value:e.loading,expression:"loading"}],staticClass:"load-box",attrs:{tip:"正在加载"}}),e._v(" "),e.loading?e._e():n("div",[e.addressList.length?n("div",{staticClass:"edit"},[n("app-header",[e._v("\n          收货地址\n          "),n("router-link",{attrs:{slot:"right",tag:"span",to:"/personal/address/edit1"},slot:"right"},[e._v("编辑")])],1),e._v(" "),n("div",{staticClass:"h-main"},[n("ul",{staticClass:"address-list"},e._l(e.addressList,function(t){return n("li",[n("p",[e._v("\n                "+e._s(t.consignee)+"\n                "),n("span",[e._v(e._s(t.consigneeMobile))])]),e._v(" "),n("h4",[e._v(e._s(t.address))])])}))])],1):n("div",{staticClass:"add"},[n("app-header",[e._v("\n          收货地址\n        ")]),e._v(" "),n("div",{staticClass:"h-main"},[n("div",{staticClass:"add-content"},[n("img",{attrs:{src:"/static/images/add-address.png",alt:""}}),e._v(" "),n("router-link",{staticClass:"base-btn",attrs:{tag:"button",to:"/personal/address/edit0"}},[e._v("添加收货地址")])],1)])],1)]),e._v(" "),n("router-view")],1)])},l=[],c={render:d,staticRenderFns:l},A=c,m=n("VU/8"),p=i,u=m(r,A,!1,p,null,null);t.default=u.exports},"7oHl":function(e,t,n){t=e.exports=n("FZ+f")(!0),t.push([e.i,"\n.receive-address {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.receive-address .load-box {\n  margin-top: 100px;\n}\n.receive-address .edit .app-header {\n  border-bottom: 1px solid #e5e7e9;\n  background: #f7f7f7;\n}\n.receive-address .edit .address-list li {\n  margin-left: 15px;\n  padding: 15px;\n  padding-left: 0;\n  border-bottom: 1px solid #e5e7e9;\n  font-size: 14px;\n}\n.receive-address .edit .address-list li p {\n  color: #959da6;\n}\n.receive-address .edit .address-list li p span {\n  margin-left: 30px;\n}\n.receive-address .edit .address-list li h4 {\n  margin-top: 5px;\n  font-weight: bold;\n}\n.receive-address .add .add-content {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n  -webkit-flex-direction: column;\n          flex-direction: column;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.receive-address .add .add-content img {\n  margin: 85px 0;\n  width: 154px;\n}\n.receive-address .add .add-content button {\n  width: 290px;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/address/index.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,kBAAkB;CACnB;AACD;EACE,iCAAiC;EACjC,oBAAoB;CACrB;AACD;EACE,kBAAkB;EAClB,cAAc;EACd,gBAAgB;EAChB,iCAAiC;EACjC,gBAAgB;CACjB;AACD;EACE,eAAe;CAChB;AACD;EACE,kBAAkB;CACnB;AACD;EACE,gBAAgB;EAChB,kBAAkB;CACnB;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,6BAA6B;EAC7B,8BAA8B;EAC9B,+BAA+B;UACvB,uBAAuB;EAC/B,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;CAC7B;AACD;EACE,eAAe;EACf,aAAa;CACd;AACD;EACE,aAAa;CACd",file:"index.vue",sourcesContent:["\n.receive-address {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n}\n.receive-address .load-box {\n  margin-top: 100px;\n}\n.receive-address .edit .app-header {\n  border-bottom: 1px solid #e5e7e9;\n  background: #f7f7f7;\n}\n.receive-address .edit .address-list li {\n  margin-left: 15px;\n  padding: 15px;\n  padding-left: 0;\n  border-bottom: 1px solid #e5e7e9;\n  font-size: 14px;\n}\n.receive-address .edit .address-list li p {\n  color: #959da6;\n}\n.receive-address .edit .address-list li p span {\n  margin-left: 30px;\n}\n.receive-address .edit .address-list li h4 {\n  margin-top: 5px;\n  font-weight: bold;\n}\n.receive-address .add .add-content {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n  -webkit-flex-direction: column;\n          flex-direction: column;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.receive-address .add .add-content img {\n  margin: 85px 0;\n  width: 154px;\n}\n.receive-address .add .add-content button {\n  width: 290px;\n}"],sourceRoot:""}])},bNJR:function(e,t,n){var i=n("7oHl");"string"==typeof i&&(i=[[e.i,i,""]]),i.locals&&(e.exports=i.locals);n("rjj0")("46e9305c",i,!0,{})},ksDW:function(e,t,n){"use strict";function i(e){n("mBPg")}Object.defineProperty(t,"__esModule",{value:!0});var s=n("POHo"),o=n("SQ4B"),a=n("M1N4"),r=n("Bfwr"),d=(s.a,a.a,r.a,{name:"index",components:{slide:s.a,sel:a.a,Loading:r.a},data:function(){return{form:{consignee:"",consignee_mobile:"",area_province_id:"",area_city_id:"",address:"",zip_code:""},provinceList:[],cityList:[],btnLoading:!1,show:!0,lock:!1}},methods:{changeProvince:function(e){this.lock=!1,this.form.area_province_id=e.id},changeCity:function(e){this.form.area_city_id=e.id},backPage:function(){this.$emit("close",!0)},getAddressInfo:function(){var e=this;o.a.post("/user/address-info",{},function(t){if(0!==t.code)return void e.$vux.toast.show(t.msg);e.lock=!0,e.form.consignee=t.content.consignee,e.form.consignee_mobile=t.content.consigneeMobile,e.form.area_province_id=t.content.areaProvinceId,e.form.area_city_id=t.content.areaCityId,e.form.address=t.content.address,e.form.zip_code=t.content.zipCode})},getProvinceList:function(){var e=this;o.a.post("/area/area/get-city-list",{},function(t){if(0!==t.code)return void e.$vux.toast.show(t.msg);e.provinceList=t.content,"0"===e.$route.params.type?e.form.area_province_id=e.provinceList[0].id:e.getAddressInfo()})},getCityList:function(){var e=this;o.a.post("/area/area/get-city-list",{id:this.form.area_province_id},function(t){if(0!==t.code)return void e.$vux.toast.show(t.msg);e.cityList=t.content,e.lock||(e.form.area_city_id=e.cityList[0].id)})},submitAddressFrom:function(){return this.form.consignee?this.form.consignee_mobile&&/^1\d{10}$/.test(this.form.consignee_mobile)?this.form.area_province_id&&this.form.area_city_id&&this.form.address?(this.btnLoading=!0,void this.saveAddress()):void this.$vux.toast.show("请完善收货人地址"):void this.$vux.toast.show("请输入有效的电话号码"):void this.$vux.toast.show("请输入收货人姓名")},saveAddress:function(){var e=this;o.a.post("/user/address-save",this.form,function(t){if(e.btnLoading=!1,0!==t.code)return void e.$vux.toast.show(t.msg);e.$vux.toast.show({text:t.msg,type:"success"}),setTimeout(function(){e.$router.back()},1500)})}},created:function(){this.getProvinceList()},watch:{"form.area_province_id":function(e){this.getCityList()},"form.area_city_id":function(e,t){e&&!t&&(this.show=!1)}}}),l=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("slide",[n("div",{staticClass:"address-submit"},[n("loading",{attrs:{show:e.show,text:""}}),e._v(" "),n("app-header",[e._v("\n      收货地址\n    ")]),e._v(" "),n("div",{staticClass:"h-main"},[n("div",{staticClass:"form"},[n("div",{staticClass:"form-item"},[n("div",{staticClass:"label"},[e._v("\n            收货人姓名\n            "),n("span",{staticClass:"must"},[e._v("*")]),e._v(" "),n("br"),e._v(" "),n("span",[e._v("为您邮寄产品")])]),e._v(" "),n("input",{directives:[{name:"model",rawName:"v-model",value:e.form.consignee,expression:"form.consignee"}],attrs:{type:"text",placeholder:"输入收货人姓名"},domProps:{value:e.form.consignee},on:{input:function(t){t.target.composing||e.$set(e.form,"consignee",t.target.value)}}})]),e._v(" "),n("div",{staticClass:"form-item"},[n("div",{staticClass:"label"},[e._v("\n            收货人电话\n            "),n("span",{staticClass:"must"},[e._v("*")])]),e._v(" "),n("input",{directives:[{name:"model",rawName:"v-model",value:e.form.consignee_mobile,expression:"form.consignee_mobile"}],attrs:{type:"text",onkeyup:"(this.v=function(){this.value=this.value.replace(/[^0-9-]+/,'');}).call(this)",onblur:"this.v()",placeholder:"输入收货人电话",maxlength:"11"},domProps:{value:e.form.consignee_mobile},on:{input:function(t){t.target.composing||e.$set(e.form,"consignee_mobile",t.target.value)}}})]),e._v(" "),n("div",{staticClass:"form-item"},[n("div",{staticClass:"label"},[e._v("\n            收货人地址\n            "),n("span",{staticClass:"must"},[e._v("*")])]),e._v(" "),n("sel",{attrs:{dataList:e.provinceList,placeholder:"请选择",value:"id",label:"areaname",select:this.form.area_province_id},on:{changeSel:e.changeProvince}})],1),e._v(" "),n("div",{staticClass:"form-item"},[n("sel",{attrs:{dataList:e.cityList,placeholder:"请选择",value:"id",label:"areaname",select:this.form.area_city_id},on:{changeSel:e.changeCity}})],1),e._v(" "),n("div",{staticClass:"form-item"},[n("input",{directives:[{name:"model",rawName:"v-model",value:e.form.address,expression:"form.address"}],attrs:{type:"text",placeholder:"详细地址"},domProps:{value:e.form.address},on:{input:function(t){t.target.composing||e.$set(e.form,"address",t.target.value)}}})]),e._v(" "),n("div",{staticClass:"form-item"},[n("div",{staticClass:"label"},[e._v("\n            邮编\n          ")]),e._v(" "),n("input",{directives:[{name:"model",rawName:"v-model",value:e.form.zip_code,expression:"form.zip_code"}],attrs:{type:"text",placeholder:"邮编"},domProps:{value:e.form.zip_code},on:{input:function(t){t.target.composing||e.$set(e.form,"zip_code",t.target.value)}}})]),e._v(" "),n("div",{staticClass:"sbm-btn-box"},[n("x-button",{staticClass:"base-btn",attrs:{type:"warn","show-loading":e.btnLoading},nativeOn:{click:function(t){return e.submitAddressFrom(t)}}},[e._v("下一步\n          ")])],1)])])],1)])},c=[],A={render:l,staticRenderFns:c},m=A,p=n("VU/8"),u=i,f=p(d,m,!1,u,null,null);t.default=f.exports},mBPg:function(e,t,n){var i=n("1jYV");"string"==typeof i&&(i=[[e.i,i,""]]),i.locals&&(e.exports=i.locals);n("rjj0")("3f20ae96",i,!0,{})}});
//# sourceMappingURL=11.js.map?v=c8a1d6078d91c6ae3475