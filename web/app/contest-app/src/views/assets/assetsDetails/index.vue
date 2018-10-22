<template>
  <slide>
    <div class="assets-details">
      <!--<x-header :left-options="{backText: ''}" class="w-header">
        {{currencyInfo.name}}
      </x-header>-->
      <app-header>
      </app-header>
      <div class="assets-details-main">
        <!--<div class="brief">
          <h1>{{currencyInfo.positionAmount}}</h1>
          <ul>
            <li>
              <p class="label">可用</p>
              <p class="number">{{currencyInfo.useAmount}}</p>
            </li>
            <li @click="goFrozen">
              <p class="label">锁仓</p>
              <p class="number">{{currencyInfo.frozenAmount}}</p>
            </li>
          </ul>
        </div>-->
        <div class="brief">
          <div class="top">
            <p>{{currencyInfo.name}}</p>
            <h3>{{currencyInfo.positionAmount}}</h3>
          </div>
          <ul class="bottom">
            <li>
              <p>可用</p>
              <h4>{{currencyInfo.useAmount}}</h4>
            </li>
            <li @click="goFrozen">
              <p>锁仓</p>
              <h4>{{currencyInfo.frozenAmount}}</h4>
            </li>
            <!--<router-link tag="li" to="/assets/transfer">可用</router-link>
            <router-link tag="li" :to="{name:'collect',query:{address:walletData.address}}">收款</router-link>-->
          </ul>
        </div>

        <ul class="tab-list">
          <li v-for="item in tabList" :key="item.name" @click="selectedTab(item)"
              :class="{'act':item.type===currentType}">{{item.name}}
          </li>
        </ul>
        <div class="detail">
          <quick-loadmore ref="vueLoad"
                          :bottom-method="handleBottom"
                          :disable-top="true" :disable-bottom="false">
            <ul class="detail-list">
              <li v-for="item in dataList">
                <p>
                  <span class="remark">{{item.remark}}</span>
                  <span class="status">{{item.statusStr}}</span>
                </p>
                <p>
                  <span class="time">{{item.effectTime}}</span>
                  <span class="amount">{{item.amount}}</span>
                </p>
              </li>
            </ul>
            <load-more tip="正在加载" v-show="loadShow"></load-more>
          </quick-loadmore>

        </div>
        <div class="handle-btn">
          <ul>
            <router-link tag="li" :to="'/assets/dts'+dtsId+'/collect'">
              <img src="/static/images/collect.png" alt="">
              <span>收款</span>
            </router-link>
            <router-link tag="li" :to="'/assets/dts'+dtsId+'/transfer'">
              <img src="/static/images/transfer.png" alt="">
              <span>转账</span>
            </router-link>
          </ul>
          <!--<div class="line"></div>-->
        </div>
      </div>
      <router-view></router-view>
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
            name: '收入明细',
          },
          {
            type: '0',
            name: '支出记录'
          }
        ],
        currentType: 1,
        dataList: [],
        page: 1,
        loadShow: true,
        currencyInfo: {},
        dtsId: this.$route.params.id
      }
    },
    methods: {
      goFrozen() {
        this.$router.push({
          path: `/assets/dts${this.dtsId}/frozen`
        })
      },

      selectedTab(item) {
        if (item.type === this.currentType) return
        this.currentType = item.type
        sessionStorage.setItem("currencyDetailType", item.type);
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
      getList() {
        http.post('/wallet/currency-detail', {
          id: this.$route.params.id,
          type: this.currentType,
          page: this.page,
          page_size: 10
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
            this.$refs.vueLoad.onBottomLoaded(false);
          }
        })
      },
      getCurrencyInfo() {
        http.post('/wallet/currency-info', {
          id: this.$route.params.id,
        }, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.currencyInfo = res.content
          // this.currencyInfo.useCode = this.currencyInfo.code.toUpperCase()
        })
      }
    },
    created() {
      this.currentType = sessionStorage.getItem("currencyDetailType") || '1'
      this.getCurrencyInfo()
      this.getList()
    },
    destroyed() {
      sessionStorage.removeItem('currentType')
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .assets-details
    fixed-full-screen()
    overflow auto
    .assets-details-main
      position absolute
      top 50px
      bottom 0
      width 100%
    .brief
      border-radius 10px
      background #FFB760
      overflow hidden
      color white
      box-shadow 0 4px 15px 4px RGBA(240, 208, 172, 0.5)
      margin 10px $space-box
      & > .top
        padding 15px 0
        background url("/static/images/assets-bg1.png") center no-repeat
        background-size cover
        text-align center
        p
          font-size $fs$font-size-large
          height $fs$font-size-large
        h3
          margin-top 10px
          font-weight 700
          font-size 36px
          height 36px
      & > .bottom
        padding 10px 0
        margin-left -1px
        overflow hidden
        li
          width 50%
          box-sizing border-box
          border-left 1px solid white
          text-align center
          float left
          h4
            margin-top 10px
            font-size $font-size-large
            height $font-size-large
    .tab-list
      box-sizing border-box
      line-height 50px
      height 50px
      margin-left $space-box
      li
        padding 0 3px
        display inline-block
        box-sizing border-box
        margin-right 30px
        font-size $font-size-medium-x
        color $color-text-minor
        &.act
          color $color-theme
    .detail
      position absolute
      top 225px
      bottom 60px
      width 100%
      overflow hidden
      .detail-list
        padding-left $space-box
        li
          padding 15px 15px 15px 0
          border-bottom 1px solid $color-border
          p
            display flex
            justify-content space-between
            align-items center
            line-height 20px
          .remark
            font-weight bold
          .time
            font-size 10px
            color $color-text-minor
          .amount
            font-size $font-size-small-s
            color $color-text-sub
          .status
            font-size $font-size-small-s
    /*.handle-btn
      position absolute
      left $space-box
      bottom $space-box
      right $space-box
      ul
        border-radius 10px
        overflow hidden
        background $color-theme
        line-height 48px
        height 48px
        li
          width 50%
          float left
          text-align center
          font-size $font-size-medium-x
          color white
      .line
        position absolute
        top 0
        left 50%
        height 48px
        width 1px
        background white*/
    .handle-btn
      position absolute
      left 0
      bottom 0
      right 0
      ul
        overflow hidden
        line-height 60px
        height 60px
        border-top 1px solid $color-border-sub
        margin-left -1px
        box-sizing border-box
        li
          width 50%
          float left
          text-align center
          font-size $font-size-medium-x
          box-sizing border-box
          border-left 1px solid $color-border-sub
          display flex
          align-items center
          justify-content center
          img
            margin-right 5px
            width 25px

</style>
