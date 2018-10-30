<template>
  <div class="app-container">
    <h4 style="display:inline-block;">财务流水</h4>
    <el-button class="btn-right" @click="addExcel">导出excel</el-button>
    <br>

    <el-input v-model="search" clearable placeholder="用户" style="margin-top:20px;width:300px;" @keyup.enter.native="searchTableData">
      <el-button slot="append" icon="el-icon-search" @click.native="searchTableData"/>
    </el-input>
    <div style="float:right;margin-top:20px;">
      <el-select v-model="moneyType" clearable placeholder="币种" style="width:100px;" @change="searchTableData">
        <el-option
          v-for="(item,index) in allMoneyType"
          :key="index"
          :label="item.name"
          :value="item.id"/>
      </el-select>
      <el-select v-model="dataType" clearable placeholder="全部" style="width:100px;" @change="searchTableData">
        <el-option
          v-for="(item,index) in allAmount"
          :key="index"
          :label="item.label"
          :value="item.value"/>
      </el-select>
      <el-date-picker
        v-model="date"
        type="datetimerange"
        range-separator="至"
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        format="yyyy 年 MM 月 dd 日"
        value-format="yyyy-MM-dd"
        @change="searchTableData"/>
    </div>
    <br>

    <el-table :data="tableDataPage" style="margin:10px 0;">
      <el-table-column prop="id" label="流水号"/>
      <el-table-column prop="mobile" label="用户"/>
      <el-table-column prop="name" label="币种"/>
      <el-table-column prop="type2" label="收支"/>
      <el-table-column prop="type" label="类型"/>
      <el-table-column prop="amount" label="数量"/>
      <el-table-column prop="status" label="状态"/>
      <el-table-column prop="createTime" label="时间"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="total"
      :page-size="20"
      layout="total, prev, pager, next, jumper"
      @current-change="changePage"/>
  </div>
</template>

<script>
import { getRuningList, getMoneyType } from '@/api/assets'
import { parseTime, pagination } from '@/utils'

export default {
  name: 'Finance',
  data() {
    return {
      search: '',
      allMoneyType: [],
      moneyType: '',
      allAmount: [
        { value: 1, label: '收入' },
        { value: 2, label: '支出' }
      ],
      dataType: '',
      date: '',
      tableData: [],
      tableDataPage: [],
      currentPage: 1
    }
  },
  computed: {
    total() {
      return this.tableData.length
    }
  },
  created() {
    getRuningList().then(res => {
      this.tableData = res.content.list
      this.tableDataPage = pagination(this.tableData, this.currentPage, 20)
    })
    getMoneyType().then(res => {
      this.allMoneyType = res.content
    })
  },
  methods: {
    // 变页数
    changePage(page) {
      this.tableDataPage = pagination(this.tableData, page, 20)
    },
    // 主表格搜索
    searchTableData() {
      if (this.date === null) this.date = ''
      getRuningList(this.search, this.moneyType, this.dataType, this.date[0], this.date[1]).then(res => {
        this.tableData = res.content.list
      }).then(() => {
        this.tableDataPage = pagination(this.tableData, this.currentPage, 20)
      })
    },
    // 导出excel
    addExcel() {
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['流水号', '用户', '币种', '收支', '类型', '数量', '状态', '时间']
        const filterVal = ['id', 'mobile', 'name', 'type2', 'type', 'amount', 'status', 'createTime']
        const list = this.tableData
        const data = this.formatJson(filterVal, list)
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: '财务流水'
        })
      })
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => {
        if (j === 'timestamp') {
          return parseTime(v[j])
        } else {
          return v[j]
        }
      }))
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>

</style>
