<template>
  <div class="app-container" @click.self="showInfo=false">
    <el-radio-group v-model="checkType" class="radioTabs" @change="changeCheckType">
      <el-radio-button label="待审核"/>
      <el-radio-button label="已通过"/>
      <el-radio-button label="未通过"/>
    </el-radio-group>
    <el-input v-model="search" clearable placeholder="节点名称/手机号" class="btn-right" style="width:210px;" @change="searchData">
      <el-button slot="append" icon="el-icon-search" @click.native="searchData"/>
    </el-input>
    <el-button class="btn-right" style="margin-right:10px;" @click="downExcel">导出excel</el-button>
    <br>

    已选择<span style="color:#3e84e9;display:inline-block;margin-top:20px;">{{ tableDataSelection.length }}</span>项
    <el-button v-if="buttons[8].child[0].isHave==1" v-show="noticeChecktoNum==2" :disabled="(tableDataSelection.length<1)" size="small" type="primary" plain @click="allPass">通过</el-button>

    <el-table
      :data="tableData"
      style="margin:10px 0;"
      @selection-change="handleSelectionChange"
      @row-click="clickRow"
      @sort-change="sortChange">
      <el-table-column type="selection" width="55"/>
      <el-table-column prop="name" label="节点名称"/>
      <el-table-column prop="typeName" label="节点类型"/>
      <el-table-column prop="mobile" label="手机号"/>
      <el-table-column prop="grt" label="质押GRT"/>
      <el-table-column prop="bpt" label="质押BPT"/>
      <el-table-column prop="tt" label="质押TT"/>
      <el-table-column prop="status" label="状态"/>
      <el-table-column prop="createTime" label="提交时间" sortable="custom"/>
      <el-table-column v-if="noticeChecktoNum!=2" prop="examineTime" label="审核时间"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="parseInt(total)"
      :page-size="pageSize"
      layout="total, prev, pager, next, jumper"
      @current-change="init"/>

    <transition name="fade">
      <div v-show="showInfo" class="fade-slide">
        <div class="title">
          <img src="@/assets/img/user.jpg" alt="">
          <span class="name">{{ rowInfo.name }}<br><span>{{ checkType }}</span></span>
          <i class="el-icon-close btn" @click="showInfo=false"/>
          <el-button v-if="buttons[8].child[0].isHave==1" v-show="noticeChecktoNum==2" type="danger" plain class="btn" style="margin:0 10px;" @click="doomFail">不通过</el-button>
          <el-button v-if="buttons[8].child[0].isHave==1" v-show="noticeChecktoNum==2" type="primary" class="btn" @click="doomPass">通过</el-button>
        </div>
        <p v-show="noticeChecktoNum==4">未通过原因：{{ rowDetail.statusRemark }}</p>
        <!-- <p style="color:#888;">logo</p>
        <img :src="rowDetail.logo" alt="" style="display:block;width:100px;height:100px;border:1px solid #ddd;">
        <p style="color:#888;margin-top:50px;">机构/个人名称</p>
        <p>{{ rowDetail.name }}</p>
        <p style="color:#888;margin-top:50px;">机构/个人简介</p>
        <p>{{ rowDetail.desc }}</p>
        <p style="color:#888;margin-top:50px;">社区建设方案</p>
        <p>{{ rowDetail.scheme }}</p> -->
        <p>基本信息</p>
        <div class="row"> <span>姓名</span> {{ rowDetail.username | noContent }} </div>
        <div class="row"> <span>手机号</span> {{ rowDetail.mobile | noContent }} </div>
        <div class="row"> <span>微信号</span> {{ rowDetail.weixin | noContent }} </div>
        <div class="row"> <span>节点类型</span> {{ rowDetail.typeName | noContent }} </div>
        <div class="row"> <span>质押GRT数量</span> {{ rowDetail.grt | noContent }} </div>
        <div class="row"> <span>GRT钱包地址</span> {{ rowDetail.grtAddress | noContent }} </div>
        <div class="row"> <span>质押BPT数量</span> {{ rowDetail.bpt | noContent }} </div>
        <div class="row"> <span>BPT钱包地址</span> {{ rowDetail.bptAddress | noContent }} </div>
        <div class="row"> <span>质押TT数量</span> {{ rowDetail.tt | noContent }} </div>
        <div class="row"> <span>TT钱包地址</span> {{ rowDetail.ttAddress | noContent }} </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { getCheckList, checkPass, checkFail, deleteNote, getNodeBase } from '@/api/nodeCheck'
import { getVerifiCode } from '@/api/public'
import { Message } from 'element-ui'
import { mapGetters } from 'vuex'

