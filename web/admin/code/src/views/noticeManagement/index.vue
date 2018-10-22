<template>
  <div class="app-container" @click.self="showNoticeInfo=false">
    <el-radio-group v-model="noticeType" class="radioTabs" @change="changeNoticeType">
      <el-radio-button label="已上架"/>
      <el-radio-button label="下架中"/>
      <el-radio-button label="全部"/>
    </el-radio-group>
    <el-button class="btn-right" @click="openAddNotice">公告发布</el-button>
    <el-button class="btn-right" type="primary" style="margin-right:10px;" @click="openSet">公告设置</el-button>

    已选择<span style="color:#3e84e9;">{{ tableDataSelection.length }}</span>项
    <el-button :disabled="(tableDataSelection.length<1)" size="small" type="danger" plain style="margin-top:20px;" @click="allDelete">删除</el-button>

    <el-table
      :data="tableDataPage"
      style="margin:10px 0;"
      @selection-change="handleSelectionChange"
      @row-click="clickRow">
      <el-table-column type="selection" width="55"/>
      <el-table-column label="展示图">
        <template slot-scope="scope">
          <img :src="scope.row.image" alt="" style="display:block;width:100px;height:100px;border:1px solid #ddd;">
        </template>
      </el-table-column>
      <el-table-column prop="title" label="公告标题"/>
      <el-table-column prop="click" label="点击量"/>
      <el-table-column label="状态">
        <template slot-scope="scope">
          <div v-if="scope.row.status==1">上架</div>
          <div v-else>下架</div>
        </template>
      </el-table-column>
      <el-table-column prop="isTop" label="排序">
        <template slot-scope="scope">
          <el-button v-if="scope.row.isTop==1" type="text" @click.native.stop="unpin(scope.row.id, scope.$index)">取消置顶</el-button>
          <el-button v-else type="text" @click.native.stop="topping(scope.row.id, scope.$index)">置顶</el-button>
        </template>
      </el-table-column>
      <el-table-column prop="createTime" label="发布时间"/>
      <el-table-column prop="updateTime" label="最近一次修改时间"/>
    </el-table>
    <el-pagination
      :current-page.sync="currentPage"
      :total="total"
      :page-size="pageSize"
      layout="total, prev, pager, next, jumper"/>

    <transition name="fade">
      <div v-show="showNoticeInfo" class="fade-slide">
        <div class="title">
          <img src="@/assets/img/user.jpg" alt="">
          <span class="name">{{ rowInfo.title }}<br><span>{{ noticeType }}</span></span>
          <i class="el-icon-close btn" @click="showNoticeInfo=false"/>
          <el-button type="danger" plain class="btn" style="margin:0 10px;" @click="noticeDelete">删除</el-button>
          <el-button v-show="rowInfo.status==1" type="primary" class="btn" @click="ifShelf(false)">下架</el-button>
          <el-button v-show="rowInfo.status==0" type="primary" class="btn" @click="ifShelf(true)">上架</el-button>
          <el-button class="btn" @click="noticeEdit">编辑</el-button>
        </div>
        <p style="margin-top:40px;">展示图</p>
        <img :src="rowInfo.image" alt="" style="display:block;width:200px;height:200px;border:1px solid #ddd;">
        <p>公告类型</p>
        <div v-if="rowInfo.type==0">
          <p>链接 <a :href="rowInfo.url" style="text-decoration: underline;color:#888;">{{ rowInfo.url }}</a></p>
        </div>
        <div v-else v-html="rowInfo.detail"/>
      </div>
    </transition>

    <el-dialog :visible.sync="dialogSet" title="公告设置">
      <div v-if="showCountData[0]">
        <p>首页展示数量</p>
        <el-select v-model="showCount" placeholder="请选择">
          <el-option v-for="(item,index) in showCountData[0].initialize" :key="index" :value="item"/>
        </el-select>
        <p>{{ showCountData[0].remark }}</p>
      </div>
      <span slot="footer">
        <el-button type="primary" @click="saveNoticeSet">确认修改</el-button>
        <el-button @click="dialogSet = false">取 消</el-button>
      </span>
    </el-dialog>

    <el-dialog :visible.sync="dialogRelease" title="发布公告" class="release">
      <el-form label-width="100px">
        <el-form-item label="首页展示图" required>
          <el-upload
            :data="{type:'notice'}"
            :show-file-list="false"
            :on-success="uploadSuccess"
            name="image_file"
            action="http://admin.contest_system.local/upload/upload/image"
            class="avatar-uploader">
            <img v-if="releaseData.image" :src="releaseData.image" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"/>
          </el-upload>
        </el-form-item>
        <el-form-item label="公告标题" required>
          <el-input v-model="releaseData.title"/>
        </el-form-item>
        <el-form-item label="时间">
          <el-date-picker
            v-model="releaseData.date"
            type="datetimerange"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            format="yyyy 年 MM 月 dd 日 HH：mm"
            value-format="yyyy-MM-dd HH:mm"
            style="width:100%;"/>
        </el-form-item>
        <el-form-item label="公告类型" required>
          <el-select v-model="releaseData.type">
            <el-option
              v-for="item in allType"
              :key="item.value"
              :label="item.label"
              :value="item.value"/>
          </el-select>
        </el-form-item>
        <el-form-item v-show="releaseData.type==0" label="链接地址" required>
          <el-input v-model="releaseData.url"/>
        </el-form-item>
        <el-form-item v-show="releaseData.type==1" label="正文内容" required>
          <div>
            <tinymce :height="300" v-model="releaseData.detail"/>
          </div>
          <div v-html="releaseData.detail"/>
        </el-form-item>
      </el-form>
      <span slot="footer">
        <el-button type="primary" @click="saveNtice(true)">保存并发布</el-button>
        <el-button type="primary" @click="saveNtice(false)">保存</el-button>
        <el-button @click="dialogRelease = false">取 消</el-button>
      </span>
    </el-dialog>

    <el-dialog :visible.sync="dialogEdit" title="发布公告" class="release">
      <el-form label-width="100px">
        <el-form-item label="首页展示图" required>
          <el-upload
            :data="{type:'notice'}"
            :show-file-list="false"
            :on-success="uploadSuccess"
            name="image_file"
            action="http://admin.contest_system.local/upload/upload/image"
            class="avatar-uploader">
            <img v-if="rowInfo.image" :src="rowInfo.image" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"/>
          </el-upload>
        </el-form-item>
        <el-form-item label="公告标题" required>
          <el-input v-model="rowInfo.title"/>
        </el-form-item>
        <el-form-item label="时间">
          <el-date-picker
            v-model="rowInfo.date"
            type="datetimerange"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            format="yyyy 年 MM 月 dd 日 HH：mm"
            value-format="yyyy-MM-dd HH:mm"
            style="width:100%;"/>
        </el-form-item>
        <el-form-item label="公告类型" required>
          <el-select v-model="rowInfo.type">
            <el-option
              v-for="item in allType"
              :key="item.value"
              :label="item.label"
              :value="item.value"/>
          </el-select>
        </el-form-item>
        <el-form-item v-show="rowInfo.type==0" label="链接地址" required>
          <el-input v-model="rowInfo.url"/>
        </el-form-item>
        <el-form-item v-show="rowInfo.type==1" label="正文内容" required>
          <div>
            <tinymce :height="300" v-model="rowInfo.detail"/>
          </div>
          <div v-html="rowInfo.detail"/>
        </el-form-item>
      </el-form>
      <span slot="footer">
        <el-button type="primary" @click="editNtice">保存</el-button>
        <el-button @click="dialogEdit = false">取 消</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getNoticeList, deleteNotice, toTop, unTop, getNoticeSetList, pushNoticeSet,
  onShelf, offShelf, addNotice, editNotice } from '@/api/notice'
