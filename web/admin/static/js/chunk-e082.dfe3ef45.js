(window.webpackJsonp=window.webpackJsonp||[]).push([["chunk-e082"],{"3jdf":function(e,t,a){e.exports=a.p+"static/img/user.b436732.jpg"},"7Qib":function(e,t,a){"use strict";a.d(t,"b",function(){return l}),a.d(t,"a",function(){return o});var n=a("Q2cO"),s=a.n(n);function l(e,t){if(0===arguments.length)return null;var a=t||"{y}-{m}-{d} {h}:{i}:{s}",n=void 0;"object"===(void 0===e?"undefined":s()(e))?n=e:(10===(""+e).length&&(e=1e3*parseInt(e)),n=new Date(e));var l={y:n.getFullYear(),m:n.getMonth()+1,d:n.getDate(),h:n.getHours(),i:n.getMinutes(),s:n.getSeconds(),a:n.getDay()};return a.replace(/{(y|m|d|h|i|s|a)+}/g,function(e,t){var a=l[t];return"a"===t?["日","一","二","三","四","五","六"][a]:(e.length>0&&a<10&&(a="0"+a),a||0)})}function o(e,t,a){var n,s=0,l=e.length;return n=(t-1)*a,s=t*a<l?t*a:l,e.slice(n,s)}},"7q6W":function(e,t,a){"use strict";var n=a("elW4");a.n(n).a},elW4:function(e,t,a){},o0Ed:function(e,t,a){"use strict";a.r(t);var n=a("t3Un");function s(e,t,a,s){return Object(n.a)({url:"/user/index",method:"post",data:{searchName:e,str_time:t,end_time:a,page:s}})}function l(e){return Object(n.a)({url:"/user/get-currency",method:"post",data:{userId:e}})}function o(e){return Object(n.a)({url:"/user/stop-user",method:"post",data:{userId:e}})}function r(e,t){return Object(n.a)({url:"/user/edit-wallet",method:"post",data:{walletId:e,address:t}})}var i=a("hrJr"),c=a("7Qib"),u={name:"UserManagement",data:function(){return{search:"",searchDate:"",tableData:[],tableDataSelection:[],currentPage:1,total:1,edidWallet:"",showUserInfo:!1,rowInfo:[],userInfoBase:[],userInfoIdentify:[],userInfoVote:[],userInfoWallet:[],userInfoVoucher:[],userInfoRecommend:[],activeName:"Base",dialogAddUser:!1,addUserName:"",addUserCode:"",pollName:"投票记录",walletName:"",walletMoney:"GRT",walletNote:"收支记录",voucherName:"获取记录",dialogEditUser:!1}},created:function(){var e=this;s(this.search,this.searchDate[0],this.searchDate[1],this.currentPage).then(function(t){e.tableData=t.content.list,e.total=parseInt(t.content.count)})},methods:{searchRun:function(){var e=this;s(this.search,this.searchDate[0],this.searchDate[1],1).then(function(t){e.tableData=t.content.list,e.total=parseInt(t.content.count),e.currentPage=1})},handleSelectionChange:function(e){this.tableDataSelection=e},clickRow:function(e){this.rowInfo=e,this.showUserInfo=!0,this.changeTabs({name:this.activeName})},changePage:function(e){var t=this;s(this.search,this.searchDate[0],this.searchDate[1],this.currentPage).then(function(e){t.tableData=e.content.list,t.total=parseInt(e.content.count)})},changeTabs:function(e){var t=this;"Base"===e.name?function(e){return Object(n.a)({url:"/user/get-user-info",method:"post",data:{userId:e}})}(this.rowInfo.id).then(function(e){t.userInfoBase=e.content}):"Identify"===e.name?function(e){return Object(n.a)({url:"/user/get-user-identify",method:"post",data:{userId:e}})}(this.rowInfo.id).then(function(e){t.userInfoIdentify=e.content}):"Vote"===e.name?function(e){return Object(n.a)({url:"/user/get-user-vote",method:"post",data:{userId:e}})}(this.rowInfo.id).then(function(e){t.userInfoVote=e.content}):"Wallet"===e.name?l(this.rowInfo.id).then(function(e){t.userInfoWallet=e.content,t.walletName=t.userInfoWallet[0].name}):"Voucher"===e.name?function(e){return Object(n.a)({url:"/user/get-user-voucher",method:"post",data:{userId:e}})}(this.rowInfo.id).then(function(e){t.userInfoVoucher=e.content}):"Recommend"===e.name&&function(e){return Object(n.a)({url:"/user/get-user-recommend",method:"post",data:{userId:e}})}(this.rowInfo.id).then(function(e){t.userInfoRecommend=e.content})},free:function(){var e=this;this.$confirm("确定冻结吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){o(e.rowInfo.id).then(function(e){Object(i.Message)({message:e.msg,type:"success"})}).then(function(t){e.rowInfo.status="0"})})},thaw:function(){var e=this;this.$confirm("确定解冻吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){(function(e){return Object(n.a)({url:"/user/open-user",method:"post",data:{userId:e}})})(e.rowInfo.id).then(function(e){Object(i.Message)({message:e.msg,type:"success"})}).then(function(t){e.rowInfo.status="1"})})},allFreeze:function(){var e=this;this.tableDataSelection.length<1||this.$confirm("确定解冻吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){var t="";e.tableDataSelection.map(function(e,a,n){t=t+","+e.id}),o(t.replace(",","")).then(function(e){Object(i.Message)({message:e.msg,type:"success"})}).then(function(t){s().then(function(t){e.tableData=t.content.list,e.total=parseInt(t.content.count)})})})},rowEdit:function(){var e=this;l(this.rowInfo.id).then(function(t){e.userInfoWallet=t.content,e.rowInfo.walletData=t.content,e.walletName=e.userInfoWallet[0].name,e.edidWallet=e.userInfoWallet[0].name,e.dialogEditUser=!0})},addUser:function(){var e=this;(function(e,t){return Object(n.a)({url:"/user/create-user",method:"post",data:{mobile:e,code:t}})})(this.addUserName,this.addUserCode).then(function(t){Object(i.Message)({message:t.msg,type:"success"}),e.dialogAddUser=!1,s(e.search,e.searchDate[0],e.searchDate[1],e.currentPage).then(function(t){e.tableData=t.content.list,e.total=parseInt(t.content.count)})})},editUser:function(){var e=this;this.dialogEditUser=!1,function(e,t){return Object(n.a)({url:"/user/edit-user",method:"post",data:{userId:e,name:t}})}(this.rowInfo.id,this.rowInfo.mobile).then(function(t){Object(i.Message)({message:t.msg,type:"success"}),s(e.search,e.searchDate[0],e.searchDate[1],e.currentPage).then(function(t){e.tableData=t.content.list,e.total=parseInt(t.content.count)})});for(var t=0;t<this.rowInfo.walletData.length;t++)r(this.rowInfo.walletData[t].id,this.rowInfo.walletData[t].address);this.changeTabs({name:"Wallet"})},addExcel:function(){var e=this;Promise.all([a.e("chunk-7fcb"),a.e("chunk-17ef")]).then(a.bind(null,"S/jZ")).then(function(t){var a=e.tableData,n=e.formatJson(["username","userType","nodeName","num","referee","status","createTime","lastLoginTime"],a);t.export_json_to_excel({header:["用户","类型","拥有节点","已投票数","推荐人","状态","注册时间","最近一次登录时间"],data:n,filename:"用户管理"})})},formatJson:function(e,t){return t.map(function(t){return e.map(function(e){return"timestamp"===e?Object(c.b)(t[e]):t[e]})})}}},d=(a("7q6W"),a("ZrdR")),m=Object(d.a)(u,function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"app-container",on:{click:function(t){if(t.target!==t.currentTarget)return null;e.showUserInfo=!1}}},[n("h4",{staticStyle:{display:"inline-block"}},[e._v("用户管理")]),e._v(" "),n("el-button",{staticClass:"btn-right",on:{click:function(t){e.dialogAddUser=!0}}},[e._v("新增用户")]),e._v(" "),n("el-button",{staticClass:"btn-right",staticStyle:{"margin-right":"10px"},on:{click:e.addExcel}},[e._v("导出excel")]),e._v(" "),n("br"),e._v(" "),n("el-input",{staticStyle:{"margin-top":"20px",width:"300px"},attrs:{placeholder:"用户","suffix-icon":"el-icon-search"},model:{value:e.search,callback:function(t){e.search=t},expression:"search"}}),e._v(" "),n("div",{staticStyle:{float:"right","margin-top":"20px"}},[e._v("\n    注册时间\n    "),n("el-date-picker",{staticStyle:{width:"400px"},attrs:{type:"daterange","range-separator":"至","start-placeholder":"开始日期","end-placeholder":"结束日期",format:"yyyy 年 MM 月 dd 日","value-format":"yyyy-MM-dd"},model:{value:e.searchDate,callback:function(t){e.searchDate=t},expression:"searchDate"}}),e._v(" "),n("el-button",{on:{click:e.searchRun}},[e._v("查询")])],1),e._v(" "),n("br"),e._v("\n\n  已选择"),n("span",{staticStyle:{color:"#3e84e9"}},[e._v(e._s(e.tableDataSelection.length))]),e._v("项\n  "),n("el-button",{staticStyle:{"margin-top":"20px"},attrs:{size:"small",type:"primary"},on:{click:e.allFreeze}},[e._v("冻结")]),e._v(" "),n("el-table",{ref:"userTable",staticStyle:{width:"100%",margin:"10px 0"},attrs:{data:e.tableData},on:{"selection-change":e.handleSelectionChange,"row-click":e.clickRow}},[n("el-table-column",{attrs:{type:"selection",width:"55"}}),e._v(" "),n("el-table-column",{attrs:{prop:"username",label:"用户"}}),e._v(" "),n("el-table-column",{attrs:{prop:"userType",label:"类型"}}),e._v(" "),n("el-table-column",{attrs:{prop:"nodeName",label:"拥有节点"}}),e._v(" "),n("el-table-column",{attrs:{prop:"num",label:"已投票数"}}),e._v(" "),n("el-table-column",{attrs:{prop:"referee",label:"推荐人"}}),e._v(" "),n("el-table-column",{attrs:{label:"状态"},scopedSlots:e._u([{key:"default",fn:function(t){return[1==t.row.status?n("div",[e._v("正常")]):n("div",[e._v("停用")])]}}])}),e._v(" "),n("el-table-column",{attrs:{prop:"createTime",label:"注册时间"}}),e._v(" "),n("el-table-column",{attrs:{prop:"lastLoginTime",label:"最近一次登录时间"}})],1),e._v(" "),n("el-pagination",{attrs:{"current-page":e.currentPage,total:e.total,"page-size":20,layout:"total, prev, pager, next, jumper"},on:{"update:currentPage":function(t){e.currentPage=t},"current-change":e.changePage}}),e._v(" "),n("transition",{attrs:{name:"fade"}},[n("div",{directives:[{name:"show",rawName:"v-show",value:e.showUserInfo,expression:"showUserInfo"}],staticClass:"fade-slide"},[n("div",{staticClass:"title"},[n("img",{attrs:{src:a("3jdf"),alt:""}}),e._v(" "),n("span",{staticClass:"name"},[e._v(e._s(e.userInfoBase.mobile)),n("br"),n("span",[e._v("用户")])]),e._v(" "),n("i",{staticClass:"el-icon-close btn",on:{click:function(t){e.showUserInfo=!1}}}),e._v(" "),n("el-button",{staticClass:"btn",staticStyle:{margin:"0 10px"},attrs:{type:"primary"},on:{click:e.rowEdit}},[e._v("编辑")]),e._v(" "),n("el-button",{directives:[{name:"show",rawName:"v-show",value:1==e.rowInfo.status,expression:"rowInfo.status==1"}],staticClass:"btn",attrs:{type:"danger"},on:{click:e.free}},[e._v("冻结")]),e._v(" "),n("el-button",{directives:[{name:"show",rawName:"v-show",value:0==e.rowInfo.status,expression:"rowInfo.status==0"}],staticClass:"btn",attrs:{type:"danger"},on:{click:e.thaw}},[e._v("解冻")])],1),e._v(" "),n("div",{staticClass:"info"},[n("el-row",{staticClass:"info-row",attrs:{gutter:5}},[n("el-col",[n("el-card",{attrs:{shadow:"never"}},[n("div",{staticClass:"title"},[e._v("类型")]),e._v("\n              "+e._s(e.userInfoBase.userType)+"\n            ")])],1),e._v(" "),n("el-col",[n("el-card",{attrs:{shadow:"never"}},[n("div",{staticClass:"title"},[e._v("拥有节点")]),e._v("\n              "+e._s(e.userInfoBase.username)+"\n            ")])],1),e._v(" "),n("el-col",[n("el-card",{attrs:{shadow:"never"}},[n("div",{staticClass:"title"},[e._v("已投票数")]),e._v("\n              "+e._s(e.userInfoBase.num)+"\n            ")])],1),e._v(" "),n("el-col",[n("el-card",{attrs:{shadow:"never"}},[n("div",{staticClass:"title"},[e._v("推荐人")]),e._v("\n              "+e._s(e.userInfoBase.referee)+"\n            ")])],1),e._v(" "),n("el-col",[n("el-card",{attrs:{shadow:"never"}},[n("div",{staticClass:"title"},[e._v("推荐码")]),e._v("\n              "+e._s(e.userInfoBase.recommendCode)+"\n            ")])],1)],1)],1),e._v(" "),n("el-tabs",{staticClass:"tabs",on:{"tab-click":e.changeTabs},model:{value:e.activeName,callback:function(t){e.activeName=t},expression:"activeName"}},[n("el-tab-pane",{attrs:{label:"基本信息",name:"Base"}},[e._v("\n          账户\n          "),n("p",[e._v(e._s(e.userInfoBase.username))])]),e._v(" "),n("el-tab-pane",{attrs:{label:"实名信息",name:"Identify"}},[n("p",[n("span",{staticStyle:{"margin-right":"150px"}},[e._v("姓名："+e._s(e.userInfoIdentify.realName))]),e._v(" "),n("span",[e._v("身份证号："+e._s(e.userInfoIdentify.number))])]),e._v(" "),n("p",[e._v("手持身份证正面")]),e._v(" "),n("img",{staticStyle:{display:"block",width:"178px",height:"178px",border:"1px solid #ddd"},attrs:{src:e.userInfoIdentify.picFront,alt:""}}),e._v(" "),n("p",[e._v("手持身份证背面")]),e._v(" "),n("img",{staticStyle:{display:"block",width:"178px",height:"178px",border:"1px solid #ddd"},attrs:{src:e.userInfoIdentify.picBack,alt:""}})]),e._v(" "),n("el-tab-pane",{attrs:{label:"投票明细",name:"Vote"}},[n("el-radio-group",{staticClass:"radioTabs",model:{value:e.pollName,callback:function(t){e.pollName=t},expression:"pollName"}},[n("el-radio-button",{attrs:{label:"投票记录"}}),e._v(" "),n("el-radio-button",{attrs:{label:"赎回记录"}})],1),e._v(" "),n("el-table",{directives:[{name:"show",rawName:"v-show",value:"投票记录"==e.pollName,expression:"pollName=='投票记录'"}],attrs:{data:e.userInfoVote.vote}},[n("el-table-column",{attrs:{prop:"nodeName",label:"节点名称"}}),e._v(" "),n("el-table-column",{attrs:{prop:"typeName",label:"类型"}}),e._v(" "),n("el-table-column",{attrs:{prop:"type",label:"方式"}}),e._v(" "),n("el-table-column",{attrs:{prop:"voteNumber",label:"数量"}}),e._v(" "),n("el-table-column",{attrs:{prop:"createTime",label:"投票时间"}})],1),e._v(" "),n("el-table",{directives:[{name:"show",rawName:"v-show",value:"赎回记录"==e.pollName,expression:"pollName=='赎回记录'"}],attrs:{data:e.userInfoVote.unvote}},[n("el-table-column",{attrs:{prop:"nodeName",label:"节点名称"}}),e._v(" "),n("el-table-column",{attrs:{prop:"typeName",label:"类型"}}),e._v(" "),n("el-table-column",{attrs:{prop:"voteNumber",label:"数量"}}),e._v(" "),n("el-table-column",{attrs:{prop:"undoTime",label:"赎回时间"}})],1)],1),e._v(" "),n("el-tab-pane",{staticClass:"wallet",attrs:{label:"资产",name:"Wallet"}},[n("el-tabs",{attrs:{type:"card"},model:{value:e.walletName,callback:function(t){e.walletName=t},expression:"walletName"}},e._l(e.userInfoWallet,function(t,a){return n("el-tab-pane",{key:a,attrs:{label:t.name,name:t.name}},[n("p",[e._v("收款地址")]),e._v(" "),n("p",{staticStyle:{color:"#888","padding-bottom":"30px"}},[e._v(e._s(t.address))]),e._v(" "),n("div",{staticClass:"wallet-info"},[n("span",[e._v("合计："+e._s(t.positionAmount))]),e._v(" "),n("span",[e._v("可用："+e._s(t.useAmount))]),e._v(" "),n("span",[e._v("冻结："+e._s(t.frozenAmount))])]),e._v(" "),n("el-radio-group",{staticClass:"radioTabs",model:{value:e.walletNote,callback:function(t){e.walletNote=t},expression:"walletNote"}},[n("el-radio-button",{attrs:{label:"收支记录"}}),e._v(" "),n("el-radio-button",{attrs:{label:"冻结记录"}})],1),e._v(" "),n("div",{directives:[{name:"show",rawName:"v-show",value:"收支记录"==e.walletNote,expression:"walletNote=='收支记录'"}],staticClass:"note"},e._l(t.inAndOut,function(t,a){return n("div",{key:a,staticClass:"row"},[n("span",[e._v(e._s(t.type))]),e._v(" "),n("span",[e._v(e._s(t.createTime))]),e._v(" "),n("span",[e._v(e._s(t.amount))])])})),e._v(" "),n("div",{directives:[{name:"show",rawName:"v-show",value:"冻结记录"==e.walletNote,expression:"walletNote=='冻结记录'"}],staticClass:"note"},e._l(t.frozen,function(t,a){return n("div",{key:a,staticClass:"row"},[n("span",[e._v(e._s(t.type))]),e._v(" "),n("span",[e._v(e._s(t.createTime))]),e._v(" "),n("span",[e._v(e._s(t.amount))])])}))],1)}))],1),e._v(" "),n("el-tab-pane",{attrs:{label:"投票券",name:"Voucher"}},[n("p",[e._v("剩余数量："+e._s(e.userInfoVoucher.length)+"票")]),e._v(" "),n("el-radio-group",{staticClass:"radioTabs",model:{value:e.voucherName,callback:function(t){e.voucherName=t},expression:"voucherName"}},[n("el-radio-button",{attrs:{label:"获取记录"}}),e._v(" "),n("el-radio-button",{attrs:{label:"使用记录"}})],1),e._v(" "),n("el-table",{directives:[{name:"show",rawName:"v-show",value:"获取记录"==e.voucherName,expression:"voucherName=='获取记录'"}],attrs:{data:e.userInfoVoucher.voucherList}},[n("el-table-column",{attrs:{prop:"username",label:"推荐用户"}}),e._v(" "),n("el-table-column",{attrs:{prop:"nodeName",label:"节点名称"}}),e._v(" "),n("el-table-column",{attrs:{prop:"typeName",label:"节点类型"}}),e._v(" "),n("el-table-column",{attrs:{prop:"voucherNum",label:"获取数量"}}),e._v(" "),n("el-table-column",{attrs:{prop:"createTime",label:"获取时间"}})],1),e._v(" "),n("el-table",{directives:[{name:"show",rawName:"v-show",value:"使用记录"==e.voucherName,expression:"voucherName=='使用记录'"}],attrs:{data:e.userInfoVoucher.voucherDetailList}},[n("el-table-column",{attrs:{prop:"nodeName",label:"投票节点"}}),e._v(" "),n("el-table-column",{attrs:{prop:"typeName",label:"节点类型"}}),e._v(" "),n("el-table-column",{attrs:{prop:"username",label:"用户"}}),e._v(" "),n("el-table-column",{attrs:{prop:"amount",label:"投票数量"}}),e._v(" "),n("el-table-column",{attrs:{prop:"createTime",label:"使用时间"}})],1)],1),e._v(" "),n("el-tab-pane",{attrs:{label:"推荐记录",name:"Recommend"}},[n("el-table",{attrs:{data:e.userInfoRecommend}},[n("el-table-column",{attrs:{prop:"username",label:"推荐用户"}}),e._v(" "),n("el-table-column",{attrs:{prop:"nodeName",label:"节点名称"}}),e._v(" "),n("el-table-column",{attrs:{prop:"typeName",label:"节点类型"}}),e._v(" "),n("el-table-column",{attrs:{prop:"createTime",label:"推荐时间"}})],1)],1)],1)],1)]),e._v(" "),n("el-dialog",{attrs:{visible:e.dialogAddUser,title:"新增用户"},on:{"update:visible":function(t){e.dialogAddUser=t}}},[n("el-form",{attrs:{"label-width":"80px"}},[n("el-form-item",{attrs:{label:"账号："}},[n("el-input",{model:{value:e.addUserName,callback:function(t){e.addUserName=t},expression:"addUserName"}})],1),e._v(" "),n("el-form-item",{attrs:{label:"推荐码："}},[n("el-input",{model:{value:e.addUserCode,callback:function(t){e.addUserCode=t},expression:"addUserCode"}})],1)],1),e._v(" "),n("span",{attrs:{slot:"footer"},slot:"footer"},[n("el-button",{attrs:{type:"primary"},on:{click:e.addUser}},[e._v("确 认")]),e._v(" "),n("el-button",{on:{click:function(t){e.dialogAddUser=!1}}},[e._v("取 消")])],1)],1),e._v(" "),n("el-dialog",{attrs:{visible:e.dialogEditUser,title:"用户编辑"},on:{"update:visible":function(t){e.dialogEditUser=t}}},[n("el-form",{attrs:{"label-width":"80px"}},[n("el-form-item",{attrs:{label:"账号："}},[n("el-input",{model:{value:e.rowInfo.mobile,callback:function(t){e.$set(e.rowInfo,"mobile",t)},expression:"rowInfo.mobile"}})],1),e._v(" "),n("el-tabs",{model:{value:e.edidWallet,callback:function(t){e.edidWallet=t},expression:"edidWallet"}},e._l(e.rowInfo.walletData,function(t,a){return n("el-tab-pane",{key:a,attrs:{label:t.name,name:t.name}},[n("p",[e._v("收款地址")]),e._v(" "),n("el-input",{model:{value:t.address,callback:function(a){e.$set(t,"address",a)},expression:"item.address"}})],1)}))],1),e._v(" "),n("span",{attrs:{slot:"footer"},slot:"footer"},[n("el-button",{attrs:{type:"primary"},on:{click:e.editUser}},[e._v("确 认")]),e._v(" "),n("el-button",{on:{click:function(t){e.dialogEditUser=!1}}},[e._v("取 消")])],1)],1)],1)},[],!1,null,"383939f4",null);m.options.__file="index.vue";t.default=m.exports}}]);