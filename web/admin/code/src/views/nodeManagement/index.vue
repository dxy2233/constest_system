<template>
  <div class="app-container" @click.self="showNodeInfo=false">
    <h4>节点管理</h4>

    <el-radio-group v-model="nodeType" @change="changeNodeType">
      <el-radio-button v-for="(item,index) in allType" :key="index" :label="item.name"/>
    </el-radio-group>
    <el-button style="float:right;" @click="openNodeSet">节点设置</el-button>
    <el-button style="float:right;margin-right:10px;" @click="openHistory">历史排名</el-button>
    <el-button style="float:right;" @click="addExcel">导出excel</el-button>
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
        format="yyyy 年 MM 月 dd 日"
        value-format="yyyy-MM-dd"/>
      <el-button @click="searchTableData">查询</el-button>
    </div>
    <br>

    已选择<span style="color:#3e84e9;">2</span>项
    <el-button size="small" style="margin-top:20px;" @click="closeAll">停用</el-button>

    <el-table
      ref="multipleTable"
      :data="tableDataPage"
      style="width: 100%;margin:10px 0;"
      @selection-change="handleSelectionChange"
      @row-click="clickRow">
      <el-table-column type="selection" width="55"/>
      <el-table-column prop="index" label="排名"/>
      <el-table-column prop="name" label="节点名称"/>
      <el-table-column prop="voteNumber" label="票数"/>
      <el-table-column prop="count" label="支持人数"/>
      <el-table-column label="质押资产">
        <template slot-scope="scope">
          {{ scope.row.consume }}GRT
        </template>
      </el-table-column>
      <el-table-column prop="createTime" label="加入时间"/>
      <el-table-column label="状态">
        <template slot-scope="scope">
          <el-button v-if="scope.row.status==1" size="mini" type="success" round>正常</el-button>
          <el-button v-if="scope.row.status!=1" size="mini" type="danger" round>停用</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="total"
      :page-size="20"
      layout="total, prev, pager, next, jumper"
      @current-change="changePage"/>

    <transition name="fade">
      <div v-show="showNodeInfo" class="fade-slide">
        <div class="title">
          <img src="@/assets/img/user.jpg" alt="">
          <span class="name">{{ rowInfo.name }}<br><span>{{ nodeType }}</span></span>
          <i class="el-icon-close btn" @click="showNodeInfo=false"/>
          <el-button v-show="rowInfo.status==1" type="primary" class="btn" style="margin:0 10px;" @click="closeNode">停用</el-button>
          <el-button v-show="rowInfo.status==0" type="primary" class="btn" style="margin:0 10px;" @click="openNode">启用</el-button>
          <el-button type="primary" class="btn" @click="nodeBaseEdit">编辑</el-button>
          <el-button v-show="isCandidate&&rowInfo.isTenure==0" type="primary" class="btn" @click="openTenure">任职</el-button>
          <el-button v-show="isCandidate&&rowInfo.isTenure==1" type="primary" class="btn" @click="closeTenure">卸任</el-button>
        </div>
        <div class="info">
          <el-row :gutter="5" class="info-row">
            <el-col v-for="(item,index) in cardData" :key="index">
              <el-card shadow="never">
                <div class="title">{{ item.name }}</div>
                {{ item.value }}
              </el-card>
            </el-col>
          </el-row>
        </div>
        <el-tabs v-model="activeName" class="tabs" @tab-click="changeTabs">
          <el-tab-pane label="基本信息" name="0">
            <p style="color:#888;">logo</p>
            <img :src="nodeInfoBase.logo" alt="" style="display:block;width:100px;height:100px;border:1px solid #ddd;">
            <p style="color:#888;margin-top:50px;">机构/个人名称</p>
            <p>{{ nodeInfoBase.name }}</p>
            <p style="color:#888;margin-top:50px;">机构/个人简介</p>
            <p>{{ nodeInfoBase.desc }}</p>
            <p style="color:#888;margin-top:50px;">社区建设方案</p>
            <p>{{ nodeInfoBase.scheme }}</p>
          </el-tab-pane>
          <el-tab-pane label="实名信息" name="1">
            <p>
              <span style="margin-right:150px;">姓名：{{ nodeInfoIdentify.realname }}</span>
              <span>身份证号：{{ nodeInfoIdentify.number }}</span>
            </p>
            <p>手持身份证正面</p>
            <img :src="nodeInfoIdentify.picfront" alt="">
            <p>手持身份证背面</p>
            <img :src="nodeInfoIdentify.picback" alt="">
          </el-tab-pane>
          <el-tab-pane label="投票明细" name="2">
            <el-radio-group v-model="pollName" class="radioTabs">
              <el-radio-button label="投票记录"/>
              <el-radio-button label="赎回记录"/>
            </el-radio-group>
            <el-table v-show="pollName=='投票记录'" :data="nodeInfoVote.votelist">
              <el-table-column prop="mobile" label="手机号"/>
              <el-table-column prop="votenumber" label="票数"/>
              <el-table-column prop="createtime" label="投票时间"/>
            </el-table>
            <el-table v-show="pollName=='赎回记录'" :data="nodeInfoVote.orderlist">
              <el-table-column type="index" label="排名"/>
              <el-table-column prop="mobile" label="用户"/>
              <el-table-column prop="votenumber" label="合计票数"/>
            </el-table>
          </el-tab-pane>
          <el-tab-pane label="享有权益" name="3">
            <el-table :data="nodeInfoRule" border>
              <el-table-column prop="name" label="权益" align="center"/>
              <el-table-column prop="content" label="描述" align="center"/>
            </el-table>
          </el-tab-pane>
        </el-tabs>
      </div>
    </transition>

    <el-dialog :visible.sync="dialogEdit" title="节点编辑" class="node-edit">
      <div class="item">
        <div class="title">基本信息</div>
        <img :src="nodeInfoBase.logo" alt="">
        <el-upload
          :show-file-list="false"
          :on-success="handleAvatarSuccess"
          :data="{type:'logo'}"
          name="image_file"
          action="http://admin.contest_system.local/upload/upload/image">
          <el-button style="margin-top:30px;">更换logo</el-button>
        </el-upload>
      </div>
      <div class="item">
        <div class="title">机构/个人名称</div>
        <el-input v-model="nodeInfoBase.name"/>
      </div>
      <div class="item">
        <div class="title">机构/个人简介</div>
        <el-input v-model="nodeInfoBase.desc" :rows="2" type="textarea"/>
      </div>
      <div class="item">
        <div class="title">社区建设方案</div>
        <el-input v-model="nodeInfoBase.scheme" :rows="2" type="textarea"/>
      </div>
      <span slot="footer">
        <el-button type="primary" @click="editNodeBase">确 定</el-button>
        <el-button @click="dialogEdit = false">取 消</el-button>
      </span>
    </el-dialog>

    <el-dialog :visible.sync="dialogSet" title="节点设置" class="dialog-set">
      <el-radio-group v-model="dialogSetType">
        <el-radio-button v-for="(item,index) in allType" :key="index" :label="item.name"/>
      </el-radio-group>
      <el-button style="float:right;" @click="openRule">权益设置</el-button>
      <div class="row">节点审核功能<el-switch v-model="dialogSetData.isExamine" active-value="1" inactive-value="0"/></div>
      <div class="row">候选人功能<el-switch v-model="dialogSetData.isCandidate" active-value="1" inactive-value="0"/></div>
      <div class="row">节点投票功能<el-switch v-model="dialogSetData.isVote" active-value="1" inactive-value="0"/></div>
      <div class="row">节点排名功能<el-switch v-model="dialogSetData.isOrder" active-value="1" inactive-value="0"/></div>
      <h3 style="padding:20px 0 0;">规则设置</h3>
      <div class="rule">
        <div>
          <p>任职数量</p>
          <el-input v-model="dialogSetData.tenureNum" placeholder="请输入内容"/>
          <p>当前任职数量 {{ this_tenureNum }}</p>
        </div>
        <div>
          <p>候选数量</p>
          <el-input v-model="dialogSetData.maxCandidate" placeholder="请输入内容"/>
          <p>当前候选数量 {{ this_maxCandidate }}</p>
        </div>
        <div>
          <p>质押资产</p>
          <el-input v-model="dialogSetData.minMoney" placeholder="请输入内容" style="width:80%;"/> GRT
        </div>
      </div>
      <h3 style="padding:20px 0 0;">享有权益</h3>
      <div class="right">
        <el-radio-group v-model="dialogSetRightType">
          <el-radio-button label="任职"/>
          <el-radio-button label="排名权益"/>
        </el-radio-group>
        <div v-show="dialogSetRightType=='任职'">
          <div class="right-checkbox">
            <el-checkbox v-for="(item,index) in dialogSetRuleList.isTenure" :key="index" v-model="item.checked">
              {{ item.name }}
            </el-checkbox>
          </div>
        </div>
        <div v-show="dialogSetRightType=='排名权益'">
          <div v-for="(item,index) in dialogSetRuleList.noTenure" :key="index" class="right-checkbox">
            <div class="row">
              <el-checkbox v-model="item.checked" style="flex:3;">{{ item.name }}</el-checkbox>
              <span>排名</span>
              <el-select v-model="item.minOrder" placeholder="请选择" size="mini" style="width:100px;">
                <el-option v-for="(item,index) in 100" :key="index" :value="index+1"/>
              </el-select>
              <span>——</span>
              <el-select v-model="item.maxOrder" placeholder="请选择" size="mini" style="width:100px;">
                <el-option v-for="(item,index) in 100" :key="index" :value="index+1"/>
              </el-select>
            </div>
          </div>
        </div>
      </div>
      <span slot="footer">
        <el-button type="primary" @click="saveNodeSet">确认修改</el-button>
        <el-button @click="dialogSet = false">取 消</el-button>
      </span>
    </el-dialog>
    <el-dialog :visible.sync="dialogRight" title="权益设置" class="dialog-right">
      <el-radio-group v-model="dialogRightName" class="radioTabs">
        <el-radio-button label="任职权益"/>
        <el-radio-button label="排名权益"/>
      </el-radio-group>
      <div v-show="dialogRightName=='任职权益'">
        <div class="rigth-edit">
          <div class="row"><div>权益</div><div>描述</div></div>
          <div v-for="(item,index) in dialogSetRuleList.isTenure" :key="index" class="row">
            <div><el-input v-model="item.name" placeholder="请输入内容" size="mini"/></div>
            <div><el-input v-model="item.content" placeholder="请输入内容" size="mini"/></div>
            <i class="el-icon-circle-close-outline" @click="deleteRule(true, index)"/>
          </div>
        </div>
        <div class="add">
          <i class="el-icon-circle-plus-outline" @click="addRule(true)"> 添加一行</i>
        </div>
      </div>
      <div v-show="dialogRightName=='排名权益'">
        <div class="rigth-edit">
          <div class="row"><div>权益</div><div>描述</div></div>
          <div v-for="(item,index) in dialogSetRuleList.noTenure" :key="index" class="row">
            <div><el-input v-model="item.name" placeholder="请输入内容" size="mini"/></div>
            <div><el-input v-model="item.content" placeholder="请输入内容" size="mini"/></div>
            <i class="el-icon-circle-close-outline" @click="deleteRule(false, index)"/>
          </div>
        </div>
        <div class="add">
          <i class="el-icon-circle-plus-outline" @click="addRule(false)"> 添加一行</i>
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="saveRuleList">保 存</el-button>
        <el-button @click="dialogRight = false">取 消</el-button>
      </span>
    </el-dialog>

    <el-dialog :visible.sync="dialogHistory" title="历史排名">
      <el-radio-group v-model="dialogHistoryType" @change="changeHistoryType">
        <el-radio-button v-for="(item,index) in allType" :key="index" :label="item.name"/>
      </el-radio-group>
      <el-button style="float:right;" @click="addExcelHistory">导出excel</el-button>
      <div style="margin-top:20px;">
        截止时间
        <el-date-picker
          v-model="dialogHistorySearch"
          type="datetime"
          format="yyyy 年 MM 月 dd 日 HH：mm"
          value-format="yyyy-MM-dd HH:mm"
          placeholder="选择日期时间"
          style="width:250px;"/>
        <el-button @click="historySearch">查询</el-button>
      </div>
      <el-table :data="dialogHistoryDataPage" style="margin:10px 0;">
        <el-table-column prop="index" label="排名"/>
        <el-table-column prop="name" label="节点名称"/>
        <el-table-column prop="username" label="账号"/>
        <el-table-column prop="voteNumber" label="票数"/>
        <el-table-column prop="count" label="支持人数"/>
        <el-table-column label="状态">
          <template slot-scope="scope">
            <span v-if="scope.row.status==1">在职</span>
            <span v-else>候选</span>
          </template>
        </el-table-column>
      </el-table>
      <el-pagination
        :current-page.sync="historyCurrentPage"
        :total="historyTotal"
        :page-size="20"
        layout="total, prev, pager, next, jumper"
        @current-change="changeHistoryPage"/>
    </el-dialog>
  </div>
