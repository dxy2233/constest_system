<template>
  <div class="app-container" @click.self="showUserInfo=false">
    <h4 style="display:inline-block;">用户管理</h4>
    <el-button class="btn-right" @click="dialogAddUser=true;ifAddUser=true">新增用户</el-button>
    <el-button class="btn-right" style="margin-right:10px;" @click="addExcel">导出excel</el-button>
    <br>

    <el-input v-model="search" placeholder="用户" suffix-icon="el-icon-search" style="margin-top:20px;width:300px;"/>
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
        style="width:400px;"/>
      <el-button @click="searchRun">查询</el-button>
    </div>
    <br>

    已选择<span style="color:#3e84e9;">{{ tableDataSelection.length }}</span>项
    <el-button size="small" type="primary" style="margin-top:20px;" @click="allFreeze">冻结</el-button>

    <el-table
      ref="userTable"
      :data="tableData"
      style="width: 100%;margin:10px 0;"
      @selection-change="handleSelectionChange"
      @row-click="clickRow">
      <el-table-column type="selection" width="55"/>
      <el-table-column prop="username" label="用户"/>
      <el-table-column prop="userType" label="类型"/>
      <el-table-column prop="nodeName" label="拥有节点"/>
      <el-table-column prop="num" label="已投票数"/>
      <el-table-column label="状态">
        <template slot-scope="scope">
          <div v-if="scope.row.status==1">正常</div>
          <div v-else>停用</div>
        </template>
      </el-table-column>
      <el-table-column prop="create_time" label="注册时间"/>
      <el-table-column prop="last_login_time" label="最近一次登录时间"/>
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
          <span class="name">{{ userInfoBase.nodename }}<br><span>用户</span></span>
          <i class="el-icon-close btn" @click="showUserInfo=false"/>
          <el-button type="primary" class="btn" style="margin: 0 10px;" @click="rowEdit">编辑</el-button>
          <el-button v-show="rowInfo.status==1" type="danger" class="btn" @click="free">冻结</el-button>
          <el-button v-show="rowInfo.status==0" type="danger" class="btn" @click="thaw">解冻</el-button>
        </div>
        <div class="info">
          <el-row :gutter="24">
            <el-col :span="6">
              <el-card shadow="never">
                <div class="title">类型</div>
                {{ userInfoBase.usertype }}
              </el-card>
            </el-col>
            <el-col :span="6">
              <el-card shadow="never">
                <div class="title">拥有节点</div>
                {{ userInfoBase.username }}
              </el-card>
            </el-col>
            <el-col :span="6">
              <el-card shadow="never">
                <div class="title">已投票数</div>
                {{ userInfoBase.num }}
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
              <span style="margin-right:150px;">姓名：{{ userInfoIdentify.realname }}</span>
              <span>身份证号：{{ userInfoIdentify.number }}</span>
            </p>
            <p>手持身份证正面</p>
            <img :src="userInfoIdentify.picfront" alt="">
            <p>手持身份证背面</p>
            <img :src="userInfoIdentify.picback" alt="">
          </el-tab-pane>
          <el-tab-pane label="投票明细" name="Vote">
            <el-radio-group v-model="pollName" class="radioTabs">
              <el-radio-button label="投票记录"/>
              <el-radio-button label="赎回记录"/>
            </el-radio-group>
            <el-table v-show="pollName=='投票记录'" :data="userInfoVote.vote">
              <el-table-column prop="nodename" label="节点名称"/>
              <el-table-column prop="typename" label="类型"/>
              <el-table-column prop="type" label="方式"/>
              <el-table-column prop="votenumber" label="数量"/>
              <el-table-column prop="createtime" label="投票时间"/>
            </el-table>
            <el-table v-show="pollName=='赎回记录'" :data="userInfoVote.unvote">
              <el-table-column prop="nodename" label="节点名称"/>
              <el-table-column prop="typename" label="类型"/>
              <el-table-column prop="votenumber" label="数量"/>
              <el-table-column prop="createtime" label="赎回时间"/>
            </el-table>
          </el-tab-pane>

          <el-tab-pane label="钱包" name="Wallet" class="wallet">
            <el-tabs v-model="walletName" type="card">
              <el-tab-pane v-for="(item,index) in userInfoWallet" :key="index" :label="item.name" :name="item.name">
                <p>钱包地址</p>
                <p style="color:#888;">{{ item.address }}</p>
                <el-tabs v-model="walletMoney" type="card">
                  <el-tab-pane v-for="(item2,index2) in item.list" :key="index2" :label="item2.name" :name="item2.name">
                    <div class="wallet-info">
                      <span>合计：{{ item2.positionamount }}</span>
                      <span>可用：{{ item2.useamount }}</span>
                      <span>冻结：{{ item2.frozenamount }}</span>
                    </div>
                    <el-radio-group v-model="walletNote" class="radioTabs">
                      <el-radio-button label="收支记录"/>
                      <el-radio-button label="冻结记录"/>
                    </el-radio-group>
                    <div v-show="walletNote=='收支记录'" class="note">
                      <div v-for="(item3,index3) in item2.inandout" :key="index3" class="row">
                        <span>{{ item3.remark }}</span>
                        <span>{{ item3.effecttime }}</span>
                        <span>{{ item3.amount }}</span>
                      </div>
                    </div>
                    <div v-show="walletNote=='冻结记录'" class="note">
                      <div v-for="(item3,index3) in item2.frozen" :key="index3" class="row">
                        <span>{{ item3.remark }}</span>
                        <span>{{ item3.effecttime }}</span>
                        <span>{{ item3.amount }}</span>
                      </div>
                    </div>
                  </el-tab-pane>
                </el-tabs>
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
              <el-table-column prop="nodename" label="节点名称"/>
              <el-table-column prop="typename" label="节点类型"/>
              <el-table-column prop="vouchernum" label="获取数量"/>
              <el-table-column prop="createtime" label="获取时间"/>
            </el-table>
            <el-table v-show="voucherName=='使用记录'" :data="userInfoVoucher.voucherDetailList">
              <el-table-column prop="nodename" label="投票节点"/>
              <el-table-column prop="typename" label="节点类型"/>
              <el-table-column prop="username" label="用户"/>
              <el-table-column prop="amount" label="投票数量"/>
              <el-table-column prop="createtime" label="使用时间"/>
            </el-table>
          </el-tab-pane>
          <el-tab-pane label="推荐记录" name="Recommend">
            <el-table :data="userInfoRecommend">
              <el-table-column prop="username" label="推荐用户"/>
              <el-table-column prop="nodename" label="节点名称"/>
              <el-table-column prop="typename" label="节点类型"/>
              <el-table-column prop="createtime" label="推荐时间"/>
            </el-table>
          </el-tab-pane>
        </el-tabs>
      </div>
    </transition>

    <el-dialog :visible.sync="dialogAddUser" title="用户编辑">
      <el-form label-width="60px">
        <el-form-item label="账号："><el-input v-model="addUserName"/></el-form-item>
        <el-tabs v-if="!ifAddUser" v-model="edidWallet">
          <el-tab-pane v-for="(item,index) in userInfoWallet" :key="index" :label="item.name" :name="item.name">
            <p>钱包地址</p>
            <el-input v-model="item.address"/>
          </el-tab-pane>
        </el-tabs>
      </el-form>
      <span slot="footer">
        <el-button type="primary" @click="editUser">确 认</el-button>
        <el-button @click="dialogAddUser = false">取 消</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getUserList, getUserBase, getUserIdentify, getUserVote, getUserWallet,
  getUserVoucher, getUserRecommend, freezeUser, thawUser, addUser, addWallet } from '@/api/admin'
