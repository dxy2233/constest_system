(window.webpackJsonp=window.webpackJsonp||[]).push([["chunk-3591"],{"/umX":function(t,e,a){"use strict";e.__esModule=!0;var n=function(t){return t&&t.__esModule?t:{default:t}}(a("9dlP"));e.default=function(t,e,a){return e in t?(0,n.default)(t,e,{value:a,enumerable:!0,configurable:!0,writable:!0}):t[e]=a,t}},"6HaC":function(t,e,a){"use strict";a.d(e,"a",function(){return o});var n=a("t3Un");function o(){return Object(n.a)({url:"/download/get-download-code",method:"post"})}},"9dlP":function(t,e,a){t.exports={default:a("I1YO"),__esModule:!0}},ACJA:function(t,e,a){"use strict";var n=a("fzjc");a.n(n).a},I1YO:function(t,e,a){a("NKWc");var n=a("fz6b").Object;t.exports=function(t,e,a){return n.defineProperty(t,e,a)}},NKWc:function(t,e,a){var n=a("yFux");n(n.S+n.F*!a("RqZd"),"Object",{defineProperty:a("anRQ").f})},Np98:function(t,e,a){"use strict";a.r(e);var n=a("6ZY3"),o=a.n(n),l=a("/umX"),i=a.n(l),r=a("bS4n"),c=a.n(r),d=a("YReu"),s=a.n(d),p=a("t3Un");function u(){return Object(p.a)({url:"/cycle/index",method:"post"})}var m=a("6HaC"),h=a("hrJr"),v={name:"PollManagement",data:function(){var t=this;return{search:"",searchDate:"",tableData:[],total:1,currentPage:1,order:null,dialogSet:!1,dialogSetData:[],pushSetData:[],dialogRank:!1,dialogRankAllType:[{value:0,label:"全部"},{value:1,label:"普通投票"},{value:2,label:"支付投票"},{value:3,label:"投票券"}],dialogRankType:0,dialogRankData:[],dialogRankDataPage:[],rankTotal:1,dialogRankDate:"",rankCurrentPage:1,dialogCamp:!1,dialogCampData:[],dialogAddCamp:!1,addCampForm:{camp:"",hold:"",id:""},addCampFormRules:{camp:[{required:!0,message:"请选择日期",trigger:"change"}],hold:[{validator:function(e,a,n){""===a||null===a?n(new Error("请输入日期")):new Date(t.addCampForm.hold[0]).getTime()<new Date(t.addCampForm.camp[1]).getTime()?n(new Error("任职日期必须在竞选结束后!")):n()},required:!0,trigger:"change"}]},dialogCampHistoryData:[],campTotal:1,campCurrentPage:1}},created:function(){this.init()},methods:{init:function(){var t=this;(function(t,e,a,n,o){return Object(p.a)({url:"/vote/index",method:"post",data:{searchName:t,page:e,str_time:a,end_time:n,order:o}})})(this.search,this.currentPage,this.searchDate[0],this.searchDate[1],this.order).then(function(e){t.tableData=e.content.list,t.total=e.content.count})},sortChange:function(t){this.currentPage=1,null===t.prop?this.order=null:"voteNumber"===t.prop&&"ascending"===t.order?this.order=1:"voteNumber"===t.prop&&"descending"===t.order?this.order=4:"type"===t.prop&&"ascending"===t.order?this.order=2:"type"===t.prop&&"descending"===t.order?this.order=5:"createTime"===t.prop&&"ascending"===t.order?this.order=3:"createTime"===t.prop&&"descending"===t.order&&(this.order=6),this.init()},searchTableData:function(){null===this.searchDate&&(this.searchDate=""),this.currentPage=1,this.init()},openVoteSet:function(){var t=this;Object(p.a)({url:"/vote/get-setting-list",method:"post"}).then(function(e){t.dialogSetData=e.content,t.pushSetData=[],t.dialogSetData.forEach(function(e,a,n){t.pushSetData.push(i()({},e.key,e.value.toString()))})}),this.dialogSet=!0},saveVoteSet:function(){var t=this,e={};this.pushSetData.map(function(t,a,n){o()(e,t)}),function(t){var e=s()(t,[]);return Object(p.a)({url:"/vote/set-vote",method:"post",data:c()({},e)})}(e).then(function(e){Object(h.Message)({message:e.msg,type:"success"}),t.dialogSet=!1})},initRank:function(){var t=this;(function(t,e,a){return Object(p.a)({url:"/vote/get-vote-order",method:"post",data:{end_time:t,type:e,page:a}})})(this.dialogRankDate,this.dialogRankType,this.rankCurrentPage).then(function(e){t.dialogRankData=e.content.list,t.rankTotal=e.content.count})},initCampHistory:function(){var t=this;(function(t){return Object(p.a)({url:"/cycle/history",method:"post",data:{page:t}})})(this.campCurrentPage).then(function(e){t.dialogCampHistoryData=e.content.list,t.campTotal=e.content.count})},openCamp:function(){var t=this;u().then(function(e){t.dialogCampData=e.content,t.dialogCamp=!0}),this.initCampHistory()},saveCamp:function(){var t=this;this.$refs.camp.validate(function(e){if(!e)return console.log("error submit!!"),!1;""===t.addCampForm.id?function(t,e,a,n){return Object(p.a)({url:"/cycle/create-cycle",method:"post",data:{cycle_start_time:t,cycle_end_time:e,tenure_start_time:a,tenure_end_time:n}})}(t.addCampForm.camp[0],t.addCampForm.camp[1],t.addCampForm.hold[0],t.addCampForm.hold[1]).then(function(e){Object(h.Message)({message:e.msg,type:"success"}),u().then(function(e){t.dialogCampData=e.content,t.dialogAddCamp=!1})}):function(t,e,a,n,o){return Object(p.a)({url:"/cycle/update-cycle",method:"post",data:{id:t,cycle_start_time:e,cycle_end_time:a,tenure_start_time:n,tenure_end_time:o}})}(t.addCampForm.id,t.addCampForm.camp[0],t.addCampForm.camp[1],t.addCampForm.hold[0],t.addCampForm.hold[1]).then(function(e){Object(h.Message)({message:e.msg,type:"success"}),u().then(function(e){t.dialogCampData=e.content,t.dialogAddCamp=!1})})})},openEditCamp:function(t){this.addCampForm.id=this.dialogCampData[t].id,this.addCampForm.camp=[],this.addCampForm.camp[0]=this.dialogCampData[t].cycleStartTime,this.addCampForm.camp[1]=this.dialogCampData[t].cycleEndTime,this.addCampForm.hold=[],this.addCampForm.hold[0]=this.dialogCampData[t].tenureStartTime,this.addCampForm.hold[1]=this.dialogCampData[t].tenureEndTime,this.dialogAddCamp=!0},delCamp:function(t){var e=this;this.$confirm("确定删除吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){(function(t){return Object(p.a)({url:"/cycle/del",method:"post",data:{id:t}})})(t).then(function(t){Object(h.Message)({message:t.msg,type:"success"}),u().then(function(t){e.dialogCampData=t.content})})})},downExcel:function(){var t=this;if(this.searchDate)var e=this.searchDate[0],a=this.searchDate[1];else e="",a="";Object(m.a)().then(function(n){var o="/vote/download?download_code="+n.content+"&searchName="+t.search+"&str_time="+e+"&end_time="+a,l=document.createElement("a");l.style.display="none",l.target="_blank",l.href=o,document.body.appendChild(l),l.click(),document.body.removeChild(l)})},downRankExcel:function(){var t=this;Object(m.a)().then(function(e){var a="/vote/vote-order-download?download_code="+e.content+"&type="+t.dialogRankType+"&end_time="+t.dialogRankDate,n=document.createElement("a");n.style.display="none",n.target="_blank",n.href=a,document.body.appendChild(n),n.click(),document.body.removeChild(n)})}}},g=(a("ACJA"),a("ZrdR")),_=Object(g.a)(v,function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"app-container"},[a("h4",{staticStyle:{display:"inline-block"}},[t._v("投票管理")]),t._v(" "),a("el-button",{staticClass:"btn-right",on:{click:function(e){t.initRank(),t.dialogRank=!0}}},[t._v("投票排名")]),t._v(" "),a("el-button",{staticClass:"btn-right",staticStyle:{"margin-right":"10px"},attrs:{type:"primary"},on:{click:t.openVoteSet}},[t._v("投票设置")]),t._v(" "),a("el-button",{staticClass:"btn-right",on:{click:t.openCamp}},[t._v("竞选设置")]),t._v(" "),a("el-button",{staticClass:"btn-right",on:{click:t.downExcel}},[t._v("导出excel")]),t._v(" "),a("br"),t._v(" "),a("el-input",{staticStyle:{"margin-top":"20px",width:"300px"},attrs:{clearable:"",placeholder:"用户/节点名称"},on:{change:t.searchTableData},model:{value:t.search,callback:function(e){t.search=e},expression:"search"}},[a("el-button",{attrs:{slot:"append",icon:"el-icon-search"},nativeOn:{click:function(e){return t.searchTableData(e)}},slot:"append"})],1),t._v(" "),a("div",{staticStyle:{float:"right","margin-top":"20px"}},[t._v("\n    投票时间\n    "),a("el-date-picker",{staticStyle:{width:"400px"},attrs:{type:"daterange","range-separator":"至","start-placeholder":"开始日期","end-placeholder":"结束日期",format:"yyyy 年 MM 月 dd 日","value-format":"yyyy-MM-dd"},on:{change:t.searchTableData},model:{value:t.searchDate,callback:function(e){t.searchDate=e},expression:"searchDate"}})],1),t._v(" "),a("br"),t._v(" "),a("el-table",{staticStyle:{margin:"10px 0"},attrs:{data:t.tableData},on:{"sort-change":t.sortChange}},[a("el-table-column",{attrs:{prop:"mobile",label:"投票用户"}}),t._v(" "),a("el-table-column",{attrs:{prop:"name",label:"投票节点"}}),t._v(" "),a("el-table-column",{attrs:{prop:"voteNumber",label:"投出票数",sortable:"custom"}}),t._v(" "),a("el-table-column",{attrs:{prop:"type",label:"投票方式",sortable:"custom"}}),t._v(" "),a("el-table-column",{attrs:{prop:"createTime",label:"投票时间",sortable:"custom"}})],1),t._v(" "),a("el-pagination",{attrs:{"current-page":t.currentPage,total:parseInt(t.total),"page-size":20,layout:"total, prev, pager, next, jumper"},on:{"update:currentPage":function(e){t.currentPage=e},"current-change":t.init}}),t._v(" "),a("el-dialog",{staticClass:"dialog-set",attrs:{visible:t.dialogSet,title:"投票设置"},on:{"update:visible":function(e){t.dialogSet=e}}},[t._l(t.dialogSetData,function(e,n){return a("div",{key:n},["radio"==e.type?a("div",{staticClass:"switch"},[a("span",[t._v(t._s(e.name))]),t._v(" "),a("el-switch",{attrs:{"active-value":"1","inactive-value":"0"},model:{value:t.pushSetData[n][e.key],callback:function(a){t.$set(t.pushSetData[n],e.key,a)},expression:"pushSetData[index][item.key]"}})],1):t._e(),t._v(" "),"text"==e.type?a("div",{staticClass:"txt"},[a("span",[t._v(t._s(e.name)+t._s(t.pushSetData[n][e.key]))])]):t._e(),t._v(" "),"input"==e.type?a("div",{staticClass:"item"},[a("span",{staticClass:"title"},[t._v(t._s(e.name))]),t._v(" "),a("el-input",{staticStyle:{width:"200px"},model:{value:t.pushSetData[n][e.key],callback:function(a){t.$set(t.pushSetData[n],e.key,a)},expression:"pushSetData[index][item.key]"}}),t._v(" "),a("span",[t._v(t._s(e.remark))])],1):t._e()])}),t._v(" "),a("span",{attrs:{slot:"footer"},slot:"footer"},[a("el-button",{attrs:{type:"primary"},on:{click:t.saveVoteSet}},[t._v("确认修改")]),t._v(" "),a("el-button",{on:{click:function(e){t.dialogSet=!1}}},[t._v("取 消")])],1)],2),t._v(" "),a("el-dialog",{attrs:{visible:t.dialogCamp,fullscreen:!0,title:"竞选设置"},on:{"update:visible":function(e){t.dialogCamp=e}}},[t._l(t.dialogCampData,function(e,n){return a("div",{key:n},[a("h3",{staticStyle:{display:"inline-block"}},[t._v("投票竞选"+t._s(n+1))]),t._v(" "),a("el-button",{staticStyle:{float:"right"},attrs:{type:"danger",plain:""},on:{click:function(a){t.delCamp(e.id)}}},[t._v("删除")]),t._v(" "),a("el-button",{staticStyle:{float:"right","margin-right":"20px"},on:{click:function(e){t.openEditCamp(n)}}},[t._v("编辑")]),t._v(" "),a("br"),t._v(" "),a("div",{staticClass:"camp-info"},[a("div",[a("span",[t._v("竞选开始时间")]),t._v(" "),a("span",[t._v(t._s(e.cycleStartTime))])]),t._v(" "),a("div",[a("span",[t._v("竞选截止时间")]),t._v(" "),a("span",[t._v(t._s(e.cycleEndTime))])]),t._v(" "),a("div",[a("span",[t._v("任职开始时间")]),t._v(" "),a("span",[t._v(t._s(e.tenureStartTime))])]),t._v(" "),a("div",[a("span",[t._v("任职到期时间")]),t._v(" "),a("span",[t._v(t._s(e.tenureEndTime))])])])],1)}),t._v(" "),a("el-button",{staticStyle:{"margin-top":"20px"},on:{click:function(e){t.dialogAddCamp=!0}}},[t._v("+新增竞选投票")]),t._v(" "),a("el-dialog",{attrs:{visible:t.dialogAddCamp,title:"新增竞选投票",center:"","append-to-body":""},on:{"update:visible":function(e){t.dialogAddCamp=e},closed:function(e){t.addCampForm.camp="",t.addCampForm.hold="",t.addCampForm.id=""}}},[a("h3",[t._v("竞选投票")]),t._v(" "),a("el-form",{ref:"camp",attrs:{model:t.addCampForm,rules:t.addCampFormRules,"label-position":"top","label-width":"80px"}},[a("el-form-item",{attrs:{label:"竞选开始时间——竞选截止时间",prop:"camp"}},[a("el-date-picker",{staticStyle:{width:"100%"},attrs:{type:"datetimerange","range-separator":"至","start-placeholder":"竞选开始时间","end-placeholder":"竞选截止时间",format:"yyyy 年 MM 月 dd 日 HH:mm","value-format":"yyyy-MM-dd HH:mm"},model:{value:t.addCampForm.camp,callback:function(e){t.$set(t.addCampForm,"camp",e)},expression:"addCampForm.camp"}})],1),t._v(" "),a("el-form-item",{attrs:{label:"任职开始时间——任职截止时间",prop:"hold"}},[a("el-date-picker",{staticStyle:{width:"100%"},attrs:{type:"datetimerange","range-separator":"至","start-placeholder":"竞选开始时间","end-placeholder":"竞选截止时间",format:"yyyy 年 MM 月 dd 日 HH:mm","value-format":"yyyy-MM-dd HH:mm"},model:{value:t.addCampForm.hold,callback:function(e){t.$set(t.addCampForm,"hold",e)},expression:"addCampForm.hold"}})],1)],1),t._v(" "),a("span",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{on:{click:function(e){t.dialogAddCamp=!1}}},[t._v("取 消")]),t._v(" "),a("el-button",{attrs:{type:"primary"},on:{click:t.saveCamp}},[t._v("确 定")])],1)],1),t._v(" "),a("p",[t._v("历史记录")]),t._v(" "),a("el-table",{staticStyle:{margin:"10px 0"},attrs:{data:t.dialogCampHistoryData}},[a("el-table-column",{attrs:{prop:"id",label:"序号"}}),t._v(" "),a("el-table-column",{attrs:{prop:"cycleStartTime",label:"竞选开始时间"}}),t._v(" "),a("el-table-column",{attrs:{prop:"cycleEndTime",label:"竞选截止时间"}}),t._v(" "),a("el-table-column",{attrs:{prop:"tenureStartTime",label:"任职时间"}}),t._v(" "),a("el-table-column",{attrs:{prop:"tenureEndTime",label:"到期时间"}})],1),t._v(" "),a("el-pagination",{attrs:{"current-page":t.campCurrentPage,total:parseInt(t.campTotal),"page-size":20,layout:"total, prev, pager, next, jumper"},on:{"update:currentPage":function(e){t.campCurrentPage=e},"current-change":t.initCampHistory}})],2),t._v(" "),a("el-dialog",{attrs:{visible:t.dialogRank,title:"投票排名"},on:{"update:visible":function(e){t.dialogRank=e},closed:function(e){t.rankCurrentPage=1,t.dialogRankType=0,t.dialogRankDate=""}}},[a("el-select",{on:{change:function(e){t.rankCurrentPage=1,t.initRank()}},model:{value:t.dialogRankType,callback:function(e){t.dialogRankType=e},expression:"dialogRankType"}},t._l(t.dialogRankAllType,function(t,e){return a("el-option",{key:e,attrs:{label:t.label,value:t.value}})})),t._v(" "),a("el-button",{staticStyle:{float:"right"},on:{click:t.downRankExcel}},[t._v("导出excel")]),t._v(" "),a("div",{staticStyle:{"margin-top":"20px"}},[t._v("\n      截止时间\n      "),a("el-date-picker",{attrs:{type:"date",placeholder:"选择日期时间",format:"yyyy 年 MM 月 dd 日","value-format":"yyyy-MM-dd"},on:{change:function(e){t.rankCurrentPage=1,t.initRank()}},model:{value:t.dialogRankDate,callback:function(e){t.dialogRankDate=e},expression:"dialogRankDate"}})],1),t._v(" "),a("el-table",{staticStyle:{margin:"10px 0"},attrs:{data:t.dialogRankData}},[a("el-table-column",{attrs:{prop:"order",label:"排名"}}),t._v(" "),a("el-table-column",{attrs:{prop:"mobile",label:"账号"}}),t._v(" "),a("el-table-column",{attrs:{prop:"num",label:"票数"}}),t._v(" "),a("el-table-column",{attrs:{label:"方式"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n          "+t._s(t.dialogRankAllType[t.dialogRankType].label)+"\n        ")]}}])})],1),t._v(" "),a("el-pagination",{attrs:{"current-page":t.rankCurrentPage,total:parseInt(t.rankTotal),"page-size":20,layout:"total, prev, pager, next, jumper"},on:{"update:currentPage":function(e){t.rankCurrentPage=e},"current-change":t.initRank}})],1)],1)},[],!1,null,"ee79aa3e",null);_.options.__file="index.vue";e.default=_.exports},fzjc:function(t,e,a){}}]);