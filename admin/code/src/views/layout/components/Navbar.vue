<template>
  <el-menu class="navbar" mode="horizontal">
    <hamburger :toggle-click="toggleSideBar" :is-active="sidebar.opened" class="hamburger-container"/>
    <breadcrumb />
    <el-dropdown class="avatar-container" trigger="click">
      <div class="avatar-wrapper">
        <p style="line-height:1;font-size:18px;font-weight:bold;">{{ name }}</p>
        <!-- <img :src="avatar+'?imageView2/1/w/80/h/80'" class="user-avatar"> -->
        <i class="el-icon-caret-bottom"/>
      </div>
      <el-dropdown-menu slot="dropdown" class="user-dropdown">
        <!-- <router-link class="inlineBlock" to="/">
          <el-dropdown-item>
            首页
          </el-dropdown-item>
        </router-link>
        <el-dropdown-item divided> -->
        <el-dropdown-item>
          <span style="display:block;" @click="dialogPW = true">修改密码</span>
        </el-dropdown-item>
        <el-dropdown-item>
          <span style="display:block;" @click="logout">注销</span>
        </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
    <el-dialog :visible.sync="dialogPW" title="修改密码">
      <el-form ref="changePWform" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="原密码" prop="password">
          <el-input v-model="form.password"/>
        </el-form-item>
        <el-form-item label="新密码" prop="new_password">
          <el-input v-model="form.new_password"/>
        </el-form-item>
        <el-form-item label="确认新密码" prop="new_password_2">
          <el-input v-model="form.new_password_2"/>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogPW = false">取 消</el-button>
        <el-button type="primary" @click="changePW">确 定</el-button>
      </div>
    </el-dialog>
  </el-menu>
</template>

<script>
import { mapGetters } from 'vuex'
import { resetPW } from '@/api/login'
import { Message } from 'element-ui'
import Breadcrumb from '@/components/Breadcrumb'
import Hamburger from '@/components/Hamburger'

export default {
  components: {
    Breadcrumb,
    Hamburger
  },
  data() {
    const validatePass2 = (rule, value, callback) => {
      if (value === '') {
        callback(new Error('请再次输入密码'))
      } else if (value !== this.form.new_password) {
        callback(new Error('两次输入密码不一致!'))
      } else {
        callback()
      }
    }
    return {
      dialogPW: false,
      form: {
        password: '',
        new_password: '',
        new_password_2: ''
      },
      rules: {
        password: [
          { required: true, message: '请输入原密码', trigger: 'blur' }
        ],
        new_password: [
          { required: true, message: '请输入新密码', trigger: 'blur' },
          { max: 17, min: 6, message: '密码长度必须大于5小于18', trigger: 'blur' }
        ],
        new_password_2: [
          { validator: validatePass2, required: true, trigger: 'blur' }
        ]
      }
    }
  },
  computed: {
    ...mapGetters([
      'sidebar',
      'avatar',
      'name'
    ])
  },
  methods: {
    toggleSideBar() {
      this.$store.dispatch('ToggleSideBar')
    },
    logout() {
      this.$store.dispatch('LogOut').then(() => {
        location.reload() // 为了重新实例化vue-router对象 避免bug
      })
    },
    changePW() {
      this.$refs['changePWform'].validate((valid) => {
        if (valid) {
          resetPW(this.form).then(res => {
            Message({ message: res.msg, type: 'success' })
            this.dialogPW = false
          })
        } else {
          console.log('error submit!!')
          return false
        }
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.navbar {
  height: 50px;
  line-height: 50px;
  border-radius: 0px !important;
  .hamburger-container {
    line-height: 58px;
    height: 50px;
    float: left;
    padding: 0 10px;
  }
  .screenfull {
    position: absolute;
    right: 90px;
    top: 16px;
    color: red;
  }
  .avatar-container {
    height: 50px;
    display: inline-block;
    position: absolute;
    right: 35px;
    .avatar-wrapper {
      cursor: pointer;
      margin-top: 5px;
      position: relative;
      .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
      }
      // .el-icon-caret-bottom {
      //   position: absolute;
      //   right: -20px;
      //   top: 25px;
      //   font-size: 12px;
      // }
      .el-icon-caret-bottom {
        position: absolute;
        right: -20px;
        top: 5px;
        font-size: 12px;
      }
    }
  }
}
</style>
