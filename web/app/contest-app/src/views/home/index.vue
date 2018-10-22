<template>
  <div class="home">
    <!--<router-link to="/home/notice-details/1">gogogogoog</router-link>-->
    <div class="home-main">
      <header>
        <h2>首页</h2>
        <p>
          <router-link to="/home/vote" tag="span">节点投票</router-link>
          <router-link to="/home/contribute" tag="span">贡献榜</router-link>
        </p>
      </header>
      <div class="notice-swiper">
        <swiper height="135px" class="notice-swiper" dots-position="left">
          <swiper-item v-for="(item, index) in swiperList" class="notice-swiper-item"
                       :key="index">
            <router-link v-if="item.type==='1'" tag="div" class="img-box"
                         :style='{ backgroundImage: "url(" + item.image + ")"}'
                         :to="'/home/notice/dts'+item.id"></router-link>
            <a v-else :href="item.url" target="_blank" class="img-box"></a>
          </swiper-item>
        </swiper>
        <!--<p class="and-more">查看更多>></p>-->
        <router-link tag="p" to="/home/notice" class="and-more">查看更多>></router-link>
      </div>
      <div class="line"></div>
      <div class="ranking">
        <ul class="title-tab">
          <li v-for="(tab,index) in nodeTab" :class="{'act':tab.id===currentNodeId}" @click="selectTab(tab.id)">
            {{tab.name}}
          </li>
        </ul>
        <!--<span class="all-rank">全部排名</span>-->
        <router-link class="all-rank" to="/home/node" tag="span">全部排名</router-link>
        <div class="rank-list-box">
          <rank-list :list="nodeList"></rank-list>
        </div>
      </div>
    </div>
    <!--<router-link to="/home/node-details">gogogogoog</router-link>-->
    <router-view></router-view>
  </div>

</template>

<script>
  import {Swiper, SwiperItem, Tab, TabItem,} from 'vux'
  import rankList from 'components/rankList/index'
  import http from 'js/http'

  const baseList = [{
    url: 'javascript:',
    img: 'https://ww1.sinaimg.cn/large/663d3650gy1fq66vvsr72j20p00gogo2.jpg',
    title: '送你一朵fua'
  }, {
    url: 'javascript:',
    img: 'https://ww1.sinaimg.cn/large/663d3650gy1fq66vw1k2wj20p00goq7n.jpg',
    title: '送你一辆车'
  }]
  export default {
    name: "home",
    components: {
      Swiper, SwiperItem, Tab, TabItem,
      rankList
    },
    data() {
      return {
        swiperList: baseList,
        nodeTab: [
          '超级节点',
          '高级节点'
        ],
        currentTab: 0,
        currentNodeId: '',
        nodeList: []
      }
    },
    methods: {
      jjj(id) {
        id = 1
        let aaa = '333'
        this.$router.push({
          path: `/home/notice-details/${aaa}`
        })
      },
      selectTab(index) {
        this.currentTab = index
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
          this.nodeTab = res.content
          this.currentNodeId = res.content[0].id
          this.getNodeList()
        })
      },
      getNodeList() {
        http.post('/node/vote', {
          id: this.currentNodeId
        }, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.nodeList = res.content
          // console.log(this.nodeList)
          // this.currentNodeId = res.content[0].id
        })
      }

    },
    created() {
      this.getNoticeList()
      this.getNodeTab()
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
    padding $space-box
    .line
      height 1px
      width 100%
      background-color $color-border
      margin-top 10px
    header
      display flex
      justify-content space-between
      align-items flex-end
      /*background-image url('https://ww1.sinaimg.cn/large/663d3650gy1fq66vw1k2wj20p00goq7n.jpg')*/
      h2
        font-weight bold
        color $color-text-base
        font-size $font-size-large-x
      span
        color $color-text-sub
        font-size $font-size-medium
        margin-left 24px
    .notice-swiper
      padding 15px 0
      overflow visible
      .and-more
        float right
        color $color-text-sub
        font-size $font-size-small-s
      .img-box
        display block
        background-size cover
        background-position center center
        border-radius 4px
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

