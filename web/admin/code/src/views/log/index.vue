<template>
  <div class="app-container">
    <h4 style="display:inline-block;">操作日志</h4>
    <el-button class="btn-right" @click="downExcel">导出excel</el-button>
    <br>

    <el-input v-model="searchName" clearable placeholder="操作人" style="margin-top:20px;width:300px;" @change="search">
      <el-button slot="append" icon="el-icon-search" @click.native="search"/>
    </el-input>
    <div style="float:right;margin-top:20px;">
      操作时间&nbsp;
      <el-date-picker
        v-model="searchDate"
        type="daterange"
        range-separator="至"
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        format="yyyy 年 MM 月 dd 日"
        value-format="yyyy-MM-dd"
        style="width:400px;"
        @change="search"/>
    </div>

    <el-table :data="tableData" style="margin:10px 0;">
      <el-table-column prop="createTime" label="操作时间"/>
      <el-table-column prop="realName" label="操作人"/>
      <el-table-column prop="department" label="部门"/>
      <el-table-column prop="controller" label="操作模块"/>
      <el-table-column prop="action" label="操作内容"/>
      <el-table-column prop="ip" label="操作设备IP"/>
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
import { getLogList } from '@/api/system'
import { getVerifiCode } from '@/api/public'

export default {
  name: 'Log',
  data() {
    return {
      peopleOptions: [],
      searchName: '',
      searchDate: '',
      tableData: [],
      total: 1,
      currentPage: 1
    }
  },
  created() {
    this.init()
  },
  methods: {
    init() {
      getLogList(this.currentPage, this.searchName, this.searchDate[0], this.searchDate[1]).then(res => {
        this.tableData = res.content.data
        this.total = res.content.count
      })
    },
    search() {
      if (this.searchDate === null) this.searchDate = ''
      this.currentPage = 1
      this.init()
    },
    // 下载excel
    downExcel() {
      if (this.searchDate) {
        var str = this.searchDate[0]
        var end = this.searchDate[1]
      } else {
        str = ''
        end = ''
      }
      getVerifiCode().then(res => {
        var url = `/log/download?download_code=${res.content}&userName=${this.searchName}&strTime=${str}&endTime=${end}`
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
