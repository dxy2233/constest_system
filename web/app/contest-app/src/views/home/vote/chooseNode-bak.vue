<template>
  <slide>
    <div class="choose-node">
      <!--<x-header :left-options="{backText: ''}" class="">
        <div slot="overwrite-left" class="icon-back header-left-icon" @click.native="backVote"></div>
        节点选择
      </x-header>-->
      <app-header>
        <span slot="left" class="icon-back" @click="backVote"></span>
        节点选择
      </app-header>
      <div class="choose-node-content">
        <div class="left">
          <ul>
            <li v-for="(tab,index) in nodeTab" :class="{'act':tab.id===currentNodeId}" @click="selectTab(tab)">
              <i class="line"></i>
              <span>{{tab.name}}</span>
            </li>
          </ul>
        </div>
        <div class="right">
          <!--<quick-loadmore ref="vueLoad"
                          :bottom-method="handleBottom"
                          :disable-top="true" :disable-bottom="false">
            <ul class="node-list">
              <li v-for="item in nodeList" @click="selectedNode(item)" :class="{'act':item.id == selectId}">
                <img :src="item.logo" alt="" class="img">
                <span class="name">{{item.name}}</span>
                <div class="tenure" v-if="item.isTenure">
                  <span>任职</span>
                </div>
              </li>
            </ul>
            <load-more tip="正在加载" v-show="loadShow"></load-more>
          </quick-loadmore>-->
          <scroller :on-infinite="handleBottom" ref="my_scroller">
            <ul class="node-list">
              <li v-for="item in nodeList" @click="selectedNode(item)" :class="{'act':item.id == selectId}">
                <img :src="item.logo" alt="" class="img">
                <span class="name">{{item.name}}</span>
                <div class="tenure" v-if="item.isTenure">
                  <span>任职</span>
                </div>
              </li>
            </ul>
          </scroller>
        </div>
      </div>

    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'

  export default {
    name: "index",
    props: ['selectId'],
    components: {
      slide
    },
    data() {
      return {
        nodeList: [],
        nodeTab: [],
        currentNodeId: '',
        page: 1,
        loadShow: true,
        total:''
      }
    },
    methods: {
      backVote() {
        this.$emit('close', true)
      },
      selectTab(item) {
        console.log(item)
        if (item.id === this.currentNodeId) return
        this.currentNodeId = item.id
        this.currentNodeId = item.id
        sessionStorage.setItem("chooseNodeId", item.id);
        this.page = 1
        this.nodeList = []
        this.total = ''
        this.$refs.my_scroller.finishInfinite(false);
        /*this.loadShow = true
        this.$refs.vueLoad.onBottomLoaded();
        this.getNodeList()*/
      },
      selectedNode(item) {
        this.$emit('selectedNode', item)
        this.backVote()
      },
      handleBottom() {
        /*this.page++
        this.getNodeList()*/
        if (this.currentNodeId === ''){
          this.getNodeTab(()=>{
            this.handleBottom()
          })
          return
        }

        if (this.total!==''&&this.nodeList.length >= parseInt(this.total)){
          this.$refs.my_scroller.finishInfinite(true);
          return
        }

        http.post('/node/vote', {
          id: this.currentNodeId,
          page: this.page,
          page_size: 10
        }, (res) => {
          if (this.loadShow) this.loadShow = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.nodeList = this.nodeList.concat(res.content.list)
          // this.counttime = res.content.counttime
          this.total = res.content.count
          this.page++
          this.$refs.my_scroller.finishInfinite(false);
        })

      },
      getNodeTab(cb) {
        http.post('/node', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.nodeTab = res.content
          this.currentNodeId = sessionStorage.getItem('chooseNodeId') || res.content[0].id
          cb()
        })
      },
      getNodeList() {
        http.post('/node/vote', {
          id: this.currentNodeId,
          page: this.page,
          page_size: 10
        }, (res) => {
          if (this.loadShow) this.loadShow = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.nodeList = this.nodeList.concat(res.content.list)
          this.counttime = res.content.counttime
          if (this.nodeList.length < parseInt(res.content.count)) {
            this.$refs.vueLoad.onBottomLoaded();
          } else {
            this.$refs.vueLoad.onBottomLoaded(false);
          }
        })
      },
    },
    created() {
      // this.getNodeTab()
    },
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .choose-node
    fixed-full-screen()
    .app-header
      border-bottom 1px solid $color-border-sub
    .choose-node-content
      position absolute
      top 50px
      left 0
      bottom 0
      right 0
      & > div
        position relative
        height 100%
        float left
      & > .left
        overflow auto
        width 90px
        border-right 1px solid $color-border-sub
        box-sizing border-box
        text-align center
        span
          display inline-block
          line-height 50px
          width 60px
          border-bottom 1px solid $color-border-sub
        li
          position relative
          &.act
            color $color-theme
            .line
              position absolute
              left 0
              top 15px
              display inline-block
              width 3px
              height 20px
              background $color-theme

      & > .right
        width calc(100% - 90px)
        overflow hidden
        .node-list
          min-height $space-box
          li
            position relative
            display flex
            align-items center
            border-bottom 1px solid $color-border-sub
            padding $space-box
            overflow hidden
            &.act
              color $color-theme
            .img
              width 27px
              height 27px
              margin-right 18px
              border-radius 50%
            .tenure
              position absolute
              right -30px
              top -30px
              background $color-theme
              color #fff
              width 60px
              height 60px
              text-align center
              font-size $font-size-small-s
              transform-compatible(rotate(45deg))
              span
                position relative
                top 43px
</style>
