<template>
  <div class="app-container" @click.self="showInfo=false">
    <el-radio-group v-model="checkType" class="radioTabs" @change="changeCheckType">
      <el-radio-button label="待审核"/>
      <el-radio-button label="已通过"/>
      <el-radio-button label="未通过"/>
    </el-radio-group>
    <el-button v-if="buttons[10].child[1].isHave==1" class="btn-right" style="margin-left:10px;" @click="openTransferSet">转账设置</el-button>
    <el-button class="btn-right" style="margin-right:10px;" @click="downExcel">导出excel</el-button>
    <br>

    <el-input v-model="search" clearable placeholder="流水号/手机号" style="width:200px;" @change="searchData">
      <el-button slot="append" icon="el-icon-search" @click.native="searchData"/>
    </el-input>
    <el-date-picker
      v-model="date"
      type="daterange"
      range-separator="至"
      start-placeholder="开始日期"
      end-placeholder="结束日期"
      format="yyyy 年 MM 月 dd 日"
      value-format="yyyy-MM-dd"
      style="float:right;width:400px;"
      @change="searchData"/>
    <span style="float:right;line-height:2.5;padding:0 5px;">申请时间</span>
    <el-select v-model="moneyType" clearable placeholder="积分" style="float:right;" @change="searchData">
      <el-option
        v-for="item in allMoneyType"
        :key="item.id"
        :label="item.name"
        :value="item.id"/>
    </el-select>
    <el-select v-model="dataType" clearable placeholder="类型" style="float:right;" @change="searchData">
      <el-option
        v-for="item in allDataType"
        :key="item.id"
        :label="item.name"
        :value="item.id"/>
    </el-select>
    <br>

    已选择<span style="color:#3e84e9;display:inline-block;margin-top:20px;">{{ tableDataSelection.length }}</span>项
    <el-button v-if="buttons[10].child[0].isHave==1" v-show="checkTypetoNum==0" :disabled="(tableDataSelection.length<1)" size="small" type="primary" plain @click="allDoomPass">通过</el-button>

    <el-table
      :data="tableData"
      style="margin:10px 0;"
      @selection-change="handleSelectionChange"
      @row-click="clickRow">
      <el-table-column type="selection" width="55"/>
      <el-table-column prop="orderNumber" label="流水号"/>
      <el-table-column prop="name" label="积分"/>
      <el-table-column prop="mobile" label="用户"/>
      <el-table-column prop="amount" label="数量"/>
      <el-table-column prop="tag" label="IET账号"/>
      <el-table-column prop="type" label="类型"/>
      <el-table-column prop="remark" label="备注"/>
      <el-table-column label="状态">
        <template slot-scope="scope">
          {{ checkType }}
        </template>
      </el-table-column>
      <el-table-column prop="createTime" label="申请时间"/>
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
          <span class="name">{{ rowInfo.mobile }}<br><span>{{ checkType }}</span></span>
          <i class="el-icon-close btn" @click="showInfo=false"/>
          <el-button v-if="buttons[10].child[0].isHave==1" v-show="checkType=='待审核'" type="danger" plain class="btn" style="margin:0 10px;" @click="doomFail">不通过</el-button>
          <el-button v-if="buttons[10].child[0].isHave==1" v-show="checkType=='待审核'" type="primary" class="btn" @click="doomPass">通过</el-button>
        </div>
        <p v-show="checkTypetoNum==2">未通过原因：{{ rowInfo.statusRemark }}</p>
        <p><span>流水号</span>{{ rowInfo.orderNumber }}</p>
        <p><span>积分</span>{{ rowInfo.name }}<span>类型</span>{{ rowInfo.type }}</p>
        <p><span>数量</span>{{ rowInfo.amount }}<span>剩余划拨数量</span>{{ payment }}</p>
        <p><span>备注</span>{{ rowInfo.remark }}</p>
        <p><span>对方钱包地址</span>{{ rowInfo.destinationAddress }}</p>
        <p><span>申请时间</span>{{ rowInfo.createTime }}</p>
      </div>
    </transition>

    <el-dialog :visible.sync="dialogSet" title="转账设置">
      <p>转账需完成实名认证<el-switch v-model="form.is_identify" active-value="1" inactive-value="0" style="float:right;"/></p>
      <el-tabs v-model="setType" :before-leave="beforeChangeTabs">
        <el-tab-pane
          v-for="(item,index) in allMoneyType"
          :key="index"
          :label="item.name"
          :name="item.id">
          <el-form :ref="'transferForm' + (index + 1)" :model="form" :rules="rules">
            <el-form-item label="单笔最小转账数量" prop="withdraw_min_amount">
              <el-input v-model="form.withdraw_min_amount">
                <template slot="append">{{ item.name }}</template>
              </el-input>
            </el-form-item>
            <el-form-item label="每日单次最高转账数量" prop="withdraw_max_amount">
              <el-input v-model="form.withdraw_max_amount">
                <template slot="append">{{ item.name }}</template>
              </el-input>
            </el-form-item>
            <el-form-item label="大于该值转账需审核" prop="withdraw_audit_amount">
              <el-input v-model="form.withdraw_audit_amount">
                <template slot="append">{{ item.name }}</template>
              </el-input>
            </el-form-item>
            <el-form-item label="每日累计转账数量" prop="withdraw_day_amount">
              <el-input v-model="form.withdraw_day_amount">
                <template slot="append">{{ item.name }}</template>
              </el-input>
            </el-form-item>
          </el-form>
        </el-tab-pane>
      </el-tabs>
      <span slot="footer">
        <el-button type="primary" @click="saveSet">确认修改</el-button>
        <el-button @click="dialogSet = false">取 消</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getList, editSet, passTrial, failTrial, getSetValue, walletInfo } from '@/api/transfer'
