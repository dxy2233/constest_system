<template>
  <div class="app-container" @click.self="showUserInfo=false">
    <h4 style="display:inline-block;">用户管理</h4>
    <el-button class="btn-right" type="primary" @click="dialogAddUser=true">新增用户</el-button>
    <!-- <el-button class="btn-right" style="margin-right:10px;" @click="addExcel">导出excel</el-button> -->
    <el-button class="btn-right" style="margin-right:10px;">
      <a :href="downUrl">导出excel</a>
    </el-button>
    <!-- <input type="file" ref="file" name="" value="">
    <button @click="cc">text</button> -->
    <br>

    <el-input v-model="search" clearable placeholder="用户" style="margin-top:20px;width:300px;" @keyup.enter.native="searchRun">
      <el-button slot="append" icon="el-icon-search" @click.native="searchRun"/>
    </el-input>
    <div style="float:right;margin-top:20px;">
      注册时间
      <el-date-picker
        v-model="searchDate"
        type="daterange"
        range-separator="至"
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        format="yyyy 年 MM 月 dd 日"
        value-format="yyyy-MM-dd"
        style="width:400px;"
        @change="searchRun"/>
    </div>
    <br>

    已选择<span style="color:#3e84e9;">{{ tableDataSelection.length }}</span>项
    <el-button :disabled="(tableDataSelection.length<1)" size="small" type="danger" plain style="margin-top:20px;" @click="allFreeze">停用</el-button>

    <el-table
      :data="tableData"
      style="margin:10px 0;"
      @selection-change="handleSelectionChange"
      @row-click="clickRow">
      <el-table-column type="selection" width="55"/>
      <el-table-column prop="mobile" label="用户"/>
      <el-table-column prop="userType" label="类型"/>
      <el-table-column prop="nodeName" label="拥有节点"/>
      <el-table-column prop="num" label="已投票数"/>
      <el-table-column prop="referee" label="推荐人"/>
      <el-table-column prop="status" label="状态"/>
      <el-table-column prop="createTime" label="注册时间"/>
      <el-table-column prop="lastLoginTime" label="最近一次登录时间"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="total"
      :page-size="20"
      layout="total, prev, pager, next, jumper"
      @current-change="changePage"/>

    <transition name="fade">
      <div v-show="showUserInfo" class="fade-slide">
        <div class="title">
          <img src="@/assets/img/user.jpg" alt="">
          <span class="name">{{ userInfoBase.mobile }}<br><span>用户</span></span>
          <i class="el-icon-close btn" @click="showUserInfo=false"/>
          <el-button type="primary" class="btn" style="margin: 0 10px;" @click="rowEdit">编辑</el-button>
          <el-button v-show="rowInfo.status=='正常'" type="danger" plain class="btn" @click="free">停用</el-button>
          <el-button v-show="rowInfo.status=='冻结'" type="primary" plain class="btn" @click="thaw">启用</el-button>
        </div>
        <div class="info">
          <el-row :gutter="5" class="info-row">
            <el-col>
              <el-card shadow="never">
                <div class="title">类型</div>
                {{ userInfoBase.userType }}
              </el-card>
            </el-col>
            <el-col>
              <el-card shadow="never">
                <div class="title">拥有节点</div>
                {{ userInfoBase.nodeName }}
              </el-card>
            </el-col>
            <el-col>
              <el-card shadow="never">
                <div class="title">已投票数</div>
                {{ userInfoBase.num }}
              </el-card>
            </el-col>
            <el-col>
              <el-card shadow="never">
                <div class="title">推荐人</div>
                {{ userInfoBase.referee }}
              </el-card>
            </el-col>
            <el-col>
              <el-card shadow="never">
                <div class="title">我的推荐码</div>
                {{ userInfoBase.recommendCode }}
              </el-card>
            </el-col>
          </el-row>
        </div>
        <el-tabs v-model="activeName" class="tabs" @tab-click="changeTabs">
          <el-tab-pane label="基本信息" name="Base">
            账户
            <p>{{ userInfoBase.username }}</p>
          </el-tab-pane>
          <el-tab-pane label="实名信息" name="Identify">
            <p>
              <span style="margin-right:150px;">姓名：{{ userInfoIdentify.realName }}</span>
              <span>身份证号：{{ userInfoIdentify.number }}</span>
            </p>
            <p>手持身份证正面</p>
            <img :src="userInfoIdentify.picFront" alt="" style="display:block;width:178px;height:178px;border:1px solid #ddd;">
            <p>手持身份证背面</p>
            <img :src="userInfoIdentify.picBack" alt="" style="display:block;width:178px;height:178px;border:1px solid #ddd;">
          </el-tab-pane>
          <el-tab-pane label="投票明细" name="Vote">
            <el-radio-group v-model="pollName" class="radioTabs">
              <el-radio-button label="投票记录"/>
              <el-radio-button label="赎回记录"/>
            </el-radio-group>
            <el-table v-show="pollName=='投票记录'" :data="userInfoVote.vote">
              <el-table-column prop="nodeName" label="节点名称"/>
              <el-table-column prop="typeName" label="类型"/>
              <el-table-column prop="type" label="方式"/>
              <el-table-column prop="voteNumber" label="数量"/>
              <el-table-column prop="createTime" label="投票时间"/>
            </el-table>
            <el-table v-show="pollName=='赎回记录'" :data="userInfoVote.unvote">
              <el-table-column prop="nodeName" label="节点名称"/>
              <el-table-column prop="typeName" label="类型"/>
              <el-table-column prop="voteNumber" label="数量"/>
              <el-table-column prop="undoTime" label="赎回时间"/>
            </el-table>
          </el-tab-pane>

          <el-tab-pane label="资产" name="Wallet" class="wallet">
            <el-tabs v-model="walletName" type="card">
              <el-tab-pane v-for="(item,index) in userInfoWallet" :key="index" :label="item.name" :name="item.name">
                <p>收款地址</p>
                <p style="color:#888;padding-bottom:30px;">{{ item.address }}</p>
                <div class="wallet-info">
                  <span>合计：{{ item.positionAmount }}</span>
                  <span>可用：{{ item.useAmount }}</span>
                  <span>冻结：{{ item.frozenAmount }}</span>
                </div>
                <el-radio-group v-model="walletNote" class="radioTabs">
                  <el-radio-button label="收支记录"/>
                  <el-radio-button label="锁仓记录"/>
                </el-radio-group>
                <div v-show="walletNote=='收支记录'" class="note">
                  <div v-for="(item2,index2) in item.inAndOut" :key="index2" class="row">
                    <span>{{ item2.type }}</span>
                    <span>{{ item2.createTime }}</span>
                    <span>{{ item2.amount }}</span>
                  </div>
                </div>
                <div v-show="walletNote=='锁仓记录'" class="note">
                  <div v-for="(item3,index3) in item.frozen" :key="index3" class="row">
                    <span>{{ item3.type }}</span>
                    <span>{{ item3.createTime }}</span>
                    <span>{{ item3.amount }}</span>
                  </div>
                </div>
              </el-tab-pane>
            </el-tabs>
          </el-tab-pane>

          <el-tab-pane label="投票券" name="Voucher">
            <p>剩余数量：{{ userInfoVoucher.length }}票</p>
            <el-radio-group v-model="voucherName" class="radioTabs">
              <el-radio-button label="获取记录"/>
              <el-radio-button label="使用记录"/>
            </el-radio-group>
            <el-table v-show="voucherName=='获取记录'" :data="userInfoVoucher.voucherList">
              <el-table-column prop="username" label="推荐用户"/>
              <el-table-column prop="nodeName" label="节点名称"/>
              <el-table-column prop="typeName" label="节点类型"/>
              <el-table-column prop="voucherNum" label="获取数量"/>
              <el-table-column prop="createTime" label="获取时间"/>
            </el-table>
            <el-table v-show="voucherName=='使用记录'" :data="userInfoVoucher.voucherDetailList">
              <el-table-column prop="nodeName" label="投票节点"/>
              <el-table-column prop="typeName" label="节点类型"/>
              <el-table-column prop="username" label="用户"/>
              <el-table-column prop="amount" label="投票数量"/>
              <el-table-column prop="createTime" label="使用时间"/>
            </el-table>
          </el-tab-pane>
          <el-tab-pane label="推荐记录" name="Recommend">
            <el-table :data="userInfoRecommend">
              <el-table-column prop="username" label="推荐用户"/>
              <el-table-column prop="nodeName" label="节点名称"/>
              <el-table-column prop="typeName" label="节点类型"/>
              <el-table-column prop="createTime" label="推荐时间"/>
            </el-table>
          </el-tab-pane>
        </el-tabs>
      </div>
    </transition>

    <el-dialog :visible.sync="dialogAddUser" title="新增用户">
      <el-form ref="addUser" :model="addData" :rules="rules" label-width="80px">
        <el-form-item label="账号：" prop="addUserName"><el-input v-model="addData.addUserName"/></el-form-item>
        <el-form-item label="推荐码："><el-input v-model="addData.addUserCode"/></el-form-item>
      </el-form>
      <span slot="footer">
        <el-button type="primary" @click="saveAddUser">确 认</el-button>
        <el-button @click="dialogAddUser=false">取 消</el-button>
      </span>
    </el-dialog>

    <el-dialog :visible.sync="dialogEditUser" title="用户编辑">
      <el-form label-width="120px">
        <el-form-item label="账号：">{{ rowInfo.mobile }}</el-form-item>
        <el-form-item v-if="userInfoBase.referee=='-'" label="推荐人推荐码："><el-input v-model="rowInfo.code"/></el-form-item>
        <el-tabs v-model="edidWallet">
          <el-tab-pane v-for="(item,index) in rowInfo.walletData" :key="index" :label="item.name" :name="item.name">
            <p>收款地址</p>
            <p>{{ item.address | address }}</p>
          </el-tab-pane>
        </el-tabs>
      </el-form>
      <span slot="footer">
        <el-button type="primary" @click="editUser">确 认</el-button>
        <el-button @click="dialogEditUser = false">取 消</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getUserList, getUserBase, getUserIdentify, getUserVote, getUserVoucher,
  getUserRecommend, getUserWallet, freezeUser, thawUser, editUser, addUser } from '@/api/admin'
