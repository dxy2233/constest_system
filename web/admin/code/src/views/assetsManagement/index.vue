<template>
  <div class="app-container">
    <h4 style="display:inline-block;">资产管理</h4>
    <el-button class="btn-right" type="primary" @click="openLock">锁仓记录</el-button>
    <el-button class="btn-right" style="margin-right:10px;" @click="downExcel">导出excel</el-button>
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
      <el-select v-model="amount" clearable placeholder="总额" style="width:100px;" @change="searchTableData">
        <el-option
          v-for="(item,index) in allAmount"
          :key="index"
          :label="item.label"
          :value="item.value"/>
      </el-select>
      <span style="margin-left:20px;">数量</span>
      <el-input v-model="min" clearable placeholder="最小" style="width:100px;" @change="searchTableData"/>
      ——
      <el-input v-model="max" clearable placeholder="最大" style="width:100px;" @change="searchTableData"/>
    </div>
    <br>

    <el-table :data="tableData" style="margin:10px 0;">
      <el-table-column prop="mobile" label="用户"/>
      <el-table-column prop="name" label="币种"/>
      <el-table-column prop="positionAmount" label="总额"/>
      <el-table-column prop="useAmount" label="可用"/>
      <el-table-column prop="frozenAmount" label="锁仓"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="parseInt(total)"
      :page-size="20"
      layout="total, prev, pager, next, jumper"
      @current-change="changePage"/>

    <el-dialog :visible.sync="dialogLock" title="锁仓记录">
      <el-input v-model="searchLockData" clearable placeholder="用户" style="width:150px;" @keyup.enter.native="searchLock">
        <el-button slot="append" icon="el-icon-search" @click.native="searchLock"/>
      </el-input>
      <el-select v-model="lockMoneyType" clearable placeholder="币种" style="width:100px;" @change="searchLock">
        <el-option
          v-for="(item,index) in allMoneyType"
          :key="index"
          :label="item.name"
          :value="item.id"/>
      </el-select>
      <el-button style="float:right;" @click="downLockExcel">导出excel</el-button>
      <div style="margin-top:20px;">
        <span>时间</span>
        <el-date-picker
          v-model="lockDate"
          type="daterange"
          range-separator="至"
          start-placeholder="开始日期"
          end-placeholder="结束日期"
          format="yyyy 年 MM 月 dd 日"
          value-format="yyyy-MM-dd"
          style="width:400px;"
          @change="searchLock"/>
      </div>
      <el-table :data="lockTableData" style="margin:10px 0;">
        <el-table-column prop="mobile" label="用户"/>
        <el-table-column prop="name" label="币种"/>
        <el-table-column prop="amount" label="数量"/>
        <el-table-column prop="remark" label="描述"/>
        <el-table-column prop="createTime" label="时间"/>
      </el-table>
      <el-pagination
        :current-page.sync="lockCurrentPage"
        :total="parseInt(lockTotal)"
        :page-size="20"
        layout="total, prev, pager, next, jumper"
        @current-change="changeLockPage"/>
    </el-dialog>
  </div>
</template>

<script>
import { getFinanceList, getMoneyType, getLockList } from '@/api/assets'
import { getVerifiCode } from '@/api/public'

export default {
  name: 'AssetsManagement',
  data() {
    return {
      search: '',
      allMoneyType: [],
      moneyType: '',
      allAmount: [
        { value: 2, label: '锁仓' },
        { value: 3, label: '可用' }
      ],
      amount: '',
      min: '',
      max: '',
      tableData: [],
      total: 1,
      currentPage: 1,
      dialogLock: false,
      searchLockData: '',
      lockMoneyType: '',
      lockDate: '',
      lockTableData: [],
      lockTotal: 1,
      lockCurrentPage: 1
    }
  },
  created() {
    getMoneyType().then(res => {
      this.allMoneyType = res.content
    })
    getFinanceList(this.search, this.moneyType, this.amount, this.min, this.max, null, 1).then(res => {
      this.tableData = res.content.list
      this.total = res.content.count
    })
  },
  methods: {
    // 变页数
    changePage(page) {
      getFinanceList(this.search, this.moneyType, this.amount, this.min, this.max, null, page).then(res => {
        this.tableData = res.content.list
        this.total = res.content.count
      })
    },
    // 主表格搜索
    searchTableData() {
      this.currentPage = 1
      getFinanceList(this.search, this.moneyType, this.amount, this.min, this.max, null, 1).then(res => {
        this.tableData = res.content.list
        this.total = res.content.count
      })
    },
    // 打开锁仓记录
    openLock() {
      getLockList(null, null, null, null, 1).then(res => {
        this.lockTableData = res.content.list
        this.lockTotal = res.content.count
        this.dialogLock = true
      })
    },
    changeLockPage(page) {
      getLockList(this.searchLockData, this.lockMoneyType, this.lockDate[0], this.lockDate[1], page).then(res => {
        this.lockTableData = res.content.list
        this.lockTotal = res.content.count
      })
    },
    // 锁仓记录搜索
    searchLock() {
      if (this.lockDate === null) this.lockDate = ''
      this.lockCurrentPage = 1
      getLockList(this.searchLockData, this.lockMoneyType, this.lockDate[0], this.lockDate[1], 1).then(res => {
        this.lockTableData = res.content.list
        this.lockTotal = res.content.count
      })
    },
    // 下载excel
    downExcel() {
      getVerifiCode().then(res => {
        var url = `/finance/download?download_code=${res.content}&searchName=${this.search}&currency_id=${this.moneyType}&type=${this.amount}&min=${this.min}&max=${this.max}`
        const elink = document.createElement('a')
        elink.style.display = 'none'
        elink.href = url
        document.body.appendChild(elink)
        elink.click()
        document.body.removeChild(elink)
      })
    },
    downLockExcel() {
      if (this.lockDate) {
        var str = this.lockDate[0]
        var end = this.lockDate[1]
      } else {
        str = ''
        end = ''
      }
      getVerifiCode().then(res => {
        var url = `/finance/frozen-download?download_code=${res.content}&searchName=${this.searchLockData}&currency_id=${this.lockMoneyType}&str_time=${str}&end_time=${end}`
        const elink = document.createElement('a')
        elink.style.display = 'none'
        elink.href = url
        document.body.appendChild(elink)
        elink.click()
        document.body.removeChild(elink)
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>

</style>
