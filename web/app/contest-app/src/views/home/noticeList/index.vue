<template>
  <slide>
    <div class="notice-list">
      <!--<x-header :left-options="{backText: ''}">全部公告</x-header>-->
      <app-header>
        全部公告
      </app-header>
      <div class="main">
        <quick-loadmore ref="vueLoad"
                        :bottom-method="handleBottom"
                        :disable-top="true" :disable-bottom="false">
          <ul class="list">

            <li v-for="(item,index) in dataList"
                :style='{ backgroundImage: "url(" + item.image + ")"}'
                @click="lookDetails(item)">
            </li>

          </ul>
          <load-more tip="正在加载" v-show="loadShow"></load-more>
        </quick-loadmore>

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
        loadShow: true
      }
    },
    methods: {

      handleBottom() {
        this.page++
        this.getList()
      },
      getList() {
        http.post('/notice', {
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
      lookDetails(obj) {
        if (obj.type === '1') {
          this.$router.push({
            path: `/home/notice/${obj.id}`
          })
        } else {//0 是外部
          window.location.href = obj.url
        }
      }
    },
    created() {
      this.getList()
    }
  }
</script>
<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .notice-list
    fixed-full-screen()
    .main
      position absolute
      top 50px
      bottom 0
      width 100%
      overflow hidden
      .list
        padding 20px $space-box 0 $space-box
        li
          margin-bottom 25px
          background-size cover
          background-position center center
          border-radius 4px
          width 100%
          height 135px

</style>
