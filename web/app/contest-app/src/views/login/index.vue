<template>
  <slide>
    <div class="login">
      <app-header>
        <!--<x-icon slot="left" type="ios-close-empty" @cick="backPath"></x-icon>-->
        <div slot="left" class="icon-close" @click="backPath"></div>
      </app-header>
      <div class="h-main">
        <div class="login-main">
          <h3 class="title">创建/登录共生态账号</h3>
          <!--<img alt="" class="img-code" :src="imgCode.imageData" @click="getImgCode()">-->
          <group title="" class="login-form">
            <x-input @on-blur="existMobile" title="" placeholder="+86 请输入手机号" mask="99999999999"
                     v-model="loginForm.mobile" :max="11"></x-input>
            <x-input title="" placeholder="图片验证码" v-model="loginForm.captcha_code">
              <div class="img-code-box" slot="right-full-height">
                <img class="img-code" v-if="imgCode.imageData"
                     :src="imgCode.imageData" @click="getImgCode">
              </div>
            </x-input>
            <x-input title="" class="weui-vcode" placeholder="验证码" v-model="loginForm.vcode">
              <!--<x-button class="send-code" slot="right" type="primary" mini>发送验证码</x-button>-->
              <x-button @click.native="clickCodeBtn"
                        :gradients="[colorSubTheme,colorTheme]" class="send-code" slot="right" mini>{{codeStr}}
              </x-button>
            </x-input>
            <x-input v-show="reCodeShow" title="" placeholder="邀请码（选填）"
                     v-model="loginForm.re_code"></x-input>
          </group>
          <div class="submit-box">
            <x-button :gradients="[colorSubTheme,colorTheme]" @click.native="submitLogin" :show-loading="btnLoading">
              立即开启
            </x-button>
          </div>
        </div>
      </div>
    </div>
  </slide>
</template>

<script>
  import http from 'js/http'
  import {XInput, Group, XButton, Cell} from 'vux'
  import {colorTheme, colorSubTheme} from 'js/constant'
  import {mapState, mapMutations, mapGetters} from 'vuex'
  import slide from 'components/slide/bottom'
  import {GetUrlParam} from 'js/mixin'

  const imgCodeType = 'UserLogin'

  export default {
    name: "login",
    components: {
      XInput,
      XButton,
      Group,
      Cell,
      slide
    },
    data() {
      return {
        loginForm: {
          mobile: '',
          vcode: '',
          captcha_code: '',
          re_code: ''
        },
        colorTheme: colorTheme,
        colorSubTheme: colorSubTheme,
        imgCode: {
          imageData: '',
          imageCode: ''
        },
        wait: 0,
        reCodeShow: false,
        btnLoading: false,
        // pathCode:''
      }
    },
    methods: {
      backPath() {
        this.$router.go(-2)
        // this.$router.back()
        /*console.log(window.history.length)
        if (window.history.length <= 1) {
          this.$router.replace({path:'/'})
          return false
        } else {
          this.$router.go(-1)
        }*/
      },
      existMobile() {
        let clickMb = this.clickMobile(this.loginForm.mobile)
        if (clickMb) {
          this.$vux.toast.show(clickMb)
          return
        }
        http.post('/validate/user/exist-mobile', {mobile: this.loginForm.mobile}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.reCodeShow = !res.content
        })
      },

      submitLogin() {
        let clickMb = this.clickMobile(this.loginForm.mobile)
        if (clickMb) {
          this.$vux.toast.show(clickMb)
          return
        }
        if (!this.loginForm.captcha_code) {
          this.$vux.toast.show('请输入图片验证码')
          return
        }
        if (!this.loginForm.vcode) {
          this.$vux.toast.show('请输入手机验证码')
          return
        }
        /*if (this.reCodeShow) {
          if (!this.loginForm.wallet_address) {
            this.$vux.toast.show('请输入钱包地址')
            return
          }
          if (this.loginForm.wallet_address.length !== 26) {
            this.$vux.toast.show({
              text: '请输入有效的钱包地址',
              width: '12em'
            })
            return
          }
        }*/
        this.getLogin()
      },
      getLogin() {
        this.btnLoading = true
        http.post('/login', Object.assign({
          image_code: this.imgCode.imageCode,
          type: imgCodeType
        }, this.loginForm), (res) => {
          this.$vux.toast.show(res.msg)
          this.btnLoading = false
          if (res.code === 0) {
            sessionStorage.setItem("loginMsg", JSON.stringify(res.content));
            this.setLoginMsg(res.content)
            for (let item in this.loginForm) {
              this.loginForm[item] = ''
            }
            this.existPayPsw()
          }
        })
      },
      existPayPsw() {
        http.post('/pay/exist-pass', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          let n = res.content ? '1' : '0'
          this.setPayPsw(n)
          sessionStorage.setItem('payPsw', n)
          if (res.content) {//已有支付密码
            setTimeout(() => {
              this.$router.back()
            }, 1000)
          } else {//去设置
            this.$router.replace({
              path: '/setpsw'
            })
          }
        })
      },
      clickMobile(mobile) {
        if (!mobile) {
          return '请输入手机号'
        }
        if (!(/^1\d{10}$/.test(mobile))) {
          return '请输入有效的手机号'
        }
        return ''
      },
      clickCodeBtn() {
        let clickMb = this.clickMobile(this.loginForm.mobile)
        if (clickMb) {
          this.$vux.toast.show(clickMb)
          return
        }
        if (this.wait <= 0) {
          this.getCode()
        }
      },
      getCode() {
        this.wait = 60
        let itv = setInterval(() => {
          if (this.wait) {
            this.wait--
          } else {
            clearInterval(itv)
          }
        }, 1000)

        http.post('/sms/sms/user-login', {mobile: this.loginForm.mobile}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
          }
        })
      },
      getImgCode() {
        http.post('/captcha/captcha/show', {type: imgCodeType}, (res) => {
          this.imgCode = res.content
        })
      },
      ...mapMutations({
        setLoginMsg: 'LOGIN_MSG',
        setPayPsw: 'PAY_PSW'
      })
    },
    computed: {
      codeStr() {
        if (!this.wait) {
          return '获取验证码'
        } else {
          return `重新获取(${this.wait}s)`
        }
      }
    },
    created() {
      // let pathCode = this.$route.query.re_code
      let pathCode = GetUrlParam('re_code')
      // console.log(pathCode)
      if (pathCode) {
        this.loginForm.re_code = pathCode
        this.reCodeShow = true
      }
      this.getImgCode()
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .login
    fixed-full-screen()
    .icon-close
      font-size 28px
      margin-top 20px
    .login-main
      padding 10% 8%
    .submit-box
      margin 60px 0
    .login-form
      ipt-pr(#B4B5BC)
      height 190px
      input
        font-size $font-size-small
    .send-code
      padding-left 0
      padding-right 0
      width 115px
      margin-right -15px
    .code-img
      margin-right 0
    .title
      margin-bottom 45px
      font-size $font-size-medium
    .input-item
      margin 10px 0
    .weui-cell
      padding-left 0
      &:before
        left 0
    .weui-cells
      &:before
        display none
    .img-code-box
      overflow hidden
      cursor pointer
      .img-code
        height 32px
        width 115px
        margin-top 6px
        border-radius 4px
</style>
