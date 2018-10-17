<template>
  <div class="app-container">
    <h4 style="display:inline-block;">投票管理</h4>
    <el-button class="btn-right" @click="openVoteRank">投票排名</el-button>
    <el-button class="btn-right" style="margin-right:10px;" @click="openVoteSet">投票设置</el-button>
    <el-button class="btn-right" @click="addExcel">导出excel</el-button>
    <br>

    <el-input v-model="search" placeholder="用户/节点名称" suffix-icon="el-icon-search" style="margin-top:20px;width:300px;"/>
    <div style="float:right;margin-top:20px;">
      投票时间
      <el-date-picker
        v-model="searchDate"
        type="datetimerange"
        range-separator="至"
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        format="yyyy 年 MM 月 dd 日 HH：mm"
        value-format="yyyy-MM-dd HH:mm"/>
      <el-button @click="searchTableData">查询</el-button>
    </div>
    <br>

    <el-table :data="tableDataPage" style="margin:10px 0;">
      <el-table-column prop="mobile" label="投票用户"/>
      <el-table-column prop="name" label="投票节点"/>
      <el-table-column prop="voteNumber" label="投出票数"/>
      <el-table-column prop="type" label="投票方式"/>
      <el-table-column prop="createTime" label="投票时间"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="total"
      :page-size="20"
      layout="total, prev, pager, next, jumper"
      @current-change="changePage"/>

    <el-dialog :visible.sync="dialogSet" title="节点编辑" class="dialog-set">
      <div v-for="(item,index) in dialogSetData" :key="index">
        <div v-if="item.type=='radio'" class="switch">
          <span>{{ item.name }}</span>
          <el-switch v-model="pushSetData[index][item.key]" active-value="1" inactive-value="0" active-color="#13ce66" inactive-color="#ff4949"/>
        </div>
        <div v-if="item.type=='select'">
          <p>{{ item.name }}</p>
          <el-select v-model="pushSetData[index][item.key]" placeholder="请选择" size="small">
            <el-option v-for="(item2,index2) in item.initialize" :key="index2" :value="index2" :label="item2"/>
          </el-select> /天
          <!-- <el-button style="float:right;margin-top:-10px;">刷新数据</el-button> -->
        </div>
        <div v-if="item.type=='input'" class="item">
          <span class="title">{{ item.name }}</span>
          <el-input v-model="pushSetData[index][item.key]" style="width:200px;"/>
          <span>{{ item.remark }}</span>
        </div>
      </div>
      <span slot="footer">
        <el-button type="primary" @click="saveVoteSet">确认修改</el-button>
        <el-button @click="dialogSet = false">取 消</el-button>
      </span>
    </el-dialog>

    <el-dialog :visible.sync="dialogRank" title="投票排名">
      <el-select v-model="dialogRankType" @change="changeRankType">
        <el-option
          v-for="(item,index) in dialogRankAllType"
          :key="index"
          :label="item.label"
          :value="item.value"/>
      </el-select>
      <el-button style="float:right;" @click="addExcelRank">导出excel</el-button>
      <div style="margin-top:20px;">
        截止时间
        <el-date-picker
          v-model="dialogRankDate"
          type="datetime"
          placeholder="选择日期时间"
          format="yyyy 年 MM 月 dd 日 HH：mm"
          value-format="yyyy-MM-dd HH:mm"
          style="width:250px;"/>
        <el-button @click="searchRank">查询</el-button>
      </div>
      <el-table ref="multipleTable" :data="dialogRankDataPage" style="margin:10px 0;">
        <el-table-column prop="index" label="排名"/>
        <el-table-column prop="mobile" label="账号"/>
        <el-table-column prop="num" label="票数"/>
        <el-table-column label="方式">
          <template slot-scope="scope">
            {{ dialogRankAllType[dialogRankType].label }}
          </template>
        </el-table-column>
      </el-table>
      <el-pagination
        :current-page.sync="rankCurrentPage"
        :total="rankTotal"
        :page-size="20"
        layout="total, prev, pager, next, jumper"
        @current-change="changeRankPage"/>
    </el-dialog>
  </div>
</template>

