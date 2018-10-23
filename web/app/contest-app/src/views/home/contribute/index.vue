<template>
  <slide>
    <div class="contribute-list">
      <app-header>
        贡献榜
        <!--<router-link tag="span" to="/home/contribute/rule" slot="right">奖励规则</router-link>-->
      </app-header>
      <div class="main">
        <div class="top">
          <ul class="list-tab">
            <li v-for="(tab,index) in contributeTab" :class="{'act':tab.type===currentContributeType}"
                @click="selectTab(tab.type)">
              {{tab.name}}
            </li>
          </ul>
          <!--<div class="time" v-if="counttime">
            <p>统计时间 {{counttime}}</p>
          </div>-->
        </div>
        <div class="bottom">
          <scroller :on-infinite="handleBottom" ref="my_scroller">
            <ul class="list contribute-list-data">
              <li v-for="(item,index) in dataList">
                <div class="top">
                  <p>
                    <span class="num">{{++index}}</span>
                    {{item.mobile}}
                  </p>
                  <p class="right">
                    {{item.statusStr}}
                  </p>
                </div>
                <div class="bottom">
                  <p>
                  </p>
                  <p class="right">
                    {{item.voteNumber}}
                    票</p>
                </div>
              </li>
            </ul>
          </scroller>
        </div>
        <router-view></router-view>
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
        dataList: [],
        page: 1,
        loadShow: true,
        total:'',
        contributeTab: [
          {
            type: 'all',
            name: '总票榜'
          },
          {
            type: 'pay',
            name: '支付投票榜'
          },
        ],
        currentContributeType: sessionStorage.getItem('currentContributeType') || 'all',
        counttime: "",
      }
    },
    methods: {
      selectTab(type) {
        if (type === this.currentContributeType) return
        this.currentContributeType = type
        sessionStorage.setItem("currentContributeType", type);
        this.page = 1
        this.dataList = []
        this.total = ''
        this.$refs.my_scroller.finishInfinite(false);
      },
      handleBottom() {
        if (this.total!==''&&this.dataList.length >= parseInt(this.total)){
          this.$refs.my_scroller.finishInfinite(true);
          return
        }
        http.post('/vote', {
          type: this.currentContributeType,
          page: this.page,
          page_size: 10
        }, (res) => {
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
    },
    created() {
    }
  }
</script>
<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .contribute-list
    fixed-full-screen()
    .main
      position absolute
      top 50px
      bottom 0
      width 100%
      overflow hidden
      .contribute-list-data
        padding 0 $space-box
        min-height $space-box
        li
          padding 10px 0
          border-bottom 1px solid $color-border
          & > div
            display flex
            justify-content space-between
            &.top
              margin-bottom 5px
            .num
              margin-right 20px
            .right
              color $color-text-minor
      &>.top
        .time
          text-align center
          font-size $font-size-small-s
          p
            display inline-block
            padding 5px 10px
            background $color-background-sub
            color $color-text-minor
            border-radius 3px
        .list-tab
          display flex
          justify-content center
          width 100%
          margin 5px auto
          font-size $font-size-medium
          color $color-text-sub
          li
            height 35px
            line-height 35px
            box-sizing border-box
            margin 0 10px
            &.act
              color $color-text-base
              border-bottom 2px solid $color-theme

      &>.bottom
        position absolute
        bottom 0
        top 50px
        width 100%
        overflow hidden

</style>
