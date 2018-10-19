<template>
  <div class="app-container" @click.self="showInfo=false">
    <el-radio-group v-model="checkType" class="radioTabs" @change="changeCheckType">
      <el-radio-button label="待审核"/>
      <el-radio-button label="已通过"/>
      <el-radio-button label="未通过"/>
    </el-radio-group>
    <el-button class="btn-right" style="margin-left:10px;" @click="searchData">查询</el-button>
    <el-input v-model="search" placeholder="节点名称/手机号" suffix-icon="el-icon-search" class="btn-right" style="width:200px;"/>
    <br>

    已选择<span style="color:#3e84e9;">{{ tableDataSelection.length }}</span>项
    <el-button size="small" style="margin-top:20px;" @click="allPass">通过</el-button>

    <el-table
      :data="tableDataPage"
      style="margin:10px 0;"
      @selection-change="handleSelectionChange"
      @row-click="clickRow">
      <el-table-column type="selection" width="55"/>
      <el-table-column prop="sss" label="节点名称"/>
      <el-table-column prop="title" label="手机号"/>
      <el-table-column prop="click" label="质押资产"/>
      <el-table-column label="状态">
        <template slot-scope="scope">
          <div v-if="scope.row.status==1">上架</div>
          <div v-else>下架</div>
        </template>
      </el-table-column>
      <el-table-column prop="updateTime" label="提交时间"/>
      <el-table-column prop="updateTime" label="审核时间"/>
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
          <span class="name">{{ rowInfo.title }}<br><span>{{ checkType }}</span></span>
          <i class="el-icon-close btn" @click="showInfo=false"/>
          <el-button type="danger" class="btn" style="margin:0 10px;">删除</el-button>
          <el-button type="primary" class="btn">通过</el-button>
          <el-button type="primary" class="btn">不通过</el-button>
          <el-button type="primary" class="btn">删除记录</el-button>
        </div>
        <p style="color:#888;">logo</p>
        <img :src="rowInfo.logo" alt="" style="display:block;width:100px;height:100px;border:1px solid #ddd;">
        <p style="color:#888;margin-top:50px;">机构/个人名称</p>
        <p>{{ rowInfo.name }}</p>
        <p style="color:#888;margin-top:50px;">机构/个人简介</p>
        <p>{{ rowInfo.desc }}</p>
        <p style="color:#888;margin-top:50px;">社区建设方案</p>
        <p>{{ rowInfo.scheme }}</p>
      </div>
    </transition>

    <!-- <el-dialog :visible.sync="dialogEdit" title="节点编辑" class="node-edit">
      <div class="item">
        <div class="title">基本信息</div>
        <img :src="nodeInfoBase.logo" alt="">
        <el-upload
          :show-file-list="false"
          :on-success="handleAvatarSuccess"
          :data="{type:'logo'}"
          name="image_file"
          action="http://admin.contest_system.local/upload/upload/image">
          <el-button style="margin-top:30px;">更换logo</el-button>
        </el-upload>
      </div>
      <div class="item">
        <div class="title">机构/个人名称</div>
        <el-input v-model="nodeInfoBase.name"/>
      </div>
      <div class="item">
        <div class="title">机构/个人简介</div>
        <el-input v-model="nodeInfoBase.desc" :rows="2" type="textarea"/>
      </div>
      <div class="item">
        <div class="title">社区建设方案</div>
        <el-input v-model="nodeInfoBase.scheme" :rows="2" type="textarea"/>
      </div>
      <span slot="footer">
        <el-button type="primary" @click="editNodeBase">确 定</el-button>
        <el-button @click="dialogEdit = false">取 消</el-button>
      </span>
    </el-dialog> -->
  </div>
</template>

<script>
import { getCheckList, checkPass, checkFail } from '@/api/nodeCheck'
import { Message } from 'element-ui'
import { pagination } from '@/utils'

export default {
  name: 'NodeCheck',
  data() {
    return {
      checkType: '待审核',
      search: '',
      tableData: [],
      tableDataSelection: [],
      currentPage: 1,
      pageSize: 20,
      showInfo: false,
      rowInfo: []
    }
  },
  computed: {
    total() {
      return this.tableData.length
    },
    tableDataPage() {
      return pagination(this.tableData, this.currentPage, this.pageSize)
    },
    noticeChecktoNum() {
      if (this.checkType === '待审核') {
        return 2
      } else if (this.checkType === '已通过') {
        return 1
      } else if (this.checkType === '未通过') {
        return 4
      }
    }
  },
  created() {
    getCheckList(this.noticeChecktoNum).then(res => {
      this.tableData = res.content
    })
  },
  methods: {
    // 切换审核数据类型
    changeCheckType() {
      this.showInfo = false
      getCheckList(this.noticeChecktoNum).then(res => {
        this.tableData = res.content
      })
    },
    // 选择table
    handleSelectionChange(val) {
      this.tableDataSelection = val
    },
    // 搜索
    searchData() {
      getCheckList(this.noticeChecktoNum, this.search).then(res => {
        this.tableData = res.content
      })
    },
    // 点击表格行
    clickRow(row) {
      this.rowInfo = row
      this.showInfo = true
    },
    // 通过审核
    // 批量通过审核
    allPass() {
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
        checkPass(allId.replace(',', '')).then(res => {
          Message({ message: res.msg, type: 'success' })
          getCheckList(this.noticeChecktoNum).then(res => {
            this.tableData = res.content
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
</style>
