<template>
  <slide>
    <div class="rcmd-record">
      <app-header>
        推荐记录
      </app-header>
      <div class="record-main">
        <scroller :on-infinite="handleBottom" ref="my_scroller">
          <div class="no-data" v-if="!dataList.length&&!loadShow">
            <img src="/static/images/state-fail.png" alt="">
          </div>
          <ul class="list">
            <li v-for="item in dataList" :key="item.id">
              <h4>{{item.mobile}}</h4>
              <p>
                <span>{{item.typeName}}</span>
                <span>{{item.createTime}}</span>
              </p>
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
      slide
    },
    data() {
      return {
        dataList: [],
        page: 1,
        loadShow: true,
        total:''
      }
    },
    methods: {
      handleBottom() {
        if (this.total!==''&&this.dataList.length >= parseInt(this.total)){
          this.$refs.my_scroller.finishInfinite(true);
          return
        }
        http.post('/node/recommend', {
          page: this.page,
          page_size: 10
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
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .rcmd-record
    fixed-full-screen()
    &>.app-header
      border-bottom 1px solid $color-border-sub
    .record-main
      position absolute
      top 50px
      bottom 0
      left 0
      right 0
      overflow hidden
      .list
        min-height $space-box
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