import { Message } from 'element-ui'
import { parseTime } from '@/utils'

export default {
  name: 'UserManagement',
  data() {
    return {
      search: '',
      searchDate: '',
      tableData: [],
      tableDataSelection: [],
      currentPage: 1,
      total: 1,
      ifAddUser: false,
      edidWallet: '',
      showUserInfo: false,
      rowInfo: [], // 表格选中的信息
      userInfoBase: [], // 基础
      userInfoIdentify: [], // 实名
      userInfoVote: [], // 投票
      userInfoWallet: [], // 钱包
      userInfoVoucher: [], // 投票券
      userInfoRecommend: [], // 推荐记录
      activeName: 'Base',
      dialogAddUser: false,
      addUserName: '',
      pollName: '投票记录',
      walletName: '',
      walletMoney: 'GRT',
      walletNote: '收支记录',
      voucherName: '获取记录'
    }
  },
  created() {
    getUserList().then(res => {
      this.tableData = res.content.list
      this.total = parseInt(res.content.count)
    })
  },
  methods: {
    searchRun() {
      getUserList(this.search, this.searchDate[0], this.searchDate[1]).then(res => {
        this.tableData = res.content.list
        this.total = parseInt(res.content.count)
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
      this.changeTabs({ name: this.activeName })
    },
    // 变页面
    changePage(page) {
      getUserList(null, null, null, page).then(res => {
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
      this.$confirm('确定冻结吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        freezeUser(this.rowInfo.id).then(res => {
          Message({ message: res.msg, type: 'success' })
        }).then(res => {
          this.rowInfo.status = '0'
        })
      })
    },
    // 解冻用户
    thaw() {
      this.$confirm('确定解冻吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        thawUser(this.rowInfo.id).then(res => {
          Message({ message: res.msg, type: 'success' })
        }).then(res => {
          this.rowInfo.status = '1'
        })
      })
    },
    // 批量冻结
    allFreeze() {
      this.$confirm('确定解冻吗?', '提示', {
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
      this.dialogAddUser = true
      this.ifAddUser = false
      this.addUserName = this.rowInfo.username
      getUserWallet(this.rowInfo.id).then(res => {
        this.userInfoWallet = res.content
        this.walletName = this.userInfoWallet[0].name
        this.edidWallet = this.userInfoWallet[0].name
      })
    },
    // 编辑上传用户信息
    editUser() {
      this.dialogAddUser = false
      if (this.ifAddUser) {
        addUser(null, this.addUserName).then(res => {
          Message({ message: res.msg, type: 'success' })
        })
      } else {
        addUser(this.rowInfo.id, this.addUserName).then(res => {
          Message({ message: res.msg, type: 'success' })
        })
        for (var i = 0; i < this.userInfoWallet.length; i++) {
          addWallet(this.userInfoWallet[i].id, this.userInfoWallet[i].address)
        }
      }
    },
    // 导出excel
    addExcel() {
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['用户', '类型', '拥有节点', '已投票数', '状态', '注册时间', '最近一次登录时间']
        const filterVal = ['username', 'userType', 'nodeName', 'num', 'status', 'create_time', 'last_login_time']
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
        margin-top: 5px;
        border-bottom: 1px solid #ddd;
      }
    }
  }
}
</style>
