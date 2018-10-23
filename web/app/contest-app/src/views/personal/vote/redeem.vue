<template>
  <slide>
    <div class="vote-redeem">
      <app-header>
        赎回记录
      </app-header>
      <div class="vote-content">
        <quick-loadmore ref="vueLoad"
                        :bottom-method="handleBottom"
                        :disable-top="true" :disable-bottom="false">
          <div class="no-data" v-if="!dataList.length&&!loadShow">
            <img src="/static/images/state-fail.png" alt="">
            <p>你还没有赎回记录</p>
          </div>
          <ul class="vote-list">
            <li v-for="item in dataList">
              <div class="top">
                <h3>{{item.name}}</h3>
                <h4>{{item.voteNumber+'票'}}
                <span>{{item.statusStr}}</span>
                </h4>
              </div>
              <div class="bottom">
                <p>{{item.typeStr}}</p>
                <p>{{item.createTime}}</p>
              </div>
            </li>
          </ul>
          <load-more tip="正在加载" v-show="loadShow"></load-more>
        </quick-loadmore>

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
      }
    },
    methods: {
      handleBottom() {
        this.page++
        this.getList()
      },
      getList() {
        http.post('/vote/logs', {
          page: this.page,
          page_size: 10,
          type:0
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
      this.getList()
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .vote-redeem
    fixed-full-screen()
    .vote-content
      position absolute
      top 50px
      bottom 0
      left 0
      right 0
      overflow hidden
      .vote-list
        li
          padding $space-box
          border-bottom 1px solid $color-border
          &>div
            display flex
            justify-content space-between
            align-items center
            h3
              font-size $font-size-medium
            h4
              font-size $font-size-small
              span
                font-size $font-size-small-s
                color $color-text-minor
            p
              font-size $font-size-small-s
              color $color-text-minor



</style>
