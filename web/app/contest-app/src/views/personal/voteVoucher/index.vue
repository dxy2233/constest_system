<template>
  <slide>
    <div class="vote-voucher">
      <!--<x-header :left-options="{backText: ''}" class="w-header">我的投票券
      </x-header>-->
      <app-header>
        我的投票券
      </app-header>
      <div class="vote-voucher-content">
        <div class="brief-card-box">
          <div class="brief-card">
            <div class="left">
              <h3>{{voucherInfo}}票</h3>
              <h5>活动截止后失效</h5>
            </div>
            <div class="right">
              <button>立即投票</button>
            </div>
          </div>
        </div>
        <ul class="tab-list">
          <li v-for="item in tabList" :key="item.name" @click="selectedTab(item)"
              :class="{'act':item.type===currentType}">{{item.name}}
          </li>
        </ul>
        <div class="list-box">
          <quick-loadmore ref="vueLoad"
                          :bottom-method="handleBottom"
                          :disable-top="true" :disable-bottom="false">
            <ul class="list">
              <li v-for="item in dataList">
                <p>
                  <span class="">{{currentType==='1'?item.mobile:item.name}}</span>
                  <span class=""></span>
                </p>
                <p>
                  <span class="small">{{item.createTime}}</span>
                  <span class="light">{{currentType==='1'?item.voucherNum:item.amount}}</span>
                </p>
              </li>
            </ul>
            <load-more tip="正在加载" v-show="loadShow"></load-more>
          </quick-loadmore>
        </div>
      </div>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'

  export default {
    name: "index",
    components: {
      slide
    },
    data() {
      return {
        tabList: [
          {
            type: '1',
            name: '获取记录',
          },
          {
            type: '0',
            name: '使用记录'
          }
        ],
        currentType: '1',
        dataList: [],
        page: 1,
        loadShow: true,
        currencyInfo: {},
        voucherInfo: 0
      }
    },
    methods: {
      selectedTab(item) {
        if (item.type === this.currentType) return
        this.currentType = item.type
        sessionStorage.setItem("voteVoucherType", item.type);
        this.page = 1
        this.dataList = []
        this.loadShow = true
        this.$refs.vueLoad.onBottomLoaded();
        this.getList()
      },
      handleBottom() {
        this.page++
        this.getList()
      },
      getVoucherInfo() {
        http.post('/vote/voucher-info', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.voucherInfo = res.content.count
        })
      },
      getList() {
        http.post('/vote/voucher', {
          type: this.currentType,
          page: this.page,
          page_size: 10
        }, (res) => {
          if (this.loadShow) this.loadShow = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          /*for(let i = 0;i<20;i++){
            this.dataList = this.dataList.concat(res.content.list)
          }*/
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
      this.currentType = sessionStorage.getItem("voteVoucherType") || '1'
      this.getVoucherInfo()
      this.getList()
    },
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .vote-voucher
    fixed-full-screen()
    .vote-voucher-content
      position absolute
      top 57px
      bottom 0
      left 0
      right 0
      .brief-card-box
        height 100px
        padding 0 $space-box
      .brief-card
        display flex
        justify-content space-between
        padding 15px 20px
        color white
        linear-gradient-compatible(#FFAA4F, #FFB357)
        border-radius 10px
        box-shadow 0 4px 15px 4px RGBA(240, 208, 172, 0.5)
        h3
          font-size $font-size-large-x
          margin-bottom 5px
        button
          line-height 35px
          background white
          color $color-theme
          border-radius 20px
          width 80px
          border 0
      .tab-list
        display flex
        justify-content space-around
        box-sizing border-box
        margin 0 $space-box
        border-bottom 1px solid $color-border
        li
          line-height 35px
          height 35px
          box-sizing border-box
          font-size $font-size-medium
          &.act
            border-bottom 2px solid $color-theme
      .list-box
        position absolute
        top 135px
        bottom 0
        width 100%
        overflow hidden
        .list
          margin 0 $space-box
        li
          border-bottom 1px solid $color-border
          padding 10px 0
          p
            display flex
            justify-content space-between
            align-items center
            line-height 20px
          .small
            font-size 10px
            color $color-text-minor
          .light
            color $color-text-sub

</style>