export default {
  name: 'NodeCheck',
  filters: {
    noContent(value) {
      if (value === '' || !value) return '—'
      else return value
    }
  },
  data() {
    return {
      checkType: '待审核',
      search: '',
      tableData: [],
      total: 1,
      order: null,
      tableDataSelection: [],
      currentPage: 1,
      pageSize: 20,
      showInfo: false,
      rowInfo: [],
      rowDetail: []
    }
  },
  computed: {
    ...mapGetters([
      'buttons'
    ]),
    noticeChecktoNum() {
      if (this.checkType === '待审核') {
        return 2
      } else if (this.checkType === '已通过') {
        return 1
      } else if (this.checkType === '未通过') {
        return 4
      }
    }
  },
  created() {
    this.init()
  },
  methods: {
    init() {
      getCheckList(this.noticeChecktoNum, this.search, this.currentPage, this.order).then(res => {
        this.tableData = res.content.list
        this.total = res.content.count
      })
    },
    // 排序
    sortChange(val) {
      this.currentPage = 1
      if (val.prop === null) this.order = null
      else if (val.prop === 'createTime' && val.order === 'ascending') this.order = 1
      else if (val.prop === 'createTime' && val.order === 'descending') this.order = 2
      this.init()
    },
    // 切换审核数据类型
    changeCheckType() {
      this.showInfo = false
      this.search = ''
      this.currentPage = 1
      this.init()
    },
    // 选择table
    handleSelectionChange(val) {
      this.tableDataSelection = val
    },
    // 搜索
    searchData() {
      this.currentPage = 1
      this.init()
    },
    // 点击表格行
    clickRow(row) {
      this.rowInfo = row
      getNodeBase(row.id).then(res => {
        this.rowDetail = res.content
        this.showInfo = true
      })
    },
    // 通过审核
    doomPass() {
      this.$confirm('确定通过吗?(请仔细核对，通过后不可取消)', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        checkPass(this.rowInfo.id).then(res => {
          Message({ message: res.msg, type: 'success' })
          this.showInfo = false
          this.init()
        })
      })
    },
    // 批量通过审核
    allPass() {
      if (this.tableDataSelection.length < 1) return
      this.$confirm('确定全部通过吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        let allId = ''
        this.tableDataSelection.map((item, index, items) => {
          allId = allId + ',' + item.id
        })
        checkPass(allId.replace(',', '')).then(res => {
          Message({ message: res.msg, type: 'success' })
          this.init()
        })
      })
    },
    // 不通过
    doomFail() {
      this.$prompt('请填写不通过原因', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        inputPattern: /^\S{1,50}$/,
        inputErrorMessage: '请输入1~50不包含空格的内容'
      }).then(({ value }) => {
        checkFail(this.rowInfo.id, value).then(res => {
          Message({ message: res.msg, type: 'success' })
          this.showInfo = false
          this.init()
        })
      })
    },
    // 删除记录
    // delteFailNote() {
    //   deleteNote(this.rowInfo.id).then(res => {
    //     Message({ message: res.msg, type: 'success' })
    //     this.showInfo = false
    //     this.init()
    //   })
    // },
    // 批量删除记录
    allFail() {
      if (this.tableDataSelection.length < 1) return
      this.$confirm('确定删除全部记录吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        let allId = ''
        this.tableDataSelection.map((item, index, items) => {
          allId = allId + ',' + item.id
        })
        deleteNote(allId.replace(',', '')).then(res => {
          Message({ message: res.msg, type: 'success' })
          this.init()
        })
      })
    },
    downExcel() {
      if (this.tableDataSelection.length > 0) {
        let id = ''
        this.tableDataSelection.forEach((item, index) => {
          id = `${id}${item.id},`
        })
        getVerifiCode().then(res => {
          var url = `/node/examine-download?download_code=${res.content}&status=${this.noticeChecktoNum}&id=${id}`
          const elink = document.createElement('a')
          elink.style.display = 'none'
          elink.target = '_blank'
          elink.href = url
          document.body.appendChild(elink)
          elink.click()
          document.body.removeChild(elink)
        })
        return
      }
      getVerifiCode().then(res => {
        var url = `/node/examine-download?download_code=${res.content}&searchName=${this.search}&status=${this.noticeChecktoNum}&order=${this.order}`
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
.btn-right {
  margin-top: -39px;
}

.row {
  border-bottom: 1px solid #ddd;
  padding-bottom: 5px;
  margin-bottom: 10px;
  span {
    display: inline-block;
    width: 220px;
    color: #888;
    padding-right: 120px;
  }
}
</style>
