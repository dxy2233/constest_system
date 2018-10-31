<template>
  <div class="app-container">
    <h4 style="display:inline-block;">投票管理</h4>
    <el-button class="btn-right" @click="openVoteRank">投票排名</el-button>
    <el-button class="btn-right" type="primary" style="margin-right:10px;" @click="openVoteSet">投票设置</el-button>
    <el-button class="btn-right" @click="downExcel">导出excel</el-button>
    <br>

    <el-input v-model="search" clearable placeholder="用户/节点名称" style="margin-top:20px;width:300px;" @change="searchTableData">
      <el-button slot="append" icon="el-icon-search" @click.native="searchTableData"/>
    </el-input>
    <div style="float:right;margin-top:20px;">
      投票时间
      <el-date-picker
        v-model="searchDate"
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
      <el-table-column prop="mobile" label="投票用户"/>
      <el-table-column prop="name" label="投票节点"/>
      <el-table-column prop="voteNumber" label="投出票数"/>
      <el-table-column prop="type" label="投票方式"/>
      <el-table-column prop="createTime" label="投票时间"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="parseInt(total)"
      :page-size="20"
      layout="total, prev, pager, next, jumper"
      @current-change="changePage"/>

    <el-dialog :visible.sync="dialogSet" title="投票设置" class="dialog-set">
      <div v-for="(item,index) in dialogSetData" :key="index">
        <div v-if="item.type=='radio'" class="switch">
          <span>{{ item.name }}</span>
          <el-switch v-model="pushSetData[index][item.key]" active-value="1" inactive-value="0" @change="changeSwitch"/>
        </div>
        <div v-if="item.type=='text'" class="txt">
          <span>{{ item.name }}{{ pushSetData[index][item.key] }}</span>
        </div>
        <div v-if="item.type=='time' && showTimeOver==1" class="time">
          <span>{{ item.name }}</span>
          <el-date-picker
            v-model="pushSetData[index][item.key]"
            type="datetime"
            format="yyyy 年 MM 月 dd 日 HH:mm:ss"
            value-format="yyyy-MM-dd HH:mm:ss"
            placeholder="选择日期时间"
            style="width:250px;"/>
          <el-button style="float:right;" @click="manuakStop">手动截止</el-button>
        </div>
        <!-- <div v-if="item.type=='select'">
          <p>{{ item.name }}</p>
          <el-select v-model="pushSetData[index][item.key]" placeholder="请选择" size="small">
            <el-option v-for="(item2,index2) in item.initialize" :key="index2" :value="index2" :label="item2"/>
          </el-select> /天
        </div> -->
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
      <el-button style="float:right;" @click="downRankExcel">导出excel</el-button>
      <div style="margin-top:20px;">
        截止时间
        <el-date-picker
          v-model="dialogRankDate"
          type="date"
          placeholder="选择日期时间"
          format="yyyy 年 MM 月 dd 日"
          value-format="yyyy-MM-dd"
          @change="searchRank"/>
      </div>
      <el-table ref="multipleTable" :data="dialogRankData" style="margin:10px 0;">
        <el-table-column prop="order" label="排名"/>
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
        :total="parseInt(rankTotal)"
        :page-size="20"
        layout="total, prev, pager, next, jumper"
        @current-change="changeRankPage"/>
    </el-dialog>
  </div>
</template>

<script>
import { getVoteList, getVoteSet, pushVoteSet, getVoteRank, refresh } from '@/api/poll'
import { getVerifiCode } from '@/api/public'
import { Message } from 'element-ui'

export default {
  name: 'PollManagement',
  data() {
    return {
      search: '',
      searchDate: '',
      tableData: [],
      total: 1,
      currentPage: 1,
      dialogSet: false,
      dialogSetData: [],
      pushSetData: [],
      dialogRank: false,
      dialogRankAllType: [
        { value: 0, label: '全部' },
        { value: 1, label: '普通投票' },
        { value: 2, label: '支付投票' },
        { value: 3, label: '投票券' }
      ],
      dialogRankType: 0,
      dialogRankData: [],
      dialogRankDataPage: [],
      rankTotal: 1,
      dialogRankDate: '',
      rankCurrentPage: 1,
      showTimeOver: null
    }
  },
  created() {
    getVoteList(this.search, 1, this.searchDate[0], this.searchDate[1]).then(res => {
      this.tableData = res.content.list
      this.total = res.content.count
    })
  },
  methods: {
    // 变页数
    changePage(page) {
      getVoteList(this.search, page, this.searchDate[0], this.searchDate[1]).then(res => {
        this.tableData = res.content.list
        this.total = res.content.count
      })
    },
    // 主表格搜索
    searchTableData() {
      if (this.searchDate === null) this.searchDate = ''
      getVoteList(this.search, 1, this.searchDate[0], this.searchDate[1]).then(res => {
        this.tableData = res.content.list
        this.total = res.content.count
      })
    },
    // 打开投票设置
    openVoteSet() {
      getVoteSet().then(res => {
        this.dialogSetData = res.content
        this.pushSetData = []
        this.dialogSetData.forEach((item, index, arry) => {
          this.pushSetData.push({ [item.key]: item.value.toString() })
          if (item.key === 'stop_vote') {
            this.showTimeOver = item.value
          }
        })
      })
      this.dialogSet = true
    },
    changeSwitch(val) {
      this.showTimeOver = val
    },
    // 手动截至投票时间
    manuakStop(index) {
      var nowDate = new Date()
      var time = nowDate.getFullYear() + '-' + (nowDate.getMonth() + 1) + '-' +
        nowDate.getDate() + ' ' + nowDate.toLocaleTimeString('chinese', { hour12: false })
      this.pushSetData.map((item, index, arry) => {
        if (item.hasOwnProperty('end_update_time')) arry[index].end_update_time = time
      })
      refresh()
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
      getVoteRank(null, this.dialogRankType, 1).then(res => {
        this.dialogRankData = res.content.list
        this.rankTotal = res.content.count
        this.dialogRank = true
      })
    },
    // 改变页数
    changeRankPage(page) {
      getVoteRank(this.dialogRankDate, this.dialogRankType, page).then(res => {
        this.dialogRankData = res.content.list
        this.rankTotal = res.content.count
      })
    },
    // 切换投票类型
    changeRankType(val) {
      getVoteRank(this.dialogRankDate, val, 1).then(res => {
        this.dialogRankData = res.content.list
        this.rankTotal = res.content.count
      })
    },
    // 排名搜索
    searchRank() {
      getVoteRank(this.dialogRankDate, this.dialogRankType, 1).then(res => {
        this.dialogRankData = res.content.list
        this.rankTotal = res.content.count
      })
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
        var url = `/vote/download?download_code=${res.content}&searchName=${this.search}&str_time=${str}&end_time=${end}`
        const elink = document.createElement('a')
        elink.style.display = 'none'
        elink.href = url
        document.body.appendChild(elink)
        elink.click()
        document.body.removeChild(elink)
      })
    },
    downRankExcel() {
      getVerifiCode().then(res => {
        var url = `/vote/vote-order-download?download_code=${res.content}&type=${this.dialogRankType}&end_time=${this.dialogRankDate}`
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
.dialog-set {
  .switch {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #ddd;
  }
  .txt {
    margin-top: 20px;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
  }
  .time {
    margin-top: 20px;
  }
  .item {
    margin-top: 20px;
    .title {
      display: inline-block;
      width: 90px;
    }
  }
}
</style>
