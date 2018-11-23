<template>
  <div class="app-container" @click.self="showInfo=false">
    <h4 style="display:inline-block;">成员列表</h4>
    <el-button class="btn-right" @click="openDialog">新建管理员</el-button>
    <br>

    <el-input v-model="searchData" clearable placeholder="姓名/手机号" style="margin-top:20px;width:300px;" @change="search">
      <el-button slot="append" icon="el-icon-search" @click.native="search"/>
    </el-input>

    <el-table :data="tableData" :row-class-name="tableRowClassName" style="margin:10px 0;" @row-click="clickRow">
      <el-table-column prop="realName" label="姓名"/>
      <el-table-column prop="name" label="账号"/>
      <el-table-column prop="mobile" label="手机号"/>
      <el-table-column prop="roleName" label="角色"/>
      <el-table-column prop="createTime" label="注册时间"/>
      <el-table-column prop="lastLoginTime" label="最后一次登录时间"/>
      <el-table-column prop="status" label="状态"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="parseInt(total)"
      :page-size="20"
      layout="total, prev, pager, next, jumper"
      @current-change="init"/>

    <transition name="fade">
      <div v-show="showInfo" class="fade-slide">
        <div class="title">
          <img src="@/assets/img/user.jpg" alt="">
          <span style="line-height:45px;padding-left:10px;">{{ rowInfo.realName }}</span>
          <i class="el-icon-close btn" @click="showInfo=false"/>
          <el-button v-show="rowInfo.status=='正常'" type="danger" plain class="btn" style="margin:0 10px;" @click="changeStatus(0)">停用</el-button>
          <el-button v-show="rowInfo.status=='停用'" type="primary" plain class="btn" style="margin:0 10px;" @click="changeStatus(1)">启用</el-button>
          <el-button type="primary" class="btn" @click="openDialog('edit')">编辑</el-button>
        </div>
        <br>
        <div class="row">
          <div>
            <span>姓名</span>
            <span>{{ rowInfo.realName }}</span>
          </div>
          <div>
            <span>部门</span>
            <span>{{ rowInfo.department }}</span>
          </div>
        </div>
        <div class="row">
          <div>
            <span>手机号</span>
            <span>{{ rowInfo.mobile }}</span>
          </div>
          <div>
            <span>角色</span>
            <span>{{ rowInfo.roleName }}</span>
          </div>
        </div>
        <div class="row">
          <div>
            <span>账号</span>
            <span>{{ rowInfo.name }}</span>
          </div>
          <div>
            <span>状态</span>
            <span>{{ rowInfo.status }}</span>
          </div>
        </div>
      </div>
    </transition>

    <el-dialog :visible.sync="dialog" title="编辑管理员" @closed="resetForm">
      <el-form ref="memberForm" :model="form" :rules="rules" label-width="60px">
        <el-form-item label="姓名" prop="realName">
          <el-input v-model="form.realName"/>
        </el-form-item>
        <el-form-item label="部门" prop="department">
          <el-input v-model="form.department"/>
        </el-form-item>
        <el-form-item label="手机号" prop="mobile">
          <el-input v-model="form.mobile"/>
        </el-form-item>
        <el-form-item label="角色" prop="roleId">
          <el-select v-model="form.roleId">
            <el-option
              v-for="item in roleList"
              :key="item.id"
              :label="item.name"
              :value="item.id"/>
          </el-select>
        </el-form-item>
        <el-form-item label="账号" prop="name">
          <el-input v-model="form.name"/>
        </el-form-item>
        <el-form-item label="状态" prop="status">
          <el-select v-model="form.status">
            <el-option
              v-for="item in statusOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"/>
          </el-select>
        </el-form-item>
      </el-form>
      <span slot="footer">
        <el-button type="primary" @click="commitMember">确认</el-button>
        <el-button @click="dialog = false">取 消</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getMemList, addMen, editMen, getRoleList, onMen, offMen } from '@/api/system'
import { Message } from 'element-ui'

export default {
  name: 'Member',
  data() {
    return {
      searchData: '',
      tableData: [],
      total: 1,
      currentPage: 1,
      showInfo: false,
      rowIndex: '',
      dialog: false,
      roleList: [],
      statusOptions: [
        { value: '1', label: '正常' },
        { value: '0', label: '停用' }
      ],
      form: {
        realName: '',
        department: '',
        mobile: '',
        roleId: '2',
        name: '',
        status: '1',
        id: ''
      },
      rules: {
        realName: [{ }],
        department: [{ }],
        mobile: [
          { pattern: /^1\d{10}$/, message: '请输入正确的手机号码', trigger: 'blur' }
        ],
        roleId: [{ }],
        name: [
          { required: true, message: '请输入账号', trigger: 'blur' }
        ],
        status: [{ }]
      }
    }
  },
  computed: {
    rowInfo() {
      if (this.rowIndex === '') return []
      else return this.tableData[this.rowIndex]
    }
  },
  created() {
    this.init()
  },
  methods: {
    init() {
      getMemList(this.currentPage, this.searchData).then(res => {
        this.tableData = res.content.data
        this.total = res.content.count
      })
    },
    search() {
      this.currentPage = 1
      this.init()
    },
    // 点击表格行
    clickRow(row) {
      this.rowIndex = row.index
      this.showInfo = true
    },
    // 表格添加index
    tableRowClassName({ row, rowIndex }) {
      row.index = rowIndex
    },
    // 打开新增||编辑弹窗
    openDialog(type) {
      getRoleList().then(res => {
        this.roleList = res.content
        if (type === 'edit') {
          this.form.realName = this.rowInfo.realName
          this.form.department = this.rowInfo.department
          this.form.mobile = this.rowInfo.mobile
          this.form.roleId = this.rowInfo.roleId
          this.form.name = this.rowInfo.name
          this.form.status = this.rowInfo.status
          this.form.id = this.rowInfo.id
        }
        this.dialog = true
      })
    },
    // 关闭弹出框重置表单
    resetForm() {
      this.form.realName = ''
      this.form.department = ''
      this.form.mobile = ''
      this.form.roleId = '2'
      this.form.name = ''
      this.form.status = '1'
      this.form.id = ''
    },
    commitMember() {
      this.$refs['memberForm'].validate((valid) => {
        if (valid) {
          if (this.form.id) {
            if (this.form.status === '正常') this.form.status = '1'
            if (this.form.status === '停用') this.form.status = '0'
            editMen(this.form).then(res => {
              this.dialog = false
              Message({ message: res.msg, type: 'success' })
              this.init()
            })
          } else {
            addMen(this.form).then(res => {
              this.dialog = false
              Message({ message: res.msg, type: 'success' })
              this.init()
            })
          }
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    changeStatus(type) {
      if (type === 0) {
        offMen(this.rowInfo.id).then(res => {
          this.init()
          Message({ message: res.msg, type: 'success' })
        })
      } else {
        onMen(this.rowInfo.id).then(res => {
          this.init()
          Message({ message: res.msg, type: 'success' })
        })
      }
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.fade-slide {
  .row {
    display: flex;
    margin-top: 30px;
    > div {
      flex: 1;
      span {
        display: block;
        margin-top: 10px;
      }
      span:nth-child(1) {
        color: #888;
      }
    }
  }
}
</style>