</template>

<script>
import { getNodeList, getNodeType, getNodeBase, getNodeIdentify, getNodeVote, getNodeRule,
  onTenure, offTenure, stopNode, onNode, updataBase, getNodeSet, getRuleList, pushRuleList,
  getHistory, pushNodeSet } from '@/api/nodePage'
import { Message } from 'element-ui'
import { parseTime, pagination } from '@/utils'

export default {
  name: 'NodeManagement',
  data() {
    return {
      allType: [],
      nodeType: '',
      search: '',
      searchDate: '',
      tableData: [], // 表格总数据
      tableDataPage: [], // 每页展示数据
      rowInfo: [],
      tableDataSelection: [],
      currentPage: 1,
      showNodeInfo: false,
      cardData: [
        { name: '当前排名', value: null },
        { name: '用户', value: null },
        { name: '票数', value: null },
        { name: '投票人数', value: null },
        { name: '资产质押', value: null }
      ],
      activeName: 0,
      nodeInfoBase: [], // 基本信息
      nodeInfoIdentify: [], // 实名信息
      nodeInfoVote: [], // 投票信息
      nodeInfoRule: [], // 权限信息
      pollName: '投票记录',
      dialogEdit: false,
      uploadLogo: '', // 上传图片后返回的地址
      dialogSet: false,
      dialogSetType: '',
      dialogSetData: {},
      this_tenureNum: '', // 当前任职数
      this_maxCandidate: '', // 当前候选数
      dialogSetRightType: '任职',
      dialogRight: false,
      dialogRightName: '任职权益',
      dialogSetRuleList: [], // 权益列表
      dialogHistory: false,
      dialogHistoryData: [],
      dialogHistoryDataPage: [],
      dialogHistorySearch: '',
      dialogHistoryType: '',
      historyCurrentPage: 1
    }
  },
  computed: {
    // tableData总页数
    total() {
      return this.tableData.length
    },
    // 历史排名总页数
    historyTotal() {
      return this.dialogHistoryData.length
    },
    // 当前节点类型索引
    typeIndex() {
      let tem = 0
      this.allType.map((item, index) => {
        if (this.nodeType === item.name) tem = index
      })
      return tem
    },
    // 当前节点类型是否能任职
    isCandidate() {
      let tem = 10
      this.allType.map((item, index) => {
        if (this.nodeType === item.name) tem = item.isCandidate
      })
      return tem
    },
    // 当前历史排名节点id
    historyId() {
      let tem = 0
      this.allType.map((item, index) => {
        if (this.dialogHistoryType === item.name) tem = item.id
      })
      return tem
    }
  },
  created() {
    // 获取节点类型后获取第一个类型的数据
    getNodeType().then(res => {
      this.allType = res.content
      this.nodeType = res.content[0].name
      this.dialogSetType = res.content[0].name
      this.dialogHistoryType = res.content[0].name
    }).then(res => {
      getNodeList(null, null, null, this.allType[0].id).then(res => {
        this.tableData = res.content
        this.tableData.forEach((item, index, arry) => {
          Object.assign(item, { index: index + 1 })
        })
        this.tableDataPage = pagination(this.tableData, this.currentPage, 20)
      })
    })
  },
  methods: {
    // 切换节点类型
    changeNodeType(val) {
      getNodeList(null, null, null, this.allType[this.typeIndex].id).then(res => {
        this.tableData = res.content
      })
    },
    // 主表格搜索
    searchTableData() {
      getNodeList(this.search, this.searchDate[0], this.searchDate[1], this.allType[this.typeIndex].id).then(res => {
        this.tableData = res.content
        this.tableData.forEach((item, index, arry) => {
          Object.assign(item, { index: index + 1 })
        })
        this.tableDataPage = pagination(this.tableData, this.currentPage, 20)
      })
    },
    // 选择table
    handleSelectionChange(val) {
      this.tableDataSelection = val
    },
    // 变页数
    changePage(page) {
      this.tableDataPage = pagination(this.tableData, page, 20)
    },
    // 点击表格行
    clickRow(row) {
      this.rowInfo = row
      this.showNodeInfo = true
      this.cardData[0].value = row.index
      this.cardData[1].value = row.username
      this.cardData[2].value = row.voteNumber
      this.cardData[3].value = row.count
      this.cardData[4].value = row.consume
      this.changeTabs({ name: this.activeName })
    },
    // 选项卡切换
    changeTabs(val) {
      if (val.name === '0') {
        getNodeBase(this.rowInfo.id).then(res => {
          this.nodeInfoBase = res.content
        })
      } else if (val.name === '1') {
        getNodeIdentify(this.rowInfo.id).then(res => {
          this.nodeInfoIdentify = res.content
        })
      } else if (val.name === '2') {
        getNodeVote(this.rowInfo.id).then(res => {
          this.nodeInfoVote = res.content
        })
      } else if (val.name === '3') {
        getNodeRule(this.rowInfo.id).then(res => {
          this.nodeInfoRule = res.content
        })
      }
    },
    // 任职
    openTenure() {
      this.$confirm('确定任职?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        onTenure(this.rowInfo.id).then(res => {
          Message({ message: res.msg, type: 'success' })
          this.rowInfo.isTenure = 1
        })
      })
    },
    // 卸任
    closeTenure() {
      this.$confirm('确定卸任?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        offTenure(this.rowInfo.id).then(res => {
          Message({ message: res.msg, type: 'success' })
          this.rowInfo.isTenure = 0
        })
      })
    },
    // 启用
    openNode() {
      this.$confirm('确定启用?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        onNode(this.rowInfo.id).then(res => {
          Message({ message: res.msg, type: 'success' })
          this.rowInfo.status = 1
        })
      })
    },
    // 停用
    closeNode() {
      this.$confirm('确定停用?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        stopNode(this.rowInfo.id).then(res => {
          Message({ message: res.msg, type: 'success' })
          this.rowInfo.status = 0
        })
      })
    },
    // 批量停用
    closeAll() {
      this.$confirm('确定停用吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        let allId = ''
        this.tableDataSelection.map((item, index, items) => {
          allId = allId + ',' + item.id
        })
        stopNode(allId.replace(',', '')).then(res => {
          Message({ message: res.msg, type: 'success' })
          getNodeList(null, null, null, this.allType[this.typeIndex].id).then(res => {
            this.tableData = res.content
            this.tableData.forEach((item, index, arry) => {
              Object.assign(item, { index: index + 1 })
            })
            this.tableDataPage = pagination(this.tableData, this.currentPage, 20)
          })
        })
      })
    },
    // 节点基本信息编辑按钮
    nodeBaseEdit() {
      this.dialogEdit = true
      this.changeTabs({ name: '0' })
    },
    // 上传logo回调
    handleAvatarSuccess(res, file) {
      this.uploadLogo = res.content
      this.nodeInfoBase.logo = URL.createObjectURL(file.raw)
    },
    // 修改节点基本信息
    editNodeBase() {
      this.dialogEdit = false
      updataBase(this.rowInfo.id, this.uploadLogo, this.nodeInfoBase.name,
        this.nodeInfoBase.desc, this.nodeInfoBase.scheme).then(res => {
        Message({ message: res.msg, type: 'success' })
      })
    },
    // 打开节点设置
    openNodeSet() {
      getNodeSet(this.allType[0].id).then(res => {
        this.dialogSetData = res.content
        this.this_tenureNum = res.content.tenureNum
        this.this_maxCandidate = res.content.maxCandidate
      }).then(res => {
        getRuleList().then(res => {
          this.dialogSetRuleList = res.content
        }).then(() => {
          this.dialogSetRuleList.isTenure.forEach((item, index, arry) => {
            for (var i = 0; i < this.dialogSetData.ruleList.length; i++) {
              if (this.dialogSetData.ruleList[i].ruleId === item.id) {
                arry[index].checked = true
                arry[index].maxOrder = this.dialogSetData.ruleList[i].maxOrder
                arry[index].minOrder = this.dialogSetData.ruleList[i].minOrder
              } else {
                arry[index].maxOrder = 0
                arry[index].minOrder = 0
              }
            }
          })
          this.dialogSetRuleList.noTenure.forEach((item, index, arry) => {
            for (var i = 0; i < this.dialogSetData.ruleList.length; i++) {
              if (this.dialogSetData.ruleList[i].ruleId === item.id) {
                arry[index].checked = true
                arry[index].maxOrder = this.dialogSetData.ruleList[i].maxOrder
                arry[index].minOrder = this.dialogSetData.ruleList[i].minOrder
              } else {
                arry[index].maxOrder = 1
                arry[index].minOrder = 1
              }
            }
          })
          this.dialogSet = true
        })
      })
    },
    // 打开权益设置
    openRule() {
      getRuleList().then(res => {
        this.dialogSetRuleList = res.content
      })
      this.dialogRight = true
    },
    // 删除权益
    deleteRule(type, index) {
      if (type) {
        this.dialogSetRuleList.isTenure.splice(index, 1)
      } else {
        this.dialogSetRuleList.noTenure.splice(index, 1)
      }
    },
    // 增加权益
    addRule(type) {
      if (type) {
        this.dialogSetRuleList.isTenure.push({ name: '', content: '', isTenure: '1' })
      } else {
        this.dialogSetRuleList.noTenure.push({ name: '', content: '', isTenure: '0' })
      }
    },
    // 上传权益列表
    saveRuleList() {
      var temData = [[], []]
      temData[0] = this.dialogSetRuleList.isTenure
      temData[1] = this.dialogSetRuleList.noTenure
      pushRuleList(temData).then(res => {
        Message({ message: res.msg, type: 'success' })
        this.dialogRight = false
      })
    },
    // 上传节点设置
    saveNodeSet() {
      var temList = []
      this.dialogSetRuleList.isTenure.forEach((item, index, arry) => {
        if (item.checked) {
          arry[index].ruleId = item.id
          temList.push(arry[index])
        }
      })
      this.dialogSetRuleList.noTenure.forEach((item, index, arry) => {
        if (item.checked) {
          arry[index].ruleId = item.id
          temList.push(arry[index])
        }
      })
      this.dialogSetData.ruleList = temList
      pushNodeSet(this.dialogSetData).then(res => {
        Message({ message: res.msg, type: 'success' })
        this.dialogSet = false
      })
    },
    // 打开历史排名
    openHistory() {
      this.dialogHistory = true
      getHistory(this.historyId).then(res => {
        this.dialogHistoryData = res.content
        this.dialogHistoryData.forEach((item, index, arry) => {
          Object.assign(item, { index: index + 1 })
        })
        this.dialogHistoryDataPage = pagination(this.dialogHistoryData, this.historyCurrentPage, 20)
      })
    },
    // 变页数
    changeHistoryPage(page) {
      this.dialogHistoryDataPage = pagination(this.dialogHistoryData, page, 20)
    },
    // 切换历史排名的表格类型
    changeHistoryType(val) {
      getHistory(this.historyId).then(res => {
        this.dialogHistoryData = res.content
        this.dialogHistoryData.forEach((item, index, arry) => {
          Object.assign(item, { index: index + 1 })
        })
        this.dialogHistoryDataPage = pagination(this.dialogHistoryData, this.historyCurrentPage, 20)
      })
    },
    // 历史排名搜索
    historySearch() {
      if (this.dialogHistorySearch) {
        getHistory(this.historyId, this.dialogHistorySearch).then(res => {
          this.dialogHistoryData = res.content
          this.dialogHistoryData.forEach((item, index, arry) => {
            Object.assign(item, { index: index + 1 })
          })
          this.dialogHistoryDataPage = pagination(this.dialogHistoryData, this.historyCurrentPage, 20)
        })
      }
    },
    // 导出excel
    addExcel() {
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['排名', '节点名称', '票数', '支持人数', '质押资产', '加入时间', '状态']
        const filterVal = ['index', 'name', 'voteNumber', 'count', 'consume', 'createTime', 'status']
        const list = this.tableData
        const data = this.formatJson(filterVal, list)
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: '节点管理'
        })
      })
    },
    addExcelHistory() {
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['排名', '节点名称', '账号', '票数', '支持人数', '状态']
        const filterVal = ['index', 'name', 'username', 'voteNumber', 'count', 'status']
        const list = this.dialogHistoryData
        const data = this.formatJson(filterVal, list)
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: '历史排名'
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
.node-edit {
  .item {
    margin-bottom: 20px;
    clear: both;
    img {
      display: inline-block;
      width: 100px;
      height: 100px;
      border: 1px solid #ddd;
      border-radius: 50%;
      float: left;
      margin: 0 20px 10px 0;
    }
    .title {
      margin-bottom: 10px;
    }
  }
}

.dialog-set {
  margin-top: -100px;
  .row {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
  }
  .rule {
    display: flex;
    justify-content: space-between;
  }
  .right-checkbox {
    display: flex;
    flex-flow: column;
    > label {
      margin: 20px 0 0;
      border-bottom: 1px solid #ddd;
      padding-bottom: 5px;
    }
    > .row {
      align-items: center;
      > span {
        font-size: 14px;
        padding: 0 5px;
      }
    }
  }
}

.dialog-right {
  .rigth-edit {
    float: left;
    width: 90%;
    margin: 20px 0;
    border: 1px solid #ddd;
    .row {
      height: 30px;
      line-height: 30px;
      text-align: center;
      display: flex;
      justify-content: space-between;
      position: relative;
      i {
        font-size: 24px;
        padding: 6px 0 0 10px;
        position: absolute;
        top: -2px;
        right: -30px;
      }
      :nth-child(1) {
        flex: 1;
        border-right: 1px solid #ddd;
      }
      :nth-child(2) {
        flex: 1;
      }
    }
    .row:not(:last-child) {
      border-bottom: 1px solid #ddd;
    }
    /deep/ input {
      text-align: center;
    }
  }
  .add {
    clear: both;
    font-size: 16px;
  }
  i:hover {
    cursor: pointer;
  }
}
</style>
