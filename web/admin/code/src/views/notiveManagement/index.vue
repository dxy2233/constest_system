<template>
  <div class="app-container">
    <el-radio-group v-model="notiveTpye" class="radioTabs">
      <el-radio-button label="已上架"/>
      <el-radio-button label="下架中"/>
      <el-radio-button label="全 部"/>
    </el-radio-group>
    <el-button class="btn-right">公告发布</el-button>
    <el-button class="btn-right" style="margin-right:10px;">公告设置</el-button>

    已选择<span style="color:#3e84e9;">{{ tableDataSelection.length }}</span>项
    <el-button size="small" type="primary" style="margin-top:20px;">删除</el-button>

    <el-table
      ref="notiveTable"
      :data="tableData"
      style="width: 100%;margin:10px 0;"
      @selection-change="handleSelectionChange"
      @row-click="clickRow">
      <el-table-column type="selection" width="55"/>
      <el-table-column prop="username" label="展示图"/>
      <el-table-column prop="userType" label="公告标题"/>
      <el-table-column prop="nodeName" label="点击量"/>
      <el-table-column label="状态">
        <template slot-scope="scope">
          <div v-if="scope.row.status==1">上架</div>
          <div v-else>下架</div>
        </template>
      </el-table-column>
      <el-table-column prop="create_time" label="排序"/>
      <el-table-column prop="last_login_time" label="发布时间"/>
      <el-table-column prop="last_login_time" label="最近一次修改时间"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="total"
      :page-size="20"
      layout="total, prev, pager, next, jumper"/>
  </div>
</template>

<script>
export default {
  name: 'NotiveManagement',
  data() {
    return {
      notiveTpye: '已上架',
      tableData: [],
      tableDataSelection: [],
      currentPage: 1,
      total: 1
    }
  },
  created() {

  },
  methods: {
    handleSelectionChange(val) {
      this.tableDataSelection = val
    },
    clickRow(row) {
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.btn-right {
  margin-top: -39px;
}
</style>
