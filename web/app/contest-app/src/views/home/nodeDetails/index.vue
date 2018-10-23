<template>
  <slide>
    <div class="node-details">
      <!--<x-header :left-options="{backText: ''}">
        <a slot="right" @click="goVoting">投票明细</a>
      </x-header>-->
      <app-header>
        <span @click="goVoting" slot="right">投票明细</span>
      </app-header>
      <!--<div class="h-main">
        <div class="content-box">
          <div class="top">
            <div class="avatar">
              &lt;!&ndash;<x-icon class="avatar-icon" type="ios-help" size="80"></x-icon>&ndash;&gt;
              <img :src="info.logo||'https://ww1.sinaimg.cn/large/663d3650gy1fq66vvsr72j20p00gogo2.jpg'" alt="" class="avatar-icon">
              <x-button class="user-rank" :gradients="['#FFE1A0','#F75F42']">超级</x-button>
            </div>
            <div class="text">
              <h4>{{info.name}}</h4>
              <p>{{info.peopleNumber}}人支持</p>
              <h4>{{info.voteNumber}}票</h4>
            </div>
          </div>
          <div class="bottom">
            <dl>
              <dt>简介</dt>
              <dd>{{info.desc}}</dd>
            </dl>
            <dl>
              <dt>建设方案</dt>
              <dd>{{info.scheme}}</dd>
            </dl>
          </div>
          <x-button type="warn">支持TA</x-button>
        </div>
      </div>-->
      <div class="node-details-content">
        <div class="top" :style="bgStyle">
          <img :src="nodeInfo.logo" alt="图片路径错误" class="img">
          <p class="name">{{nodeInfo.name}}</p>
          <span class="sign right-sign" v-if="!nodeInfo.isTenure">任职</span>
          <span class="sign left-sign" v-if="nodeInfo.typeName" v-html="nodeInfo.typeName">超级节点</span>
        </div>
        <div class="bottom">
          <div class="nav">
            <div>
              <h2>{{nodeInfo.voteNumber}}</h2>
              <p>票</p>
            </div>
            <div>
              <h4>{{nodeInfo.peopleNumber}}</h4>
              <p>人支持</p>
            </div>
          </div>
          <dl>
            <dt>简介</dt>
            <dd>
              {{nodeInfo.desc}}
            </dd>
          </dl>
          <dl>
            <dt>建设方案</dt>
            <dd>
              {{nodeInfo.scheme}}
            </dd>
          </dl>
        </div>
      </div>
      <div class="btn-box">
        <!--<button>支持TA</button>-->
        <router-link tag="button" :to="{path:'/home/vote',query:{nodeId:nodeInfo.id,nodeName:nodeInfo.name}}">支持TA
        </router-link>
      </div>
      <router-view></router-view>
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
        nodeInfo: {}
      }
    },
    computed: {
      bgStyle() {
        let id = this.nodeInfo.typeId
        if (!id) id = 1
        return {
          backgroundImage: "url(/static/images/personal-node/bg_" + id + ".jpg)"
        }
      }
    },
    methods: {
      goVoting() {
        this.$router.push({
          path: `/home/node/dts${this.$route.params.id}/voting`
        })
      },
      getData() {
        http.post('/node/info', {
          id: this.$route.params.id,
        }, (res) => {

          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.nodeInfo = res.content
          // console.log(this.noticeInfo)
        })
      }
    },
    created() {
      this.getData()
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .node-details
    fixed-full-screen()
    overflow auto
    background $color-background-sub
    &>.app-header
      background $color-background-sub
    .node-details-content
      margin 20px
      margin-top 60px
      background $color-background
      border-radius 20px
      overflow auto
      margin-bottom 75px
      .top
        position relative
        height 200px
        background-image url("/static/images/personal-node/bg_1.jpg")
        color white
        display flex
        justify-content center
        align-items center
        flex-direction column
        .name
          font-size $font-size-large
          margin-top 20px
        .img
          width 85px
          height 85px
          border-radius 50%
        .sign
          position absolute
          top 20px
          border-radius 10px
          font-size $font-size-small-s
          background rgba(150, 150, 150, .3)
          padding 5px 10px
        .left-sign
          left 20px
        .right-sign
          right 20px
      .bottom
        padding 30px 25px
        min-height 250px
        .nav
          text-align center
          padding-bottom 20px
          & > div
            display flex
            align-items flex-end
            justify-content center
            margin-bottom 15px
          h2
            font-size $font-size-large-x
          h4
            font-size $font-size-medium
          p
            margin-left 10px
            color $color-text-minor
        dl
          margin-bottom 45px
        dt
          margin-bottom 25px
          font-size $font-size-large
          font-weight bold
        dd
          display flex
          align-items center
          font-size $font-size-medium

    .btn-box
      position fixed
      bottom 0
      left 0
      right 0
      padding 15px 20px
      background-image: -webkit-linear-gradient(top, rgba(243, 240, 243, 0) 0%, rgba(243, 240, 243, 1) 100%);
      background-image: -moz-linear-gradient(top, rgba(243, 240, 243, 0) 0%, rgba(243, 240, 243, 1) 100%);
      background-image: -o-linear-gradient(top, rgba(243, 240, 243, 0) 0%, rgba(243, 240, 243, 1) 100%);
      background-image: linear-gradient(top, rgba(243, 240, 243, 0) 0%, rgba(243, 240, 243, 1) 100%);
      button
        display block
        width 100%
        line-height 45px
        color white
        border 0
        background $color-theme
        font-size $font-size-medium-x
        border-radius 10px
</style>

