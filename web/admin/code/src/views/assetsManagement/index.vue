<template>
  <div class="app-container">
    <h4 style="display:inline-block;">资产管理</h4>
    <el-button class="btn-right" @click="openLock">锁仓记录</el-button>
    <el-button class="btn-right" style="margin-right:10px;" @click="addExcel">导出excel</el-button>
    <br>

    <el-input v-model="search" placeholder="用户/节点名称" suffix-icon="el-icon-search" style="margin-top:20px;width:300px;"/>
    <div style="float:right;margin-top:20px;">
      <el-select v-model="moneyType" clearable placeholder="币种" style="width:100px;">
        <el-option
          v-for="(item,index) in allMoneyType"
          :key="index"
          :label="item.name"
          :value="item.id"/>
      </el-select>
      <el-select v-model="amount" clearable placeholder="总额" style="width:100px;">
        <el-option
          v-for="(item,index) in allAmount"
          :key="index"
          :label="item.label"
          :value="item.value"/>
      </el-select>
      <span style="margin-left:20px;">数量</span>
      <el-input v-model="min" placeholder="最小" style="width:100px;"/>
      ——
      <el-input v-model="max" placeholder="最大" style="width:100px;"/>
      <el-button @click="searchTableData">查询</el-button>
    </div>
    <br>

    <el-table :data="tableDataPage" style="margin:10px 0;">
      <el-table-column prop="mobile" label="用户"/>
      <el-table-column prop="wallet" label="钱包"/>
      <el-table-column prop="walletId" label="币种"/>
      <el-table-column prop="positionAmount" label="总额"/>
      <el-table-column prop="useAmount" label="可用"/>
      <el-table-column prop="frozenAmount" label="锁仓"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="total"
      :page-size="20"
      layout="total, prev, pager, next, jumper"
      @current-change="changePage"/>

    <el-dialog :visible.sync="dialogLock" title="锁仓记录">
      <el-input v-model="searchLockData" placeholder="用户" suffix-icon="el-icon-search" style="width:150px;"/>
      <el-select v-model="lockMoneyType" clearable placeholder="币种" style="width:100px;">
        <el-option
          v-for="(item,index) in allMoneyType"
          :key="index"
          :label="item.name"
          :value="item.id"/>
      </el-select>
      <el-button style="float:right;" @click="addExcelLock">导出excel</el-button>
      <div style="margin-top:20px;">
        <span>时间</span>
        <el-date-picker
          v-model="lockDate"
          type="datetimerange"
          range-separator="至"
          start-placeholder="开始日期"
          end-placeholder="结束日期"
          format="yyyy 年 MM 月 dd 日 HH：mm"
          value-format="yyyy-MM-dd HH:mm"
          style="width:500px;"/>
        <el-button @click="searchLock">查询</el-button>
      </div>
      <el-table :data="lockTableDataPage" style="margin:10px 0;">
        <el-table-column prop="mobile" label="用户"/>
        <el-table-column prop="wallet" label="钱包"/>
        <el-table-column prop="name" label="币种"/>
        <el-table-column prop="amount" label="数量"/>
        <el-table-column prop="remark" label="描述"/>
        <el-table-column prop="createTime" label="时间"/>
      </el-table>
      <el-pagination
        :current-page.sync="lockCurrentPage"
        :total="lockTotal"
        :page-size="20"
        layout="total, prev, pager, next, jumper"
        @current-change="changeLockPage"/>
    </el-dialog>
  </div>
</template>

<script>
import { getFinanceList, getMoneyType, getLockList } from '@/api/assets'
import { parseTime, pagination } from '@/utils'

export default {
  name: 'AssetsManagement',
  data() {
    return {
      search: '',
      allMoneyType: [],
      moneyType: '',
      allAmount: [
        { value: 2, label: '冻结' },
        { value: 3, label: '可用' }
      ],
      amount: '',
      min: '',
      max: '',
      tableData: [],
      tableDataPage: [],
      currentPage: 1,
      dialogLock: false,
      searchLockData: '',
      lockMoneyType: '',
      lockDate: '',
      lockTableData: [],
      lockTableDataPage: [],
      lockCurrentPage: 1
    }
  },
  computed: {
    total() {
      return this.tableData.length
    },
    lockTotal() {
      return this.lockTableData.length
    }
  },
  created() {
    getFinanceList().then(res => {
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
      getFinanceList(this.search, this.moneyType, this.amount, this.min, this.max).then(res => {
        this.tableData = res.content.list
        this.tableDataPage = pagination(this.tableData, this.currentPage, 20)
      })
    },
    // 打开锁仓记录
    openLock() {
      getLockList().then(res => {
        this.lockTableData = res.content.list
        this.lockTableDataPage = pagination(this.lockTableData, this.lockCurrentPage, 20)
        this.dialogLock = true
      })
    },
    changeLockPage(page) {
      this.lockTableDataPage = pagination(this.lockTableData, page, 20)
    },
    // 锁仓记录搜索
    searchLock() {
      getLockList(this.searchLockData, this.lockMoneyType, this.lockDate[0], this.lockDate[1]).then(res => {
        this.lockTableData = res.content.list
        this.lockTableDataPage = pagination(this.lockTableData, this.lockCurrentPage, 20)
      })
    },
    // 导出excel
    addExcel() {
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['用户', '钱包', '币种', '总额', '可用', '锁仓']
        const filterVal = ['mobile', 'wallet', 'walletId', 'positionAmount', 'useAmount', 'frozenAmount']
        const list = this.tableData
        const data = this.formatJson(filterVal, list)
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: '资产管理'
        })
      })
    },
    addExcelLock() {
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['用户', '钱包', '币种', '数量', '描述', '时间']
        const filterVal = ['mobile', 'wallet', 'name', 'amount', 'remark', 'createTime']
        const list = this.lockTableData
        const data = this.formatJson(filterVal, list)
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: '锁仓记录'
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
