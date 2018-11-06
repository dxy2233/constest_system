<template>
  <div class="app-container">
    <h4 style="display:inline-block;">投票管理</h4>
    <el-button class="btn-right" @click="initRank();dialogRank=true">投票排名</el-button>
    <el-button class="btn-right" type="primary" style="margin-right:10px;" @click="openVoteSet">投票设置</el-button>
    <el-button class="btn-right" @click="openCamp">竞选设置</el-button>
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

    <el-table :data="tableData" style="margin:10px 0;" @sort-change="sortChange">
      <el-table-column prop="mobile" label="投票用户"/>
      <el-table-column prop="name" label="投票节点"/>
      <el-table-column prop="voteNumber" label="投出票数" sortable="custom"/>
      <el-table-column prop="type" label="投票方式" sortable="custom"/>
      <el-table-column prop="createTime" label="投票时间" sortable="custom"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="parseInt(total)"
      :page-size="20"
      layout="total, prev, pager, next, jumper"
      @current-change="init"/>

    <el-dialog :visible.sync="dialogSet" title="投票设置" class="dialog-set">
      <div v-for="(item,index) in dialogSetData" :key="index">
        <div v-if="item.type=='radio'" class="switch">
          <span>{{ item.name }}</span>
          <el-switch v-model="pushSetData[index][item.key]" active-value="1" inactive-value="0"/>
        </div>
        <div v-if="item.type=='text'" class="txt">
          <span>{{ item.name }}{{ pushSetData[index][item.key] }}</span>
        </div>
        <!-- <div v-if="item.type=='time' && showTimeOver==1" class="time">
          <span>{{ item.name }}</span>
          <el-date-picker
            v-model="pushSetData[index][item.key]"
            type="datetime"
            format="yyyy 年 MM 月 dd 日 HH:mm:ss"
            value-format="yyyy-MM-dd HH:mm:ss"
            placeholder="选择日期时间"
            style="width:250px;"/>
          <el-button style="float:right;" @click="manuakStop">手动截止</el-button>
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

    <el-dialog :visible.sync="dialogCamp" :fullscreen="true" title="竞选设置">
      <div v-for="(item,index) in dialogCampData" :key="index">
        <h3 style="display:inline-block;">投票竞选{{ index + 1 }}</h3>
        <el-button v-if="new Date().getTime() + 1000 * 60 * 60 * 24 < new Date(item.cycleStartTime).getTime()" type="danger" plain style="float:right;margin-left:20px;" @click="delCamp(item.id)">删除</el-button>
        <el-button style="float:right;" @click="openEditCamp(index)">编辑</el-button>
        <br>
        <div class="camp-info">
          <div>
            <span>竞选开始时间</span>
            <span>{{ item.cycleStartTime }}</span>
          </div>
          <div>
            <span>竞选截止时间</span>
            <span>{{ item.cycleEndTime }}</span>
          </div>
          <div>
            <span>任职开始时间</span>
            <span>{{ item.tenureStartTime }}</span>
          </div>
          <div>
            <span>任职到期时间</span>
            <span>{{ item.tenureEndTime }}</span>
          </div>
        </div>
      </div>
      <el-button style="margin-top:20px;" @click="openAddCamp">+新增竞选投票</el-button>
      <el-dialog
        :visible.sync="dialogAddCamp"
        title="竞选投票"
        center
        append-to-body
        @closed="$refs['camp'].clearValidate();addCampForm.id='';addCampForm.cycleStartTime='';addCampForm.cycleEndTime='';addCampForm.tenureStartTime='';addCampForm.tenureEndTime='';">
        <h3>竞选投票</h3>
        <el-form ref="camp" :model="addCampForm" :rules="addCampFormRules" label-position="top" label-width="80px" class="timeForm">
          <el-form-item label="竞选开始时间" prop="id" style="display:none;">
            <el-input v-model="addCampForm.id"/>
          </el-form-item>
          <el-form-item label="竞选开始时间" prop="cycleStartTime">
            <el-date-picker
              :picker-options="timePcik0"
              :clearable="false"
              :disabled="new Date(addCampForm.cycleStartTime).getTime() - 1000 * 60 * 60 * 24 < new Date().getTime()"
              v-model="addCampForm.cycleStartTime"
              popper-class="cleartxt"
              type="datetime"
              format="yyyy 年 MM 月 dd 日 HH:mm"
              value-format="yyyy-MM-dd HH:mm"
              style="width:100%;"/>
          </el-form-item>
          <el-form-item label="竞选截止时间" prop="cycleEndTime">
            <el-date-picker
              :picker-options="timePcik0"
              :clearable="false"
              :disabled="new Date(addCampForm.cycleEndTime).getTime() - 1000 * 60 * 60 * 24 < new Date().getTime()"
              v-model="addCampForm.cycleEndTime"
              popper-class="cleartxt"
              type="datetime"
              format="yyyy 年 MM 月 dd 日 HH:mm"
              value-format="yyyy-MM-dd HH:mm"
              style="width:100%;"/>
          </el-form-item>
          <el-form-item label="任职开始时间" prop="tenureStartTime">
            <el-date-picker
              :picker-options="timePcik0"
              :clearable="false"
              :disabled="new Date(addCampForm.tenureStartTime).getTime() - 1000 * 60 * 60 * 24 < new Date().getTime()"
              v-model="addCampForm.tenureStartTime"
              popper-class="cleartxt"
              type="datetime"
              format="yyyy 年 MM 月 dd 日 HH:mm"
              value-format="yyyy-MM-dd HH:mm"
              style="width:100%;"/>
          </el-form-item>
          <el-form-item label="任职截止时间" prop="tenureEndTime">
            <el-date-picker
              :picker-options="timePcik0"
              :clearable="false"
              :disabled="new Date(addCampForm.tenureEndTime).getTime() - 1000 * 60 * 60 * 24 < new Date().getTime()"
              v-model="addCampForm.tenureEndTime"
              popper-class="cleartxt"
              type="datetime"
              format="yyyy 年 MM 月 dd 日 HH:mm"
              value-format="yyyy-MM-dd HH:mm"
              style="width:100%;"/>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogAddCamp = false">取 消</el-button>
          <el-button type="primary" @click="saveCamp">确 定</el-button>
        </span>
      </el-dialog>
      <p>历史记录</p>
      <el-table :data="dialogCampHistoryData" style="margin: 10px 0;">
        <el-table-column prop="id" label="序号"/>
        <el-table-column prop="cycleStartTime" label="竞选开始时间"/>
        <el-table-column prop="cycleEndTime" label="竞选截止时间"/>
        <el-table-column prop="tenureStartTime" label="任职时间"/>
        <el-table-column prop="tenureEndTime" label="到期时间"/>
      </el-table>
      <el-pagination
        :current-page.sync="campCurrentPage"
        :total="parseInt(campTotal)"
        :page-size="20"
        layout="total, prev, pager, next, jumper"
        @current-change="initCampHistory"/>
    </el-dialog>

    <el-dialog :visible.sync="dialogRank" title="投票排名" @closed="rankCurrentPage=1;dialogRankType=0;dialogRankDate=''">
      <el-select v-model="dialogRankType" @change="rankCurrentPage=1;initRank()">
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
          @change="rankCurrentPage=1;initRank()"/>
      </div>
      <el-table :data="dialogRankData" style="margin:10px 0;">
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
        @current-change="initRank"/>
    </el-dialog>
  </div>
