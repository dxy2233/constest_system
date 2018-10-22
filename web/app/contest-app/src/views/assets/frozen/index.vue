<template>
  <slide>
    <div class="assets-frozen">
      <!--<x-header :left-options="{backText: ''}" class="w-header">
        锁仓记录
      </x-header>-->
      <app-header>
        锁仓记录
      </app-header>
      <div class="main">
        <quick-loadmore ref="vueLoad"
                        :bottom-method="handleBottom"
                        :disable-top="true" :disable-bottom="false">
          <ul class="list">
            <li v-for="item in dataList">
              <p>
                <span class="remark">{{item.remark}}</span>
                <span class="status"></span>
              </p>
              <p>
                <span class="time">{{item.createTime}}</span>
                <span class="amount">{{item.amount}}</span>
              </p>
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
        loadShow: true
      }
    },
    methods: {

      handleBottom() {
        this.page++
        this.getList()
      },
      getList() {
        http.post('/wallet/currency-frozen', {
          id: this.$route.params.id,
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
    },
    created() {
      this.getList()
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .assets-frozen
    fixed-full-screen()
    .main
      position absolute
      top 50px
      bottom 0
      width 100%
      overflow hidden
      .list
        li
          padding 20px $space-box
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

</style>
