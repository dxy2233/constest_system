<template>
  <div class="app-container" @click.self="showInfo=false">
    <el-radio-group v-model="checkType" class="radioTabs" @change="changeCheckType">
      <el-radio-button label="待审核"/>
      <el-radio-button label="已通过"/>
      <el-radio-button label="未通过"/>
    </el-radio-group>
    <el-input v-model="search" clearable placeholder="姓名/手机号/身份证号" class="btn-right" style="width:256px;" @change="searchData">
      <el-button slot="append" icon="el-icon-search" @click.native="searchData"/>
    </el-input>
    <el-button class="btn-right" style="margin-right:10px;" @click="downExcel">导出excel</el-button>
    <br>

    已选择<span style="color:#3e84e9;display:inline-block;margin-top:20px;">{{ tableDataSelection.length }}</span>项
    <el-button v-if="buttons[9].child[0].isHave==1" v-show="checkTypetoNum==0" :disabled="(tableDataSelection.length<1)" size="small" type="primary" plain @click="allDoomPass">通过</el-button>

    <el-table
      :data="tableData"
      style="margin:10px 0;"
      @selection-change="handleSelectionChange"
      @row-click="clickRow">
      <el-table-column type="selection" width="55"/>
      <el-table-column prop="mobile" label="手机号"/>
      <el-table-column prop="realname" label="姓名"/>
      <el-table-column prop="number" label="身份证号"/>
      <el-table-column prop="status" label="状态"/>
      <el-table-column prop="createTime" label="提交时间"/>
      <el-table-column v-if="checkTypetoNum!=0" prop="examineTime" label="审核时间"/>
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
          <span class="name">{{ rowInfo.realname }}<br><span>{{ checkType }}</span></span>
          <i class="el-icon-close btn" @click="showInfo=false"/>
          <el-button v-if="buttons[9].child[0].isHave==1" v-show="checkType=='待审核'" type="danger" plain class="btn" style="margin:0 10px;" @click="doomFail">不通过</el-button>
          <el-button v-if="buttons[9].child[0].isHave==1" v-show="checkType=='待审核'" type="primary" class="btn" @click="doomPass">通过</el-button>
        </div>
        <p v-show="checkTypetoNum==2">未通过原因：{{ rowDetail.statusRemark }}</p>
        <p style="margin-top:50px;">
          <span style="margin-right:150px;">姓名：{{ rowInfo.realname }}</span>
          <span>身份证号：{{ rowInfo.number }}</span>
        </p>
        <p>手持身份证正面</p>
        <img :src="rowDetail.picFront" alt="" class="image" @click="showLargeImg(rowDetail.picFront)">
        <p>手持身份证背面</p>
        <img :src="rowDetail.picBack" alt="" class="image" style="margin-bottom:50px;" @click="showLargeImg(rowDetail.picBack)">
      </div>
    </transition>

    <el-dialog :visible.sync="dialogLargeImg">
      <img :src="largeImg" alt="" style="display:block;margin:0 auto;">
    </el-dialog>
  </div>
</template>

<script>
import { getList, getDetail, passVerified, failVerified } from '@/api/verified'
import { getVerifiCode } from '@/api/public'
import { Message } from 'element-ui'
import { mapGetters } from 'vuex'

export default {
  name: 'Verified',
  data() {
    return {
      checkType: '待审核',
      search: '',
      tableData: [],
      total: 1,
      tableDataSelection: [],
      currentPage: 1,
      pageSize: 20,
      showInfo: false,
      rowInfo: [],
      rowDetail: [],
      dialogLargeImg: false,
      largeImg: ''
    }
  },
  computed: {
    ...mapGetters([
      'buttons'
    ]),
    checkTypetoNum() {
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
      getList(this.checkTypetoNum, this.search, this.currentPage).then(res => {
        this.tableData = res.content.list
        this.total = res.content.count
      })
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
      this.showInfo = true
      getDetail(row.id).then(res => {
        this.rowDetail = res.content
      })
    },
    // 通过
    doomPass() {
      this.$confirm('确定通过吗?(请仔细核对，通过后不可取消)', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        passVerified(this.rowInfo.id).then(res => {
          this.showInfo = false
          Message({ message: res.msg, type: 'success' })
          this.init()
        })
      })
    },
    // 批量通过
    allDoomPass() {
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
        passVerified(allId.replace(',', '')).then(res => {
          this.showInfo = false
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
        failVerified(this.rowInfo.id, value).then(res => {
          this.showInfo = false
          Message({ message: res.msg, type: 'success' })
          this.init()
        })
      })
    },
    // 放大图片
    showLargeImg(src) {
      // this.largeImg = src.substring(0, src.indexOf('!'))
      window.open(src.substring(0, src.indexOf('!')))
      // this.dialogLargeImg = true
    },
    downExcel() {
      if (this.tableDataSelection.length > 0) {
        let id = ''
        this.tableDataSelection.forEach((item, index) => {
          id = `${id}${item.id},`
        })
        getVerifiCode().then(res => {
          var url = `/identify/download?download_code=${res.content}&status=${this.checkTypetoNum}&id=${id}`
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
        var url = `/identify/download?download_code=${res.content}&searchName=${this.search}&status=${this.checkTypetoNum}`
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
