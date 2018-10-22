<template>
  <slide>
    <div class="psw">
      <!--<x-header :left-options="{backText: ''}"></x-header>-->
      <app-header></app-header>
      <div class="h-main psw-content">
        <div class="title">支付密码</div>
        <group>
          <cell-box is-link @click.native="show=true">
            <span class="text">重置支付密码</span>
          </cell-box>
          <cell-box is-link link="/personal/psw/index/revise">
            <span class="text">修改支付密码</span>
          </cell-box>
          <cell-box></cell-box>
        </group>
      </div>
      <x-dialog v-model="show" class="send-sms-dialog">
        <div class="title">
          安全验证
          <span class="icon-close close" @click="show=false"></span>
        </div>
        <div class="hint">当前手机号{{hidMobile(loginMsg?loginMsg.mobile:'')}}</div>
        <div class="ipt-box">
          <input type="text" v-model="vcode" class="vcode-ipt" placeholder="请输入您的短信验证码">
          <span class="vcode-text" @click="clickCodeBtn">{{codeStr}}</span>
        </div>
        <x-button type="warn" class="reset-btn" @click.native="submitVcode" :show-loading="btnLoading">确认重置</x-button>
      </x-dialog>
      <router-view></router-view>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import {hidMobile} from 'js/mixin'
  import {Group, CellBox, XDialog} from 'vux'
  import {mapGetters} from 'vuex'

  export default {
    name: "index",
    components: {
      slide,
      Group,
      CellBox,
      XDialog
    },
    data() {
      return {
        hidMobile: hidMobile,
        wait: 0,
        show: false,
        vcode: '',
        btnLoading: false
      }
    },
    computed: {
      codeStr() {
        if (!this.wait) {
          return '获取验证码'
        } else {
          return `重新获取(${this.wait}s)`
        }
      },
      ...mapGetters([
        "loginMsg",
      ]),
    },
    methods: {
      clickCodeBtn() {
        this.wait = 60
        let itv = setInterval(() => {
          if (this.wait) {
            this.wait--
          } else {
            clearInterval(itv)
          }
        }, 1000)
        http.post('/sms/sms/user-pay-pass', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
          }
        })
      },
      submitVcode() {
        if (!this.vcode) {
          this.$vux.toast.show('请输入您的短信验证码')
          return
        }
        this.btnLoading = true
        http.post('/sms/sms/user-validate-vcode', {
          vcode:this.vcode
        }, (res) => {
          this.btnLoading = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          sessionStorage.setItem("resetVcode", this.vcode);
          this.show = false
          this.$router.push({
            path: '/personal/psw/index/reset'
          })
        })
      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .psw
    fixed-full-screen()
    overflow auto
    .vux-header
      background $color-background
      border-color $color-background
    .psw-content
      & > .title
        padding 30px $space-box
        font-weight bold
        font-size $font-size-large
      .weui-cells:after, .weui-cells:before
        border 0
      .weui-cell
        padding-top 20px
        padding-bottom 20px
    .send-sms-dialog
      .weui-dialog
        padding 25px $space-box
        .title
          margin-bottom 45px
          font-size $font-size-medium-x
          .close
            float right
        .hint
          text-align left
          margin-bottom 5px
        .ipt-box
          position relative
          .vcode-ipt
            /*padding 0 5px*/
            width 100%
            line-height 35px
            border-bottom 1px solid $color-border
          .vcode-text
            position absolute
            top 0
            right 0
            color $color-theme
            line-height 35px
        .reset-btn
          line-height 40px
          font-size $font-size-small
          margin-top 40px


</style>
