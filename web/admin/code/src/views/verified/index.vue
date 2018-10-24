<template>
  <div class="app-container" @click.self="showInfo=false">
    <el-radio-group v-model="checkType" class="radioTabs" @change="changeCheckType">
      <el-radio-button label="待审核"/>
      <el-radio-button label="已通过"/>
      <el-radio-button label="未通过"/>
    </el-radio-group>
    <el-button class="btn-right" style="margin-left:10px;" @click="searchData">查询</el-button>
    <el-input v-model="search" placeholder="姓名/手机号/身份证号" suffix-icon="el-icon-search" class="btn-right" style="width:200px;"/>
    <br>

    已选择<span style="color:#3e84e9;display:inline-block;margin-top:20px;">{{ tableDataSelection.length }}</span>项
    <el-button v-show="checkTypetoNum==0" :disabled="(tableDataSelection.length<1)" size="small" type="primary" plain @click="allDoomPass">通过</el-button>

    <el-table
      :data="tableDataPage"
      style="margin:10px 0;"
      @selection-change="handleSelectionChange"
      @row-click="clickRow">
      <el-table-column type="selection" width="55"/>
      <el-table-column prop="mobile" label="手机号"/>
      <el-table-column prop="realname" label="姓名"/>
      <el-table-column prop="number" label="身份证号"/>
      <el-table-column prop="status" label="状态"/>
      <el-table-column prop="createTime" label="提交时间"/>
      <el-table-column v-if="checkTypetoNum!=0" prop="examineTime" label="审核时间"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="total"
      :page-size="pageSize"
      layout="total, prev, pager, next, jumper"/>

    <transition name="fade">
      <div v-show="showInfo" class="fade-slide">
        <div class="title">
          <img src="@/assets/img/user.jpg" alt="">
          <span class="name">{{ rowInfo.realname }}<br><span>{{ checkType }}</span></span>
          <i class="el-icon-close btn" @click="showInfo=false"/>
          <el-button v-show="checkType=='待审核'" type="danger" plain class="btn" style="margin:0 10px;" @click="doomFail">不通过</el-button>
          <el-button v-show="checkType=='待审核'" type="primary" class="btn" @click="doomPass">通过</el-button>
        </div>
        <p v-show="checkTypetoNum==2">未通过原因：{{ rowDetail.statusRemark }}</p>
        <p style="margin-top:50px;">
          <span style="margin-right:150px;">姓名：{{ rowInfo.realname }}</span>
          <span>身份证号：{{ rowInfo.number }}</span>
        </p>
        <p>手持身份证正面</p>
        <img :src="rowDetail.picFront" alt="" class="image">
        <p>手持身份证背面</p>
        <img :src="rowDetail.picBack" alt="" class="image" style="margin-bottom:50px;">
      </div>
    </transition>
  </div>
</template>

<script>
import { getList, getDetail, passVerified, failVerified } from '@/api/verified'
import { Message } from 'element-ui'
import { pagination } from '@/utils'

export default {
  name: 'Verified',
  data() {
    return {
      checkType: '待审核',
      search: '',
      tableData: [],
      tableDataSelection: [],
      currentPage: 1,
      pageSize: 20,
      showInfo: false,
      rowInfo: [],
      rowDetail: []
    }
  },
  computed: {
    total() {
      return this.tableData.length
    },
    tableDataPage() {
      return pagination(this.tableData, this.currentPage, this.pageSize)
    },
    checkTypetoNum() {
      if (this.checkType === '待审核') {
        return 0
      } else if (this.checkType === '已通过') {
        return 1
      } else if (this.checkType === '未通过') {
        return 2
      }
    }
  },
  created() {
    getList(this.checkTypetoNum).then(res => {
      this.tableData = res.content.list
    })
  },
  methods: {
    // 切换审核数据类型
    changeCheckType() {
      this.showInfo = false
      getList(this.checkTypetoNum).then(res => {
        this.tableData = res.content.list
      })
    },
    // 选择table
    handleSelectionChange(val) {
      this.tableDataSelection = val
    },
    // 搜索
    searchData() {
      getList(this.checkTypetoNum, this.search).then(res => {
        this.tableData = res.content.list
      })
    },
    // 点击表格行
    clickRow(row) {
      this.rowInfo = row
      this.showInfo = true
      getDetail(row.id).then(res => {
        this.rowDetail = res.content
      })
    },
    // 通过
    doomPass() {
      passVerified(this.rowInfo.id).then(res => {
        this.showInfo = false
        Message({ message: res.msg, type: 'success' })
        getList(this.checkTypetoNum).then(res => {
          this.tableData = res.content.list
        })
      })
    },
    // 批量通过
    allDoomPass() {
      if (this.tableDataSelection.length < 1) return
      this.$confirm('确定全部通过吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        let allId = ''
        this.tableDataSelection.map((item, index, items) => {
          allId = allId + ',' + item.id
        })
        passVerified(allId.replace(',', '')).then(res => {
          this.showInfo = false
          Message({ message: res.msg, type: 'success' })
          getList(this.checkTypetoNum).then(res => {
            this.tableData = res.content.list
          })
        })
      })
    },
    // 不通过
    doomFail() {
      this.$prompt('请填写不通过原因', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消'
      }).then(({ value }) => {
        failVerified(this.rowInfo.id, value).then(res => {
          this.showInfo = false
          Message({ message: res.msg, type: 'success' })
          getList(this.checkTypetoNum).then(res => {
            this.tableData = res.content.list
          })
        })
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.btn-right {
  margin-top: -39px;
}

.image {
  display: block;
  height: 400px;
  border: 1px solid #ddd;
}
</style>
