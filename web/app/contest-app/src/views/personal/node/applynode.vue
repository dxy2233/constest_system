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
              等值茅台珍藏酒品，超过12项
              身份及实体权益,共同参与生态建设
              和权益分红。
            </div>
            <div class="tel">
              <img src="/static/images/apply/left.png" alt="" class="left-img">
              <div class="text">
                即刻报名，独享专属权益
              </div>
              <img src="/static/images/apply/right.png" alt="" class="right-img">
            </div>
            <div class="contact">
              <p>财富热线</p>
              <h4>18586823227</h4>
            </div>
          </div>
        </div>
        <div class="bottom">
          <img src="/static/images/apply/bottom.png" alt="">
          <!--<div class="apply-btn">
            <input type="checkbox" v-model="agree">
            <p>勾选表示已同意《节点申请协议》</p>
          </div>-->
        </div>
        <div class="apply-btn">
          <div class="check-box">
            <input type="checkbox" v-model="agree">
            <p>勾选表示已同意
              <router-link tag="span" to="/personal/applynode/agreement">《节点申请协议》</router-link>
            </p>
          </div>
          <button @click="goSubmit" class="base-btn">立即申请</button>
        </div>
      </div>
      <router-view></router-view>
      <div v-transfer-dom>
        <confirm v-model="idfConfirmShow"
                 title="您还没有完成实名认证"
                 @on-confirm="onIdfConfirm">
          <p style="text-align:center;">申请节点必须实名认证</p>
        </confirm>
        <confirm v-model="nodeConfirmShow"
                 title="您已拥有节点"
                 confirm-text="查看节点"
                 @on-confirm="onNodeConfirm">
          <p style="text-align:center;">暂时无法申请</p>
        </confirm>
      </div>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import {Qrcode, Confirm, TransferDomDirective as TransferDom} from 'vux'
  import {mapState, mapMutations, mapGetters} from 'vuex'

  export default {
    name: "index",
    directives: {
      TransferDom
    },
    components: {
      slide,
      Qrcode,
      Confirm
    },
    data() {
      return {
        applyUrl: 'http://uaq5pzd9vm1kxhdk.mikecrm.com/6aNQyf2',
        agree: false,
        idfConfirmShow: false,
        nodeConfirmShow:false
      }
    },
    methods: {
      goSubmit() {
        if (!this.agree) {
          this.$vux.toast.show('请先阅读并同意节点申请协议')
          return
        }
        if (this.myNodeInfo.status !== -1) {
          this.nodeConfirmShow = true
          return
        }

        if (this.identifyMsg.status !== 1) {
          this.idfConfirmShow = true
          return
        }

        this.$router.push({
          path: '/personal/applynode/submit'
        })
      },
      onIdfConfirm() {
        this.$router.push({
          path: '/personal/identify/' + this.identifyPath
        })
      },
      onNodeConfirm() {
        this.$router.push({
          path: '/personal/node/' + this.nodePath
        })
      }
    },
    computed: {
      identifyPath() {
        if (!this.identifyMsg) return ''
        if (!this.identifyMsg.isIdentify) return 'submit'
        switch (this.identifyMsg.status) {
          case 0:
            return 'wait'
          case 1:
            return 'success'
          case 2:
            return 'fail'
        }
      },
      nodePath(){
        //0 停用 1 已生效 2 审核中 3 撤销 4 审核未通过 5 删除'
        if (!this.myNodeInfo) return ''
        switch (this.myNodeInfo.status) {
          case 1:
            return 'index'
          case 2:
            return 'wait'
          case 4:
            return 'fail'
        }
      },
      ...mapGetters([
        "identifyMsg",
        "myNodeInfo"
      ]),
    },

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
        padding-bottom 25px
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
          bottom -25px
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
              top 10px
              width 30px
            .left-img
              left -30px
            .right-img
              right -30px
            h4
              font-size $font-size-large
            .text
              text-align center
              color white
              font-weight bold
              font-size $font-size-medium-x
              line-height 40px
          .contact
            padding 30px 0
            color #ff3ba1
            text-align center
            p
              font-size $font-size-medium-x
            h4
              font-weight bold
              font-size 26px
              margin-top 10px
      .bottom
        background #421e88
        padding-top 50px
        padding-bottom 20px
        img
          display block
          margin 0 auto
          width 80%
      .apply-btn
        padding-bottom 50px
        padding-left 5%
        padding-right 5%
        background $color-background-sub
        .check-box
          display flex
          align-items center
          p
            color $color-text-minor
            padding 18px 5px
          span
            color $color-theme
        button
          margin-bottom 20px


</style>
