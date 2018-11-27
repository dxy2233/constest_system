<template>
  <div class="app-container">
    <h4 style="display:inline-block;">财务流水</h4>
    <el-button v-if="buttons[6].child[0].isHave==1" class="btn-right" @click="downExcel">导出excel</el-button>
    <br>

    <el-input v-model="search" clearable placeholder="用户" style="margin-top:20px;width:300px;" @change="searchTableData">
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
        type="daterange"
        range-separator="至"
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        format="yyyy 年 MM 月 dd 日"
        value-format="yyyy-MM-dd"
        style="width:400px;"
        @change="searchTableData"/>
    </div>
    <br>

    <el-table :data="tableData" style="margin:10px 0;">
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
      :total="parseInt(total)"
      :page-size="20"
      layout="total, prev, pager, next, jumper"
      @current-change="init"/>
  </div>
</template>

<script>
import { getRuningList, getMoneyType } from '@/api/assets'
import { getVerifiCode } from '@/api/public'
import { mapGetters } from 'vuex'

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
      total: 1,
      currentPage: 1
    }
  },
  computed: {
    ...mapGetters([
      'buttons'
    ])
  },
  created() {
    getMoneyType().then(res => {
      this.allMoneyType = res.content
      this.init()
    })
  },
  methods: {
    init() {
      getRuningList(this.search, this.moneyType, this.dataType, this.date[0], this.date[1], this.currentPage).then(res => {
        this.tableData = res.content.list
        this.total = res.content.count
      })
    },
    // 主表格搜索
    searchTableData() {
      if (this.date === null) this.date = ''
      this.currentPage = 1
      this.init()
    },
    // 下载excel
    downExcel() {
      if (this.date) {
        var str = this.date[0]
        var end = this.date[1]
      } else {
        str = ''
        end = ''
      }
      getVerifiCode().then(res => {
        var url = `/finance/finance-download?download_code=${res.content}&searchName=${this.search}&currency_id=${this.moneyType}&type=${this.dataType}&str_time=${str}&end_time=${end}`
        const elink = document.createElement('a')
        elink.style.display = 'none'
        elink.target = '_blank'
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
