webpackJsonp([5],{"0k+n":function(t,e,n){function o(t,e){this.typeNumber=t,this.errorCorrectLevel=e,this.modules=null,this.moduleCount=0,this.dataCache=null,this.dataList=[]}var r=n("T9ab"),i=n("rXbU"),a=n("u5CL"),s=n("HOPj"),c=n("PjAo"),u=o.prototype;u.addData=function(t){var e=new r(t);this.dataList.push(e),this.dataCache=null},u.isDark=function(t,e){if(t<0||this.moduleCount<=t||e<0||this.moduleCount<=e)throw new Error(t+","+e);return this.modules[t][e]},u.getModuleCount=function(){return this.moduleCount},u.make=function(){if(this.typeNumber<1){var t=1;for(t=1;t<40;t++){for(var e=i.getRSBlocks(t,this.errorCorrectLevel),n=new a,o=0,r=0;r<e.length;r++)o+=e[r].dataCount;for(var r=0;r<this.dataList.length;r++){var c=this.dataList[r];n.put(c.mode,4),n.put(c.getLength(),s.getLengthInBits(c.mode,t)),c.write(n)}if(n.getLengthInBits()<=8*o)break}this.typeNumber=t}this.makeImpl(!1,this.getBestMaskPattern())},u.makeImpl=function(t,e){this.moduleCount=4*this.typeNumber+17,this.modules=new Array(this.moduleCount);for(var n=0;n<this.moduleCount;n++){this.modules[n]=new Array(this.moduleCount);for(var r=0;r<this.moduleCount;r++)this.modules[n][r]=null}this.setupPositionProbePattern(0,0),this.setupPositionProbePattern(this.moduleCount-7,0),this.setupPositionProbePattern(0,this.moduleCount-7),this.setupPositionAdjustPattern(),this.setupTimingPattern(),this.setupTypeInfo(t,e),this.typeNumber>=7&&this.setupTypeNumber(t),null==this.dataCache&&(this.dataCache=o.createData(this.typeNumber,this.errorCorrectLevel,this.dataList)),this.mapData(this.dataCache,e)},u.setupPositionProbePattern=function(t,e){for(var n=-1;n<=7;n++)if(!(t+n<=-1||this.moduleCount<=t+n))for(var o=-1;o<=7;o++)e+o<=-1||this.moduleCount<=e+o||(this.modules[t+n][e+o]=0<=n&&n<=6&&(0==o||6==o)||0<=o&&o<=6&&(0==n||6==n)||2<=n&&n<=4&&2<=o&&o<=4)},u.getBestMaskPattern=function(){for(var t=0,e=0,n=0;n<8;n++){this.makeImpl(!0,n);var o=s.getLostPoint(this);(0==n||t>o)&&(t=o,e=n)}return e},u.createMovieClip=function(t,e,n){var o=t.createEmptyMovieClip(e,n);this.make();for(var r=0;r<this.modules.length;r++)for(var i=1*r,a=0;a<this.modules[r].length;a++){var s=1*a,c=this.modules[r][a];c&&(o.beginFill(0,100),o.moveTo(s,i),o.lineTo(s+1,i),o.lineTo(s+1,i+1),o.lineTo(s,i+1),o.endFill())}return o},u.setupTimingPattern=function(){for(var t=8;t<this.moduleCount-8;t++)null==this.modules[t][6]&&(this.modules[t][6]=t%2==0);for(var e=8;e<this.moduleCount-8;e++)null==this.modules[6][e]&&(this.modules[6][e]=e%2==0)},u.setupPositionAdjustPattern=function(){for(var t=s.getPatternPosition(this.typeNumber),e=0;e<t.length;e++)for(var n=0;n<t.length;n++){var o=t[e],r=t[n];if(null==this.modules[o][r])for(var i=-2;i<=2;i++)for(var a=-2;a<=2;a++)this.modules[o+i][r+a]=-2==i||2==i||-2==a||2==a||0==i&&0==a}},u.setupTypeNumber=function(t){for(var e=s.getBCHTypeNumber(this.typeNumber),n=0;n<18;n++){var o=!t&&1==(e>>n&1);this.modules[Math.floor(n/3)][n%3+this.moduleCount-8-3]=o}for(var n=0;n<18;n++){var o=!t&&1==(e>>n&1);this.modules[n%3+this.moduleCount-8-3][Math.floor(n/3)]=o}},u.setupTypeInfo=function(t,e){for(var n=this.errorCorrectLevel<<3|e,o=s.getBCHTypeInfo(n),r=0;r<15;r++){var i=!t&&1==(o>>r&1);r<6?this.modules[r][8]=i:r<8?this.modules[r+1][8]=i:this.modules[this.moduleCount-15+r][8]=i}for(var r=0;r<15;r++){var i=!t&&1==(o>>r&1);r<8?this.modules[8][this.moduleCount-r-1]=i:r<9?this.modules[8][15-r-1+1]=i:this.modules[8][15-r-1]=i}this.modules[this.moduleCount-8][8]=!t},u.mapData=function(t,e){for(var n=-1,o=this.moduleCount-1,r=7,i=0,a=this.moduleCount-1;a>0;a-=2)for(6==a&&a--;;){for(var c=0;c<2;c++)if(null==this.modules[o][a-c]){var u=!1;i<t.length&&(u=1==(t[i]>>>r&1));var l=s.getMask(e,o,a-c);l&&(u=!u),this.modules[o][a-c]=u,r--,-1==r&&(i++,r=7)}if((o+=n)<0||this.moduleCount<=o){o-=n,n=-n;break}}},o.PAD0=236,o.PAD1=17,o.createData=function(t,e,n){for(var r=i.getRSBlocks(t,e),c=new a,u=0;u<n.length;u++){var l=n[u];c.put(l.mode,4),c.put(l.getLength(),s.getLengthInBits(l.mode,t)),l.write(c)}for(var d=0,u=0;u<r.length;u++)d+=r[u].dataCount;if(c.getLengthInBits()>8*d)throw new Error("code length overflow. ("+c.getLengthInBits()+">"+8*d+")");for(c.getLengthInBits()+4<=8*d&&c.put(0,4);c.getLengthInBits()%8!=0;)c.putBit(!1);for(;;){if(c.getLengthInBits()>=8*d)break;if(c.put(o.PAD0,8),c.getLengthInBits()>=8*d)break;c.put(o.PAD1,8)}return o.createBytes(c,r)},o.createBytes=function(t,e){for(var n=0,o=0,r=0,i=new Array(e.length),a=new Array(e.length),u=0;u<e.length;u++){var l=e[u].dataCount,d=e[u].totalCount-l;o=Math.max(o,l),r=Math.max(r,d),i[u]=new Array(l);for(var f=0;f<i[u].length;f++)i[u][f]=255&t.buffer[f+n];n+=l;var h=s.getErrorCorrectPolynomial(d),m=new c(i[u],h.getLength()-1),A=m.mod(h);a[u]=new Array(h.getLength()-1);for(var f=0;f<a[u].length;f++){var g=f+A.getLength()-a[u].length;a[u][f]=g>=0?A.get(g):0}}for(var p=0,f=0;f<e.length;f++)p+=e[f].totalCount;for(var C=new Array(p),v=0,f=0;f<o;f++)for(var u=0;u<e.length;u++)f<i[u].length&&(C[v++]=i[u][f]);for(var f=0;f<r;f++)for(var u=0;u<e.length;u++)f<a[u].length&&(C[v++]=a[u][f]);return C},t.exports=o},"9Oz4":function(t,e,n){var o=n("pCvh");"string"==typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);n("rjj0")("b4e60ec6",o,!0,{})},"Bj/7":function(t,e,n){function o(t,e,n){if(!t&&!e&&!n)throw new Error("Missing required arguments");if(!s.string(e))throw new TypeError("Second argument must be a String");if(!s.fn(n))throw new TypeError("Third argument must be a Function");if(s.node(t))return r(t,e,n);if(s.nodeList(t))return i(t,e,n);if(s.string(t))return a(t,e,n);throw new TypeError("First argument must be a String, HTMLElement, HTMLCollection, or NodeList")}function r(t,e,n){return t.addEventListener(e,n),{destroy:function(){t.removeEventListener(e,n)}}}function i(t,e,n){return Array.prototype.forEach.call(t,function(t){t.addEventListener(e,n)}),{destroy:function(){Array.prototype.forEach.call(t,function(t){t.removeEventListener(e,n)})}}}function a(t,e,n){return c(document.body,t,e,n)}var s=n("iDEd"),c=n("ZE5A");t.exports=o},GjcV:function(t,e,n){"use strict";function o(t){n("9Oz4")}Object.defineProperty(e,"__esModule",{value:!0});var r=n("POHo"),i=n("SQ4B"),a=n("wpcj"),s=n("V33R"),c=n.n(s),u=n("/kga"),l=(n("8Pnd"),r.a,a.a,u.a,{name:"index",components:{slide:r.a,Qrcode:a.a,XDialog:u.a},data:function(){return{rcmdCode:"",isAddCode:!0,show:!1,code:"",btnLoading:!1}},methods:{getRcmdCode:function(){var t=this;i.a.post("/user/recommend-code",{},function(e){if(0!==e.code)return void t.$vux.toast.show(e.msg);t.rcmdCode=e.content.code,t.isAddCode=e.content.reCode})},submitCode:function(){var t=this;if(!this.code)return void this.$vux.toast.show("请输入或粘贴介绍人邀请码");this.btnLoading=!0,i.a.post("/user/add-recommend",{re_code:this.code},function(e){t.btnLoading=!1,0!==e.code?t.$vux.toast.show(e.msg):t.show=!1,t.code="",t.isAddCode=!0})}},computed:{qrcodeData:function(){return window.location.href.split("#")[0]+"#/login?re_code="+this.rcmdCode}},created:function(){this.getRcmdCode()},mounted:function(){var t=this,e=new c.a("#copy");e.on("success",function(e){t.$vux.toast.show("复制成功")}),e.on("error",function(e){t.$vux.toast.show("复制失败，你可以选择手动复制")})}}),d=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("slide",[n("div",{staticClass:"recommend"},[n("app-header",[t._v("\n      我的推荐\n      "),n("router-link",{attrs:{slot:"right",tag:"span",to:"/personal/rcmd/record"},slot:"right"},[t._v("推荐记录")])],1),t._v(" "),n("div",{staticClass:"recommend-content"},[t.isAddCode?t._e():n("div",{staticClass:"hint"},[n("span",[t._v("你还没有填写介绍人邀请码,")]),t._v(" "),n("span",{staticClass:"add-code",on:{click:function(e){t.show=!0}}},[t._v("填写邀请码")])]),t._v(" "),n("div",{staticClass:"recommend-box"},[n("div",{staticClass:"top"},[n("h5",[t._v("您的邀请码")]),t._v(" "),n("h1",[t._v(t._s(t.rcmdCode))]),t._v(" "),n("button",{attrs:{id:"copy","data-clipboard-text":t.rcmdCode}},[t._v("复制")])]),t._v(" "),n("div",{staticClass:"center"},[n("div",{staticClass:"circle"}),t._v(" "),n("div",{staticClass:"line"}),t._v(" "),n("div",{staticClass:"circle"})]),t._v(" "),n("div",{staticClass:"bottom"},[n("qrcode",{attrs:{value:t.qrcodeData,type:"img",size:125}}),t._v(" "),n("p",[t._v("\n            长按识别或扫描二维码\n            "),n("br"),t._v("\n            加入节点享受节点权益\n          ")])],1)])]),t._v(" "),n("x-dialog",{staticClass:"add-code-dialog",model:{value:t.show,callback:function(e){t.show=e},expression:"show"}},[n("div",{staticClass:"title"},[t._v("\n        填写邀请码\n        "),n("span",{staticClass:"icon-close close",on:{click:function(e){t.show=!1}}})]),t._v(" "),n("div",{staticClass:"ipt-box"},[n("input",{directives:[{name:"model",rawName:"v-model",value:t.code,expression:"code"}],staticClass:"vcode-ipt",attrs:{type:"text",placeholder:"输入或粘贴介绍人邀请码"},domProps:{value:t.code},on:{input:function(e){e.target.composing||(t.code=e.target.value)}}})]),t._v(" "),n("x-button",{staticClass:"reset-btn",attrs:{type:"warn","show-loading":t.btnLoading},nativeOn:{click:function(e){return t.submitCode(e)}}},[t._v("提交")])],1),t._v(" "),n("router-view"),t._v(" "),n("router-view")],1)])},f=[],h={render:d,staticRenderFns:f},m=h,A=n("VU/8"),g=o,p=A(l,m,!1,g,null,null);e.default=p.exports},HOPj:function(t,e,n){var o=n("c1w4"),r=n("PjAo"),i=n("tzRw"),a={PATTERN000:0,PATTERN001:1,PATTERN010:2,PATTERN011:3,PATTERN100:4,PATTERN101:5,PATTERN110:6,PATTERN111:7},s={PATTERN_POSITION_TABLE:[[],[6,18],[6,22],[6,26],[6,30],[6,34],[6,22,38],[6,24,42],[6,26,46],[6,28,50],[6,30,54],[6,32,58],[6,34,62],[6,26,46,66],[6,26,48,70],[6,26,50,74],[6,30,54,78],[6,30,56,82],[6,30,58,86],[6,34,62,90],[6,28,50,72,94],[6,26,50,74,98],[6,30,54,78,102],[6,28,54,80,106],[6,32,58,84,110],[6,30,58,86,114],[6,34,62,90,118],[6,26,50,74,98,122],[6,30,54,78,102,126],[6,26,52,78,104,130],[6,30,56,82,108,134],[6,34,60,86,112,138],[6,30,58,86,114,142],[6,34,62,90,118,146],[6,30,54,78,102,126,150],[6,24,50,76,102,128,154],[6,28,54,80,106,132,158],[6,32,58,84,110,136,162],[6,26,54,82,110,138,166],[6,30,58,86,114,142,170]],G15:1335,G18:7973,G15_MASK:21522,getBCHTypeInfo:function(t){for(var e=t<<10;s.getBCHDigit(e)-s.getBCHDigit(s.G15)>=0;)e^=s.G15<<s.getBCHDigit(e)-s.getBCHDigit(s.G15);return(t<<10|e)^s.G15_MASK},getBCHTypeNumber:function(t){for(var e=t<<12;s.getBCHDigit(e)-s.getBCHDigit(s.G18)>=0;)e^=s.G18<<s.getBCHDigit(e)-s.getBCHDigit(s.G18);return t<<12|e},getBCHDigit:function(t){for(var e=0;0!=t;)e++,t>>>=1;return e},getPatternPosition:function(t){return s.PATTERN_POSITION_TABLE[t-1]},getMask:function(t,e,n){switch(t){case a.PATTERN000:return(e+n)%2==0;case a.PATTERN001:return e%2==0;case a.PATTERN010:return n%3==0;case a.PATTERN011:return(e+n)%3==0;case a.PATTERN100:return(Math.floor(e/2)+Math.floor(n/3))%2==0;case a.PATTERN101:return e*n%2+e*n%3==0;case a.PATTERN110:return(e*n%2+e*n%3)%2==0;case a.PATTERN111:return(e*n%3+(e+n)%2)%2==0;default:throw new Error("bad maskPattern:"+t)}},getErrorCorrectPolynomial:function(t){for(var e=new r([1],0),n=0;n<t;n++)e=e.multiply(new r([1,i.gexp(n)],0));return e},getLengthInBits:function(t,e){if(1<=e&&e<10)switch(t){case o.MODE_NUMBER:return 10;case o.MODE_ALPHA_NUM:return 9;case o.MODE_8BIT_BYTE:case o.MODE_KANJI:return 8;default:throw new Error("mode:"+t)}else if(e<27)switch(t){case o.MODE_NUMBER:return 12;case o.MODE_ALPHA_NUM:return 11;case o.MODE_8BIT_BYTE:return 16;case o.MODE_KANJI:return 10;default:throw new Error("mode:"+t)}else{if(!(e<41))throw new Error("type:"+e);switch(t){case o.MODE_NUMBER:return 14;case o.MODE_ALPHA_NUM:return 13;case o.MODE_8BIT_BYTE:return 16;case o.MODE_KANJI:return 12;default:throw new Error("mode:"+t)}}},getLostPoint:function(t){for(var e=t.getModuleCount(),n=0,o=0;o<e;o++)for(var r=0;r<e;r++){for(var i=0,a=t.isDark(o,r),s=-1;s<=1;s++)if(!(o+s<0||e<=o+s))for(var c=-1;c<=1;c++)r+c<0||e<=r+c||0==s&&0==c||a==t.isDark(o+s,r+c)&&i++;i>5&&(n+=3+i-5)}for(var o=0;o<e-1;o++)for(var r=0;r<e-1;r++){var u=0;t.isDark(o,r)&&u++,t.isDark(o+1,r)&&u++,t.isDark(o,r+1)&&u++,t.isDark(o+1,r+1)&&u++,0!=u&&4!=u||(n+=3)}for(var o=0;o<e;o++)for(var r=0;r<e-6;r++)t.isDark(o,r)&&!t.isDark(o,r+1)&&t.isDark(o,r+2)&&t.isDark(o,r+3)&&t.isDark(o,r+4)&&!t.isDark(o,r+5)&&t.isDark(o,r+6)&&(n+=40);for(var r=0;r<e;r++)for(var o=0;o<e-6;o++)t.isDark(o,r)&&!t.isDark(o+1,r)&&t.isDark(o+2,r)&&t.isDark(o+3,r)&&t.isDark(o+4,r)&&!t.isDark(o+5,r)&&t.isDark(o+6,r)&&(n+=40);for(var l=0,r=0;r<e;r++)for(var o=0;o<e;o++)t.isDark(o,r)&&l++;return n+=Math.abs(100*l/e/e-50)/5*10}};t.exports=s},Jssu:function(t,e){function n(t,e){for(;t&&t.nodeType!==o;){if("function"==typeof t.matches&&t.matches(e))return t;t=t.parentNode}}var o=9;if("undefined"!=typeof Element&&!Element.prototype.matches){var r=Element.prototype;r.matches=r.matchesSelector||r.mozMatchesSelector||r.msMatchesSelector||r.oMatchesSelector||r.webkitMatchesSelector}t.exports=n},"LF/X":function(t,e,n){var o,r,i;!function(a,s){r=[t,n("SPM9")],o=s,void 0!==(i="function"==typeof o?o.apply(e,r):o)&&(t.exports=i)}(0,function(t,e){"use strict";function n(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}var o=function(t){return t&&t.__esModule?t:{default:t}}(e),r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},i=function(){function t(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}return function(e,n,o){return n&&t(e.prototype,n),o&&t(e,o),e}}(),a=function(){function t(e){n(this,t),this.resolveOptions(e),this.initSelection()}return i(t,[{key:"resolveOptions",value:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};this.action=t.action,this.container=t.container,this.emitter=t.emitter,this.target=t.target,this.text=t.text,this.trigger=t.trigger,this.selectedText=""}},{key:"initSelection",value:function(){this.text?this.selectFake():this.target&&this.selectTarget()}},{key:"selectFake",value:function(){var t=this,e="rtl"==document.documentElement.getAttribute("dir");this.removeFake(),this.fakeHandlerCallback=function(){return t.removeFake()},this.fakeHandler=this.container.addEventListener("click",this.fakeHandlerCallback)||!0,this.fakeElem=document.createElement("textarea"),this.fakeElem.style.fontSize="12pt",this.fakeElem.style.border="0",this.fakeElem.style.padding="0",this.fakeElem.style.margin="0",this.fakeElem.style.position="absolute",this.fakeElem.style[e?"right":"left"]="-9999px";var n=window.pageYOffset||document.documentElement.scrollTop;this.fakeElem.style.top=n+"px",this.fakeElem.setAttribute("readonly",""),this.fakeElem.value=this.text,this.container.appendChild(this.fakeElem),this.selectedText=(0,o.default)(this.fakeElem),this.copyText()}},{key:"removeFake",value:function(){this.fakeHandler&&(this.container.removeEventListener("click",this.fakeHandlerCallback),this.fakeHandler=null,this.fakeHandlerCallback=null),this.fakeElem&&(this.container.removeChild(this.fakeElem),this.fakeElem=null)}},{key:"selectTarget",value:function(){this.selectedText=(0,o.default)(this.target),this.copyText()}},{key:"copyText",value:function(){var t=void 0;try{t=document.execCommand(this.action)}catch(e){t=!1}this.handleResult(t)}},{key:"handleResult",value:function(t){this.emitter.emit(t?"success":"error",{action:this.action,text:this.selectedText,trigger:this.trigger,clearSelection:this.clearSelection.bind(this)})}},{key:"clearSelection",value:function(){this.trigger&&this.trigger.focus(),window.getSelection().removeAllRanges()}},{key:"destroy",value:function(){this.removeFake()}},{key:"action",set:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"copy";if(this._action=t,"copy"!==this._action&&"cut"!==this._action)throw new Error('Invalid "action" value, use either "copy" or "cut"')},get:function(){return this._action}},{key:"target",set:function(t){if(void 0!==t){if(!t||"object"!==(void 0===t?"undefined":r(t))||1!==t.nodeType)throw new Error('Invalid "target" value, use a valid Element');if("copy"===this.action&&t.hasAttribute("disabled"))throw new Error('Invalid "target" attribute. Please use "readonly" instead of "disabled" attribute');if("cut"===this.action&&(t.hasAttribute("readonly")||t.hasAttribute("disabled")))throw new Error('Invalid "target" attribute. You can\'t cut text from elements with "readonly" or "disabled" attributes');this._target=t}},get:function(){return this._target}}]),t}();t.exports=a})},LpmL:function(t,e){t.exports={L:1,M:0,Q:3,H:2}},PjAo:function(t,e,n){function o(t,e){if(void 0==t.length)throw new Error(t.length+"/"+e);for(var n=0;n<t.length&&0==t[n];)n++;this.num=new Array(t.length-n+e);for(var o=0;o<t.length-n;o++)this.num[o]=t[o+n]}var r=n("tzRw");o.prototype={get:function(t){return this.num[t]},getLength:function(){return this.num.length},multiply:function(t){for(var e=new Array(this.getLength()+t.getLength()-1),n=0;n<this.getLength();n++)for(var i=0;i<t.getLength();i++)e[n+i]^=r.gexp(r.glog(this.get(n))+r.glog(t.get(i)));return new o(e,0)},mod:function(t){if(this.getLength()-t.getLength()<0)return this;for(var e=r.glog(this.get(0))-r.glog(t.get(0)),n=new Array(this.getLength()),i=0;i<this.getLength();i++)n[i]=this.get(i);for(var i=0;i<t.getLength();i++)n[i]^=r.gexp(r.glog(t.get(i))+e);return new o(n,0).mod(t)}},t.exports=o},SPM9:function(t,e){function n(t){var e;if("SELECT"===t.nodeName)t.focus(),e=t.value;else if("INPUT"===t.nodeName||"TEXTAREA"===t.nodeName){var n=t.hasAttribute("readonly");n||t.setAttribute("readonly",""),t.select(),t.setSelectionRange(0,t.value.length),n||t.removeAttribute("readonly"),e=t.value}else{t.hasAttribute("contenteditable")&&t.focus();var o=window.getSelection(),r=document.createRange();r.selectNodeContents(t),o.removeAllRanges(),o.addRange(r),e=o.toString()}return e}t.exports=n},T9ab:function(t,e,n){function o(t){this.mode=r.MODE_8BIT_BYTE,this.data=t}var r=n("c1w4");o.prototype={getLength:function(t){return this.data.length},write:function(t){for(var e=0;e<this.data.length;e++)t.put(this.data.charCodeAt(e),8)}},t.exports=o},V33R:function(t,e,n){var o,r,i;!function(a,s){r=[t,n("LF/X"),n("WreF"),n("Bj/7")],o=s,void 0!==(i="function"==typeof o?o.apply(e,r):o)&&(t.exports=i)}(0,function(t,e,n,o){"use strict";function r(t){return t&&t.__esModule?t:{default:t}}function i(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function a(t,e){if(!t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!e||"object"!=typeof e&&"function"!=typeof e?t:e}function s(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function, not "+typeof e);t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,enumerable:!1,writable:!0,configurable:!0}}),e&&(Object.setPrototypeOf?Object.setPrototypeOf(t,e):t.__proto__=e)}function c(t,e){var n="data-clipboard-"+t;if(e.hasAttribute(n))return e.getAttribute(n)}var u=r(e),l=r(n),d=r(o),f="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},h=function(){function t(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}return function(e,n,o){return n&&t(e.prototype,n),o&&t(e,o),e}}(),m=function(t){function e(t,n){i(this,e);var o=a(this,(e.__proto__||Object.getPrototypeOf(e)).call(this));return o.resolveOptions(n),o.listenClick(t),o}return s(e,t),h(e,[{key:"resolveOptions",value:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};this.action="function"==typeof t.action?t.action:this.defaultAction,this.target="function"==typeof t.target?t.target:this.defaultTarget,this.text="function"==typeof t.text?t.text:this.defaultText,this.container="object"===f(t.container)?t.container:document.body}},{key:"listenClick",value:function(t){var e=this;this.listener=(0,d.default)(t,"click",function(t){return e.onClick(t)})}},{key:"onClick",value:function(t){var e=t.delegateTarget||t.currentTarget;this.clipboardAction&&(this.clipboardAction=null),this.clipboardAction=new u.default({action:this.action(e),target:this.target(e),text:this.text(e),container:this.container,trigger:e,emitter:this})}},{key:"defaultAction",value:function(t){return c("action",t)}},{key:"defaultTarget",value:function(t){var e=c("target",t);if(e)return document.querySelector(e)}},{key:"defaultText",value:function(t){return c("text",t)}},{key:"destroy",value:function(){this.listener.destroy(),this.clipboardAction&&(this.clipboardAction.destroy(),this.clipboardAction=null)}}],[{key:"isSupported",value:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:["copy","cut"],e="string"==typeof t?[t]:t,n=!!document.queryCommandSupported;return e.forEach(function(t){n=n&&!!document.queryCommandSupported(t)}),n}}]),e}(l.default);t.exports=m})},WreF:function(t,e){function n(){}n.prototype={on:function(t,e,n){var o=this.e||(this.e={});return(o[t]||(o[t]=[])).push({fn:e,ctx:n}),this},once:function(t,e,n){function o(){r.off(t,o),e.apply(n,arguments)}var r=this;return o._=e,this.on(t,o,n)},emit:function(t){var e=[].slice.call(arguments,1),n=((this.e||(this.e={}))[t]||[]).slice(),o=0,r=n.length;for(o;o<r;o++)n[o].fn.apply(n[o].ctx,e);return this},off:function(t,e){var n=this.e||(this.e={}),o=n[t],r=[];if(o&&e)for(var i=0,a=o.length;i<a;i++)o[i].fn!==e&&o[i].fn._!==e&&r.push(o[i]);return r.length?n[t]=r:delete n[t],this}},t.exports=n},ZE5A:function(t,e,n){function o(t,e,n,o,r){var a=i.apply(this,arguments);return t.addEventListener(n,a,r),{destroy:function(){t.removeEventListener(n,a,r)}}}function r(t,e,n,r,i){return"function"==typeof t.addEventListener?o.apply(null,arguments):"function"==typeof n?o.bind(null,document).apply(null,arguments):("string"==typeof t&&(t=document.querySelectorAll(t)),Array.prototype.map.call(t,function(t){return o(t,e,n,r,i)}))}function i(t,e,n,o){return function(n){n.delegateTarget=a(n.target,e),n.delegateTarget&&o.call(t,n)}}var a=n("Jssu");t.exports=r},c1w4:function(t,e){t.exports={MODE_NUMBER:1,MODE_ALPHA_NUM:2,MODE_8BIT_BYTE:4,MODE_KANJI:8}},iDEd:function(t,e){e.node=function(t){return void 0!==t&&t instanceof HTMLElement&&1===t.nodeType},e.nodeList=function(t){var n=Object.prototype.toString.call(t);return void 0!==t&&("[object NodeList]"===n||"[object HTMLCollection]"===n)&&"length"in t&&(0===t.length||e.node(t[0]))},e.string=function(t){return"string"==typeof t||t instanceof String},e.fn=function(t){return"[object Function]"===Object.prototype.toString.call(t)}},pCvh:function(t,e,n){e=t.exports=n("FZ+f")(!0),e.push([t.i,"\n.recommend {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  background: #28272c;\n}\n.recommend .app-header {\n  background: none !important;\n  color: #fff;\n}\n.recommend .recommend-content {\n  padding-top: 75px;\n}\n.recommend .recommend-content .hint {\n  text-align: center;\n  color: #fff;\n}\n.recommend .recommend-content .hint .add-code {\n  text-decoration: underline;\n}\n.recommend .recommend-content .recommend-box {\n  position: absolute;\n  top: 110px;\n  bottom: 30px;\n  left: 30px;\n  right: 30px;\n  border-radius: 10px;\n  background: #fff;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n  -webkit-flex-direction: column;\n          flex-direction: column;\n  -webkit-justify-content: space-around;\n          justify-content: space-around;\n}\n.recommend .recommend-content .recommend-box .top {\n  text-align: center;\n  color: #ff4800;\n  padding: 30px 0;\n}\n.recommend .recommend-content .recommend-box .top h5 {\n  font-size: 18px;\n}\n.recommend .recommend-content .recommend-box .top h1 {\n  padding: 30px 0;\n  font-size: 40px;\n}\n.recommend .recommend-content .recommend-box .top button {\n  color: #fff;\n  width: 120px;\n  line-height: 45px;\n  border: 0;\n  height: 45px;\n  background: -webkit-linear-gradient(top, #ff6440, #ff1641); /* Safari 5.1 - 6.0 */ /* Opera 11.1 - 12.0 */ /* Firefox 3.6 - 15 */\n  background: linear-gradient(to bottom, #ff6440, #ff1641); /* 标准的语法 */\n  border-radius: 10px;\n  font-size: 16px;\n}\n.recommend .recommend-content .recommend-box .center {\n  margin-left: -15px;\n  margin-right: -15px;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n}\n.recommend .recommend-content .recommend-box .center .line {\n  border-top: 1px dashed #ff4800;\n/*flex 1*/\n  width: calc(100% - 90px);\n}\n.recommend .recommend-content .recommend-box .center .circle {\n/*flex 0 0 40px*/\n  width: 30px;\n  height: 30px;\n  border-radius: 50%;\n  background: #28272c;\n}\n.recommend .recommend-content .recommend-box .bottom {\n  padding: 20px;\n  text-align: center;\n}\n.recommend .recommend-content .recommend-box .bottom p {\n  margin-top: 10px;\n  line-height: 18px;\n  color: #b4b5bc;\n}\n.recommend .add-code-dialog .weui-dialog {\n  padding: 25px 15px;\n  width: 75%;\n}\n.recommend .add-code-dialog .weui-dialog .title {\n  margin-bottom: 30px;\n  text-align: left;\n  font-size: 18px;\n}\n.recommend .add-code-dialog .weui-dialog .title .close {\n  float: right;\n}\n.recommend .add-code-dialog .weui-dialog .ipt-box {\n  position: relative;\n}\n.recommend .add-code-dialog .weui-dialog .ipt-box .vcode-ipt {\n  width: 100%;\n  line-height: 35px;\n  border-bottom: 1px solid #dbdbdb;\n}\n.recommend .add-code-dialog .weui-dialog .reset-btn {\n  line-height: 40px;\n  font-size: 14px;\n  margin-top: 40px;\n}","",{version:3,sources:["F:/contest_system/web/app/contest-app/src/views/personal/recommend/index.vue"],names:[],mappings:";AACA;EACE,gBAAgB;EAChB,OAAO;EACP,UAAU;EACV,QAAQ;EACR,SAAS;EACT,uBAAuB;EACvB,WAAW;EACX,oBAAoB;CACrB;AACD;EACE,4BAA4B;EAC5B,YAAY;CACb;AACD;EACE,kBAAkB;CACnB;AACD;EACE,mBAAmB;EACnB,YAAY;CACb;AACD;EACE,2BAA2B;CAC5B;AACD;EACE,mBAAmB;EACnB,WAAW;EACX,aAAa;EACb,WAAW;EACX,YAAY;EACZ,oBAAoB;EACpB,iBAAiB;EACjB,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,6BAA6B;EAC7B,8BAA8B;EAC9B,+BAA+B;UACvB,uBAAuB;EAC/B,sCAAsC;UAC9B,8BAA8B;CACvC;AACD;EACE,mBAAmB;EACnB,eAAe;EACf,gBAAgB;CACjB;AACD;EACE,gBAAgB;CACjB;AACD;EACE,gBAAgB;EAChB,gBAAgB;CACjB;AACD;EACE,YAAY;EACZ,aAAa;EACb,kBAAkB;EAClB,UAAU;EACV,aAAa;EACb,2DAA2D,CAAC,sBAAsB,CAAC,uBAAuB,CAAC,sBAAsB;EACjI,yDAAyD,CAAC,WAAW;EACrE,oBAAoB;EACpB,gBAAgB;CACjB;AACD;EACE,mBAAmB;EACnB,oBAAoB;EACpB,qBAAqB;EACrB,sBAAsB;EACtB,cAAc;EACd,0BAA0B;EAC1B,4BAA4B;UACpB,oBAAoB;EAC5B,0BAA0B;EAC1B,uCAAuC;UAC/B,+BAA+B;CACxC;AACD;EACE,+BAA+B;AACjC,UAAU;EACR,yBAAyB;CAC1B;AACD;AACA,iBAAiB;EACf,YAAY;EACZ,aAAa;EACb,mBAAmB;EACnB,oBAAoB;CACrB;AACD;EACE,cAAc;EACd,mBAAmB;CACpB;AACD;EACE,iBAAiB;EACjB,kBAAkB;EAClB,eAAe;CAChB;AACD;EACE,mBAAmB;EACnB,WAAW;CACZ;AACD;EACE,oBAAoB;EACpB,iBAAiB;EACjB,gBAAgB;CACjB;AACD;EACE,aAAa;CACd;AACD;EACE,mBAAmB;CACpB;AACD;EACE,YAAY;EACZ,kBAAkB;EAClB,iCAAiC;CAClC;AACD;EACE,kBAAkB;EAClB,gBAAgB;EAChB,iBAAiB;CAClB",file:"index.vue",sourcesContent:["\n.recommend {\n  position: fixed;\n  top: 0;\n  bottom: 0;\n  left: 0;\n  right: 0;\n  background-color: #fff;\n  z-index: 5;\n  background: #28272c;\n}\n.recommend .app-header {\n  background: none !important;\n  color: #fff;\n}\n.recommend .recommend-content {\n  padding-top: 75px;\n}\n.recommend .recommend-content .hint {\n  text-align: center;\n  color: #fff;\n}\n.recommend .recommend-content .hint .add-code {\n  text-decoration: underline;\n}\n.recommend .recommend-content .recommend-box {\n  position: absolute;\n  top: 110px;\n  bottom: 30px;\n  left: 30px;\n  right: 30px;\n  border-radius: 10px;\n  background: #fff;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n  -webkit-flex-direction: column;\n          flex-direction: column;\n  -webkit-justify-content: space-around;\n          justify-content: space-around;\n}\n.recommend .recommend-content .recommend-box .top {\n  text-align: center;\n  color: #ff4800;\n  padding: 30px 0;\n}\n.recommend .recommend-content .recommend-box .top h5 {\n  font-size: 18px;\n}\n.recommend .recommend-content .recommend-box .top h1 {\n  padding: 30px 0;\n  font-size: 40px;\n}\n.recommend .recommend-content .recommend-box .top button {\n  color: #fff;\n  width: 120px;\n  line-height: 45px;\n  border: 0;\n  height: 45px;\n  background: -webkit-linear-gradient(top, #ff6440, #ff1641); /* Safari 5.1 - 6.0 */ /* Opera 11.1 - 12.0 */ /* Firefox 3.6 - 15 */\n  background: linear-gradient(to bottom, #ff6440, #ff1641); /* 标准的语法 */\n  border-radius: 10px;\n  font-size: 16px;\n}\n.recommend .recommend-content .recommend-box .center {\n  margin-left: -15px;\n  margin-right: -15px;\n  display: -webkit-box;\n  display: -webkit-flex;\n  display: flex;\n  -webkit-box-align: center;\n  -webkit-align-items: center;\n          align-items: center;\n  -webkit-box-pack: justify;\n  -webkit-justify-content: space-between;\n          justify-content: space-between;\n}\n.recommend .recommend-content .recommend-box .center .line {\n  border-top: 1px dashed #ff4800;\n/*flex 1*/\n  width: calc(100% - 90px);\n}\n.recommend .recommend-content .recommend-box .center .circle {\n/*flex 0 0 40px*/\n  width: 30px;\n  height: 30px;\n  border-radius: 50%;\n  background: #28272c;\n}\n.recommend .recommend-content .recommend-box .bottom {\n  padding: 20px;\n  text-align: center;\n}\n.recommend .recommend-content .recommend-box .bottom p {\n  margin-top: 10px;\n  line-height: 18px;\n  color: #b4b5bc;\n}\n.recommend .add-code-dialog .weui-dialog {\n  padding: 25px 15px;\n  width: 75%;\n}\n.recommend .add-code-dialog .weui-dialog .title {\n  margin-bottom: 30px;\n  text-align: left;\n  font-size: 18px;\n}\n.recommend .add-code-dialog .weui-dialog .title .close {\n  float: right;\n}\n.recommend .add-code-dialog .weui-dialog .ipt-box {\n  position: relative;\n}\n.recommend .add-code-dialog .weui-dialog .ipt-box .vcode-ipt {\n  width: 100%;\n  line-height: 35px;\n  border-bottom: 1px solid #dbdbdb;\n}\n.recommend .add-code-dialog .weui-dialog .reset-btn {\n  line-height: 40px;\n  font-size: 14px;\n  margin-top: 40px;\n}"],sourceRoot:""}])},rXbU:function(t,e,n){function o(t,e){this.totalCount=t,this.dataCount=e}var r=n("LpmL");o.RS_BLOCK_TABLE=[[1,26,19],[1,26,16],[1,26,13],[1,26,9],[1,44,34],[1,44,28],[1,44,22],[1,44,16],[1,70,55],[1,70,44],[2,35,17],[2,35,13],[1,100,80],[2,50,32],[2,50,24],[4,25,9],[1,134,108],[2,67,43],[2,33,15,2,34,16],[2,33,11,2,34,12],[2,86,68],[4,43,27],[4,43,19],[4,43,15],[2,98,78],[4,49,31],[2,32,14,4,33,15],[4,39,13,1,40,14],[2,121,97],[2,60,38,2,61,39],[4,40,18,2,41,19],[4,40,14,2,41,15],[2,146,116],[3,58,36,2,59,37],[4,36,16,4,37,17],[4,36,12,4,37,13],[2,86,68,2,87,69],[4,69,43,1,70,44],[6,43,19,2,44,20],[6,43,15,2,44,16],[4,101,81],[1,80,50,4,81,51],[4,50,22,4,51,23],[3,36,12,8,37,13],[2,116,92,2,117,93],[6,58,36,2,59,37],[4,46,20,6,47,21],[7,42,14,4,43,15],[4,133,107],[8,59,37,1,60,38],[8,44,20,4,45,21],[12,33,11,4,34,12],[3,145,115,1,146,116],[4,64,40,5,65,41],[11,36,16,5,37,17],[11,36,12,5,37,13],[5,109,87,1,110,88],[5,65,41,5,66,42],[5,54,24,7,55,25],[11,36,12],[5,122,98,1,123,99],[7,73,45,3,74,46],[15,43,19,2,44,20],[3,45,15,13,46,16],[1,135,107,5,136,108],[10,74,46,1,75,47],[1,50,22,15,51,23],[2,42,14,17,43,15],[5,150,120,1,151,121],[9,69,43,4,70,44],[17,50,22,1,51,23],[2,42,14,19,43,15],[3,141,113,4,142,114],[3,70,44,11,71,45],[17,47,21,4,48,22],[9,39,13,16,40,14],[3,135,107,5,136,108],[3,67,41,13,68,42],[15,54,24,5,55,25],[15,43,15,10,44,16],[4,144,116,4,145,117],[17,68,42],[17,50,22,6,51,23],[19,46,16,6,47,17],[2,139,111,7,140,112],[17,74,46],[7,54,24,16,55,25],[34,37,13],[4,151,121,5,152,122],[4,75,47,14,76,48],[11,54,24,14,55,25],[16,45,15,14,46,16],[6,147,117,4,148,118],[6,73,45,14,74,46],[11,54,24,16,55,25],[30,46,16,2,47,17],[8,132,106,4,133,107],[8,75,47,13,76,48],[7,54,24,22,55,25],[22,45,15,13,46,16],[10,142,114,2,143,115],[19,74,46,4,75,47],[28,50,22,6,51,23],[33,46,16,4,47,17],[8,152,122,4,153,123],[22,73,45,3,74,46],[8,53,23,26,54,24],[12,45,15,28,46,16],[3,147,117,10,148,118],[3,73,45,23,74,46],[4,54,24,31,55,25],[11,45,15,31,46,16],[7,146,116,7,147,117],[21,73,45,7,74,46],[1,53,23,37,54,24],[19,45,15,26,46,16],[5,145,115,10,146,116],[19,75,47,10,76,48],[15,54,24,25,55,25],[23,45,15,25,46,16],[13,145,115,3,146,116],[2,74,46,29,75,47],[42,54,24,1,55,25],[23,45,15,28,46,16],[17,145,115],[10,74,46,23,75,47],[10,54,24,35,55,25],[19,45,15,35,46,16],[17,145,115,1,146,116],[14,74,46,21,75,47],[29,54,24,19,55,25],[11,45,15,46,46,16],[13,145,115,6,146,116],[14,74,46,23,75,47],[44,54,24,7,55,25],[59,46,16,1,47,17],[12,151,121,7,152,122],[12,75,47,26,76,48],[39,54,24,14,55,25],[22,45,15,41,46,16],[6,151,121,14,152,122],[6,75,47,34,76,48],[46,54,24,10,55,25],[2,45,15,64,46,16],[17,152,122,4,153,123],[29,74,46,14,75,47],[49,54,24,10,55,25],[24,45,15,46,46,16],[4,152,122,18,153,123],[13,74,46,32,75,47],[48,54,24,14,55,25],[42,45,15,32,46,16],[20,147,117,4,148,118],[40,75,47,7,76,48],[43,54,24,22,55,25],[10,45,15,67,46,16],[19,148,118,6,149,119],[18,75,47,31,76,48],[34,54,24,34,55,25],[20,45,15,61,46,16]],o.getRSBlocks=function(t,e){var n=o.getRsBlockTable(t,e);if(void 0==n)throw new Error("bad rs block @ typeNumber:"+t+"/errorCorrectLevel:"+e);for(var r=n.length/3,i=new Array,a=0;a<r;a++)for(var s=n[3*a+0],c=n[3*a+1],u=n[3*a+2],l=0;l<s;l++)i.push(new o(c,u));return i},o.getRsBlockTable=function(t,e){switch(e){case r.L:return o.RS_BLOCK_TABLE[4*(t-1)+0];case r.M:return o.RS_BLOCK_TABLE[4*(t-1)+1];case r.Q:return o.RS_BLOCK_TABLE[4*(t-1)+2];case r.H:return o.RS_BLOCK_TABLE[4*(t-1)+3];default:return}},t.exports=o},tzRw:function(t,e){for(var n={glog:function(t){if(t<1)throw new Error("glog("+t+")");return n.LOG_TABLE[t]},gexp:function(t){for(;t<0;)t+=255;for(;t>=256;)t-=255;return n.EXP_TABLE[t]},EXP_TABLE:new Array(256),LOG_TABLE:new Array(256)},o=0;o<8;o++)n.EXP_TABLE[o]=1<<o;for(var o=8;o<256;o++)n.EXP_TABLE[o]=n.EXP_TABLE[o-4]^n.EXP_TABLE[o-5]^n.EXP_TABLE[o-6]^n.EXP_TABLE[o-8];for(var o=0;o<255;o++)n.LOG_TABLE[n.EXP_TABLE[o]]=o;t.exports=n},u5CL:function(t,e){function n(){this.buffer=new Array,this.length=0}n.prototype={get:function(t){var e=Math.floor(t/8);return 1==(this.buffer[e]>>>7-t%8&1)},put:function(t,e){for(var n=0;n<e;n++)this.putBit(1==(t>>>e-n-1&1))},getLengthInBits:function(){return this.length},putBit:function(t){var e=Math.floor(this.length/8);this.buffer.length<=e&&this.buffer.push(0),t&&(this.buffer[e]|=128>>>this.length%8),this.length++}},t.exports=n},wpcj:function(t,e,n){"use strict";function o(t){return t.webkitBackingStorePixelRatio||t.mozBackingStorePixelRatio||t.msBackingStorePixelRatio||t.oBackingStorePixelRatio||t.backingStorePixelRatio||1}function r(t){var e,n,o,r;for(e="",o=t.length,n=0;n<o;n++)r=t.charCodeAt(n),r>=1&&r<=127?e+=t.charAt(n):r>2047?(e+=String.fromCharCode(224|r>>12&15),e+=String.fromCharCode(128|r>>6&63),e+=String.fromCharCode(128|r>>0&63)):(e+=String.fromCharCode(192|r>>6&31),e+=String.fromCharCode(128|r>>0&63));return e}function i(t){return t.webkitBackingStorePixelRatio||t.mozBackingStorePixelRatio||t.msBackingStorePixelRatio||t.oBackingStorePixelRatio||t.backingStorePixelRatio||1}function a(t){var e,n,o,r;for(e="",o=t.length,n=0;n<o;n++)r=t.charCodeAt(n),r>=1&&r<=127?e+=t.charAt(n):r>2047?(e+=String.fromCharCode(224|r>>12&15),e+=String.fromCharCode(128|r>>6&63),e+=String.fromCharCode(128|r>>0&63)):(e+=String.fromCharCode(192|r>>6&31),e+=String.fromCharCode(128|r>>0&63));return e}var s=n("0k+n"),c=n.n(s),u=n("LpmL"),l=n.n(u),d=(String,Number,String,String,String,String,{name:"qrcode",props:{value:String,size:{type:Number,default:160},level:{type:String,default:"L"},bgColor:{type:String,default:"#FFFFFF"},fgColor:{type:String,default:"#000000"},type:{type:String,default:"img"}},mounted:function(){var t=this;this.$nextTick(function(){t.render()})},data:function(){return{imgData:""}},watch:{value:function(){this.render()},size:function(){this.render()},level:function(){this.render()},bgColor:function(){this.render()},fgColor:function(){this.render()}},methods:{render:function(){var t=this;if(void 0!==this.value){var e=new c.a(-1,l.a[this.level]);e.addData(a(this.value)),e.make();var n=this.$refs.canvas,o=n.getContext("2d"),r=e.modules,s=this.size/r.length,u=this.size/r.length,d=(window.devicePixelRatio||1)/i(o);n.height=n.width=this.size*d,o.scale(d,d),r.forEach(function(e,n){e.forEach(function(e,r){o.fillStyle=e?t.fgColor:t.bgColor;var i=Math.ceil((r+1)*s)-Math.floor(r*s),a=Math.ceil((n+1)*u)-Math.floor(n*u);o.fillRect(Math.round(r*s),Math.round(n*u),i,a)})}),"img"===this.type&&(this.imgData=n.toDataURL("image/png"))}}}}),f=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("canvas",{directives:[{name:"show",rawName:"v-show",value:"canvas"===t.type,expression:"type === 'canvas'"}],ref:"canvas",style:{height:t.size+"px",width:t.size+"px"},attrs:{height:t.size,width:t.size}}),t._v(" "),"img"===t.type?n("img",{style:{height:t.size+"px",width:t.size+"px"},attrs:{src:t.imgData}}):t._e()])},h=[],m={render:f,staticRenderFns:h},A=m,g=n("VU/8"),p=g(d,A,!1,null,null,null);e.a=p.exports}});
//# sourceMappingURL=5.5d10a2f2d92a998f4510.js.map