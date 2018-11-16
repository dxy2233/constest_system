<template>
  <slide>
    <div class="invite">
      <app-header></app-header>
      <div class="invite-content">
        <div class="avatar">
          <img :src="nodeInfo.logo||'/static/images/node-avatar-default.jpg'" alt="" class="">
        </div>
        <h6>{{nodeInfo.name}}</h6>
        <h1>邀请您来为我投票</h1>
        <qrcode :value="qrcodeData" type="img" :size="165" class="qrcode-box"></qrcode>
        <p>投票链接二维码</p>
        <div class="copy-btn" id="copy" :data-clipboard-text="qrcodeData">复制投票链接</div>
      </div>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import {Qrcode} from 'vux'
  import Clipboard from 'clipboard';

  export default {
    name: "index",
    components: {
      slide,
      Qrcode
    },
    data(){
      return{
        nodeInfo:{}
      }
    },
    created(){
      this.nodeInfo = JSON.parse(localStorage.getItem('myNodeInfo'))
      console.log(this.nodeInfo)
    },
    computed:{
      qrcodeData(){
        let url = window.location.href
        let aurl = url.split('#')
        // return `${aurl[0]}#/login?re_code=${this.rcmdCode}`
        // return `${aurl[0]}#/home/vote?nodeId=${this.nodeInfo.id}&nodeName=${this.nodeInfo.name}`
        return `${aurl[0]}#/home/node/dts${this.nodeInfo.id}`
      }
    },
    mounted(){
      const clipboard = new Clipboard('#copy');

      clipboard.on('success', (e) => {
        this.$vux.toast.show('复制成功')
      });

      clipboard.on('error', (e) => {
        this.$vux.toast.show('复制失败，你可以选择扫描以及二维码，或刷新重试')
      });
    },
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .invite
    fixed-full-screen()
    background $color-background-sub
    overflow auto
    .app-header
      background $color-background-sub !important
    .invite-content
      margin 20px
      margin-top 95px
      display flex
      flex-direction column
      align-items center
      background url("/static/images/invite-bg.png") center no-repeat
      background-size cover
      border-radius 4px
    .avatar
      width 85px
      height 85px
      border 3px solid #E5CC6F
      border-radius 50%
      margin-top -45px
      overflow hidden
      background white
      img
        width 100%
        height 100%
    h6
      margin-top 20px
      color #CCAA74
      font-size $font-size-medium
    h1
      margin-top 35px
      margin-bottom 50px
      color #E5CC6F
      font-size 28px
    .qrcode-box
      border 1px solid white
    p
      margin-top 15px
      font-size $font-size-medium
      color #D5D5D5
    .copy-btn
      margin 30px 0
      width 255px
      text-align center
      color #0F0F0F
      line-height 42px
      font-size $font-size-medium-x
      background #E5CC6F
      border-radius 10px



</style>
