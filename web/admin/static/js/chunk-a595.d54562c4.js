(window.webpackJsonp=window.webpackJsonp||[]).push([["chunk-a595"],{"/umX":function(t,e,a){"use strict";e.__esModule=!0;var n=function(t){return t&&t.__esModule?t:{default:t}}(a("9dlP"));e.default=function(t,e,a){return e in t?(0,n.default)(t,e,{value:a,enumerable:!0,configurable:!0,writable:!0}):t[e]=a,t}},"6HaC":function(t,e,a){"use strict";a.d(e,"a",function(){return o});var n=a("t3Un");function o(){return Object(n.a)({url:"/download/get-download-code",method:"post"})}},"9dlP":function(t,e,a){t.exports={default:a("I1YO"),__esModule:!0}},AfML:function(t,e,a){"use strict";var n=a("gAsn");a.n(n).a},I1YO:function(t,e,a){a("NKWc");var n=a("fz6b").Object;t.exports=function(t,e,a){return n.defineProperty(t,e,a)}},NKWc:function(t,e,a){var n=a("yFux");n(n.S+n.F*!a("RqZd"),"Object",{defineProperty:a("anRQ").f})},Np98:function(t,e,a){"use strict";a.r(e);var n=a("6ZY3"),o=a.n(n),l=a("/umX"),i=a.n(l),r=a("bS4n"),s=a.n(r),c=a("YReu"),u=a.n(c),d=a("t3Un");var p=a("6HaC"),h=a("hrJr"),v={name:"PollManagement",data:function(){return{search:"",searchDate:"",tableData:[],total:1,currentPage:1,order:null,dialogSet:!1,dialogSetData:[],pushSetData:[],dialogRank:!1,dialogRankAllType:[{value:0,label:"全部"},{value:1,label:"普通投票"},{value:2,label:"支付投票"},{value:3,label:"投票券"}],dialogRankType:0,dialogRankData:[],dialogRankDataPage:[],rankTotal:1,dialogRankDate:"",rankCurrentPage:1,showTimeOver:null}},created:function(){this.init()},methods:{init:function(){var t=this;(function(t,e,a,n,o){return Object(d.a)({url:"/vote/index",method:"post",data:{searchName:t,page:e,str_time:a,end_time:n,order:o}})})(this.search,this.currentPage,this.searchDate[0],this.searchDate[1],this.order).then(function(e){t.tableData=e.content.list,t.total=e.content.count})},sortChange:function(t){this.currentPage=1,null===t.prop?this.order=null:"voteNumber"===t.prop&&"ascending"===t.order?this.order=1:"voteNumber"===t.prop&&"descending"===t.order?this.order=4:"type"===t.prop&&"ascending"===t.order?this.order=2:"type"===t.prop&&"descending"===t.order?this.order=5:"createTime"===t.prop&&"ascending"===t.order?this.order=3:"createTime"===t.prop&&"descending"===t.order&&(this.order=6),this.init()},searchTableData:function(){null===this.searchDate&&(this.searchDate=""),this.currentPage=1,this.init()},openVoteSet:function(){var t=this;Object(d.a)({url:"/vote/get-setting-list",method:"post"}).then(function(e){t.dialogSetData=e.content,t.pushSetData=[],t.dialogSetData.forEach(function(e,a,n){t.pushSetData.push(i()({},e.key,e.value.toString())),"stop_vote"===e.key&&(t.showTimeOver=e.value)})}),this.dialogSet=!0},changeSwitch:function(t){this.showTimeOver=t},manuakStop:function(t){var e=new Date,a=e.getFullYear()+"-"+(e.getMonth()+1)+"-"+e.getDate()+" "+e.toLocaleTimeString("chinese",{hour12:!1});this.pushSetData.map(function(t,e,n){t.hasOwnProperty("end_update_time")&&(n[e].end_update_time=a)}),Object(d.a)({url:"/vote/now-reload",method:"post"})},saveVoteSet:function(){var t=this,e={};this.pushSetData.map(function(t,a,n){o()(e,t)}),function(t){var e=u()(t,[]);return Object(d.a)({url:"/vote/set-vote",method:"post",data:s()({},e)})}(e).then(function(e){Object(h.Message)({message:e.msg,type:"success"}),t.dialogSet=!1})},initRank:function(){var t=this;(function(t,e,a){return Object(d.a)({url:"/vote/get-vote-order",method:"post",data:{end_time:t,type:e,page:a}})})(this.dialogRankDate,this.dialogRankType,this.rankCurrentPage).then(function(e){t.dialogRankData=e.content.list,t.rankTotal=e.content.count})},downExcel:function(){var t=this;if(this.searchDate)var e=this.searchDate[0],a=this.searchDate[1];else e="",a="";Object(p.a)().then(function(n){var o="/vote/download?download_code="+n.content+"&searchName="+t.search+"&str_time="+e+"&end_time="+a,l=document.createElement("a");l.style.display="none",l.target="_blank",l.href=o,document.body.appendChild(l),l.click(),document.body.removeChild(l)})},downRankExcel:function(){var t=this;Object(p.a)().then(function(e){var a="/vote/vote-order-download?download_code="+e.content+"&type="+t.dialogRankType+"&end_time="+t.dialogRankDate,n=document.createElement("a");n.style.display="none",n.target="_blank",n.href=a,document.body.appendChild(n),n.click(),document.body.removeChild(n)})}}},g=(a("AfML"),a("ZrdR")),m=Object(g.a)(v,function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"app-container"},[a("h4",{staticStyle:{display:"inline-block"}},[t._v("投票管理")]),t._v(" "),a("el-button",{staticClass:"btn-right",on:{click:function(e){t.initRank(),t.dialogRank=!0}}},[t._v("投票排名")]),t._v(" "),a("el-button",{staticClass:"btn-right",staticStyle:{"margin-right":"10px"},attrs:{type:"primary"},on:{click:t.openVoteSet}},[t._v("投票设置")]),t._v(" "),a("el-button",{staticClass:"btn-right",on:{click:t.downExcel}},[t._v("导出excel")]),t._v(" "),a("br"),t._v(" "),a("el-input",{staticStyle:{"margin-top":"20px",width:"300px"},attrs:{clearable:"",placeholder:"用户/节点名称"},on:{change:t.searchTableData},model:{value:t.search,callback:function(e){t.search=e},expression:"search"}},[a("el-button",{attrs:{slot:"append",icon:"el-icon-search"},nativeOn:{click:function(e){return t.searchTableData(e)}},slot:"append"})],1),t._v(" "),a("div",{staticStyle:{float:"right","margin-top":"20px"}},[t._v("\n    投票时间\n    "),a("el-date-picker",{staticStyle:{width:"400px"},attrs:{type:"daterange","range-separator":"至","start-placeholder":"开始日期","end-placeholder":"结束日期",format:"yyyy 年 MM 月 dd 日","value-format":"yyyy-MM-dd"},on:{change:t.searchTableData},model:{value:t.searchDate,callback:function(e){t.searchDate=e},expression:"searchDate"}})],1),t._v(" "),a("br"),t._v(" "),a("el-table",{staticStyle:{margin:"10px 0"},attrs:{data:t.tableData},on:{"sort-change":t.sortChange}},[a("el-table-column",{attrs:{prop:"mobile",label:"投票用户"}}),t._v(" "),a("el-table-column",{attrs:{prop:"name",label:"投票节点"}}),t._v(" "),a("el-table-column",{attrs:{prop:"voteNumber",label:"投出票数",sortable:"custom"}}),t._v(" "),a("el-table-column",{attrs:{prop:"type",label:"投票方式",sortable:"custom"}}),t._v(" "),a("el-table-column",{attrs:{prop:"createTime",label:"投票时间",sortable:"custom"}})],1),t._v(" "),a("el-pagination",{attrs:{"current-page":t.currentPage,total:parseInt(t.total),"page-size":20,layout:"total, prev, pager, next, jumper"},on:{"update:currentPage":function(e){t.currentPage=e},"current-change":t.init}}),t._v(" "),a("el-dialog",{staticClass:"dialog-set",attrs:{visible:t.dialogSet,title:"投票设置"},on:{"update:visible":function(e){t.dialogSet=e}}},[t._l(t.dialogSetData,function(e,n){return a("div",{key:n},["radio"==e.type?a("div",{staticClass:"switch"},[a("span",[t._v(t._s(e.name))]),t._v(" "),a("el-switch",{attrs:{"active-value":"1","inactive-value":"0"},on:{change:t.changeSwitch},model:{value:t.pushSetData[n][e.key],callback:function(a){t.$set(t.pushSetData[n],e.key,a)},expression:"pushSetData[index][item.key]"}})],1):t._e(),t._v(" "),"text"==e.type?a("div",{staticClass:"txt"},[a("span",[t._v(t._s(e.name)+t._s(t.pushSetData[n][e.key]))])]):t._e(),t._v(" "),"time"==e.type&&1==t.showTimeOver?a("div",{staticClass:"time"},[a("span",[t._v(t._s(e.name))]),t._v(" "),a("el-date-picker",{staticStyle:{width:"250px"},attrs:{type:"datetime",format:"yyyy 年 MM 月 dd 日 HH:mm:ss","value-format":"yyyy-MM-dd HH:mm:ss",placeholder:"选择日期时间"},model:{value:t.pushSetData[n][e.key],callback:function(a){t.$set(t.pushSetData[n],e.key,a)},expression:"pushSetData[index][item.key]"}}),t._v(" "),a("el-button",{staticStyle:{float:"right"},on:{click:t.manuakStop}},[t._v("手动截止")])],1):t._e(),t._v(" "),"input"==e.type?a("div",{staticClass:"item"},[a("span",{staticClass:"title"},[t._v(t._s(e.name))]),t._v(" "),a("el-input",{staticStyle:{width:"200px"},model:{value:t.pushSetData[n][e.key],callback:function(a){t.$set(t.pushSetData[n],e.key,a)},expression:"pushSetData[index][item.key]"}}),t._v(" "),a("span",[t._v(t._s(e.remark))])],1):t._e()])}),t._v(" "),a("span",{attrs:{slot:"footer"},slot:"footer"},[a("el-button",{attrs:{type:"primary"},on:{click:t.saveVoteSet}},[t._v("确认修改")]),t._v(" "),a("el-button",{on:{click:function(e){t.dialogSet=!1}}},[t._v("取 消")])],1)],2),t._v(" "),a("el-dialog",{attrs:{visible:t.dialogRank,title:"投票排名"},on:{"update:visible":function(e){t.dialogRank=e},closed:function(e){t.rankCurrentPage=1,t.dialogRankType=0,t.dialogRankDate=""}}},[a("el-select",{on:{change:function(e){t.rankCurrentPage=1,t.initRank()}},model:{value:t.dialogRankType,callback:function(e){t.dialogRankType=e},expression:"dialogRankType"}},t._l(t.dialogRankAllType,function(t,e){return a("el-option",{key:e,attrs:{label:t.label,value:t.value}})})),t._v(" "),a("el-button",{staticStyle:{float:"right"},on:{click:t.downRankExcel}},[t._v("导出excel")]),t._v(" "),a("div",{staticStyle:{"margin-top":"20px"}},[t._v("\n      截止时间\n      "),a("el-date-picker",{attrs:{type:"date",placeholder:"选择日期时间",format:"yyyy 年 MM 月 dd 日","value-format":"yyyy-MM-dd"},on:{change:function(e){t.rankCurrentPage=1,t.initRank()}},model:{value:t.dialogRankDate,callback:function(e){t.dialogRankDate=e},expression:"dialogRankDate"}})],1),t._v(" "),a("el-table",{ref:"multipleTable",staticStyle:{margin:"10px 0"},attrs:{data:t.dialogRankData}},[a("el-table-column",{attrs:{prop:"order",label:"排名"}}),t._v(" "),a("el-table-column",{attrs:{prop:"mobile",label:"账号"}}),t._v(" "),a("el-table-column",{attrs:{prop:"num",label:"票数"}}),t._v(" "),a("el-table-column",{attrs:{label:"方式"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n          "+t._s(t.dialogRankAllType[t.dialogRankType].label)+"\n        ")]}}])})],1),t._v(" "),a("el-pagination",{attrs:{"current-page":t.rankCurrentPage,total:parseInt(t.rankTotal),"page-size":20,layout:"total, prev, pager, next, jumper"},on:{"update:currentPage":function(e){t.rankCurrentPage=e},"current-change":t.initRank}})],1)],1)},[],!1,null,"8758b3aa",null);m.options.__file="index.vue";e.default=m.exports},gAsn:function(t,e,a){}}]);