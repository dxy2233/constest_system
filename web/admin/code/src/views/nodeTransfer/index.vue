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
    <el-button v-show="noticeChecktoNum==0" :disabled="(tableDataSelection.length<1)" size="small" type="primary" plain @click="allPass">通过</el-button>

    <el-table
      :data="tableData"
      style="margin:10px 0;"
      @selection-change="handleSelectionChange"
      @row-click="clickRow"
      @sort-change="sortChange">
      <el-table-column type="selection" width="55"/>
      <el-table-column prop="nodeName" label="转让节点名称"/>
      <el-table-column prop="typeName" label="转让节点类型"/>
      <el-table-column prop="fromUserName" label="转让方姓名"/>
      <el-table-column prop="fromUserMobile" label="转让方手机"/>
      <el-table-column prop="toUserName" label="受让方姓名"/>
      <el-table-column prop="toUserMobile" label="受让方手机号"/>
      <el-table-column prop="status" label="状态"/>
      <el-table-column prop="createTime" label="提交时间" sortable="custom"/>
      <el-table-column v-if="noticeChecktoNum!=0" prop="examineTime" label="审核时间"/>
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
          <span class="name">{{ rowInfo.nodeName }}<br><span>{{ checkType }}</span></span>
          <i class="el-icon-close btn" @click="showInfo=false"/>
          <el-button v-show="noticeChecktoNum==0" type="danger" plain class="btn" style="margin:0 10px;" @click="doomFail">不通过</el-button>
          <el-button v-show="noticeChecktoNum==0" type="primary" class="btn" @click="doomPass">通过</el-button>
        </div>
        <!-- <p v-show="noticeChecktoNum==4">未通过原因：{{ rowDetail.statusRemark }}</p> -->
        <p style="padding-top:50px;">
          <span style="display:inline-block;width:250px;">转让节点类型：{{ rowDetail.nodeType }}</span>
          <span>转让方手机号：{{ rowDetail.fromUserMobile }}</span>
        </p>
        <p>
          <span style="display:inline-block;width:250px;">姓名：{{ rowDetail.fromUserName }}</span>
          <span>身份证号：{{ rowDetail.fromUserNumber }}</span>
        </p>
        <hr>
        <p>
          <span>受让方手机号：{{ rowDetail.toUserMobile }}</span>
        </p>
        <p>
          <span style="display:inline-block;width:250px;">姓名：{{ rowDetail.toUserName }}</span>
          <span>身份证号：{{ rowDetail.toUserNumber }}</span>
        </p>
        <p>申请凭证</p>
        <img v-for="(item,index) in rowDetail.images" :key="index" :src="item" class="image" @click="showLargeImg(item)">
      </div>
    </transition>
  </div>
</template>

<script>
import { getCheckList, checkPass, checkFail, getDetail } from '@/api/nodeTransfer'
import { getVerifiCode } from '@/api/public'
import { Message } from 'element-ui'
import { mapGetters } from 'vuex'

export default {
  name: 'NodeTransfer',
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
      order: '',
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
        return 0
      } else if (this.checkType === '已通过') {
        return 1
      } else if (this.checkType === '未通过') {
        return 2
      }
    }
  },
  created() {
    this.init()
  },
  methods: {
    init() {
      getCheckList(this.currentPage, this.noticeChecktoNum, this.search, this.order).then(res => {
        this.tableData = res.content.list
        this.total = res.content.count
      })
    },
    // 排序
    sortChange(val) {
      this.currentPage = 1
      if (val.prop === null) this.order = ''
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
      getDetail(row.id).then(res => {
        this.rowDetail = res.content
        this.rowDetail.images = this.rowDetail.images.split(',')
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
    // 放大图片
    showLargeImg(src) {
      window.open(src)
      // window.open(src.substring(0, src.indexOf('!')))
    },
    downExcel() {
      if (this.tableDataSelection.length > 0) {
        let id = ''
        this.tableDataSelection.forEach((item, index) => {
          id = `${id}${item.id},`
        })
        getVerifiCode().then(res => {
          var url = `/transfer/download?download_code=${res.content}&status=${this.noticeChecktoNum}&id=${id}`
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
        var url = `/transfer/download?download_code=${res.content}&searchName=${this.search}&status=${this.noticeChecktoNum}&order=${this.order}`
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

.image {
  display: block;
  height: 400px;
  border: 1px solid #ddd;
  &:hover {
    cursor: pointer;
  }
}
</style>
