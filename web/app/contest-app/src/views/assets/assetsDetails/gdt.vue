<template>
  <slide>
    <div class="assets-details-gdt">
      <app-header>
      </app-header>
      <div class="assets-details-main">
        <div class="brief">
          <div class="top">
            <p>{{currencyInfo.name}}</p>
            <h3>{{currencyInfo.positionAmount}}</h3>
          </div>
        </div>
        <ul class="tab-list">
          <li v-for="item in tabList" :key="item.name" @click="selectedTab(item)"
              :class="{'act':item.type===currentType}">{{item.name}}
          </li>
        </ul>
        <m-load @loadMore="handleBottom1" ref="my_scroller1" class="detail" v-show="currentType==='1'">
          <ul class="detail-list">
            <li v-for="item in data1.dataList">
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
        </m-load>
        <m-load @loadMore="handleBottom0" ref="my_scroller0" class="detail" v-show="currentType==='0'">
          <ul class="detail-list">
            <li v-for="item in data0.dataList">
              <p>
                <span class="remark">{{item.remark}}</span>
                <span class="status">{{item.statusStr}}</span>
              </p>
              <p>
                <span class="time">{{item.createTime}}</span>
                <span class="amount">{{item.amount}}</span>
              </p>
            </li>
          </ul>
        </m-load>

        <div class="handle-btn">
          <router-link v-if="parseInt(currencyInfo.withdrawStatus)"
                       tag="button" :to="{path:'/assets/dts'+dtsId+'/transfer',query:{name:'gdt'}}">
            <span>领取</span>
          </router-link>
        </div>
      </div>
      <router-view></router-view>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import {InlineLoading} from 'vux'
  import MLoad from 'components/mLoad/index'

  export default {
    name: "index",
    components: {
      slide,
      InlineLoading,
      MLoad
    },
    data() {
      return {
        tabList: [
          {
            type: '1',
            name: '获取明细',
          },
          {
            type: '0',
            name: '领取记录'
          }
        ],
        currentType: localStorage.getItem("currencyDetailType") || '1',
        dataList: [],
        page: 1,
        currencyInfo: {},
        dtsId: this.$route.params.id,
        total: '',
        refreshLoad: false,
        data1: {
          page: 1,
          dataList: [],
          total: ''
        },
        data0: {
          page: 1,
          dataList: [],
          total: ''
        },

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
        localStorage.setItem("currencyDetailType", item.type);
      },
      handleBottom1() {
        http.post('/wallet/currency-detail', {
          id: this.$route.params.id,
          page: this.data1.page,
          type: '1',
          page_size: 10
        }, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.data1.dataList = this.data1.dataList.concat(res.content.list)
          this.data1.total = res.content.count
          this.data1.page++
          let noMore = this.data1.dataList.length >= res.content.count
          this.$refs.my_scroller1.$emit('finishInfinite', noMore);
        })
      },
      handleBottom0() {
        http.post('/wallet/currency-auditing', {
          id: this.$route.params.id,
          page: this.data0.page,
          page_size: 10
        }, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.data0.dataList = this.data0.dataList.concat(res.content.list)
          this.data0.total = res.content.count
          this.data0.page++
          let noMore = this.data0.dataList.length >= res.content.count
          this.$refs.my_scroller0.$emit('finishInfinite', noMore);
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
        })
      }
    },
    created() {
      this.getCurrencyInfo()
    },
    destroyed() {
      localStorage.removeItem('currentType')
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .assets-details-gdt
    fixed-full-screen()

    .refresh-btn
      display flex
      align-items center

      span
        margin-left 5px

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
        padding 30px 0
        background url("/static/images/assets-bg1.png") center no-repeat
        background-size cover
        text-align center

        p
          font-size $font-size-large
          height $font-size-large

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
      top 195px
      bottom 0
      padding-bottom 60px
      width 100%
      overflow scroll

      .detail-list
        padding-left $space-box
        min-height 20px

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


    .handle-btn
      position absolute
      left 0
      bottom 0
      right 0
      background white
      padding 10px $space-box

      button
        text-align center
        border none
        font-size $font-size-medium-x
        box-sizing border-box
        width 100%
        line-height 40px
        color white
        background $color-theme
        border-radius 10px


</style>