import { getMoneyType } from '@/api/assets'
import { getVerifiCode } from '@/api/public'
import { Message } from 'element-ui'
import { mapGetters } from 'vuex'

export default {
  name: 'Transfer',
  data() {
    return {
      checkType: '待审核',
      search: '',
      date: '',
      tableData: [],
      total: 1,
      tableDataSelection: [],
      currentPage: 1,
      pageSize: 20,
      dataType: 0,
      allDataType: [
        { id: 0, name: '全部' },
        { id: 1, name: '转入' },
        { id: 2, name: '转出' }
      ],
      allMoneyType: [],
      moneyType: '',
      showInfo: false,
      rowInfo: [],
      payment: '',
      dialogSet: false,
      setType: '',
      form: {
        is_identify: '',
        withdraw_min_amount: '',
        withdraw_max_amount: '',
        withdraw_audit_amount: '',
        withdraw_day_amount: ''
      },
      form2: '',
      rules: {
        withdraw_min_amount: [
          // { type: 'number', min: 0.000001, required: true, message: '请输入大于0的数字', trigger: 'blur' }
          { pattern: /^(?!(0[0-9]{0,}$))[0-9]{1,}[.]{0,}[0-9]{0,}$/, required: true, message: '请输入大于0的数字', trigger: 'blur' }
        ],
        withdraw_max_amount: [
          { pattern: /^(?!(0[0-9]{0,}$))[0-9]{1,}[.]{0,}[0-9]{0,}$/, required: true, message: '请输入大于0的数字', trigger: 'blur' }
        ],
        withdraw_audit_amount: [
          { pattern: /^(?!(0[0-9]{0,}$))[0-9]{1,}[.]{0,}[0-9]{0,}$/, required: true, message: '请输入大于0的数字', trigger: 'blur' }
        ],
        withdraw_day_amount: [
          { pattern: /^(?!(0[0-9]{0,}$))[0-9]{1,}[.]{0,}[0-9]{0,}$/, required: true, message: '请输入大于0的数字', trigger: 'blur' }
        ]
      }
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
    getMoneyType().then(res => {
      this.allMoneyType = res.content
      this.init()
    })
  },
  methods: {
    init() {
      getList(this.checkTypetoNum, this.moneyType, this.search, this.currentPage, this.dataType, this.date[0], this.date[1]).then(res => {
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
      if (this.date === null) this.date = ''
      this.currentPage = 1
      this.init()
    },
    // 点击表格行
    clickRow(row) {
      this.rowInfo = row
      walletInfo('payment', this.rowInfo.name).then(res => {
        this.payment = res.content.payment
      })
      this.showInfo = true
    },
    // 通过
    doomPass() {
      this.$confirm('确定通过吗?(请仔细核对，通过后不可取消)', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        passTrial(this.rowInfo.id).then(res => {
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
        passTrial(allId.replace(',', '')).then(res => {
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
        failTrial(this.rowInfo.id, value).then(res => {
          this.showInfo = false
          Message({ message: res.msg, type: 'success' })
          this.init()
        })
      })
    },
    // 打开转账设置
    openTransferSet() {
      this.dialogSet = true
      this.setType = this.allMoneyType[0].id
      getSetValue(this.setType).then(res => {
        this.form = res.content
        this.form2 = JSON.stringify(res.content)
      })
    },
    beforeChangeTabs(val) {
      return new Promise((reslove, reject) => {
        if (JSON.stringify(this.form) !== this.form2) {
          this.$confirm('即将切换页面，是否保存修改的设置?', '提示', {
            confirmButtonText: '保存设置',
            cancelButtonText: '取消',
            type: 'warning'
          }).then(() => {
            reslove()
            this.saveSet()
            getSetValue(val).then(res => {
              this.form = res.content
              this.form2 = JSON.stringify(res.content)
            })
          })
        } else {
          reslove()
          getSetValue(val).then(res => {
            this.form = res.content
            this.form2 = JSON.stringify(res.content)
          })
        }
      })
    },
    // 保存转账设置
    saveSet() {
      var temName = 'transferForm' + this.setType
      this.$refs[temName][0].validate((valid) => {
        if (valid) {
          editSet({ ...this.form, currency_id: this.setType }).then(res => {
            Message({ message: res.msg, type: 'success' })
          })
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    downExcel() {
      if (this.tableDataSelection.length > 0) {
        let id = ''
        this.tableDataSelection.forEach((item, index) => {
          id = `${id}${item.id},`
        })
        getVerifiCode().then(res => {
          var url = `/withdraw/download?download_code=${res.content}&status=${this.checkTypetoNum}&id=${id}`
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
      if (this.date) {
        var str = this.date[0]
        var end = this.date[1]
      } else {
        str = ''
        end = ''
      }
      getVerifiCode().then(res => {
        var url = `/withdraw/download?download_code=${res.content}&status=${this.checkTypetoNum}&currency_id=${this.moneyType}&searchName=${this.search}&type=${this.dataType}&str_time=${str}&end_time=${end}`
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
  width: 150px;
  height: 150px;
  border: 1px solid #ddd;
}

.fade-slide {
  p {
    padding-bottom: 20px;
    span {
      color: #888;
      padding-right: 20px;
    }
    span:nth-child(2) {
      padding-left: 60px;
    }
  }
}
</style>
