<template>
  <div class="app-container" @click.self="showNodeInfo=false">
    <h4>节点管理</h4>

    <el-radio-group v-model="nodeType" @change="changeNodeType">
      <el-radio-button v-for="(item,index) in allType" :key="index" :label="item.name"/>
    </el-radio-group>
    <el-button v-if="buttons[3].child[3].isHave==1" style="float:right;" @click="openNodeSet">节点设置</el-button>
    <el-button v-if="buttons[3].child[2].isHave==1" style="float:right;margin-right:10px;" @click="dialogHistory=true;initHistory()">历史排名</el-button>
    <el-button v-if="buttons[3].child[0].isHave==1" style="float:right;" type="primary" @click="dialogAddNode=true;step=0">新增节点</el-button>
    <el-button style="float:right;" @click="dialogRecom=true;initRecom()">推荐记录</el-button>
    <el-button v-if="buttons[3].child[4].isHave==1" style="float:right;" @click="downExcel">导出excel</el-button>
    <el-button v-if="buttons[3].child[4].isHave==1" style="float:right;" @click="downExcel(0)">导出所有节点</el-button>
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

    已选择<span style="color:#3e84e9;">{{ tableDataSelection.length }}</span>项
    <el-button :disabled="(tableDataSelection.length<1)" size="small" type="danger" plain style="margin-top:20px;" @click="closeAll">停用</el-button>

    <el-table
      :data="tableData"
      :row-class-name="tableRowClassName"
      style="margin:10px 0;"
      @selection-change="handleSelectionChange"
      @row-click="clickRow"
      @sort-change="sortChange">
      <el-table-column type="selection" width="55"/>
      <el-table-column prop="order" label="排名"/>
      <el-table-column prop="name" label="节点名称"/>
      <el-table-column prop="mobile" label="用户"/>
      <el-table-column prop="recommendMobile" label="推荐人手机号"/>
      <el-table-column prop="voteNumber" label="票数"/>
      <el-table-column prop="count" label="支持人数"/>
      <el-table-column prop="grt" label="质押GRT"/>
      <el-table-column prop="bpt" label="质押BPT"/>
      <el-table-column prop="tt" label="质押TT"/>
      <el-table-column prop="isTenure" label="身份">
        <template slot-scope="scope">
          <span v-if="scope.row.isTenure==1">任职</span>
          <span v-if="scope.row.isTenure!=1">候补</span>
        </template>
      </el-table-column>
      <el-table-column prop="createTime" label="加入时间" sortable="custom"/>
      <el-table-column prop="status" label="状态"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="parseInt(total)"
      :page-size="20"
      layout="total, prev, pager, next, jumper"
      @current-change="init"/>

    <transition name="fade">
      <div v-show="showNodeInfo" class="fade-slide">
        <div class="title">
          <img src="@/assets/img/user.jpg" alt="">
          <span class="name">{{ rowInfo.name }}<br><span>{{ rowInfo.typeName }}</span></span>
          <i class="el-icon-close btn" @click="showNodeInfo=false"/>
          <el-button v-show="rowInfo.status!='停用'" type="danger" plain class="btn" style="margin:0 10px;" @click="closeNode">停用</el-button>
          <el-button v-show="rowInfo.status=='停用'" type="primary" class="btn" style="margin:0 10px;" @click="openNode">启用</el-button>
          <el-button v-if="buttons[3].child[1].isHave==1" type="primary" class="btn" @click="nodeBaseEdit">编辑</el-button>
          <el-button v-show="isCandidate&&rowInfo.isTenure==0" type="primary" class="btn" @click="openTenure">任职</el-button>
          <el-button type="primary" class="btn" @click="openTransfer">转让</el-button>
          <el-button v-show="isCandidate&&rowInfo.isTenure==1" type="danger" plain class="btn" @click="closeTenure">卸任</el-button>
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
        <el-tabs v-model="activeName" @tab-click="changeTabs">
          <el-tab-pane label="基本信息" name="-1">
            <span style="padding-right:100px;">销售配额：{{ nodeInfoOther.quota | noContent }}</span>
            <span>剩余销售配额：{{ nodeInfoOther.useQuota | noContent }}</span>
            <p>节点记录</p>
            <el-table :data="nodeInfoOther.list" style="margin:10px 0;">
              <el-table-column prop="type" label="类型"/>
              <el-table-column prop="nodeType" label="节点类型"/>
              <el-table-column prop="mobile" label="用户"/>
              <el-table-column prop="realname" label="姓名"/>
              <el-table-column prop="grt" label="质押GRT"/>
              <el-table-column prop="tt" label="质押TT"/>
              <el-table-column prop="bpt" label="质押BPT"/>
              <el-table-column prop="createTime" label="时间"/>
            </el-table>
          </el-tab-pane>
          <el-tab-pane label="节点信息" name="0">
            <p style="color:#888;">logo</p>
            <img :src="nodeInfoBase.logo" alt="" style="display:block;width:100px;height:100px;border:1px solid #ddd;">
            <p style="color:#888;margin-top:50px;">节点名称</p>
            <p>{{ nodeInfoBase.name }}</p>
            <p style="color:#888;margin-top:50px;">节点简介</p>
            <p v-html="nodeInfoBase.desc2"/>
            <p style="color:#888;margin-top:50px;">社区建设方案</p>
            <p v-html="nodeInfoBase.scheme2"/>
          </el-tab-pane>
          <el-tab-pane label="实名信息" name="1">
            <p>
              <span style="margin-right:150px;">姓名：{{ nodeInfoIdentify.realName }}</span>
              <span>身份证号：{{ nodeInfoIdentify.number }}</span>
            </p>
            <p style="padding-top:20px;">手持身份证正面</p>
            <img :src="nodeInfoIdentify.picFront" alt="">
            <p>手持身份证背面</p>
            <img :src="nodeInfoIdentify.picBack" alt="">
          </el-tab-pane>
          <el-tab-pane label="投票明细" name="2">
            <el-radio-group v-model="pollName" class="radioTabs" @change="currentPageVote=1;initVote()">
              <el-radio-button :label="1">投票记录</el-radio-button>
              <el-radio-button :label="2">支持用户</el-radio-button>
            </el-radio-group>
            <el-table v-show="pollName=='1'" :data="nodeInfoVote">
              <el-table-column prop="mobile" label="手机号"/>
              <el-table-column prop="voteNumber" label="票数"/>
              <el-table-column prop="type" label="投票方式"/>
              <el-table-column prop="createTime" label="投票时间"/>
            </el-table>
            <el-table v-show="pollName=='2'" :data="nodeInfoVote">
              <el-table-column type="index" label="排名"/>
              <el-table-column prop="mobile" label="用户"/>
              <el-table-column prop="voteNumber" label="合计票数"/>
            </el-table>
            <el-pagination
              :current-page.sync="currentPageVote"
              :total="parseInt(totalVote)"
              :page-size="20"
              layout="total, prev, pager, next, jumper"
              @current-change="initVote"/>
          </el-tab-pane>
          <el-tab-pane label="享有权益" name="3">
            <el-table :data="nodeInfoRule" border>
              <el-table-column prop="name" label="权益" align="center"/>
              <el-table-column prop="content" label="描述" align="center"/>
            </el-table>
          </el-tab-pane>
          <el-tab-pane label="收货地址" name="4">
            <p>
              <span style="margin-right:150px;">姓名：{{ nodeInfoAddress.consignee | noContent }}</span>
              <span>电话：{{ nodeInfoAddress.consigneeMobile | noContent }}</span>
            </p>
            <p style="margin-top:50px;">收货地址</p>
            <p>{{ nodeInfoAddress.address | noContent }}</p>
            <p style="margin-top:50px;">邮编</p>
            <p>{{ nodeInfoAddress.zipCode | noContent }}</p>
          </el-tab-pane>
          <el-tab-pane label="推荐记录" name="5">
            <el-table :data="nodeInfoRecommend">
              <el-table-column prop="nodeName" label="节点名称"/>
              <el-table-column prop="typeName" label="节点类型"/>
              <el-table-column prop="username" label="推荐用户"/>
              <el-table-column prop="createTime" label="推荐时间"/>
            </el-table>
          </el-tab-pane>
        </el-tabs>
      </div>
    </transition>

    <el-dialog :visible.sync="dialogEdit" title="节点编辑" class="node-edit">
      <h4>基本信息</h4>
      <div class="item">
        <img :src="nodeInfoBase.logo" alt="">
        <el-upload
          :show-file-list="false"
          :on-success="handleAvatarSuccess"
          :before-upload="beforeAvatarUpload"
          :data="{type:'logo'}"
          name="image_file"
          action="/upload/upload/image">
          <el-button style="margin-top:30px;">更换logo</el-button>
        </el-upload>
      </div>
      <div class="item">
        <div class="title">节点名称</div>
        <el-input v-model="nodeInfoBase.name"/>
      </div>
      <div class="item">
        <div class="title">节点简介</div>
        <el-input v-model="nodeInfoBase.desc" :rows="2" type="textarea"/>
      </div>
      <div class="item">
        <div class="title">社区建设方案</div>
        <el-input v-model="nodeInfoBase.scheme" :rows="2" type="textarea"/>
      </div>
      <div v-if="rowInfo.typeId!=1&&rowInfo.typeId!=5&&!nodeInfoBase.recommendMobile" class="item">
        <div class="title">添加推荐人手机号</div>
        <el-input v-model="nodeInfoBase.recommendMobile2"/>
      </div>
      <hr>
      <h4>权益信息</h4>
      <div class="item">
        <div class="title">销售配额</div>
        <el-input v-model="nodeInfoBase.quota">
          <template slot="append">￥</template>
        </el-input>
      </div>
      <span slot="footer">
        <el-button type="primary" @click="editNodeBase">确 定</el-button>
        <el-button @click="dialogEdit = false">取 消</el-button>
      </span>
    </el-dialog>

    <el-dialog :visible.sync="dialogSet" title="节点设置" class="dialog-set">
      <!-- <el-radio-group v-model="dialogSetType" @change="changeSetType">
        <el-radio-button v-for="(item,index) in allType" :key="index" :label="item.name"/>
      </el-radio-group> -->
      <el-button-group>
        <el-button v-for="(item,index) in allType" :key="index" :class="{activeBtn: item.name==dialogSetType}" @click="changeSetType(item.name)">{{ item.name }}</el-button>
      </el-button-group>
      <el-button style="float:right;" @click="dialogRight = true">权益设置</el-button>
      <!-- <div class="row">节点审核功能<el-switch v-model="dialogSetData.isExamine" active-value="1" inactive-value="0"/></div> -->
      <!-- <div class="row">候选人功能<el-switch v-model="dialogSetData.isCandidate" active-value="1" inactive-value="0"/></div> -->
      <div class="row">节点投票功能<el-switch v-model="dialogSetData.isVote" active-value="1" inactive-value="0"/></div>
      <div class="row">节点排名功能<el-switch v-model="dialogSetData.isOrder" active-value="1" inactive-value="0"/></div>
      <h3 style="padding:20px 0 0;">规则设置</h3>
      <div class="rule">
        <div>
          <p>任职数量</p>
          <el-input v-model="dialogSetData.tenureNum" placeholder="请输入内容"/>
          <p>当前任职数量 {{ this_maxCandidate }}</p>
        </div>
        <div>
          <p>候选数量</p>
          <el-input v-model="dialogSetData.maxCandidate" placeholder="请输入内容"/>
          <p>当前候选数量 {{ this_tenureNum }}</p>
        </div>
        <div>
          <p>折合GRT数量</p>
          <el-input v-model="dialogSetData.conversion" placeholder="请输入内容" style="width:80%;"/> GRT
        </div>
        <div>
          <p>质押GRT</p>
          <el-input v-model="dialogSetData.grt" placeholder="请输入内容" style="width:80%;"/> GRT
        </div>
        <div>
          <p>质押BPT</p>
          <el-input v-model="dialogSetData.bpt" placeholder="请输入内容" style="width:80%;"/> BPT
        </div>
        <div>
          <p>质押TT</p>
          <el-input v-model="dialogSetData.tt" placeholder="请输入内容" style="width:80%;"/> TT
        </div>
        <div>
          <p>赠送GDT</p>
          <el-input v-model="dialogSetData.gdtReward" placeholder="请输入内容" style="width:80%;"/> GDT
        </div>
        <div>
          <p>销售配额</p>
          <el-input v-model="dialogSetData.quota" placeholder="请输入内容" style="width:80%;"/> ￥
        </div>
      </div>
      <h3 style="padding:20px 0 0;">享有权益</h3>
      <div class="right">
        <el-radio-group v-model="dialogSetRightType">
          <el-radio-button label="任职"/>
          <el-radio-button label="候选人"/>
          <el-radio-button label="排名权益"/>
        </el-radio-group>
        <div v-show="dialogSetRightType=='任职'">
          <div class="right-checkbox">
            <el-checkbox v-for="(item,index) in dialogSetRuleList[1]" :key="index" v-model="item.checked">
              {{ item.name }}
            </el-checkbox>
          </div>
        </div>
        <div v-show="dialogSetRightType=='候选人'">
          <div class="right-checkbox">
            <el-checkbox v-for="(item,index) in dialogSetRuleList[0]" :key="index" v-model="item.checked">
              {{ item.name }}
            </el-checkbox>
          </div>
        </div>
        <div v-show="dialogSetRightType=='排名权益'">
          <div v-for="(item,index) in dialogSetRuleList[2]" :key="index" class="right-checkbox">
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
        <el-radio-button label="候选人权益"/>
        <el-radio-button label="排名权益"/>
      </el-radio-group>
      <div v-show="dialogRightName=='任职权益'">
        <div class="rigth-edit">
          <div class="row"><div>权益</div><div>描述</div></div>
          <div v-for="(item,index) in dialogSetRuleList[1]" :key="index" class="row">
            <div><el-input v-model="item.name" placeholder="请输入内容" size="mini"/></div>
            <div><el-input v-model="item.content" placeholder="请输入内容" size="mini"/></div>
            <i class="el-icon-circle-close-outline" @click="deleteRule(1, index)"/>
          </div>
        </div>
        <div class="add">
          <i class="el-icon-circle-plus-outline" @click="addRule(1)"> 添加一行</i>
        </div>
      </div>
      <div v-show="dialogRightName=='候选人权益'">
        <div class="rigth-edit">
          <div class="row"><div>权益</div><div>描述</div></div>
          <div v-for="(item,index) in dialogSetRuleList[0]" :key="index" class="row">
            <div><el-input v-model="item.name" placeholder="请输入内容" size="mini"/></div>
            <div><el-input v-model="item.content" placeholder="请输入内容" size="mini"/></div>
            <i class="el-icon-circle-close-outline" @click="deleteRule(0, index)"/>
          </div>
        </div>
        <div class="add">
          <i class="el-icon-circle-plus-outline" @click="addRule(0)"> 添加一行</i>
        </div>
      </div>
      <div v-show="dialogRightName=='排名权益'">
        <div class="rigth-edit">
          <div class="row"><div>权益</div><div>描述</div></div>
          <div v-for="(item,index) in dialogSetRuleList[2]" :key="index" class="row">
            <div><el-input v-model="item.name" placeholder="请输入内容" size="mini"/></div>
            <div><el-input v-model="item.content" placeholder="请输入内容" size="mini"/></div>
            <i class="el-icon-circle-close-outline" @click="deleteRule(2, index)"/>
          </div>
        </div>
        <div class="add">
          <i class="el-icon-circle-plus-outline" @click="addRule(2)"> 添加一行</i>
        </div>
      </div>
      <span slot="footer">
        <el-button type="primary" @click="saveRuleList">保 存</el-button>
        <el-button @click="dialogRight = false">取 消</el-button>
      </span>
    </el-dialog>

    <el-dialog :visible.sync="dialogHistory" title="历史排名" @closed="dialogHistoryType='超级节点';historyCurrentPage=1;dialogHistorySearch=''">
      <el-radio-group v-model="dialogHistoryType" @change="historyCurrentPage=1;initHistory()">
        <el-radio-button v-for="(item,index) in allType" :key="index" :label="item.name"/>
      </el-radio-group>
      <el-button style="float:right;" @click="downExcelHistory">导出excel</el-button>
      <div style="margin-top:20px;">
        截止时间
        <el-date-picker
          v-model="dialogHistorySearch"
          type="date"
          format="yyyy 年 MM 月 dd 日"
          value-format="yyyy-MM-dd"
          placeholder="选择日期时间"
          @change="historyCurrentPage=1;initHistory()"/>
      </div>
      <el-table :data="dialogHistoryData" style="margin:10px 0;">
        <el-table-column prop="order" label="排名"/>
        <el-table-column prop="nodeName" label="节点名称"/>
        <el-table-column prop="username" label="账号"/>
        <el-table-column prop="voteNumber" label="票数"/>
        <el-table-column prop="count" label="支持人数"/>
        <el-table-column prop="isTenure" label="状态"/>
      </el-table>
      <el-pagination
        :current-page.sync="historyCurrentPage"
        :total="parseInt(historyTotal)"
        :page-size="20"
        layout="total, prev, pager, next, jumper"
        @current-change="initHistory()"/>
    </el-dialog>

    <el-dialog :visible.sync="dialogAddNode" title="新增节点" @closed="clearAddData">
      <el-steps :active="step" finish-status="success" align-center>
        <el-step title="基本信息"/>
        <el-step title="实名认证"/>
        <el-step title="节点信息"/>
      </el-steps>
      <div v-show="step==0" style="margin-top:30px;">
        <el-form ref="addNodeForm1" :model="addNodeData" :rules="rules" label-width="140px">
          <el-form-item prop="mobile" label="手机号">
            <el-input v-model="addNodeData.mobile"/>
          </el-form-item>
          <el-form-item label="微信">
            <el-input v-model="addNodeData.weixin"/>
          </el-form-item>
          <el-form-item v-if="addNodeData.type_id!=1&&addNodeData.type_id!=5" prop="recommendMobile" label="推荐人手机号">
            <el-input v-model="addNodeData.recommendMobile"/>
          </el-form-item>
          <el-form-item prop="type_id" label="节点类型">
            <el-select v-model="addNodeData.type_id" placeholder="请选择" @change="recommendSelect">
              <el-option
                v-for="item in allType"
                :key="item.id"
                :label="item.name"
                :value="item.id"/>
            </el-select>
          </el-form-item>
          <el-form-item prop="is_tenure" label="节点身份">
            <el-select v-model="addNodeData.is_tenure" placeholder="请选择">
              <el-option
                v-for="item in tenureData"
                :key="item.value"
                :label="item.label"
                :value="item.value"
                :disabled="item.disabled"/>
            </el-select>
          </el-form-item>
          <el-form-item prop="grt" label="质押GRT数量">
            <el-input v-model="addNodeData.grt"/>
          </el-form-item>
          <el-form-item label="GRT钱包地址">
            <el-input v-model="addNodeData.grt_address"/>
          </el-form-item>
          <el-form-item prop="bpt" label="质押BPT数量">
            <el-input v-model="addNodeData.bpt"/>
          </el-form-item>
          <el-form-item label="BPT钱包地址">
            <el-input v-model="addNodeData.bpt_address"/>
          </el-form-item>
          <el-form-item prop="tt" label="质押TT数量">
            <el-input v-model="addNodeData.tt"/>
          </el-form-item>
          <el-form-item label="TT钱包地址">
            <el-input v-model="addNodeData.tt_address"/>
          </el-form-item>
          <el-form-item prop="gdtReward" label="赠送GDT数量">
            <el-input v-model="addNodeData.gdtReward"/>
          </el-form-item>
        </el-form>
      </div>
      <div v-show="step==1" style="margin-top:30px;">
        <el-form ref="addNodeForm2" :model="addNodeData" :rules="rules" label-width="140px">
          <el-form-item prop="realname" label="姓名">
            <el-input v-model="addNodeData.realname"/>
          </el-form-item>
          <el-form-item prop="identify" label="身份证号">
            <el-input v-model="addNodeData.identify"/>
          </el-form-item>
          <el-form-item prop="pic_front" label="手持身份证正面照">
            <el-upload
              :show-file-list="false"
              :on-success="addNodeImgF"
              :before-upload="beforeAvatarUpload"
              :data="{type:'identify'}"
              action="/upload/upload/image"
              name="image_file"
              class="avatar-uploader">
              <img v-if="addNodeData.pic_front" :src="addNodeData.pic_front" class="avatar">
              <i v-else class="el-icon-plus avatar-uploader-icon"/>
            </el-upload>
          </el-form-item>
          <el-form-item prop="pic_back" label="手持身份证背面照">
            <el-upload
              :show-file-list="false"
              :on-success="addNodeImgB"
              :before-upload="beforeAvatarUpload"
              :data="{type:'identify'}"
              action="/upload/upload/image"
              name="image_file"
              class="avatar-uploader">
              <img v-if="addNodeData.pic_back" :src="addNodeData.pic_back" class="avatar">
              <i v-else class="el-icon-plus avatar-uploader-icon"/>
            </el-upload>
          </el-form-item>
        </el-form>
      </div>
      <div v-show="step==2||step==3" style="margin-top:30px;">
        <el-form ref="addNodeForm3" :model="addNodeData" :rules="rules" label-width="110px">
          <el-form-item prop="logo" label="节点logo">
            <el-upload
              :show-file-list="false"
              :on-success="addNodeImgLogo"
              :before-upload="beforeAvatarUpload"
              :data="{type:'logo'}"
              action="/upload/upload/image"
              name="image_file"
              class="avatar-uploader">
              <img v-if="addNodeData.logo" :src="addNodeData.logo" class="avatar">
              <i v-else class="el-icon-plus avatar-uploader-icon"/>
            </el-upload>
          </el-form-item>
          <el-form-item prop="name" label="机构/个人名称">
            <el-input v-model="addNodeData.name"/>
          </el-form-item>
          <el-form-item prop="desc" label="机构/个人简介">
            <el-input v-model="addNodeData.desc" :rows="2" type="textarea"/>
          </el-form-item>
          <el-form-item prop="scheme" label="社区建设方案">
            <el-input v-model="addNodeData.scheme" :rows="2" type="textarea"/>
          </el-form-item>
        </el-form>
      </div>
      <span slot="footer">
        <el-button v-show="step>0" type="primary" @click="minStep">上一步</el-button>
        <el-button type="primary" @click="addStep">
          <span v-show="step<2">下一步</span>
          <span v-show="step>=2">确认添加</span>
        </el-button>
      </span>
    </el-dialog>

    <el-dialog :visible.sync="dialogRecom" title="推荐记录">
      <el-input v-model="recomSearchName" clearable placeholder="用户" style="width:250px;" @change="searchRecom">
        <el-button slot="append" icon="el-icon-search" @click.native="searchRecom"/>
      </el-input>
      <el-select v-model="recomType" :clearable="true" placeholder="全部" @change="searchRecom">
        <el-option
          v-for="item in allType"
          :key="item.id"
          :label="item.name"
          :value="item.id"/>
      </el-select>
      <el-button style="float:right;" @click="downRecomExcel">导出excel</el-button>
      <div style="margin-top:20px;display:inline-block;">
        推荐时间
        <el-date-picker
          v-model="recomDate"
          type="daterange"
          range-separator="至"
          start-placeholder="开始日期"
          end-placeholder="结束日期"
          format="yyyy 年 MM 月 dd 日"
          value-format="yyyy-MM-dd"
          style="width:400px;"
          @change="searchRecom"/>
      </div>
      <br>
      <el-table
        :data="recomData"
        style="margin:10px 0;">
        <el-table-column prop="pMoblie" label="用户"/>
        <el-table-column prop="pRealname" label="姓名"/>
        <el-table-column prop="pTypeId" label="类型"/>
        <el-table-column prop="uMobile" label="被推荐用户"/>
        <el-table-column prop="uRealname" label="姓名"/>
        <el-table-column prop="uTypeId" label="类型"/>
        <el-table-column prop="amount" label="赠送投票券"/>
        <el-table-column prop="createTime" label="推荐时间"/>
      </el-table>
      <el-pagination
        :current-page.sync="recomCurrentPage"
        :total="parseInt(recomTotal)"
        :page-size="20"
        layout="total, prev, pager, next, jumper"
        @current-change="initRecom"/>
    </el-dialog>

    <el-dialog :visible.sync="dialogTransfer" title="节点转让" @closed="closedTransfer">
      <el-form ref="transferFrom" :model="fromTransfer" :rules="rulesTransfer" class="transfer-form">
        <p>转让方</p>
        <p><span>手机号：{{ transferForm.mobile }}</span><span>手机号：{{ transferForm.nodeType }}</span></p>
        <p><span>姓名：{{ transferForm.realname }}</span><span>身份证号：{{ transferForm.number }}</span></p>
        <hr>
        <p>受让方</p>
        <el-form-item label="手机号" prop="mobile" label-width="70px">
          <el-input v-model="fromTransfer.mobile"/>
          <p><span>姓名：{{ transferTo.realName }}</span><span>身份证号：{{ transferTo.number }}</span></p>
        </el-form-item>
        <el-form-item label="上传申请凭证" prop="images" label-width="110px">
          <el-upload
            :on-success="transferImgSuccess"
            :on-preview="handlePictureCardPreview"
            :on-remove="handleRemove"
            :before-upload="beforeTransferUpload"
            :on-exceed="transferExceed"
            :data="{type:'node-transfer'}"
            :limit="3"
            :file-list="transferImgListL"
            action="/upload/upload/image"
            name="image_file"
            list-type="picture-card">
            <i class="el-icon-plus"/>
          </el-upload>
          <el-dialog :visible.sync="dialogVisible" append-to-body>
            <img :src="dialogImageUrl" alt="" width="100%">
          </el-dialog>
        </el-form-item>
      </el-form>
      <span slot="footer">
        <el-button @click="dialogTransfer = false">取 消</el-button>
        <el-button type="primary" @click="submitTransfer">提交转让</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getNodeList, getNodeType, getNodeBase, getNodeInfo, getNodeIdentify, getNodeVote, getNodeRule,
  getNodeAddress, onTenure, offTenure, stopNode, onNode, updataBase, getNodeSet, getRuleList, pushRuleList,
  getHistory, pushNodeSet, addNode, checkMobile, checkNode, checkRecomMobile, getNodeRecommend, getRecomList,
  transferFormInfo, checkTransferMobile, postTransfer } from '@/api/nodePage'
