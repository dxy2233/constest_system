<template>
  <div class="app-container">
    <h4 style="display:inline-block;">权限管理</h4>
    <el-button class="btn-right" @click="addRole">新建角色</el-button>

    <div class="content">
      <div class="menus">
        <el-button
          v-for="(item, index) in roleList"
          :key="index"
          :class="{active:index==activeIndex}"
          @click="activeRole=item.id;activeIndex=index">{{ item.name }}</el-button>
      </div>
      <div class="info">
        <h4 style="display:inline-block;margin-right:20px;">管理员设置</h4>
        <el-button v-show="activeRole!='1' && activeRole!='2'" size="small" @click="rename">重命名</el-button>
        <el-button v-if="buttons[38].child[0].isHave==1" size="small" style="float:right;margin-top:10px;" @click="saveRolePurview">保存</el-button>
        <el-button v-show="activeRole!='1' && activeRole!='2'" type="danger" size="small" style="float:right;margin-top:10px;" @click="deleteRole">删除角色</el-button>
        <el-checkbox-group v-model="checkList">
          <div v-for="(item,index) in rolePurviewList" :key="index" class="checkbox">
            <div class="title">
              <el-checkbox :label="item.id" @change="changeCheckValue($event, item.id)">{{ item.name }}</el-checkbox>
            </div>
            <div class="detail">
              <el-checkbox v-for="(item2,index2) in item.child" :key="index2" :label="item2.id" :disabled="checkList.indexOf(item.id)==-1">{{ item2.name }}</el-checkbox>
            </div>
          </div>
        </el-checkbox-group>
      </div>
    </div>
  </div>
</template>

<script>
import { getRoleList, getRolePurview, postRolePurview, deleteRolePurview, putRoleName, postRole } from '@/api/system'
import { Message } from 'element-ui'
import { mapGetters } from 'vuex'

export default {
  name: 'Purview',
  data() {
    return {
      roleList: [],
      activeRole: '',
      activeIndex: '',
      rolePurviewList: [],
      checkList: []
    }
  },
  computed: {
    ...mapGetters([
      'buttons'
    ])
  },
  watch: {
    activeIndex(newName, oldName) {
      if (this.roleList[newName].ruleList === null) {
        this.checkList = []
        return
      }
      // var temList = this.roleList[newName - 1].ruleList.substring(1, this.roleList[newName - 1].ruleList.length - 1)
      // this.checkList = temList.split(',')
      var temList = this.roleList[newName].ruleList
      this.checkList = JSON.parse(temList)
    }
  },
  created() {
    this.init()
  },
  methods: {
    init() {
      getRoleList().then(res => {
        this.roleList = res.content
        this.activeRole = this.roleList[0].id
        this.activeIndex = 0
        getRolePurview(this.roleList[0].id).then(res => {
          this.rolePurviewList = res.content
        })
      })
    },
    // 页面权限取消后取消button权限
    changeCheckValue(isHave, id) {
      if (!isHave) {
        var temList = []
        this.rolePurviewList.forEach((item, index, arr) => {
          if (item.id === id) {
            item.child.forEach((item, index, arr) => {
              temList.push(item.id)
            })
          }
          return
        })
        temList.forEach((item, index, arr) => {
          for (var i = 0; i < this.checkList.length; i++) {
            if (this.checkList[i] === item) {
              this.checkList.splice(i, 1)
            }
          }
        })
      }
    },
    // 保存设置
    saveRolePurview() {
      postRolePurview(this.activeRole, this.checkList).then(res => {
        Message({ message: res.msg, type: 'success' })
      })
    },
    addRole() {
      this.$prompt('角色名称', '新建角色', {
        confirmButtonText: '保存',
        cancelButtonText: '取消',
        inputPlaceholder: '最长8个中文字符',
        inputPattern: /^[\u4e00-\u9fa5]{1,8}$/,
        inputErrorMessage: '最长8个中文字符'
      }).then(({ value }) => {
        postRole(value).then(res => {
          Message({ message: res.msg, type: 'success' })
          this.init()
        })
      })
    },
    rename() {
      this.$prompt('', '重命名', {
        confirmButtonText: '保存',
        cancelButtonText: '取消',
        inputValue: this.roleList[this.activeIndex].name,
        inputPlaceholder: '最长8个中文字符',
        inputPattern: /^[\u4e00-\u9fa5]{1,8}$/,
        inputErrorMessage: '最长8个中文字符'
      }).then(({ value }) => {
        putRoleName(this.activeRole, value).then(res => {
          Message({ message: res.msg, type: 'success' })
          getRoleList().then(res => {
            this.roleList = res.content
          })
        })
      })
    },
    // 删除角色
    deleteRole() {
      this.$confirm('确认删除?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        deleteRolePurview(this.activeRole).then(res => {
          Message({ message: res.msg, type: 'success' })
          this.init()
        })
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.content {
  display: flex;
  margin-top: 20px;
  .menus {
    display: flex;
    flex-flow: column;
    margin-right: 20px;
    .active {
      background: #409eff;
      color: #fff;
    }
    button {
      margin-left: 0;
    }
  }
  .info {
    border: 1px solid #ddd;
    width: 100%;
    padding: 10px 20px;
    .checkbox {
      margin-bottom: 20px;
      border: 1px solid #ddd;
      .title {
        padding: 10px 20px;
        background: #ddd;
      }
      .detail {
        padding: 10px 20px;
        height: 40px;
      }
    }
  }
}
</style>