</template>

<script>
import { getVoteList, getVoteSet, pushVoteSet, getVoteRank, getCampHistory, getCamp,
  addCamp, deleteCamp, editCamp } from '@/api/poll'
import { getVerifiCode } from '@/api/public'
import { Message } from 'element-ui'

export default {
  name: 'PollManagement',
  data() {
    const validate0 = (rule, value, callback) => {
      if (value === '' || value === null) {
        callback(new Error('请输入日期'))
      }
      if (this.addCampForm.id === '') {
        if (new Date().getTime() + 1000 * 60 * 60 * 24 >= new Date(value).getTime()) {
          callback(new Error('竞选开始时间必须在24小时之后!'))
        } else if (new Date(this.dialogCampData[this.dialogCampData.length - 1].cycleEndTime).getTime() >= new Date(value).getTime()) {
          callback(new Error('必须在上一个竞选截止时间之后!'))
        } else {
          callback()
        }
      }
      if (this.addCampForm.id !== '') {
        if (this.campIndex > 0 && new Date(this.dialogCampData[this.campIndex - 1].cycleEndTime).getTime() >= new Date(value).getTime()) {
          callback(new Error('必须在上一个竞选截止时间之后!'))
        } else if (this.campIndex < this.dialogCampData.length - 1 && new Date(this.dialogCampData[this.campIndex + 1].cycleStartTime).getTime() <= new Date(value).getTime()) {
          callback(new Error('必须在下一个竞选开始时间之前!'))
        } else {
          callback()
        }
      }
    }
    const validate1 = (rule, value, callback) => {
      if (value === '' || value === null) {
        callback(new Error('请输入日期'))
      } else if (new Date(this.addCampForm.cycleStartTime).getTime() >= new Date(value).getTime()) {
        callback(new Error('竞选截止时间必须在开始时间之后!'))
      } else if (this.addCampForm.id !== '' && this.campIndex < this.dialogCampData.length - 1 && new Date(this.dialogCampData[this.campIndex + 1].cycleStartTime).getTime() <= new Date(value).getTime()) {
        callback(new Error('必须在下一个竞选开始时间之前!'))
      } else {
        callback()
      }
    }
    const validate2 = (rule, value, callback) => {
      if (value === '' || value === null) {
        callback(new Error('请输入日期'))
      } else if (new Date(this.addCampForm.cycleEndTime).getTime() >= new Date(value).getTime()) {
        callback(new Error('任职开始时间必须在竞选截止时间之后!'))
      } else if (this.addCampForm.id === '' && new Date(this.dialogCampData[this.dialogCampData.length - 1].tenureEndTime).getTime() >= new Date(value).getTime()) {
        callback(new Error('必须在上一个任职到期时间之后!'))
      } else if (this.addCampForm.id !== '' && this.campIndex > 0 && new Date(this.dialogCampData[this.campIndex - 1].tenureEndTime).getTime() >= new Date(value).getTime()) {
        callback(new Error('必须在上一个任职到期时间之后!'))
      } else if (this.addCampForm.id !== '' && this.campIndex < this.dialogCampData.length - 1 && new Date(this.dialogCampData[this.campIndex + 1].tenureStartTime).getTime() <= new Date(value).getTime()) {
        callback(new Error('必须在下一个任职开始时间之前!'))
      } else {
        callback()
      }
    }
    const validate3 = (rule, value, callback) => {
      if (value === '' || value === null) {
        callback(new Error('请输入日期'))
      } else if (new Date(this.addCampForm.tenureStartTime).getTime() >= new Date(value).getTime()) {
        callback(new Error('任职截止时间必须在任职开始时间之后!'))
      } else if (this.addCampForm.id !== '' && this.campIndex < this.dialogCampData.length - 1 && new Date(this.dialogCampData[this.campIndex + 1].tenureStartTime).getTime() <= new Date(value).getTime()) {
        callback(new Error('必须在下一个任职开始时间之前!'))
      } else {
        callback()
      }
    }
    return {
      search: '',
      searchDate: '',
      tableData: [],
      total: 1,
      currentPage: 1,
      order: null,
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
      dialogCamp: false,
      dialogCampData: [],
      campIndex: null,
      dialogAddCamp: false,
      timePcik0: {
        disabledDate: (time) => {
          return time.getTime() < Date.now()
        }
      },
      addCampForm: {
        cycleStartTime: '',
        cycleEndTime: '',
        tenureStartTime: '',
        tenureEndTime: '',
        id: ''
      },
      addCampFormRules: {
        cycleStartTime: [
          { validator: validate0, required: true, trigger: 'change' }
        ],
        cycleEndTime: [
          { validator: validate1, required: true, trigger: 'change' }
        ],
        tenureStartTime: [
          { validator: validate2, required: true, trigger: 'change' }
        ],
        tenureEndTime: [
          { validator: validate3, required: true, trigger: 'change' }
        ],
        id: []
      },
      dialogCampHistoryData: [],
      campTotal: 1,
      campCurrentPage: 1
    }
  },
  created() {
    this.init()
  },
  methods: {
    init() {
      getVoteList(this.search, this.currentPage, this.searchDate[0], this.searchDate[1], this.order).then(res => {
        this.tableData = res.content.list
        this.total = res.content.count
      })
    },
    sortChange(val) {
      this.currentPage = 1
      if (val.prop === null) this.order = null
      else if (val.prop === 'voteNumber' && val.order === 'ascending') this.order = 1
      else if (val.prop === 'voteNumber' && val.order === 'descending') this.order = 4
      else if (val.prop === 'type' && val.order === 'ascending') this.order = 2
      else if (val.prop === 'type' && val.order === 'descending') this.order = 5
      else if (val.prop === 'createTime' && val.order === 'ascending') this.order = 3
      else if (val.prop === 'createTime' && val.order === 'descending') this.order = 6
      this.init()
    },
    // 主表格搜索
    searchTableData() {
      if (this.searchDate === null) this.searchDate = ''
      this.currentPage = 1
      this.init()
    },
    // 打开投票设置
    openVoteSet() {
      getVoteSet().then(res => {
        this.dialogSetData = res.content
        this.pushSetData = []
        this.dialogSetData.forEach((item, index, arry) => {
          this.pushSetData.push({ [item.key]: item.value.toString() })
        })
      })
      this.dialogSet = true
    },
    // 手动截至投票时间
    // manuakStop(index) {
    //   var nowDate = new Date()
    //   var time = nowDate.getFullYear() + '-' + (nowDate.getMonth() + 1) + '-' +
    //     nowDate.getDate() + ' ' + nowDate.toLocaleTimeString('chinese', { hour12: false })
    //   this.pushSetData.map((item, index, arry) => {
    //     if (item.hasOwnProperty('end_update_time')) arry[index].end_update_time = time
    //   })
    //   refresh()
    // },
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
    initRank() {
      getVoteRank(this.dialogRankDate, this.dialogRankType, this.rankCurrentPage).then(res => {
        this.dialogRankData = res.content.list
        this.rankTotal = res.content.count
      })
    },
    initCampHistory() {
      getCampHistory(this.campCurrentPage).then(res => {
        this.dialogCampHistoryData = res.content.list
        this.campTotal = res.content.count
      })
    },
    openCamp() {
      getCamp().then(res => {
        this.dialogCampData = res.content
        this.dialogCamp = true
      })
      this.initCampHistory()
    },
    openAddCamp() {
      if (this.dialogCampData.length >= 3) {
        Message({ message: '当前最多3个投票竞选', type: 'warning' })
      } else {
        this.dialogAddCamp = true
      }
    },
    // 增加||修改竞选投票
    saveCamp() {
      this.$refs['camp'].validate((valid) => {
        if (valid) {
          if (this.addCampForm.id === '') {
            addCamp(this.addCampForm.cycleStartTime, this.addCampForm.cycleEndTime, this.addCampForm.tenureStartTime,
              this.addCampForm.tenureEndTime).then(res => {
              Message({ message: res.msg, type: 'success' })
              getCamp().then(res => {
                this.dialogCampData = res.content
                this.dialogAddCamp = false
              })
            })
          } else {
            editCamp(this.addCampForm.id, this.addCampForm.cycleStartTime, this.addCampForm.cycleEndTime, this.addCampForm.tenureStartTime,
              this.addCampForm.tenureEndTime).then(res => {
              Message({ message: res.msg, type: 'success' })
              getCamp().then(res => {
                this.dialogCampData = res.content
                this.dialogAddCamp = false
              })
            })
          }
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    // 编辑竞选投票
    openEditCamp(index) {
      this.campIndex = index
      this.addCampForm.id = this.dialogCampData[index].id
      this.addCampForm.cycleStartTime = this.dialogCampData[index].cycleStartTime
      this.addCampForm.cycleEndTime = this.dialogCampData[index].cycleEndTime
      this.addCampForm.tenureStartTime = this.dialogCampData[index].tenureStartTime
      this.addCampForm.tenureEndTime = this.dialogCampData[index].tenureEndTime
      this.dialogAddCamp = true
    },
    // 删除竞选投票
    delCamp(id) {
      this.$confirm('确定删除吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        deleteCamp(id).then(res => {
          Message({ message: res.msg, type: 'success' })
          getCamp().then(res => {
            this.dialogCampData = res.content
          })
        })
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
        elink.target = '_blank'
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
.dialog-set {
  .switch {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    padding-top: 10px;
  }
  .txt {
    margin-top: 20px;
    margin-bottom: 10px;
    padding-bottom: 10px;
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

.camp-info {
  display: flex;
  justify-content: space-between;
  font-size: 16px;
  margin-bottom: 20px;
  > div {
    flex: 1;
    > span:nth-child(1) {
      display: block;
      margin-bottom: 10px;
    }
  }
}

.timeForm {
 > div {
   display: inline-block;
   width: 49%;
 }
}
</style>
