webpackJsonp([7],{"78BN":function(t,i,n){var e=n("iyAf");"string"==typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);n("rjj0")("50c66be8",e,!0,{})},KVry:function(t,i,n){i=t.exports=n("FZ+f")(!0),i.push([t.i,"\n.identify-submit {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n}\n.identify-submit .identify-submit-content {\n  padding: 30px 20px;\n}\n.identify-submit .identify-submit-content .form {\n  padding: 0 35px;\n}\n.identify-submit .identify-submit-content .form-item {\n  margin-bottom: 20px;\n}\n.identify-submit .identify-submit-content .label {\n  margin-bottom: 8px;\n}\n.identify-submit .identify-submit-content .text-ipt {\n  width: 100%;\n  height: 35px;\n  line-height: 35px;\n  box-sizing: border-box;\n  padding: 5px;\n  border: 1px solid #e5e7e9;\n}\n.identify-submit .identify-submit-content .upload-box {\n  position: relative;\n  overflow: hidden;\n  border-radius: 3px;\n}\n.identify-submit .identify-submit-content .upload-box .default-img {\n  width: 100%;\n}\n.identify-submit .identify-submit-content .upload-box .show-img {\n  background-size: cover;\n  background-position: center;\n  position: absolute;\n  top: 0;\n  bottom: 0;\n  width: 100%;\n}\n.identify-submit .identify-submit-content .upload-front {\n  top: 5px;\n}\n.identify-submit .identify-submit-content .claim-list {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  margin: 20px 0;\n}\n.identify-submit .identify-submit-content .claim-list li {\n  width: 20%;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n  -webkit-flex-direction: column;\n          flex-direction: column;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.identify-submit .identify-submit-content .claim-list li img {\n  width: 100%;\n}\n.identify-submit .identify-submit-content .claim-list li span {\n  margin-top: 10px;\n  margin-right: 5px;\n  font-size: 12px;\n}\n.identify-submit .identify-submit-content .btn-box {\n  padding-top: 10px;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/identify/submit.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;EACX,eAAe;CAChB;AACD;EACE,mBAAmB;CACpB;AACD;EACE,gBAAgB;CACjB;AACD;EACE,oBAAoB;CACrB;AACD;EACE,mBAAmB;CACpB;AACD;EACE,YAAY;EACZ,aAAa;EACb,kBAAkB;EAClB,uBAAuB;EACvB,aAAa;EACb,0BAA0B;CAC3B;AACD;EACE,mBAAmB;EACnB,iBAAiB;EACjB,mBAAmB;CACpB;AACD;EACE,YAAY;CACb;AACD;EACE,uBAAuB;EACvB,4BAA4B;EAC5B,mBAAmB;EACnB,OAAO;EACP,UAAU;EACV,YAAY;CACb;AACD;EACE,SAAS;CACV;AACD;EACE,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;EACvC,eAAe;CAChB;AACD;EACE,WAAW;EACX,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,6BAA6B;EAC7B,8BAA8B;EAC9B,+BAA+B;UACvB,uBAAuB;EAC/B,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;CAC7B;AACD;EACE,YAAY;CACb;AACD;EACE,iBAAiB;EACjB,kBAAkB;EAClB,gBAAgB;CACjB;AACD;EACE,kBAAkB;CACnB",file:"submit.vue",sourcesContent:["\n.identify-submit {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  overflow: auto;\n}\n.identify-submit .identify-submit-content {\n  padding: 30px 20px;\n}\n.identify-submit .identify-submit-content .form {\n  padding: 0 35px;\n}\n.identify-submit .identify-submit-content .form-item {\n  margin-bottom: 20px;\n}\n.identify-submit .identify-submit-content .label {\n  margin-bottom: 8px;\n}\n.identify-submit .identify-submit-content .text-ipt {\n  width: 100%;\n  height: 35px;\n  line-height: 35px;\n  box-sizing: border-box;\n  padding: 5px;\n  border: 1px solid #e5e7e9;\n}\n.identify-submit .identify-submit-content .upload-box {\n  position: relative;\n  overflow: hidden;\n  border-radius: 3px;\n}\n.identify-submit .identify-submit-content .upload-box .default-img {\n  width: 100%;\n}\n.identify-submit .identify-submit-content .upload-box .show-img {\n  background-size: cover;\n  background-position: center;\n  position: absolute;\n  top: 0;\n  bottom: 0;\n  width: 100%;\n}\n.identify-submit .identify-submit-content .upload-front {\n  top: 5px;\n}\n.identify-submit .identify-submit-content .claim-list {\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n  margin: 20px 0;\n}\n.identify-submit .identify-submit-content .claim-list li {\n  width: 20%;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n  -webkit-flex-direction: column;\n          flex-direction: column;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n}\n.identify-submit .identify-submit-content .claim-list li img {\n  width: 100%;\n}\n.identify-submit .identify-submit-content .claim-list li span {\n  margin-top: 10px;\n  margin-right: 5px;\n  font-size: 12px;\n}\n.identify-submit .identify-submit-content .btn-box {\n  padding-top: 10px;\n}"],sourceRoot:""}])},NfG1:function(t,i,n){i=t.exports=n("FZ+f")(!0),i.push([t.i,"\n.upload-img {\n  width: 100%;\n  height: 100%;\n}\n.upload-img .file-ipt {\n  position: absolute;\n  top: 0;\n  bottom: 0;\n  width: 100%;\n  opacity: 0;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/components/uploadImg/index.vue"],names:[],mappings:";AACA;EACE,YAAY;EACZ,aAAa;CACd;AACD;EACE,mBAAmB;EACnB,OAAO;EACP,UAAU;EACV,YAAY;EACZ,WAAW;CACZ",file:"index.vue",sourcesContent:["\n.upload-img {\n  width: 100%;\n  height: 100%;\n}\n.upload-img .file-ipt {\n  position: absolute;\n  top: 0;\n  bottom: 0;\n  width: 100%;\n  opacity: 0;\n}"],sourceRoot:""}])},dWZw:function(t,i,n){"use strict";function e(t){n("qkb7")}function s(t){n("78BN")}function o(t){n("nNnh")}Object.defineProperty(i,"__esModule",{value:!0});var a=n("mvHQ"),A=n.n(a),d=n("Dd8w"),r=n.n(d),c=n("POHo"),m=n("SQ4B"),u=n("mtWM"),l=n.n(u),p=n("Bfwr"),b=n("8Pnd"),f=(p.a,String,b.a.url,String,{name:"index",components:{Loading:p.a},props:{url:{type:String,default:b.a.url+"/upload/upload/image"},fileName:{type:String,default:"image_file"}},data:function(){return{show:!1,accept:"image/gif, image/jpeg, image/png, image/jpg"}},methods:{getFileUrl:function(t){return window.URL.createObjectURL(t.files.item(0)),window.URL.createObjectURL(t.files.item(0))},add_img:function(t){var i=this,n=t.target.files[0];if(-1===this.accept.indexOf(n.type))return void this.$vux.toast.show("请选择我们支持的图片格式");if(n.size>3145728)return void this.$vux.toast.show("请选择3M以内的图片");this.show=!0;var e=new FormData;e.append(this.fileName,n,n.name),e.append("type","identify");var s={headers:{"Content-Type":"multipart/form-data"}};l.a.post(this.url,e,s).then(function(n){if(i.show=!1,0!==n.data.code)return void i.$vux.toast.show(n.data.msg);i.$emit("success",n.data.content,i.getFileUrl(t.srcElement))})}}}),g=function(){var t=this,i=t.$createElement,n=t._self._c||i;return n("div",{staticClass:"upload-img"},[n("loading",{attrs:{show:t.show}}),t._v(" "),n("input",{staticClass:"file-ipt",attrs:{type:"file"},on:{change:t.add_img}})],1)},C=[],y={render:g,staticRenderFns:C},v=y,h=n("VU/8"),B=e,E=h(f,v,!1,B,null,null),x=E.exports,w=n("NYxO"),k={name:"divider"},I=function(){var t=this,i=t.$createElement;return(t._self._c||i)("p",{staticClass:"vux-divider"},[t._t("default")],2)},M=[],j={render:I,staticRenderFns:M},D=j,Y=n("VU/8"),U=s,F=Y(k,D,!1,U,null,null),Z=F.exports,N=["√标准","×五官遮挡","×未手持","×人物模糊"],R=(c.a,r()({getIdentifyMsg:function(){var t=this;m.a.post("/identify",{},function(i){if(console.log(i),0!==i.code)return void t.$vux.toast.show(i.msg);var n={};""===i.content?n.isIdentify=!1:(n=i.content,n.isIdentify=!0),sessionStorage.setItem("identifyMsg",A()(n)),t.setIdentifyMsg(n);var e=t.switchPath(n);t.$router.replace({path:"/personal/identify/"+e})})},switchPath:function(t){if(!t.isIdentify)return"submit";switch(t.status){case 0:return"wait";case 1:return"success";case 2:return"fail"}},handleSuccessFront:function(t,i){this.frontImg=i,this.identifyForm.pic_front=t},handleSuccessBack:function(t,i){this.backImg=i,this.identifyForm.pic_back=t},submitIdentify:function(){var t=this,i=this.clickFrom();if(i)return void this.$vux.toast.show(i);m.a.post("/identify/submit",this.identifyForm,function(i){t.$vux.toast.show(i.msg),0===i.code&&t.getIdentifyMsg()})},clickFrom:function(){return this.identifyForm.realname?this.identifyForm.number?this.identifyForm.pic_front?this.identifyForm.pic_back?"":"请上传身份证国徽页":"请上传身份证人像页":"请输入身份证号":"请输入姓名"}},Object(w.c)({setIdentifyMsg:"IDENTIFY_MSG"})),["√标准","×五官遮挡","×未手持","×人物模糊"]),S={name:"index",components:{slide:c.a,uploadImg:x,Divider:Z},data:function(){return{identifyForm:{realname:"",number:""},frontImg:"",backImg:"",claimList:R}},methods:r()({getIdentifyMsg:function(){var t=this;m.a.post("/identify",{},function(i){if(console.log(i),0!==i.code)return void t.$vux.toast.show(i.msg);var n={};""===i.content?n.isIdentify=!1:(n=i.content,n.isIdentify=!0),sessionStorage.setItem("identifyMsg",A()(n)),t.setIdentifyMsg(n);var e=t.switchPath(n);t.$router.replace({path:"/personal/identify/"+e})})},switchPath:function(t){if(!t.isIdentify)return"submit";switch(t.status){case 0:return"wait";case 1:return"success";case 2:return"fail"}},handleSuccessFront:function(t,i){this.frontImg=i,this.identifyForm.pic_front=t},handleSuccessBack:function(t,i){this.backImg=i,this.identifyForm.pic_back=t},submitIdentify:function(){var t=this,i=this.clickFrom();if(i)return void this.$vux.toast.show(i);m.a.post("/identify/submit",this.identifyForm,function(i){t.$vux.toast.show(i.msg),0===i.code&&t.getIdentifyMsg()})},clickFrom:function(){return this.identifyForm.realname?this.identifyForm.number?this.identifyForm.pic_front?this.identifyForm.pic_back?"":"请上传身份证国徽页":"请上传身份证人像页":"请输入身份证号":"请输入姓名"}},Object(w.c)({setIdentifyMsg:"IDENTIFY_MSG"}))},G=function(){var t=this,i=t.$createElement,n=t._self._c||i;return n("slide",[n("div",{staticClass:"identify-submit"},[n("app-header",[t._v("\n      实名认证\n    ")]),t._v(" "),n("div",{staticClass:"h-main"},[n("div",{staticClass:"identify-submit-content"},[n("div",{staticClass:"form"},[n("div",{staticClass:"form-item"},[n("div",{staticClass:"label"},[t._v("姓名")]),t._v(" "),n("input",{directives:[{name:"model",rawName:"v-model",value:t.identifyForm.realname,expression:"identifyForm.realname"}],staticClass:"text-ipt",attrs:{type:"text"},domProps:{value:t.identifyForm.realname},on:{input:function(i){i.target.composing||t.$set(t.identifyForm,"realname",i.target.value)}}})]),t._v(" "),n("div",{staticClass:"form-item"},[n("div",{staticClass:"label"},[t._v("身份证号")]),t._v(" "),n("input",{directives:[{name:"model",rawName:"v-model",value:t.identifyForm.number,expression:"identifyForm.number"}],staticClass:"text-ipt",attrs:{type:"text"},domProps:{value:t.identifyForm.number},on:{input:function(i){i.target.composing||t.$set(t.identifyForm,"number",i.target.value)}}})]),t._v(" "),n("div",{staticClass:"form-item"},[n("div",{staticClass:"label"},[t._v("请上传手持身份证照片")]),t._v(" "),n("div",{staticClass:"upload-box upload-front"},[n("img",{staticClass:"default-img",attrs:{src:"/static/images/identify/zj_front.png",alt:""}}),t._v(" "),n("div",{directives:[{name:"show",rawName:"v-show",value:!!t.frontImg,expression:"!!frontImg"}],staticClass:"show-img",style:{backgroundImage:"url("+t.frontImg+")"}}),t._v(" "),n("upload-img",{on:{success:t.handleSuccessFront}})],1)]),t._v(" "),n("div",{staticClass:"form-item"},[n("div",{staticClass:"upload-box"},[n("img",{staticClass:"default-img",attrs:{src:"/static/images/identify/zj_back.png",alt:""}}),t._v(" "),n("div",{directives:[{name:"show",rawName:"v-show",value:!!t.backImg,expression:"!!backImg"}],staticClass:"show-img",style:{backgroundImage:"url("+t.backImg+")"}}),t._v(" "),n("upload-img",{on:{success:t.handleSuccessBack}})],1)])]),t._v(" "),n("div",{staticClass:"claim"},[n("divider",[t._v("照片拍摄要求")]),t._v(" "),n("ul",{staticClass:"claim-list"},t._l(t.claimList,function(i,e){return n("li",{key:e},[n("img",{attrs:{src:"/static/images/identify/claim_"+e+".png",alt:""}}),t._v(" "),n("span",[t._v(t._s(i))])])}))],1),t._v(" "),n("div",{staticClass:"btn-box"},[n("x-button",{staticClass:"again-btn",attrs:{type:"warn"},nativeOn:{click:function(i){return t.submitIdentify(i)}}},[t._v("下一步")])],1)])])],1)])},J=[],z={render:G,staticRenderFns:J},W=z,O=n("VU/8"),_=o,T=O(S,W,!1,_,null,null);i.default=T.exports},iyAf:function(t,i,n){i=t.exports=n("FZ+f")(!0),i.push([t.i,"\n.vux-divider {\n  display: table;\n  white-space: nowrap;\n  height: auto;\n  overflow: hidden;\n  line-height: 1;\n  text-align: center;\n  padding: 10px 0;\n  color: #666;\n}\n.vux-divider:after,.vux-divider:before {\n  content: '';\n  display: table-cell;\n  position: relative;\n  top: 50%;\n  width: 50%;\n  background-repeat: no-repeat;\n  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABaAAAAACCAYAAACuTHuKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDE0IDc5LjE1MTQ4MSwgMjAxMy8wMy8xMy0xMjowOToxNSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo1OThBRDY4OUNDMTYxMUU0OUE3NUVGOEJDMzMzMjE2NyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo1OThBRDY4QUNDMTYxMUU0OUE3NUVGOEJDMzMzMjE2NyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjU5OEFENjg3Q0MxNjExRTQ5QTc1RUY4QkMzMzMyMTY3IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjU5OEFENjg4Q0MxNjExRTQ5QTc1RUY4QkMzMzMyMTY3Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+VU513gAAADVJREFUeNrs0DENACAQBDBIWLGBJQby/mUcJn5sJXQmOQMAAAAAAJqt+2prAAAAAACg2xdgANk6BEVuJgyMAAAAAElFTkSuQmCC)\n}\n.vux-divider:before {\n  background-position: right 1em top 50%\n}\n.vux-divider:after {\n  background-position: left 1em top 50%\n}\n","",{version:3,sources:["F:/contest_system/web/app/contest-app/node_modules/vux/src/components/divider/index.vue"],names:[],mappings:";AACA;EACE,eAAe;EACf,oBAAoB;EACpB,aAAa;EACb,iBAAiB;EACjB,eAAe;EACf,mBAAmB;EACnB,gBAAgB;EAChB,YAAY;CACb;AACD;EACE,YAAY;EACZ,oBAAoB;EACpB,mBAAmB;EACnB,SAAS;EACT,WAAW;EACX,6BAA6B;EAC7B,6yCAA6yC;CAC9yC;AACD;EACE,sCAAsC;CACvC;AACD;EACE,qCAAqC;CACtC",file:"index.vue",sourcesContent:["\n.vux-divider {\n  display: table;\n  white-space: nowrap;\n  height: auto;\n  overflow: hidden;\n  line-height: 1;\n  text-align: center;\n  padding: 10px 0;\n  color: #666;\n}\n.vux-divider:after,.vux-divider:before {\n  content: '';\n  display: table-cell;\n  position: relative;\n  top: 50%;\n  width: 50%;\n  background-repeat: no-repeat;\n  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABaAAAAACCAYAAACuTHuKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDE0IDc5LjE1MTQ4MSwgMjAxMy8wMy8xMy0xMjowOToxNSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo1OThBRDY4OUNDMTYxMUU0OUE3NUVGOEJDMzMzMjE2NyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo1OThBRDY4QUNDMTYxMUU0OUE3NUVGOEJDMzMzMjE2NyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjU5OEFENjg3Q0MxNjExRTQ5QTc1RUY4QkMzMzMyMTY3IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjU5OEFENjg4Q0MxNjExRTQ5QTc1RUY4QkMzMzMyMTY3Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+VU513gAAADVJREFUeNrs0DENACAQBDBIWLGBJQby/mUcJn5sJXQmOQMAAAAAAJqt+2prAAAAAACg2xdgANk6BEVuJgyMAAAAAElFTkSuQmCC)\n}\n.vux-divider:before {\n  background-position: right 1em top 50%\n}\n.vux-divider:after {\n  background-position: left 1em top 50%\n}\n"],sourceRoot:""}])},nNnh:function(t,i,n){var e=n("KVry");"string"==typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);n("rjj0")("60bd152c",e,!0,{})},qkb7:function(t,i,n){var e=n("NfG1");"string"==typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);n("rjj0")("29ecbeae",e,!0,{})}});
//# sourceMappingURL=7.js.map?v=9d6373179238ca17f3aa