import { getVerifiCode } from '@/api/public'
import { Message } from 'element-ui'
import { mapGetters } from 'vuex'

export default {
  name: 'NodeManagement',
  filters: {
    noContent(value) {
      if (value === '' || !value) return '—'
      else return value
    }
  },
  data() {
    const validate = (rule, value, callback) => {
      if (value !== '') {
        if (!this.addNodeData.mobile) {
          callback(new Error('请输入节点手机号'))
        } else if (!/^1\d{10}$/.test(value)) {
          callback(new Error('请输入正确的手机号'))
        } else {
          checkRecomMobile(this.addNodeData.mobile, value).then(res => {
            if (!res) callback(new Error('手机号有误'))
            else callback()
          })
        }
      } else {
        callback()
      }
    }
    const checkTransfer = (rule, value, callback) => {
      if (/^1\d{10}$/.test(value)) {
        checkTransferMobile(value).then(res => {
          if (!res) {
            this.transferTo = {}
            callback(new Error('请输入正确的手机号'))
          } else {
            this.transferTo = res.content
            callback()
          }
        })
      } else {
        callback(new Error('请输入正确的手机号'))
      }
    }
    return {
      allType: [],
      nodeType: '',
      search: '',
      searchDate: '',
      tableData: [], // 表格总数据
      total: 1,
      order: null,
      rowIndex: '',
      tableDataSelection: [],
      currentPage: 1,
      showNodeInfo: false,
      cardData: [
        { name: '当前排名', value: null },
        { name: '用户', value: null },
        { name: '票数', value: null },
        { name: '投票人数', value: null },
        { name: '质押GRT', value: null },
        { name: '质押BPT', value: null },
        { name: '质押TT', value: null },
        { name: '推荐人', value: null }
      ],
      activeName: '-1',
      nodeInfoOther: [], // 1.2新加节点基本信息
      nodeInfoBase: [], // 节点信息
      nodeInfoIdentify: [], // 实名信息
      nodeInfoVote: [], // 投票信息
      totalVote: 1,
      currentPageVote: 1,
      nodeInfoRule: [], // 权限信息
      nodeInfoAddress: [], // 收货地址信息
      nodeInfoRecommend: [], // 推荐记录
      pollName: '1',
      dialogEdit: false,
      uploadLogo: '', // 上传图片后返回的地址
      dialogSet: false,
      dialogSetType: '',
      dialogSetData: {},
      dialogSetData2: {}, // 对比数据
      this_tenureNum: '', // 当前任职数
      this_maxCandidate: '', // 当前候选数
      dialogSetRightType: '任职',
      dialogRight: false,
      dialogRightName: '任职权益',
      dialogSetRuleList: [], // 权益列表
      dialogSetRuleList2: [], // 对比数据
      dialogHistory: false,
      dialogHistoryData: [],
      historyTotal: 1,
      dialogHistorySearch: '',
      dialogHistoryType: '',
      historyCurrentPage: 1,
      dialogAddNode: false,
      step: 0,
      tenureData: [
        { value: 1, label: '任职', disabled: true },
        { value: 0, label: '候选' }
      ],
      jump: false, // 是否跳过实名认证
      addNodeData: {
        mobile: '',
        weixin: '',
        recommendMobile: '',
        type_id: '',
        is_tenure: 0,
        grt: '',
        tt: '',
        bpt: '',
        grt_address: '',
        bpt_address: '',
        tt_address: '',
        gdtReward: '',
        realname: '',
        identify: '',
        pic_front: '',
        pic_back: '',
        logo: 'http://static.vguiren.com/contest_system/logo/2018/10/26/995495bd2d8da9e4b4.jpg',
        name: '暂未设置',
        desc: '该节点很懒什么都没有写',
        scheme: '该节点很懒什么都没有写'
      },
      rules: {
        mobile: [
          { required: true, message: '请输入手机号码', trigger: 'blur' },
          { pattern: /^1\d{10}$/, message: '请输入正确的手机号码', trigger: 'blur' }
        ],
        recommendMobile: [
          { validator: validate, trigger: 'blur' }
        ],
        type_id: [
          { required: true, message: '请选择节点类型', trigger: 'change' }
        ],
        is_tenure: [
          { required: true, message: '请选择节点身份', trigger: 'change' }
        ],
        grt: [
          { pattern: /^(\d|[1-9]\d+)(\.\d+)?$/, required: true, message: '请输入正确的数字', trigger: 'blur' }
        ],
        tt: [
          { pattern: /^(\d|[1-9]\d+)(\.\d+)?$/, message: '请输入正确的数字', trigger: 'blur' }
        ],
        bpt: [
          { pattern: /^(\d|[1-9]\d+)(\.\d+)?$/, message: '请输入正确的数字', trigger: 'blur' }
        ],
        gdtReward: [
          { pattern: /^(\d|[1-9]\d+)(\.\d+)?$/, required: true, message: '请输入正确的数字', trigger: 'blur' }
        ],
        realname: [
          { pattern: /^[\u4e00-\u9fa5]+$/, required: true, message: '请输入中文姓名', trigger: 'blur' }
        ],
        identify: [
          { pattern: /^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/,
            required: true, message: '请输入正确的身份证号', trigger: 'blur' }
        ],
        pic_front: [
          { required: true, message: '请上传身份证', trigger: 'change' }
        ],
        pic_back: [
          { required: true, message: '请上传身份证', trigger: 'change' }
        ],
        logo: [
          { required: true, message: '请上传图片', trigger: 'change' }
        ],
        name: [
          { required: true, message: '必填', trigger: 'blur' },
          { max: 14, message: '最多14个字', trigger: 'blur' }
        ],
        desc: [
          { required: true, message: '必填', trigger: 'blur' },
          { max: 1000, message: '最多1000个字', trigger: 'blur' }
        ],
        scheme: [
          { required: true, message: '必填', trigger: 'blur' },
          { max: 1000, message: '最多1000个字', trigger: 'blur' }
        ]
      },
      dialogRecom: false,
      recomData: [],
      recomCurrentPage: 1,
      recomTotal: 1,
      recomSearchName: '',
      recomType: null,
      recomDate: '',
      dialogTransfer: false,
      fromTransfer: {
        mobile: '',
        images: ''
      },
      rulesTransfer: {
        mobile: [
          { required: true, message: '请输入手机号码', trigger: 'blur' },
          { validator: checkTransfer, trigger: 'blur' }
        ],
        images: [
          { required: true, message: '至少上传一张图片', trigger: 'blur' }
        ]
      },
      transferForm: {}, // 转让方信息
      transferTo: {}, // 受让方信息
      dialogVisible: false,
      dialogImageUrl: '',
      transferImgListL: []
    }
  },
  computed: {
    ...mapGetters([
      'buttons'
    ]),
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
    },
    // 当前节点设置类型id
    setTypeId() {
      let tem = 0
      this.allType.map((item, index) => {
        if (this.dialogSetType === item.name) tem = item.id
      })
      return tem
    },
    rowInfo() {
      if (this.rowIndex === '') return []
      else return this.tableData[this.rowIndex]
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
      this.init()
    })
  },
  methods: {
    init() {
      getNodeList(this.search, this.searchDate[0], this.searchDate[1], this.allType[this.typeIndex].id, this.currentPage, this.order).then(res => {
        this.tableData = res.content.list
        this.total = res.content.count
      })
    },
    // 切换节点类型
    changeNodeType(val) {
      this.search = ''
      this.searchDate = ''
      this.currentPage = 1
      this.showNodeInfo = false
      this.init()
    },
    // 主表格搜索
    searchTableData() {
      if (this.searchDate === null) this.searchDate = ''
      this.currentPage = 1
      this.init()
    },
    // 选择table
    handleSelectionChange(val) {
      this.tableDataSelection = val
    },
    // 排序
    sortChange(val) {
      this.currentPage = 1
      if (val.prop === null) this.order = null
      else if (val.prop === 'createTime' && val.order === 'ascending') this.order = 1
      else if (val.prop === 'createTime' && val.order === 'descending') this.order = 2
      this.init()
    },
    tableRowClassName({ row, rowIndex }) {
      row.index = rowIndex
    },
    // 点击表格行
    clickRow(row) {
      this.rowIndex = row.index
      this.showNodeInfo = true
      this.cardData[0].value = row.order
      this.cardData[1].value = row.mobile
      this.cardData[2].value = row.voteNumber
      this.cardData[3].value = row.count
      this.cardData[4].value = row.grt
      this.cardData[5].value = row.bpt
      this.cardData[6].value = row.tt
      this.cardData[7].value = row.recommendMobile
      this.changeTabs({ name: this.activeName })
    },
    // 选项卡切换
    changeTabs(val) {
      if (val.name === '-1') {
        getNodeBase(this.rowInfo.id).then(res => {
          this.nodeInfoOther = res.content
        })
      } else if (val.name === '0') {
        getNodeInfo(this.rowInfo.id).then(res => {
          this.nodeInfoBase = res.content
          this.nodeInfoBase.scheme2 = res.content.scheme.replace(/\r\n/g, '<br/>').replace(/\n/g, '<br/>').replace(/\s/g, '&nbsp')
          this.nodeInfoBase.desc2 = res.content.desc.replace(/\r\n/g, '<br/>').replace(/\n/g, '<br/>').replace(/\s/g, '&nbsp')
        })
      } else if (val.name === '1') {
        getNodeIdentify(this.rowInfo.id).then(res => {
          this.nodeInfoIdentify = res.content
        })
      } else if (val.name === '2') {
        this.currentPageVote = 1
        this.initVote()
      } else if (val.name === '3') {
        getNodeRule(this.rowInfo.id).then(res => {
          this.nodeInfoRule = res.content
        })
      } else if (val.name === '4') {
        getNodeAddress(this.rowInfo.id).then(res => {
          this.nodeInfoAddress = res.content
        })
      } else if (val.name === '5') {
        getNodeRecommend(this.rowInfo.id).then(res => {
          this.nodeInfoRecommend = res.content
        })
      }
    },
    // 选项卡投票信息的表格数据
    initVote() {
      getNodeVote(this.rowInfo.id, this.pollName, this.currentPageVote).then(res => {
        this.nodeInfoVote = res.content.list
        this.totalVote = res.content.count
      })
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
          this.init()
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
          this.init()
        })
      })
    },
    // 批量停用
    closeAll() {
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
        stopNode(allId.replace(',', '')).then(res => {
          Message({ message: res.msg, type: 'success' })
          this.init()
        })
      })
    },
    // 节点基本信息编辑按钮
    nodeBaseEdit() {
      this.changeTabs({ name: '0' })
      this.dialogEdit = true
    },
    // 上传图片的限制
    beforeAvatarUpload(file) {
      const isImage = file.type === 'image/png' || file.type === 'image/jpeg' || file.type === 'image/jpg' || file.type === 'image/gif'
      const isLt2M = file.size / 1024 / 1024 < 20
      if (!isImage) {
        this.$message.error('上传头像图片只能是jpeg/jpg/png/gif格式!')
      }
      if (!isLt2M) {
        this.$message.error('上传头像图片大小不能超过 20MB!')
      }
      return isImage && isLt2M
    },
    // 上传logo回调
    handleAvatarSuccess(res, file) {
      if (res.code !== 0) return
      this.uploadLogo = res.content
      this.nodeInfoBase.logo = res.content
    },
    // 修改节点基本信息
    editNodeBase() {
      updataBase(this.rowInfo.id, this.nodeInfoBase.logo, this.nodeInfoBase.name,
        this.nodeInfoBase.desc, this.nodeInfoBase.scheme, this.nodeInfoBase.recommendMobile2, this.nodeInfoBase.quota).then(res => {
        Message({ message: res.msg, type: 'success' })
        this.init()
        this.changeTabs({ name: this.activeName })
        this.dialogEdit = false
      })
    },
    // 打开节点设置
    openNodeSet() {
      Promise.all([getNodeSet(this.setTypeId), getRuleList()]).then(res => {
        this.dialogSetData = res[0].content
        this.dialogSetData2 = JSON.parse(JSON.stringify(res[0].content)) // 对比数据
        this.this_tenureNum = res[0].content.allCount // 页面固定值
        this.this_maxCandidate = res[0].content.allTenure // 页面固定值
        return res[1].content
      }).then((res) => {
        res[0].forEach((item, index, arry) => {
          arry[index].checked = false
          arry[index].maxOrder = 0
          arry[index].minOrder = 0
          for (var i = 0; i < this.dialogSetData.ruleList.length; i++) {
            if (this.dialogSetData.ruleList[i].ruleId === item.id) {
              arry[index].checked = true
            }
          }
        })
        res[1].forEach((item, index, arry) => {
          arry[index].checked = false
          arry[index].maxOrder = 0
          arry[index].minOrder = 0
          for (var i = 0; i < this.dialogSetData.ruleList.length; i++) {
            if (this.dialogSetData.ruleList[i].ruleId === item.id) {
              arry[index].checked = true
            }
          }
        })
        res[2].forEach((item, index, arry) => {
          arry[index].checked = false
          arry[index].maxOrder = 1
          arry[index].minOrder = 1
          for (var i = 0; i < this.dialogSetData.ruleList.length; i++) {
            if (this.dialogSetData.ruleList[i].ruleId === item.id) {
              arry[index].checked = true
              arry[index].maxOrder = this.dialogSetData.ruleList[i].maxOrder
              arry[index].minOrder = this.dialogSetData.ruleList[i].minOrder
            }
          }
        })
        this.dialogSetRuleList = res
        this.dialogSetRuleList2 = JSON.parse(JSON.stringify(res)) // 对比数据
        this.dialogSet = true
      })
    },
    // 切换节点设置的类型
    changeSetType(name) {
      if (JSON.stringify(this.dialogSetData) !== JSON.stringify(this.dialogSetData2) ||
        JSON.stringify(this.dialogSetRuleList) !== JSON.stringify(this.dialogSetRuleList2)) {
        this.$confirm('即将切换页面，是否保存修改的设置?', '提示', {
          confirmButtonText: '保存设置',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.saveNodeSet()
        }).then(() => {
          this.dialogSetType = name
          this.dataChange()
        })
      } else {
        this.dialogSetType = name
        this.dataChange()
      }
    },
    // 切换节点设置类型时的数据交换
    dataChange() {
      getNodeSet(this.setTypeId).then(res => {
        this.dialogSetData = res.content
        this.dialogSetData2 = JSON.parse(JSON.stringify(res.content))
        this.this_tenureNum = res.content.allCount
        this.this_maxCandidate = res.content.allTenure
        this.dialogSetRuleList[0].forEach((item, index, arry) => {
          arry[index].checked = false
          arry[index].maxOrder = 0
          arry[index].minOrder = 0
          for (var i = 0; i < this.dialogSetData.ruleList.length; i++) {
            if (this.dialogSetData.ruleList[i].ruleId === item.id) {
              arry[index].checked = true
            }
          }
        })
        this.dialogSetRuleList[1].forEach((item, index, arry) => {
          arry[index].checked = false
          arry[index].maxOrder = 0
          arry[index].minOrder = 0
          for (var i = 0; i < this.dialogSetData.ruleList.length; i++) {
            if (this.dialogSetData.ruleList[i].ruleId === item.id) {
              arry[index].checked = true
            }
          }
        })
        this.dialogSetRuleList[2].forEach((item, index, arry) => {
          arry[index].checked = false
          arry[index].maxOrder = 1
          arry[index].minOrder = 1
          for (var i = 0; i < this.dialogSetData.ruleList.length; i++) {
            if (this.dialogSetData.ruleList[i].ruleId === item.id) {
              arry[index].checked = true
              arry[index].maxOrder = this.dialogSetData.ruleList[i].maxOrder
              arry[index].minOrder = this.dialogSetData.ruleList[i].minOrder
            }
          }
        })
        this.dialogSetRuleList2 = JSON.parse(JSON.stringify(this.dialogSetRuleList))
      })
    },
    // 删除权益
    deleteRule(type, index) {
      if (type === 0) {
        this.dialogSetRuleList[0].splice(index, 1)
      } else if (type === 1) {
        this.dialogSetRuleList[1].splice(index, 1)
      } else {
        this.dialogSetRuleList[2].splice(index, 1)
      }
    },
    // 增加权益
    addRule(type) {
      if (type === 0) {
        this.dialogSetRuleList[0].push({ name: '', content: '', isTenure: '0', checked: false, maxOrder: 0, minOrder: 0 })
      } else if (type === 1) {
        this.dialogSetRuleList[1].push({ name: '', content: '', isTenure: '1', checked: false, maxOrder: 0, minOrder: 0 })
      } else {
        this.dialogSetRuleList[2].push({ name: '', content: '', isTenure: '2', checked: false, maxOrder: 1, minOrder: 1 })
      }
    },
    // 上传权益列表
    saveRuleList() {
      var temData = [[], [], []]
      temData[0] = this.dialogSetRuleList[0]
      temData[1] = this.dialogSetRuleList[1]
      temData[2] = this.dialogSetRuleList[2]
      pushRuleList(temData).then(res => {
        Message({ message: res.msg, type: 'success' })
        this.dialogRight = false
      })
    },
    // 上传节点设置
    saveNodeSet() {
      var temList = []
      this.dialogSetRuleList[0].forEach((item, index, arry) => {
        if (item.checked) {
          arry[index].ruleId = item.id
          temList.push(arry[index])
        }
      })
      this.dialogSetRuleList[1].forEach((item, index, arry) => {
        if (item.checked) {
          arry[index].ruleId = item.id
          temList.push(arry[index])
        }
      })
      this.dialogSetRuleList[2].forEach((item, index, arry) => {
        if (item.checked) {
          arry[index].ruleId = item.id
          temList.push(arry[index])
        }
      })
      this.dialogSetData.ruleList = temList
      pushNodeSet(this.dialogSetData).then(res => {
        Message({ message: res.msg, type: 'success' })
      })
    },
    initHistory() {
      getHistory(this.historyId, this.dialogHistorySearch, this.historyCurrentPage).then(res => {
        this.dialogHistoryData = res.content.list
        this.historyTotal = res.content.count
      })
    },
    // 新增节点节点类型增加默认值
    recommendSelect(val) {
      getNodeSet(val).then(res => {
        this.addNodeData.grt = res.content.grt
        this.addNodeData.bpt = res.content.bpt
        this.addNodeData.tt = res.content.tt
        this.addNodeData.gdtReward = res.content.gdtReward
      })
    },
    // 添加节点下一步
    addStep() {
      if (this.step === 0) {
        this.$refs['addNodeForm1'].validate((valid) => {
          if (valid) {
            Promise.all([checkMobile(this.addNodeData.mobile), checkNode(this.addNodeData.type_id, this.addNodeData.is_tenure)]).then(res => {
              if (res[0].content.isIdentify === 0 && res[1].code === 0) this.step = 1
              if (res[0].content.isIdentify === 1 && res[1].code === 0) {
                this.step = 2
                this.jump = true
              }
            })
          } else {
            console.log('error submit!!')
            return false
          }
        })
      } else if (this.step === 1) {
        this.$refs['addNodeForm2'].validate((valid) => {
          if (valid) {
            this.step = 2
          } else {
            console.log('error submit!!')
            return false
          }
        })
      } else if (this.step === 2) {
        this.$refs['addNodeForm3'].validate((valid) => {
          if (valid) {
            addNode(this.addNodeData).then(res => {
              this.step = 3
              Message({ message: res.msg, type: 'success' })
              this.dialogAddNode = false
            })
          } else {
            console.log('error submit!!')
            return false
          }
        })
      }
    },
    // 添加节点上一步
    minStep() {
      if (this.step === 2 && this.jump) this.step = this.step - 2
      else this.step--
    },
    // 身份证正面回调
    addNodeImgF(res, file) {
      if (res.code !== 0) return
      this.addNodeData.pic_front = res.content
    },
    // 身份证背面回调
    addNodeImgB(res, file) {
      if (res.code !== 0) return
      this.addNodeData.pic_back = res.content
    },
    // LOGO回调
    addNodeImgLogo(res, file) {
      if (res.code !== 0) return
      this.addNodeData.logo = res.content
    },
    // 清除添加节点的信息
    clearAddData() {
      this.$refs['addNodeForm1'].resetFields()
      this.$refs['addNodeForm2'].resetFields()
      this.$refs['addNodeForm3'].resetFields()
      this.step = 0
      this.jump = false
    },
    // 推荐记录
    initRecom() {
      getRecomList(this.recomCurrentPage, this.recomSearchName, this.recomType, this.recomDate[0], this.recomDate[1]).then(res => {
        this.recomData = res.content.list
        this.recomTotal = res.content.count
      })
    },
    searchRecom() {
      if (this.recomDate === null) this.recomDate = ''
      this.recomCurrentPage = 1
      this.initRecom()
    },
    // 打开转让
    openTransfer() {
      transferFormInfo(this.rowInfo.id).then(res => {
        this.transferForm = res.content
        this.dialogTransfer = true
      })
    },
    // 关闭转让的回调
    closedTransfer() {
      this.transferForm = {}
      this.transferTo = {}
      this.fromTransfer.mobile = ''
      this.fromTransfer.images = ''
      this.transferImgListL = []
    },
    // 转让传图片的回调
    handleRemove(file, fileList) {
      this.fromTransfer.images = fileList.map(item =>
        item.response.content
      ).join(',')
    },
    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url
      this.dialogVisible = true
    },
    transferImgSuccess(res, file, fileList) {
      if (res.code !== 0) return
      this.fromTransfer.images = fileList.map(item =>
        item.response.content
      ).join(',')
    },
    // 上传图片超出限制
    transferExceed() {
      Message({ message: '最多上传3张', type: 'warning' })
    },
    // 上传图片的限制
    beforeTransferUpload(file) {
      const isImage = file.type === 'image/png' || file.type === 'image/jpeg' || file.type === 'image/jpg' || file.type === 'image/gif'
      const isLt2M = file.size / 1024 / 1024 < 100
      if (!isImage) {
        this.$message.error('上传图片只能是jpeg/jpg/png/gif格式!')
      }
      if (!isLt2M) {
        this.$message.error('上传图片大小不能超过 100MB!')
      }
      return isImage && isLt2M
    },
    // 提交转让数据
    submitTransfer() {
      this.$refs['transferFrom'].validate((valid) => {
        if (valid) {
          postTransfer(this.transferForm.fromId, this.transferTo.toId, this.fromTransfer.images).then(res => {
            Message({ message: res.msg, type: 'success' })
            this.dialogTransfer = false
          })
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    downExcel(type) {
      if (this.tableDataSelection.length > 0) {
        let id = ''
        this.tableDataSelection.forEach((item, index) => {
          id = `${id}${item.id},`
        })
        getVerifiCode().then(res => {
          var url = `/node/download?download_code=${res.content}&id=${id}`
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
      if (this.searchDate) {
        var str = this.searchDate[0]
        var end = this.searchDate[1]
      } else {
        str = ''
        end = ''
      }
      getVerifiCode().then(res => {
        if (type === 0) {
          var url = `/node/download?download_code=${res.content}&type=0&searchName=${this.search}&str_time=${str}&end_time=${end}`
        } else {
          url = `/node/download?download_code=${res.content}&type=${this.allType[this.typeIndex].id}&searchName=${this.search}&str_time=${str}&end_time=${end}`
        }
        const elink = document.createElement('a')
        elink.style.display = 'none'
        elink.target = '_blank'
        elink.href = url
        document.body.appendChild(elink)
        elink.click()
        document.body.removeChild(elink)
      })
    },
    downExcelHistory() {
      getVerifiCode().then(res => {
        var url = `/node/history-download?download_code=${res.content}&type=${this.historyId}&endTime=${this.dialogHistorySearch}`
        const elink = document.createElement('a')
        elink.style.display = 'none'
        elink.target = '_blank'
        elink.href = url
        document.body.appendChild(elink)
        elink.click()
        document.body.removeChild(elink)
      })
    },
    // 导出推荐记录
    downRecomExcel() {
      if (this.rocomDate) {
        var str = this.rocomDate[0]
        var end = this.rocomDate[1]
      } else {
        str = ''
        end = ''
      }
      getVerifiCode().then(res => {
        var url = `/node/recommend-download?download_code=${res.content}&searchName=${this.recomSearchName}&type=${this.recomType}&strTime=${str}&endTime=${end}`
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
.transfer-form {
  p {
    font-size: 16px;
    span:nth-child(1) {
      display: inline-block;
      width: 300px;
    }
  }
}

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
    justify-content: flex-start;
    flex-wrap: wrap;
    > div {
      margin-right: 20px;
      width: calc(33% - 20px);
    }
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