import { Message } from 'element-ui'
import { pagination } from '@/utils'
import Tinymce from '@/components/Tinymce'

export default {
  name: 'NoticeManagement',
  components: { Tinymce },
  data() {
    return {
      noticeType: '已上架',
      tableData: [],
      tableDataSelection: [],
      currentPage: 1,
      pageSize: 20,
      showNoticeInfo: false,
      rowInfo: [],
      dialogSet: false,
      showCountData: '',
      showCount: '',
      dialogRelease: false,
      dialogEdit: false,
      allType: [
        { value: '0', label: '链接' },
        { value: '1', label: '正文' }
      ],
      releaseData: {
        id: '',
        image: '',
        title: '',
        date: '',
        type: '0',
        url: '',
        detail: ``,
        status: 0
      }
    }
  },
  computed: {
    total() {
      return this.tableData.length
    },
    tableDataPage() {
      return pagination(this.tableData, this.currentPage, this.pageSize)
    },
    noticeTypetoNum() {
      if (this.noticeType === '已上架') {
        return 1
      } else if (this.noticeType === '下架中') {
        return 2
      } else if (this.noticeType === '全部') {
        return 0
      }
    }
  },
  created() {
    getNoticeList(this.noticeTypetoNum).then(res => {
      this.tableData = res.content
    })
  },
  methods: {
    // 切换公告类型
    changeNoticeType(val) {
      this.showNoticeInfo = false
      getNoticeList(this.noticeTypetoNum).then(res => {
        this.tableData = res.content
      })
    },
    // 表格勾选
    handleSelectionChange(val) {
      this.tableDataSelection = val
    },
    // 取消置顶
    unpin(id, index) {
      unTop(id).then(res => {
        var realIndex = (this.currentPage - 1) * this.pageSize + index
        this.tableData[realIndex].isTop = 0
        Message({ message: res.msg, type: 'success' })
      })
    },
    // 置顶
    topping(id, index) {
      toTop(id).then(res => {
        var realIndex = (this.currentPage - 1) * this.pageSize + index
        this.tableData[realIndex].isTop = 1
        Message({ message: res.msg, type: 'success' })
      })
    },
    // 删除
    noticeDelete() {
      this.$confirm('确定删除吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        deleteNotice(this.rowInfo.id).then(res => {
          Message({ message: res.msg, type: 'success' })
          getNoticeList(this.noticeTypetoNum).then(res => {
            this.tableData = res.content
            this.showNoticeInfo = false
          })
        })
      })
    },
    // 批量删除
    allDelete() {
      this.$confirm('确定删除吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        let allId = ''
        this.tableDataSelection.map((item, index, items) => {
          allId = allId + ',' + item.id
        })
        deleteNotice(allId.replace(',', '')).then(res => {
          Message({ message: res.msg, type: 'success' })
          getNoticeList(this.noticeTypetoNum).then(res => {
            this.tableData = res.content
          })
        })
      })
    },
    // 点击表格行
    clickRow(row) {
      this.rowInfo = row
      this.showNoticeInfo = true
    },
    // 上下架
    ifShelf(type) {
      if (type) {
        onShelf(this.rowInfo.id).then(res => {
          getNoticeList(this.noticeTypetoNum).then(res => {
            this.tableData = res.content
          })
          this.rowInfo.status = 1
          Message({ message: res.msg, type: 'success' })
        })
      } else {
        offShelf(this.rowInfo.id).then(res => {
          getNoticeList(this.noticeTypetoNum).then(res => {
            this.tableData = res.content
          })
          this.rowInfo.status = 0
          Message({ message: res.msg, type: 'success' })
        })
      }
    },
    // 编辑
    noticeEdit() {
      this.dialogEdit = true
      Object.assign(this.rowInfo, { date: '' })
    },
    // 打开公告设置
    openSet() {
      getNoticeSetList().then(res => {
        this.showCountData = res.content
        this.showCount = this.showCountData[0].value
        this.dialogSet = true
      })
    },
    // 保存公告设置
    saveNoticeSet() {
      pushNoticeSet(this.showCount).then(res => {
        Message({ message: res.msg, type: 'success' })
        this.dialogSet = false
      })
    },
    // 上传图片
    uploadSuccess(res, file) {
      // this.releaseData.image = URL.createObjectURL(file.raw)
      this.releaseData.image = res.content
    },
    // 打开公告发布
    openAddNotice() {
      this.dialogRelease = true
    },
    // 上传公告内容
    saveNtice(isRelease) {
      isRelease ? this.releaseData.status = 1 : this.releaseData.status = 0
      addNotice(this.releaseData).then(res => {
        Message({ message: res.msg, type: 'success' })
        this.dialogRelease = false
        getNoticeList(this.noticeTypetoNum).then(res => {
          this.tableData = res.content
        })
      })
    },
    // 编辑内容
    editNtice() {
      editNotice(this.rowInfo).then(res => {
        Message({ message: res.msg, type: 'success' })
        this.dialogEdit = false
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.btn-right {
  margin-top: -39px;
}

.release {
  .avatar-uploader /deep/.el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .avatar-uploader /deep/.el-upload:hover {
    border-color: #409EFF;
  }
  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
  }
  .avatar {
    width: 178px;
    height: 178px;
    display: block;
  }
}

.release /deep/.el-dialog {
  min-width: 800px;
}
</style>
