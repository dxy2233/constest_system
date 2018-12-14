<template>
  <slide>
    <div class="recommend">
      <!--<x-header :left-options="{backText: ''}">
        我的推荐
        <router-link tag="a" slot="right" to="/personal/rcmd/record">推荐记录</router-link>
      </x-header>-->
      <app-header>
        我的推荐
        <router-link tag="span" slot="right" to="/personal/rcmd/record">邀请记录</router-link>
      </app-header>
      <div class="recommend-content">
        <div class="hint" v-if="!isAddCode">
          <span>你还没有填写介绍人邀请码,</span>
          <span class="add-code" @click="show=true">填写邀请码</span>
        </div>
        <div class="recommend-box">
          <div class="top">
            <h5>您的邀请码</h5>
            <h1>{{rcmdCode}}</h1>
            <button id="copy" :data-clipboard-text="rcmdCode">复制</button>
          </div>
          <div class="center">
            <div class="circle"></div>
            <div class="line"></div>
            <div class="circle"></div>
          </div>
          <div class="bottom">
            <!--{{qrcodeData}}-->
            <qrcode :value="qrcodeData" type="img" :size="125"></qrcode>
            <p>
              邀请二维码
              <br>
              加入节点享受节点权益
            </p>
          </div>
        </div>
      </div>
      <x-dialog v-model="show" class="add-code-dialog">
        <div class="title">
          填写邀请码
          <span class="icon-close close" @click="show=false"></span>
        </div>
        <div class="ipt-box">
          <input type="text" v-model="code" class="vcode-ipt" placeholder="输入或粘贴介绍人邀请码">
        </div>
        <x-button type="warn" :disabled="btnLoading"
                  class="reset-btn" @click.native="submitCode" :show-loading="btnLoading">提交</x-button>
      </x-dialog>
      <router-view></router-view>
      <router-view></router-view>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import {Qrcode} from 'vux'
  import Clipboard from 'clipboard';
  import {XDialog} from 'vux'
  import {base} from 'js/constant'

  export default {
    name: "index",
    components: {
      slide,
      Qrcode,
      XDialog
    },
    data(){
      return{
        rcmdCode:'',
        isAddCode:true,
        show:false,
        code:'',
        btnLoading: false
      }
    },
    methods:{
      getRcmdCode(){
        http.post('/user/recommend-code', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.rcmdCode = res.content.code
          this.isAddCode = res.content.reCode
        })
      },
      submitCode() {
        if (!this.code) {
          this.$vux.toast.show('请输入或粘贴介绍人邀请码')
          return
        }
        this.btnLoading = true
        http.post('/user/add-recommend', {
          re_code:this.code
        }, (res) => {
          this.btnLoading = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
          }else {
            this.$vux.toast.show({
              text: res.msg,
              type: 'success'
            })
            this.show = false
          }
          this.code = ''
          this.isAddCode = true

        })
      }
    },
    computed:{
      qrcodeData(){
        let url = window.location.href
        let aurl = url.split('#')
        return `${aurl[0]}#/login?re_code=${this.rcmdCode}`
      }
    },
    created(){
      this.getRcmdCode()
    },
    mounted(){
      const clipboard = new Clipboard('#copy');

      clipboard.on('success', (e) => {
        this.$vux.toast.show('复制成功')
      });

      clipboard.on('error', (e) => {
        this.$vux.toast.show('复制失败，你可以选择手动复制')
      });
    },
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .recommend
    fixed-full-screen()
    background #28272c
    &>.app-header
      background none !important
      color white
    .recommend-content
      padding-top 75px
      .hint
        text-align center
        color white
        .add-code
          text-decoration underline
      .recommend-box
        position absolute
        top 110px
        bottom 30px
        left 30px
        right 30px
        border-radius 10px
        background white
        display flex
        flex-direction column
        justify-content space-around
        .top
          text-align center
          color $color-theme
          padding 30px 0
          h5
            font-size 18px
          h1
            padding 30px 0
            font-size 40px
          button
            color white
            width 120px
            line-height 45px
            border 0
            height 45px
            linear-gradient-compatible-updown(#FF6440, #FF1641)
            border-radius 10px
            font-size $font-size-medium
        .center
          margin-left -15px
          margin-right -15px
          display flex
          align-items center
          justify-content space-between
          .line
            border-top 1px dashed $color-theme
            /*flex 1*/
            width calc(100% - 90px)
          .circle
            /*flex 0 0 40px*/
            width 30px
            height 30px
            border-radius 50%
            background #28272c
        .bottom
          padding 20px
          text-align center
          p
            margin-top 10px
            line-height 18px
            color $color-text-minor


    .add-code-dialog
      .weui-dialog
        padding 25px $space-box
        width 75%
        .title
          margin-bottom 30px
          text-align left
          font-size $font-size-medium-x
          .close
            float right
        .ipt-box
          position relative
          .vcode-ipt
            width 100%
            line-height 35px
            border-bottom 1px solid $color-border
        .reset-btn
          line-height 40px
          font-size $font-size-small
          margin-top 40px

</style>
