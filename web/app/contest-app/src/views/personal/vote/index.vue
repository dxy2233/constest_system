<template>
  <slide>
    <div class="vote">
      <!--<x-header :left-options="{backText: ''}">
        我的投票
        <router-link tag="a" to="/personal/vote/redeem" slot="right" @click="">赎回记录</router-link>
      </x-header>-->
      <app-header>
        我的投票
        <router-link tag="span" to="/personal/vote/redeem" slot="right" @click="">赎回记录</router-link>
      </app-header>
      <div class="vote-content">
        <scroller :on-infinite="handleBottom" ref="my_scroller">
          <div class="no-data" v-if="!dataList.length&&!loadShow">
            <img src="/static/images/state-fail.png" alt="">
            <!--<p>你还没有投票</p>-->
          </div>
          <ul class="vote-list">
            <li v-for="item in dataList">
              <div class="top">
                <div class="left">
                  <span class="name">{{item.name}}</span>
                  <span class="sign">{{item.typeName.slice(0,2)}}</span>
                </div>
                <div class="right">{{item.voteNumber+'票'}}</div>
              </div>
              <div class="bottom">
                <div class="left">
                  <p>{{'方式：'+item.typeStr}}</p>
                  <p>{{item.createTime}}</p>
                </div>
                <div class="right" v-if="item.type==='1'">
                  <x-button v-if="item.isRevoke" type="warn" class="redeem" @click.native="clickRedeem(item.id)">赎回</x-button>
                  <p v-else>本轮竞选活动结束后才能赎回</p>
                </div>
              </div>
            </li>
          </ul>
        </scroller>

      </div>
      <x-dialog v-model="show" class="redeem-dialog">
        <div class="dlg-box">
          <div class="title">
            确认赎回投票？
            <span class="icon-close close" @click="show=false"></span>
          </div>
          <div class="hint">确认赎回后我们将于72小时将资产返还到 你的账户</div>
          <x-button type="warn" class="redeem-btn" @click.native="handRedeem" :show-loading="btnLoading">确定</x-button>
        </div>

      </x-dialog>
      <router-view></router-view>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import {XDialog} from 'vux'

  export default {
    name: "index",
    components: {
      slide,
      XDialog
    },
    data() {
      return {
        dataList: [],
        page: 1,
        loadShow: true,
        show:false,
        btnLoading: false,
        redeemId:'',
        total:''
      }
    },
    methods: {
      handRedeem() {
        this.btnLoading = true
        http.post('/vote/revoke-vote', {
          id:this.redeemId
        }, (res) => {
          this.btnLoading = false
          if (res.code === 0){
            this.show = false
            this.redeemId = ''
          }
          this.$vux.toast.show(res.msg)
          this.cleanUpData()
        })
      },
      cleanUpData(){
        this.dataList = []
        this.page = 1
        this.loadShow = true
        this.getList()
      },
      clickRedeem(id){
        this.redeemId = id
        this.show = true
      },
      handleBottom() {
        if (this.total!==''&&this.dataList.length >= parseInt(this.total)){
          this.$refs.my_scroller.finishInfinite(true);
          return
        }

        http.post('/vote/logs', {
          page: this.page,
          page_size: 10,
          type:1
        }, (res) => {
          if (this.loadShow) this.loadShow = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.dataList = this.dataList.concat(res.content.list)
          this.total = res.content.count
          this.page++
          this.$refs.my_scroller.finishInfinite(false);
        })
      },
      getList() {
        http.post('/vote/logs', {
          page: this.page,
          page_size: 10,
          type:1
        }, (res) => {
          if (this.loadShow) this.loadShow = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.dataList = this.dataList.concat(res.content.list)
          if (this.dataList.length < parseInt(res.content.count)) {
            this.$refs.vueLoad.onBottomLoaded();
          } else {
            this.$refs.vueLoad.onBottomLoaded();
          }
        })
      },
    },
    created() {
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .vote
    fixed-full-screen()
    .vote-content
      position absolute
      top 50px
      bottom 0
      left 0
      right 0
      overflow hidden
      .vote-list
        min-height 30px
        li
          padding $space-box
          border-bottom 1px solid $color-border
          &>div
            display flex
            justify-content space-between
            align-items center
            button.redeem
              border-radius 20px
              line-height 32px
              width 85px
              font-size $font-size-small
            .name
              font-weight bold
              font-size $font-size-medium
            .sign
              display inline-block
              color #FF6A2F
              border-radius 15px
              border 1px solid #FFA344
              background:rgba(255,248,242,1)
              font-size 20px
              padding 5px 10px
              margin-left -10px
              transform-compatible(scale(0.5))
          .bottom
            margin-top 5px
            .left
              line-height 18px
              color $color-text-minor
    .redeem-dialog
      .dlg-box
        padding: 25px 15px
      .title
        font-size $font-size-large
        .close
          float right
      .hint
        line-height 20px
        padding 30px 0
        color $color-text-minor
        text-align center
      .redeem-btn
        line-height 40px
        font-size $font-size-small
    .no-data
      text-align center
      padding-top 55px
      img
        width 100px
      p
        margin-top 35px
        font-size $font-size-medium
        color $color-text-minor

</style>