import { Message } from 'element-ui'
import { parseTime } from '@/utils'

export default {
  name: 'UserManagement',
  filters: {
    address(value) {
      if (!value) {
        return '-'
      } else {
        return value
      }
    }
  },
  data() {
    return {
      search: '',
      searchDate: '',
      tableData: [],
      tableDataSelection: [],
      currentPage: 1,
      total: 1,
      edidWallet: '',
      showUserInfo: false,
      rowInfo: [], // 表格选中的信息
      userInfoBase: [], // 基础
      userInfoIdentify: [], // 实名
      userInfoVote: [], // 投票
      userInfoWallet: [], // 原钱包现资产
      userInfoVoucher: [], // 投票券
      userInfoRecommend: [], // 推荐记录
      activeName: 'Base',
      dialogAddUser: false,
      addData: {
        addUserName: '',
        addUserCode: ''
      },
      rules: {
        addUserName: [
          { required: true, message: '请输入手机号码', trigger: 'blur' },
          { pattern: /^1\d{10}$/, message: '请输入正确的手机号码', trigger: 'blur' }
        ]
      },
      pollName: '投票记录',
      walletName: '',
      walletMoney: 'GRT',
      walletNote: '收支记录',
      voucherName: '获取记录',
      dialogEditUser: false
    }
  },
  computed: {
    downUrl() {
      var str
      var end
      if (this.searchDate) {
        str = this.searchDate[0]
        end = this.searchDate[1]
      } else {
        str = ''
        end = ''
      }
      return `/user/download?searchName=${this.search}&&str_time=${str}&&end_time=${end}`
    }
  },
  created() {
    getUserList(this.search, this.searchDate[0], this.searchDate[1], this.currentPage).then(res => {
      this.tableData = res.content.list
      this.total = parseInt(res.content.count)
    })
  },
  methods: {
    // cc() {
    //   getUserListExcel().then(res => {
    //     console.log(res);
    //     // var blob = new Blob([res], { type: 'application/vnd.ms-excel' })
    //     // var blob = res
    //     //
    //     // var fileName = '测试表格123.xlsx'
    //     // if ('download' in document.createElement('a')) { // 非IE下载
    //     //   const elink = document.createElement('a')
    //     //   elink.download = fileName
    //     //   elink.style.display = 'none'
    //     //   elink.href = URL.createObjectURL(blob)
    //     //   document.body.appendChild(elink)
    //     //   elink.click()
    //     //   URL.revokeObjectURL(elink.href) // 释放URL 对象
    //     //   document.body.removeChild(elink)
    //     // } else { // IE10+下载
    //     //   navigator.msSaveBlob(blob, fileName)
    //     // }
    //   })
    // },
    searchRun() {
      if (this.searchDate === null) this.searchDate = ''
      getUserList(this.search, this.searchDate[0], this.searchDate[1], 1).then(res => {
        this.tableData = res.content.list
        this.total = parseInt(res.content.count)
        this.currentPage = 1
      })
    },
    // 表格选择
    handleSelectionChange(val) {
      this.tableDataSelection = val
    },
    // 点击表格行
    clickRow(row) {
      this.rowInfo = row
      this.showUserInfo = true
      if (this.activeName === 'Base') this.changeTabs({ name: this.activeName })
      else {
        this.changeTabs({ name: 'Base' })
        this.changeTabs({ name: this.activeName })
      }
    },
    // 变页面
    changePage(page) {
      getUserList(this.search, this.searchDate[0], this.searchDate[1], this.currentPage).then(res => {
        this.tableData = res.content.list
        this.total = parseInt(res.content.count)
      })
    },
    // 选项卡切换
    changeTabs(val) {
      // if (this[`userInfo${val.name}`].length > 0) return
      if (val.name === 'Base') {
        getUserBase(this.rowInfo.id).then(res => {
          this.userInfoBase = res.content
        })
      } else if (val.name === 'Identify') {
        getUserIdentify(this.rowInfo.id).then(res => {
          this.userInfoIdentify = res.content
        })
      } else if (val.name === 'Vote') {
        getUserVote(this.rowInfo.id).then(res => {
          this.userInfoVote = res.content
        })
      } else if (val.name === 'Wallet') {
        getUserWallet(this.rowInfo.id).then(res => {
          this.userInfoWallet = res.content
          this.walletName = this.userInfoWallet[0].name
        })
      } else if (val.name === 'Voucher') {
        getUserVoucher(this.rowInfo.id).then(res => {
          this.userInfoVoucher = res.content
        })
      } else if (val.name === 'Recommend') {
        getUserRecommend(this.rowInfo.id).then(res => {
          this.userInfoRecommend = res.content
        })
      }
    },
    // 冻结用户
    free() {
      this.$confirm('确定停用吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        freezeUser(this.rowInfo.id).then(res => {
          Message({ message: res.msg, type: 'success' })
        }).then(res => {
          this.rowInfo.status = '冻结'
        })
      })
    },
    // 解冻用户
    thaw() {
      this.$confirm('确定启用吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        thawUser(this.rowInfo.id).then(res => {
          Message({ message: res.msg, type: 'success' })
        }).then(res => {
          this.rowInfo.status = '正常'
        })
      })
    },
    // 批量冻结
    allFreeze() {
      if (this.tableDataSelection.length < 1) return
      this.$confirm('确定停用吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        let allId = ''
        this.tableDataSelection.map((item, index, items) => {
          allId = allId + ',' + item.id
        })
        freezeUser(allId.replace(',', '')).then(res => {
          Message({ message: res.msg, type: 'success' })
        }).then(res => {
          getUserList().then(res => {
            this.tableData = res.content.list
            this.total = parseInt(res.content.count)
          })
        })
      })
    },
    // 编辑每一行的用户信息
    rowEdit() {
      getUserWallet(this.rowInfo.id).then(res => {
        this.userInfoWallet = res.content
        this.rowInfo.walletData = res.content
        this.walletName = this.userInfoWallet[0].name
        this.edidWallet = this.userInfoWallet[0].name
        this.dialogEditUser = true
      })
    },
    // 新增用户
    saveAddUser() {
      this.$refs['addUser'].validate((valid) => {
        if (valid) {
          addUser(this.addData.addUserName, this.addData.addUserCode).then(res => {
            Message({ message: res.msg, type: 'success' })
            this.dialogAddUser = false
            getUserList(this.search, this.searchDate[0], this.searchDate[1], this.currentPage).then(res => {
              this.tableData = res.content.list
              this.total = parseInt(res.content.count)
            })
          })
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    // 编辑上传用户信息
    editUser() {
      this.dialogEditUser = false
      editUser(this.rowInfo.id, this.rowInfo.mobile, this.rowInfo.code).then(res => {
        Message({ message: res.msg, type: 'success' })
        getUserList(this.search, this.searchDate[0], this.searchDate[1], this.currentPage).then(res => {
          this.tableData = res.content.list
          this.total = parseInt(res.content.count)
        })
      })
      this.changeTabs({ name: 'Wallet' })
    },
    // 导出excel
    addExcel() {
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['用户', '类型', '拥有节点', '已投票数', '推荐人', '状态', '注册时间', '最近一次登录时间']
        const filterVal = ['mobile', 'userType', 'nodeName', 'num', 'referee', 'status', 'createTime', 'lastLoginTime']
        const list = this.tableData
        const data = this.formatJson(filterVal, list)
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: '用户管理'
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
.fade-slide {
  .wallet {
    .wallet-info {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    .note {
      margin-top: 10px;
      border-top: 1px solid #ddd;
      .row {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        padding-bottom: 9px;
        border-bottom: 1px solid #ddd;
        span {
          flex: 1;
        }
        span:nth-child(3) {
          text-align: right;
        }
      }
    }
  }
}
</style>
