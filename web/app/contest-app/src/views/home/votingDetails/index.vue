<template>
  <slide>
    <div class="voting-details">
      <!--<x-header :left-options="{backText: ''}">
        投票明细
      </x-header>-->
      <app-header>
        投票明细
      </app-header>
      <div class="voting-tab">
        <ul>
          <li v-for="item in votingTab" :key="item.name" @click="selectedTab(item)"
              :class="{'act':item.type===currentVotingType}">{{item.name}}
          </li>
        </ul>
      </div>
      <div class="voting-list">
        <!--<quick-loadmore ref="vueLoad"
                        :bottom-method="handleBottom"
                        :disable-top="true" :disable-bottom="false">
          <div>
            <ul :class="[currentVotingType==='log'?'log-list':'user-list']" class="list">
              <li v-if="currentVotingType==='log'" v-for="item in listData">
                <div class="top">
                  <p>{{item.mobile}}</p>
                  <p>{{item.voteNumber}}票</p>
                </div>
                <div class="bottom">
                  <p>
                    {{item.createTime}}
                  </p>
                  <p>{{item.typeStr}}</p>
                </div>
              </li>
              <li v-if="currentVotingType==='user'" v-for="(item,index) in listData">
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
          </div>
        </quick-loadmore>-->
        <scroller :on-infinite="handleBottom" ref="my_scroller">
          <ul :class="[currentVotingType==='log'?'log-list':'user-list']" class="list">
            <li v-if="currentVotingType==='log'" v-for="item in listData">
              <div class="top">
                <p>{{item.mobile}}</p>
                <p>{{item.voteNumber}}票</p>
              </div>
              <div class="bottom">
                <p>
                  {{item.createTime}}
                </p>
                <p>{{item.typeStr}}</p>
              </div>
            </li>
            <li v-if="currentVotingType==='user'" v-for="(item,index) in listData">
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
        </scroller>
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
      slide,
    },
    data() {
      return {
        votingTab: [
          {
            type: 'log',
            name: '投票记录',
          },
          {
            type: 'user',
            name: '支持用户'
          }
        ],
        currentVotingType: localStorage.getItem("votingType") || 'log',
        listData: [],
        page: 1,
        loadShow: true,
        total:''
      }
    },
    methods: {
      selectedTab(item) {
        if (item.type === this.currentVotingType) return
        this.currentVotingType = item.type
        localStorage.setItem("votingType", item.type);
        this.page = 1
        this.listData = []
       /* this.loadShow = true
        this.$refs.vueLoad.onBottomLoaded();
        this.getData()*/
        this.total = ''
        this.$refs.my_scroller.finishInfinite(false);
      },
      handleBottom() {
        /*this.page++
        this.getData()*/
        if (this.total!==''&&this.listData.length >= parseInt(this.total)){
          this.$refs.my_scroller.finishInfinite(true);
          return
        }
        http.post('/node/vote-detail', {
          id: this.$route.params.id,
          type: this.currentVotingType,
          page: this.page,
          page_size: 10
        }, (res) => {
          if (this.loadShow) this.loadShow = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.listData = this.listData.concat(res.content.list)
          this.total = res.content.count
          this.page++
          this.$refs.my_scroller.finishInfinite(false);
          /*if (this.listData.length < parseInt(res.content.count)) {
            this.$refs.vueLoad.onBottomLoaded();
          } else {
            this.$refs.vueLoad.onBottomLoaded(false);
          }*/
        })

      },
      getData() {
        http.post('/node/vote-detail', {
          id: this.$route.params.id,
          type: this.currentVotingType,
          page: this.page,
          page_size: 10
        }, (res) => {
          if (this.loadShow) this.loadShow = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.listData = this.listData.concat(res.content.list)
          if (this.listData.length < parseInt(res.content.count)) {
            this.$refs.vueLoad.onBottomLoaded();
          } else {
            this.$refs.vueLoad.onBottomLoaded(false);
          }
        })
      },

    },
    created() {
     /* this.currentVotingType = localStorage.getItem("votingType") || 'log'
      this.getData()*/
    },
    destroyed() {
      localStorage.removeItem('votingType')
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .voting-details
    fixed-full-screen()
    .voting-tab
      padding-top 50px
      & > ul
        display flex
        justify-content space-around
        border-bottom 1px solid $color-border
        li
          padding 0 3px
          line-height 50px
          height 50px
          box-sizing border-box
          font-size $font-size-medium
          &.act
            border-bottom 2px solid $color-theme
    .voting-list
      position absolute
      left 0
      right 0
      bottom 0
      top 98px
      overflow hidden
      .list
        padding 0 $space-box
        min-height 30px
        li
          padding 10px 0
          border-bottom 1px solid $color-border
          & > div
            display flex
            justify-content space-between
            &.top
              margin-bottom 5px
      .log-list
        div.bottom
          font-size 10px
          color $color-text-minor
      .user-list
        .num
          margin-right 20px
        .right
          color $color-text-minor


</style>
