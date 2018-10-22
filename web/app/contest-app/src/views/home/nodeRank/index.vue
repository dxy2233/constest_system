<template>
  <slide>
    <div class="node-rank">
      <!--<x-header :left-options="{backText: ''}">节点排名
        <router-link tag="a" to="/home/node/rule" slot="right">规则说明</router-link>
      </x-header>-->
      <app-header>
        节点排名
        <router-link tag="span" to="/home/node/rule" slot="right">规则说明</router-link>
      </app-header>
      <div class="main-box">
        <div class="top">
          <ul class="list-tab">
            <li v-for="(tab,index) in nodeTab" :class="{'act':tab.id===currentNodeId}" @click="selectTab(tab)">
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
            <rank-list :list="nodeList"></rank-list>
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
        nodeList: [],
        nodeTab: [],
        currentNodeId: '',
        page: 1,
        counttime: "",
        loadShow: true
      };
    },
    deactivated() {

    },
    methods: {
      selectTab(item) {
        if (item.id === this.currentNodeId) return
        this.currentNodeId = item.id
        sessionStorage.setItem("nodeRankType", item.id);
        this.page = 1
        this.nodeList = []
        this.loadShow = true
        this.$refs.vueLoad.onBottomLoaded();
        this.getNodeList()
      },
      handleBottom() {
        this.page++
        this.getNodeList()
      },
      getNodeTab() {
        http.post('/node', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.nodeTab = res.content
          this.currentNodeId = sessionStorage.getItem('nodeRankType') || res.content[0].id
          this.getNodeList()
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
      this.getNodeTab()
    },
    mounted() {
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .node-rank
    fixed-full-screen()
    & > .main-box
      position absolute
      width 100%
      top 50px
      bottom 0
      .top
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

      .bottom
        position absolute
        bottom 0
        top 80px
        width 100%
        overflow hidden
</style>
