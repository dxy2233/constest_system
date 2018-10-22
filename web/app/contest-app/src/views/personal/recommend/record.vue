<template>
  <slide>
    <div class="record">
      <!--<x-header :left-options="{backText: ''}">
        推荐记录
      </x-header>-->
      <app-header>
        推荐记录
      </app-header>
      <div class="record-main">
        <quick-loadmore ref="vueLoad"
                        :bottom-method="handleBottom"
                        :disable-top="true" :disable-bottom="false">
          <div class="no-data" v-if="!dataList.length&&!loadShow">
            <img src="/static/images/state-fail.png" alt="">
            <p>你还没有推荐过用户</p>
          </div>
          <ul class="list">
            <li v-for="item in dataList" :key="item.id">
              <h4>{{item.name}}</h4>
              <p>
                <span>{{item.typeName}}</span>
                <span>{{item.createTime}}</span>
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
        loadShow: true,
      }
    },
    methods: {
      handleBottom() {
        this.page++
        this.getList()
      },
      getList() {
        http.post('/user/recommend', {
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
  .record
    fixed-full-screen()
    .record-main
      position absolute
      top 50px
      bottom 0
      left 0
      right 0
      overflow hidden
      .list
        li
          padding $space-box
          border-bottom 1px solid $color-border
          font-size $font-size-medium
          p
            margin-top 5px
            display flex
            justify-content space-between
            align-items center
            font-size $font-size-small-s
            color $color-text-minor
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
