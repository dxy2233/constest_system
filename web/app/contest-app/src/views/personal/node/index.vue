<template>
  <slide>
    <div class="node-index-wrapper">
      <div class="node-index">
        <app-header>
          我的节点
          <router-link tag="span" to="/personal/node/index/edit" slot="right">编辑</router-link>
        </app-header>
        <div class="node-details-content">
          <div class="top" :style="bgStyle">
            <div class="img">
              <img :src="myNodeInfo.logo||'/static/images/node-avatar-default.jpg'" alt="" class="">
            </div>
            <p class="name">{{myNodeInfo.name}}</p>
            <span class="sign right-sign" v-if="myNodeInfo.isTenure">任职</span>
            <span class="sign left-sign" v-if="myNodeInfo.typeName">{{myNodeInfo.typeName}}</span>
          </div>
          <ul class="center">
            <router-link tag="li" :to="'/home/node/dts'+myNodeInfo.id+'/voting'">
              <img src="/static/images/my-node0.png" alt="">
              <h3>投票明细</h3>
            </router-link>
            <router-link tag="li" to="/personal/node/index/interests">
              <img src="/static/images/my-node1.png" alt="">
              <h3>当前权益</h3>
            </router-link>
          </ul>
          <div class="bottom">
            <div class="nav">
              <div>
                <h2>{{myNodeInfo.voteNumber}}</h2>
                <p>票</p>
              </div>
              <div>
                <h4>{{myNodeInfo.peopleNumber}}</h4>
                <p>人支持</p>
              </div>
            </div>
            <dl>
              <dt>简介</dt>
              <dd v-html="replaceStr(myNodeInfo.desc)"></dd>
            </dl>
            <dl>
              <dt>建设方案</dt>
              <dd v-html="replaceStr(myNodeInfo.scheme)"></dd>
            </dl>
          </div>
        </div>
        <div class="btn-box">
          <router-link v-if="myNodeInfo.typeId===1||myNodeInfo.typeId===2" tag="button" to='/personal/node/index/invite'>拉票
          </router-link>
        </div>
      </div>
      <router-view></router-view>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import {mapState, mapMutations, mapGetters} from 'vuex'

  export default {
    name: "index",
    components: {
      slide
    },
    data() {
      return {
        replaceStr:function (str) {
          if (!str) return ''
          return str.replace(/\r\n/g, '<br/>').replace(/\n/g, '<br/>').replace(/\s/g, '&nbsp')
        }
      }
    },
    computed: {
      bgStyle() {
        let id = this.myNodeInfo.typeId
        if (!id) id = 1
        return {
          backgroundImage: "url(/static/images/personal-node/bg_" + id + ".jpg)"
        }
      },
      ...mapGetters([
        'myNodeInfo'
      ]),
    },
    methods: {
      goVoting() {
        this.$router.push({
          path: `/home/node/dts${this.$route.params.id}/voting`
        })
      },
    },
    created() {
    },

  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .node-index-wrapper
    fixed-full-screen()

  .node-index
    fixed-full-screen()
    background $color-background-sub
    overflow auto
    .app-header
      background $color-background-sub !important
    .node-details-content
      margin 20px
      margin-top 60px
      background $color-background
      border-radius 20px
      overflow hidden
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
          overflow hidden
          img
            width 100%
            height 100%
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
      .center
        overflow hidden
        border-bottom 1px solid $color-border
        li
          width 50%
          float left
          box-sizing border-box
          border-left 1px solid $color-border
          margin-left -1px
          display flex
          align-items center
          justify-content center
          height 55px
          font-size $font-size-medium-x
          img
            margin-right 20px
            width 26px
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
          font-size $font-size-small
          line-height 1.5em
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
        line-height 42px
        color white
        border 0
        background $color-theme
        font-size $font-size-medium-x
        border-radius 10px

</style>
