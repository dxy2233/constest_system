(window.webpackJsonp=window.webpackJsonp||[]).push([["chunk-d6c1"],{"9Zt7":function(t,e,i){"use strict";i.r(e);var n=i("bS4n"),c=i.n(n),s=i("hZMg"),a=i("hrJr"),o=i("8t5x"),l={name:"Purview",data:function(){return{roleList:[],activeRole:"",activeIndex:"",rolePurviewList:[],checkList:[]}},computed:c()({},Object(o.b)(["buttons"])),watch:{activeIndex:function(t,e){if(null!==this.roleList[t].ruleList){var i=this.roleList[t].ruleList;this.checkList=JSON.parse(i)}else this.checkList=[]}},created:function(){this.init()},methods:{init:function(){var t=this;Object(s.f)().then(function(e){t.roleList=e.content,t.activeRole=t.roleList[0].id,t.activeIndex=0,Object(s.g)(t.roleList[0].id).then(function(e){t.rolePurviewList=e.content})})},changeCheckValue:function(t,e){var i=this;if(!t){var n=[];this.rolePurviewList.forEach(function(t,i,c){t.id===e&&t.child.forEach(function(t,e,i){n.push(t.id)})}),n.forEach(function(t,e,n){for(var c=0;c<i.checkList.length;c++)i.checkList[c]===t&&i.checkList.splice(c,1)})}},saveRolePurview:function(){Object(s.k)(this.activeRole,this.checkList).then(function(t){Object(a.Message)({message:t.msg,type:"success"})})},addRole:function(){var t=this;this.$prompt("角色名称","新建角色",{confirmButtonText:"保存",cancelButtonText:"取消",inputPlaceholder:"最长8个中文字符",inputPattern:/^[\u4e00-\u9fa5]{1,8}$/,inputErrorMessage:"最长8个中文字符"}).then(function(e){var i=e.value;Object(s.j)(i).then(function(e){Object(a.Message)({message:e.msg,type:"success"}),t.init()})})},rename:function(){var t=this;this.$prompt("","重命名",{confirmButtonText:"保存",cancelButtonText:"取消",inputValue:this.roleList[this.activeIndex].name,inputPlaceholder:"最长8个中文字符",inputPattern:/^[\u4e00-\u9fa5]{1,8}$/,inputErrorMessage:"最长8个中文字符"}).then(function(e){var i=e.value;Object(s.l)(t.activeRole,i).then(function(e){Object(a.Message)({message:e.msg,type:"success"}),Object(s.f)().then(function(e){t.roleList=e.content})})})},deleteRole:function(){var t=this;this.$confirm("确认删除?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){Object(s.b)(t.activeRole).then(function(e){Object(a.Message)({message:e.msg,type:"success"}),t.init()})})}}},r=(i("dkx1"),i("ZrdR")),u=Object(r.a)(l,function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"app-container"},[i("h4",{staticStyle:{display:"inline-block"}},[t._v("权限管理")]),t._v(" "),i("el-button",{staticClass:"btn-right",on:{click:t.addRole}},[t._v("新建角色")]),t._v(" "),i("div",{staticClass:"content"},[i("div",{staticClass:"menus"},t._l(t.roleList,function(e,n){return i("el-button",{key:n,class:{active:n==t.activeIndex},on:{click:function(i){t.activeRole=e.id,t.activeIndex=n}}},[t._v(t._s(e.name))])})),t._v(" "),i("div",{staticClass:"info"},[i("h4",{staticStyle:{display:"inline-block","margin-right":"20px"}},[t._v("管理员设置")]),t._v(" "),i("el-button",{directives:[{name:"show",rawName:"v-show",value:"1"!=t.activeRole&&"2"!=t.activeRole,expression:"activeRole!='1' && activeRole!='2'"}],attrs:{size:"small"},on:{click:t.rename}},[t._v("重命名")]),t._v(" "),1==t.buttons[38].child[0].isHave?i("el-button",{staticStyle:{float:"right","margin-top":"10px"},attrs:{size:"small"},on:{click:t.saveRolePurview}},[t._v("保存")]):t._e(),t._v(" "),i("el-button",{directives:[{name:"show",rawName:"v-show",value:"1"!=t.activeRole&&"2"!=t.activeRole,expression:"activeRole!='1' && activeRole!='2'"}],staticStyle:{float:"right","margin-top":"10px"},attrs:{type:"danger",size:"small"},on:{click:t.deleteRole}},[t._v("删除角色")]),t._v(" "),i("el-checkbox-group",{model:{value:t.checkList,callback:function(e){t.checkList=e},expression:"checkList"}},t._l(t.rolePurviewList,function(e,n){return i("div",{key:n,staticClass:"checkbox"},[i("div",{staticClass:"title"},[i("el-checkbox",{attrs:{label:e.id},on:{change:function(i){t.changeCheckValue(i,e.id)}}},[t._v(t._s(e.name))])],1),t._v(" "),i("div",{staticClass:"detail"},t._l(e.child,function(n,c){return i("el-checkbox",{key:c,attrs:{label:n.id,disabled:-1==t.checkList.indexOf(e.id)}},[t._v(t._s(n.name))])}))])}))],1)])],1)},[],!1,null,"3767b3b4",null);u.options.__file="index.vue";e.default=u.exports},PtTI:function(t,e,i){},dkx1:function(t,e,i){"use strict";var n=i("PtTI");i.n(n).a}}]);