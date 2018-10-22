<template>
  <div class="assets">
    <div class="assets-wrapper" v-show="mainShow">
      <div v-if="isWallet" class="assets-content">
        <div class="title">
          <span>我的钱包</span>
          <img src="/static/images/icon-manage.png" alt="" class="icon">
        </div>
        <div class="wallet">
          <div class="top">
            <h3>{{walletData.name}}</h3>
            <p>{{walletData.address}}</p>
          </div>
          <ul class="bottom">
            <router-link tag="li" to="/assets/transfer">转账</router-link>
            <!--<li @click="openCollect">收款</li>-->
            <router-link tag="li" :to="{name:'collect',query:{address:walletData.address}}">收款</router-link>
          </ul>
        </div>
        <dl class="currency" v-show="currencyList.length">
          <dt>资产种类</dt>
          <router-link tag="dd" v-for="item in currencyList" :key="item.code" :to="'/assets/dts'+item.currencyId">
            <div class="left">
              <img src="/static/images/icon-manage.png" alt="" class="icon">
              <span>{{item.code}}</span>
            </div>
            <div class="right">{{item.positionAmount}}</div>
          </router-link>
        </dl>
      </div>
      <div v-else class="assets-created">
        <div class="title">
          <span>我的钱包</span>
        </div>
        <div class="content">
          <img src="/static/images/assets-bg0.png" alt="" class="bg">
          <div class="created-box" @click="createdWallet">
            <div class="add">
              +
            </div>
            <p>创建钱包</p>
          </div>
        </div>
      </div>
      <loading :show="showLoading"></loading>
    </div>
    <router-view></router-view>
  </div>
</template>

<script>
  import http from 'js/http'
  import {Loading} from 'vux'

  export default {
    name: "index",
    components: {
      Loading,
    },
    data() {
      return {
        walletData: {},
        mainShow: false,
        currencyList: [],
        isWallet: false,
        showLoading: false,
        existPsw: 0
      }
    },
    methods: {
      openCollect() {
        // this.$router.push({path: '/assets/collect', query:{stage: '345678'}})
        this.$router.push({name: 'collect', params: {address: this.walletData.address}})
      },
      getWalletData() {
        http.post('/wallet', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.isWallet = res.content.length > 0
          this.walletData = res.content[0]
          sessionStorage.setItem("walletData", JSON.stringify(this.walletData));
          this.getCurrencyList()
          this.mainShow = true
        })
      },
      getCurrencyList() {
        http.post('/wallet/currency', {
          id: this.walletData.id
          // id: 14
        }, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.currencyList = res.content
          console.log(this.currencyList)
          sessionStorage.setItem("currencyList", JSON.stringify(this.currencyList));
        })
      },
      createdWallet() {
        //验证是否设置支付密码
        if (!!this.existPsw) {
          this.judge()
        } else {
          http.post('/pay/exist-pass', {}, (res) => {
            if (res.code !== 0) {
              this.$vux.toast.show(res.msg)
              return
            }
            this.existPsw = res.content ? 1 : 2
            this.judge()
          })
        }
      },
      judge() {
        if (this.existPsw === 1) {//已设置支付密码
          //开始创建钱包
          this.startCreated()
        } else {
          this.$router.push({
            path: '/personal/psw/set'
          })
        }
      },
      startCreated() {
        this.showLoading = true
        http.post('/wallet/create', {}, (res) => {
          this.showLoading = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
          } else {
            this.$vux.toast.show({
              text: '钱包创建成功',
              type: 'success'
            })
            this.getWalletData()
          }
        })

      },
    },
    created() {
      this.getWalletData()
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .assets
    position: absolute
    width: 100%
    top: 0
    bottom: 50px
    overflow hidden
    .assets-wrapper
      padding 20px $space-box
      .title
        display flex
        justify-content space-between
        align-items center
        font-size $font-size-large-x
        padding-bottom 30px
        img
          width 48px
      .wallet
        border-radius 10px
        background #FFB760
        overflow hidden
        color white
        box-shadow 0 4px 15px 4px RGBA(240, 208, 172, 0.5)
        & > .top
          padding 15px 20px
          background url("/static/images/assets-bg1.png") center no-repeat
          background-size cover
          h3
            padding-bottom 20px
            padding-top 10px
            font-weight 700
            font-size $font-size-medium
          p
            font-size $fs$font-size-small-s
        & > .bottom
          padding 10px 0
          margin-left -1px
          overflow hidden
          li
            width 50%
            box-sizing border-box
            border-left 1px solid white
            text-align center
            line-height 30px
            float left

      .currency
        margin-top 20px
        dt
          font-size $font-size-large
          padding 15px 0
        dd
          display flex
          justify-content space-between
          font-size $font-size-medium
          align-items center
          padding $space-box 0
          border-bottom 1px solid $color-border
          .left
            display flex
            align-items center
          .icon
            width 32px
            margin-right 20px
      .assets-created
        .content
          position relative
          .bg
            width 100%
          .created-box
            position absolute
            top 20px
            bottom 20px
            width 100%
            display flex
            flex-direction column
            justify-content space-around
            align-items center
            .add
              width 70px
              height 70px
              border-radius 50%
              text-align center
              line-height 70px
              background white
              color #ff9e45
              font-size 50px
            p
              color white
              font-size $font-size-medium-x
</style>
