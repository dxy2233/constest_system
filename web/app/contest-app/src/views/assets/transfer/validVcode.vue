<template>
  <slide>
    <div class="valid-vcode">
      <!--<div class="m-header">
        <span class="icon-back" @click="closeTem"></span>
      </div>-->
      <!--<x-header :left-options="{backText: ''}" class="w-header">
        <div slot="overwrite-left" class="icon-back header-left-icon" @click.native="closeTem"></div>
      </x-header>-->
      <app-header>
        <span slot="left" class="icon-back" @click="closeTem"></span>
      </app-header>
      <div class="psw-field-box">
        <div class="content">
          <div class="psw-label">已向你的手机{{hidMobile(loginMsg?loginMsg.mobile:'')}}发送了验证码</div>
          <button :disabled="wait!==0" class="get-code" @click="getCode">{{codeStr}}</button>
          <div class="ipt-box">
            <ul class="psw-show">
              <li v-for="i in 6">
                <span class="dot" v-show="i<=vcodeIpt.length"></span>
              </li>
            </ul>
            <!--<input type="tel" class="psw-ipt" maxlength="6" v-model="vcodeIpt">-->
          </div>
        </div>
      </div>
      <div class="pay-tool-keyboard">
        <ul>
          <li v-for="val in keys" @click="keyUpHandle(val)">
            {{ val }}
          </li>
          <li class="del" @click="delHandle"><span class="icon-del"><</span></li>
        </ul>
      </div>
      <loading :show="loadingShow" text=""></loading>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import {Loading} from 'vux'
  import {mapState, mapMutations, mapGetters} from 'vuex'
  import {hidMobile} from 'js/mixin'


  export default {
    name: "index",
    components: {
      slide,
      Loading,
    },
    data() {
      return {
        vcodeIpt: '',
        loadingShow: false,
        fieldDisabled: false,
        wait: 0,
        hidMobile: hidMobile,
        keys: [1, 2, 3, 4, 5, 6, 7, 8, 9, '', 0],
      }
    },
    watch: {
      /*vcodeIpt(v) {
        this.vcodeIpt = v.replace(/[^\d]/g, '')
        if (this.vcodeIpt.length === 6) {
          this.$emit('valid', this.vcodeIpt)
        }
      }*/
    },
    computed: {
      codeStr() {
        if (!this.wait) {
          return '重新获取验证码'
        } else {
          return `${this.wait}s`
        }
      },
      ...mapGetters([
        "loginMsg",
      ]),
    },
    methods: {
      closeTem() {
        this.$emit('close', true)
      },
      keyUpHandle(v) {
        if (v === '' || this.vcodeIpt.length === 6) return
        this.vcodeIpt = this.vcodeIpt + v
        if (this.vcodeIpt.length >= 6) {
          this.$emit('valid', this.vcodeIpt)
        }
      },
      delHandle () {
        if (this.vcodeIpt.length <= 0) return false
        this.vcodeIpt=this.vcodeIpt.slice(0,-1)
      },
      getCode(){
        this.wait = 60
        let itv = setInterval(() => {
          if (this.wait) {
            this.wait--
          } else {
            clearInterval(itv)
          }
        }, 1000)

        http.post('/sms/sms/transfer-pass', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
          }
        })
      }
    },
    created(){
      this.getCode()
    }

  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .valid-vcode
    fixed-full-screen()
    overflow hidden
    .m-header
      padding 0 $space-box
      line-height 50px
      height 50px
      span
        font-size 18px
    .get-code
      background none
      border 0
      margin-bottom 40px
    .psw-field-box
      position absolute
      top 50px
      bottom 0
      width 100%
      overflow hidden
      .content
        text-align center
      .title
        padding 30px $space-box 0 $space-box
        font-weight bold
        font-size $font-size-large
      .psw-label
        margin-bottom 10px
        margin-top 60px
        font-size $font-size-medium-x
        text-align center
      .ipt-box
        position relative
        width 280px
        margin 0 auto
        .psw-ipt
          position absolute
          width 100%
          /*border 1px solid red*/
          top 0
          bottom 0
          left 0
          opacity 0
      .psw-show
        display flex
        justify-content center
        padding 10px 0
        li
          width 33px
          line-height 33px
          height 33px
          margin 0 5px
          text-align center
          border-bottom 1px solid $color-border
          span
            display inline-block
            width 8px
            height 8px
            border-radius 50%
            background $color-text-theme


</style>
