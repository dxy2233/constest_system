<template>
  <div class="home">
    <div class="home-main">
      <header>
        <h2>首页</h2>
        <p>
          <router-link to="/home/vote" tag="span">
            <img src="/static/images/vote.png" alt="" class="vote-img">
            投票
          </router-link>
          <router-link to="/home/contribute" tag="span">贡献榜</router-link>
        </p>
      </header>
      <div class="notice-swiper-box">
        <swiper height="135px" class="notice-swiper" dots-position="left" loop auto :duration="1000">
          <swiper-item v-for="(item, index) in swiperList" class="notice-swiper-item"
                       :key="index">
            <div class="img-box" @click="goNoticeDts(item)"
                 :style='{ backgroundImage: "url(" + item.image + ")"}'></div>
          </swiper-item>
        </swiper>
        <router-link tag="p" to="/home/notice" class="and-more">更多 ></router-link>
      </div>
      <div v-if="!countDownIsOpen" class="line"></div>
      <div v-else class="count-down">
        <img src="/static/images/time-bg.png" alt="">
        <div class="content">
          <p class="label">本轮竞选投票倒计时</p>
          <p class="data">{{downTimeStr}}</p>
        </div>
      </div>
      <div class="ranking">
        <ul class="title-tab">
          <li v-for="(tab,index) in nodeTab" :class="{'act':tab.id===currentNodeId}" @click="selectTab(tab.id)">
            {{tab.name}}
          </li>
        </ul>
        <router-link class="all-rank" to="/home/node" tag="span">更多 ></router-link>
        <div class="rank-list-box">
          <rank-list :list="nodeList"></rank-list>
          <load-more tip="正在加载" v-show="loadShow"></load-more>
          <div class="no-data" v-if="!loadShow&&!nodeList.length">暂无更多数据</div>
        </div>
      </div>
    </div>
    <router-view></router-view>
  </div>

</template>

<script>
  import {Swiper, SwiperItem, Tab, TabItem,} from 'vux'
  import rankList from 'components/rankList/index'
  import http from 'js/http'

  export default {
    name: "home",
    components: {
      Swiper, SwiperItem, Tab, TabItem,
      rankList
    },
    data() {
      return {
        swiperList: [],
        nodeTab: [],
        currentNodeId: '',
        nodeList: [],
        loadShow: true,
        downTime: 0,
        countDownIsOpen: false,
        timeInterval: '',
      }
    },
    methods: {
      goNoticeDts(item) {
        http.post('/notice/info', {
          id: item.id
        }, (res) => {
          console.log(res)
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          if (res.content.type == 0) {
            window.location.href = res.content.url
          } else {
            sessionStorage.setItem("noticeInfo", JSON.stringify(res.content));
            this.$router.push({
              path: `/home/notice/dts${item.id}`
            })
          }

        })
      },
      selectTab(index) {
        this.currentNodeId = index
        this.nodeList = []
        this.getNodeList()
      },
      getNoticeList() {
        http.post('/app/notice', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.swiperList = res.content
        })
      },
      getNodeTab() {
        http.post('/node', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          if (!res.content.length) {
            this.loadShow = false
            return
          }
          this.nodeTab = res.content
          this.currentNodeId = res.content[0].id
          this.getNodeList()
        })
      },
      getNodeList() {
        this.loadShow = true
        http.post('/node/vote', {
          id: this.currentNodeId
        }, (res) => {
          this.loadShow = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.nodeList = res.content
          // console.log(this.nodeList)
          // this.currentNodeId = res.content[0].id
        })
      },
      pageInt() {
        this.swiperList = []
        this.nodeList = []
        this.nodeTab = []
        this.currentNodeId = ''
        this.getCountTime()
        this.getNoticeList()
        this.getNodeTab()
      },
      getCountTime() {
        http.post('/vote/count-time', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.downTime = res.content.downTime
          // this.downTime = 5
          this.countDownIsOpen = res.content.countDownIsOpen
          if (this.timeInterval) clearInterval(this.timeInterval)
          this.timeInterval = setInterval(() => {
            if (this.downTime) {
              this.downTime--
            } else {
              clearInterval(this.timeInterval)
            }
          }, 1000)
        })
      }
    },
    created() {
      this.pageInt()
    },
    activated() {

    },
    computed: {
      downTimeStr() {
        if (!this.downTime) return '本轮竞选已结束'
        const px_d = 60 * 60 * 24//一天的秒
        const px_h = 60 * 60//一小时的秒
        const px_m = 60//一分钟的秒
        const px_s = 1//一秒
        let d = Math.floor(this.downTime / px_d);
        let h = Math.floor((this.downTime - d * px_d) / px_h)
        let m = Math.floor((this.downTime - d * px_d - h * px_h) / px_m)
        let s = Math.floor((this.downTime - d * px_d - h * px_h - m * px_m) / px_s)
        let r = []
        if (d > 0) {
          r.push(`${d}天`)
        }
        if (r.length || (h > 0)) {
          r.push(`${h}时`)
        }
        if (r.length || (m > 0)) {
          r.push(`${m}分`)
        }
        if (r.length || (s > 0)) {
          r.push(`${s}秒`)
        }
        return r.join('')
      }
    },
    watch: {
      '$route': function (t, f) {
        if (t.path === '/home') {
          this.pageInt()
        }
      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .home
    position: absolute
    width: 100%
    top: 0px
    bottom: 50px

  .home-main
    overflow auto
    height 100%
    padding 0 $space-box
    .count-down
      overflow hidden
      width 100%
      position relative
      img
        width 100%
        height 30px
      .content
        position absolute
        top 0
        left 0
        width 100%
      p
        width 50%
        float left
        line-height 30px
        text-align center
      .label
        color white
      .data
        color $color-theme
    .line
      height 1px
      width 100%
      background-color $color-border
      margin-top 10px
    header
      display flex
      justify-content space-between
      align-items flex-end
      line-height 50px
      padding-top $space-box
      /*background-image url('https://ww1.sinaimg.cn/large/663d3650gy1fq66vw1k2wj20p00goq7n.jpg')*/
      h2
        font-weight bold
        color $color-text-base
        font-size $font-size-large-x
      span
        color $color-text-sub
        font-size $font-size-medium
        margin-left 24px
      .vote-img
        width $font-size-medium
        margin-right 5px
    .vux-swiper
      border-radius 6px
      overflow hidden
    .notice-swiper
      padding 15px 0
      overflow visible
    .notice-swiper-box
      padding-bottom 15px
      .and-more
        float right
        color $color-text-sub
        font-size $font-size-small-s
      .img-box
        display block
        background-size cover
        background-position center center
        /*border-radius 6px*/
        width 100%
        height 100%
    .vux-indicator
      bottom -10px !important
      left -6px !important
      .vux-icon-dot
        width 32px !important
        &.active
          background-color $color-theme !important

    .ranking
      position relative
      .no-data
        text-align center
      .rank-list-box
        margin -15px
      .all-rank
        position absolute
        right 0
        top 9px
        font-size $font-size-small-s
        color $color-text-sub
      .title-tab
        display flex
        margin 15px auto
        /*font-size $font-size-medium*/
        color $color-text-sub
        li
          height 30px
          line-height 30px
          box-sizing border-box
          margin-right 10px
          &.act
            color $color-text-base
            border-bottom 2px solid $color-theme

</style>
<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  @media only screen and (max-width: 320px)
    .home-main .ranking .title-tab li
      font-size $font-size-small-s
</style>

