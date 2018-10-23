<template>
  <slide>
    <div class="contribute-list">
      <!--<x-header :left-options="{backText: ''}">贡献榜
        <router-link tag="a" to="/home/contribute/rule" slot="right">奖励规则</router-link>
      </x-header>-->
      <app-header>
        贡献榜
        <!--<router-link tag="span" to="/home/contribute/rule" slot="right">奖励规则</router-link>-->
      </app-header>
      <div class="main-box">
        <div class="top">
          <ul class="list-tab">
            <li v-for="(tab,index) in contributeTab" :class="{'act':tab.type===currentContributeType}"
                @click="selectTab(tab.type)">
              {{tab.name}}
            </li>
          </ul>
          <div class="time" v-if="counttime">
            <p>统计时间 {{counttime}}</p>
          </div>
        </div>
        <div class="bottom">
          <quick-loadmore ref="vueLoad"
                          :bottom-method="handleBottom"
                          :disable-top="true" :disable-bottom="false">
            <!--<rank-list :list="contributeList"></rank-list>-->
            <ul class="contribute-list-data">
              <li v-for="(item,index) in contributeList">
                <div class="top">
                  <p>
                    <span class="num">{{++index}}</span>
                    {{item.mobile}}</p>
                  <p class="right">{{item.statusStr}}</p>
                </div>
                <div class="bottom">
                  <p>
                  </p>
                  <p class="right">{{item.voteNumber}}票</p>
                </div>
              </li>
            </ul>
            <load-more tip="正在加载" v-show="loadShow"></load-more>
          </quick-loadmore>
        </div>
      </div>
      <router-view></router-view>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import rankList from 'components/rankList/index'
  import http from 'js/http'

  export default {
    name: "index",
    components: {
      slide,
      rankList
    },
    data() {
      return {
        dataList: [1, 2, 3, 4, 5],
        contributeList: [],
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
        currentContributeType: 'all',
        page: 1,
        counttime: "",
        loadShow: true
      };
    },
    deactivated() {

    },
    methods: {
      handleBottom() {
        this.page++
        this.getList()
      },
      selectTab(type) {
        if (type === this.currentContributeType) return
        this.currentContributeType = type
        sessionStorage.setItem("currentContributeType", type);
        this.page = 1
        this.contributeList = []
        this.$refs.vueLoad.onBottomLoaded();
        this.getList()
      },
      getList() {
        this.loadShow = true
        http.post('/vote', {
          type: this.currentContributeType,
          page: this.page,
          page_size: 10
        }, (res) => {
          this.loadShow = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.contributeList = this.contributeList.concat(res.content.list)
          this.counttime = res.content.counttime
          if (this.contributeList.length < parseInt(res.content.count)) {
            this.$refs.vueLoad.onBottomLoaded();
          } else {
            this.$refs.vueLoad.onBottomLoaded(false);
          }
        })
      },
    },
    created() {
      // this.currentContributeType = sessionStorage.getItem('currentContributeType') || 'all'
      this.getList()
    },
    mounted() {
      // console.log("mounted");
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .contribute-list
    fixed-full-screen()
    & > .main-box
      position absolute
      width 100%
      top 50px
      bottom 0
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
        top 80px
        width 100%
        overflow hidden
        .contribute-list-data
          padding 0 $space-box
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
</style>
