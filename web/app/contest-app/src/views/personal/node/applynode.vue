<template>
  <slide>
    <div class="apply-node">
      <app-header>
        申请成为节点
        <router-link tag="span" to="/personal/applynode/rules" slot="right">规则说明</router-link>
      </app-header>
      <div class="h-main">
        <div class="top">
          <img src="/static/images/apply/top.png" alt="">
        </div>
        <div class="center">
          <img src="/static/images/apply/icon.png" alt="" class="icon">
          <div class="center-box">
            <div class="info">
              目前开放动力节点，中级节点，高
              级节点，超级节点申请，名额有限，
              先到先得。成功申请节点即刻享受
              等值天朝上品珍藏酒品，超过12项
              身份及实体权益,共同参与生态建设
              和权益分红。
            </div>
            <div class="tel">
              <img src="/static/images/apply/left.png" alt="" class="left-img">
              <div class="text">
                <p>财富热线</p>
                <h4>18586823227</h4>
              </div>
              <img src="/static/images/apply/right.png" alt="" class="right-img">
            </div>
            <div class="contact">
              <a :href="applyUrl">
                <div class="button">
                  <h5>立即申请</h5>
                  <p>即刻报名，独享专属权益</p>
                </div>
              </a>
            </div>
            <div class="qr-code">
              <qrcode :value="applyUrl" type="img" :size="110"></qrcode>
              <p>扫码填写申请表</p>
            </div>
          </div>
        </div>
        <div class="bottom">
          <img src="/static/images/apply/bottom.png" alt="">
        </div>
      </div>
      <router-view></router-view>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import { Qrcode } from 'vux'

  export default {
    name: "index",
    components: {
      slide,
      Qrcode
    },
    data(){
      return{
        applyUrl:'http://uaq5pzd9vm1kxhdk.mikecrm.com/SLNncYt'
      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .apply-node
    fixed-full-screen()
    & > .h-main
      width 100%
      height 100%
      overflow auto
      .top
        background #ffca12
        padding-bottom 40px
        img
          width 100%
      .center
        background #ffca12
        position relative
        .icon
          display block
          width 100px
          position absolute
          top -40px
          left 50%
          margin-left -50px
          z-index 2
        .center-box
          position relative
          bottom -15px
          margin-left 12%
          margin-right 12%
          background white
          border-radius 2px
          padding-top 50px
          .info
            color #421e88
            font-size $font-size-medium
            font-weight bold
            padding $space-box
          .tel
            background #673dc7
            position relative
            img
              position absolute
              top 15px
              width 35px
            .left-img
              left -35px
            .right-img
              right -35px
            h4
              font-size $font-size-large
            .text
              padding 4px
              text-align center
              color white
              font-weight bold
          .contact
            padding 20px 0
            .button
              background url("/static/images/apply/button.png")
              background-size 100% 100%
              color white
              text-align center
              width 190px
              margin 0 auto
              padding 5px 0
              h5
                font-size $font-size-medium
                font-weight bold
              p
                font-size $font-size-small-s
          .qr-code
            text-align center
            padding-bottom 20px
      .bottom
        background #421e88
        padding 50px 0
        img
          display block
          margin 0 auto
          width 80%

</style>
