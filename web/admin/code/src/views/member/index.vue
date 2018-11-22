<template>
  <div class="app-container">
    <h4 style="display:inline-block;">成员列表</h4>
    <el-button class="btn-right">新建管理员</el-button>
    <br>

    <el-input v-model="searchData" clearable placeholder="姓名/手机号" style="margin-top:20px;width:300px;" @change="search">
      <el-button slot="append" icon="el-icon-search" @click.native="search"/>
    </el-input>

    <el-table :data="tableData" style="margin:10px 0;" @sort-change="sortChange">
      <el-table-column prop="mobile" label="姓名"/>
      <el-table-column prop="name" label="账号"/>
      <el-table-column prop="voteNumber" label="手机号"/>
      <el-table-column prop="type" label="角色" sortable="custom"/>
      <el-table-column prop="createTime" label="注册时间"/>
      <el-table-column prop="createTime" label="最后一次登录时间"/>
      <el-table-column prop="createTime" label="状态"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="parseInt(total)"
      :page-size="20"
      layout="total, prev, pager, next, jumper"
      @current-change="init"/>
  </div>
</template>

<script>
export default {
  name: 'Member',
  data() {
    return {
      searchData: '',
      tableData: [],
      total: 1,
      currentPage: 1
    }
  },
  methods: {
    init() {

    },
    search() {
      console.log(this.searchData);
    },
    sortChange() {

    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>

</style>