<script>
import { getVoteList, getVoteSet, pushVoteSet, getVoteRank } from '@/api/poll'
import { Message } from 'element-ui'
import { parseTime, pagination } from '@/utils'

export default {
  name: 'PollManagement',
  data() {
    return {
      search: '',
      searchDate: '',
      tableData: [],
      tableDataPage: [],
      currentPage: 1,
      dialogSet: false,
      dialogSetData: [],
      pushSetData: [],
      dialogRank: false,
      dialogRankAllType: [
        { value: 0, label: '全部' },
        { value: 1, label: '普通投票' },
        { value: 2, label: '支付投票' },
        { value: 3, label: '投票券' },
        { value: 4, label: '推荐投票' }
      ],
      dialogRankType: 0,
      dialogRankData: [],
      dialogRankDataPage: [],
      dialogRankDate: '',
      rankCurrentPage: 1
    }
  },
  computed: {
    total() {
      return this.tableData.length
    },
    rankTotal() {
      return this.dialogRankData.length
    }
  },
  created() {
    getVoteList().then(res => {
      this.tableData = res.content.list
      this.tableDataPage = pagination(this.tableData, this.currentPage, 20)
    })
  },
  methods: {
    // 变页数
    changePage(page) {
      this.tableDataPage = pagination(this.tableData, page, 20)
    },
    // 主表格搜索
    searchTableData() {
      getVoteList(this.search, null, this.searchDate[0], this.searchDate[1]).then(res => {
        this.tableData = res.content.list
        this.tableDataPage = pagination(this.tableData, this.currentPage, 20)
      })
    },
    // 打开投票设置
    openVoteSet() {
      getVoteSet().then(res => {
        this.dialogSetData = res.content
        this.dialogSetData.forEach((item, index, arry) => {
          this.pushSetData.push({ [item.key]: item.value })
        })
        this.dialogSet = true
      })
    },
    // 保存投票设置
    saveVoteSet() {
      var temList = {}
      this.pushSetData.map((item, index, arry) => {
        Object.assign(temList, item)
      })
      pushVoteSet(temList).then(res => {
        Message({ message: res.msg, type: 'success' })
        this.dialogSet = false
      })
    },
    // 打开投票排名
    openVoteRank() {
      getVoteRank(null, this.dialogRankType).then(res => {
        this.dialogRankData = res.content.list
        this.dialogRankData.forEach((item, index, arry) => {
          Object.assign(item, { index: index + 1 })
        })
        this.dialogRankDataPage = pagination(this.dialogRankData, this.rankCurrentPage, 20)
        this.dialogRank = true
      })
    },
    // 改变页数
    changeRankPage(page) {
      this.dialogRankDataPage = pagination(this.dialogRankData, page, 20)
    },
    // 切换投票类型
    changeRankType(val) {
      getVoteRank(null, val).then(res => {
        this.dialogRankData = res.content.list
        this.dialogRankData.forEach((item, index, arry) => {
          Object.assign(item, { index: index + 1 })
        })
        this.dialogRankDataPage = pagination(this.dialogRankData, this.rankCurrentPage, 20)
      })
    },
    // 排名搜索
    searchRank() {
      getVoteRank(this.dialogRankDate, this.dialogRankType).then(res => {
        this.dialogRankData = res.content.list
        this.dialogRankData.forEach((item, index, arry) => {
          Object.assign(item, { index: index + 1 })
        })
        this.dialogRankDataPage = pagination(this.dialogRankData, this.rankCurrentPage, 20)
      })
    },
    // 导出excel
    addExcel() {
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['投票用户', '投票节点', '投出票数', '投票方式', '投票时间']
        const filterVal = ['mobile', 'name', 'voteNumber', 'type', 'createTime']
        const list = this.tableData
        const data = this.formatJson(filterVal, list)
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: '投票管理'
        })
      })
    },
    addExcelRank() {
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['排名', '账号', '票数']
        const filterVal = ['index', 'mobile', 'num']
        const list = this.dialogRankData
        const data = this.formatJson(filterVal, list)
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: '投票排名'
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
.dialog-set {
  .switch {
    display: flex;
    justify-content: space-between;
  }
  .item {
    margin-top: 20px;
    .title {
      display: inline-block;
      width: 60px;
    }
  }
}
</style>
