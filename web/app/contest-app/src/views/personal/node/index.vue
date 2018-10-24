<template>
  <slide>
    <div class="node-details">
      <app-header>
        我的节点
        <router-link tag="span" to="/personal/node/interests" slot="right">当前权益</router-link>
      </app-header>
      <div class="node-details-content">
        <div class="top" :style="bgStyle">
          <img :src="nodeInfo.logo" alt="" class="avatar-icon img">
          <p class="name">{{nodeInfo.name}}</p>
          <span class="sign right-sign" v-if="nodeInfo.isTenure">任职</span>
          <span class="sign left-sign" v-if="nodeInfo.typeName">{{nodeInfo.typeName}}</span>
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
      <router-view></router-view>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'

  export default {
    name: "index",
    components:{
      slide
    },
    data(){
      return{
        nodeInfo:{}
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
    methods:{
      goVoting(){
        this.$router.push({
          path: `/home/node/dts${this.$route.params.id}/voting`
        })
      },
    },
    created(){
      this.nodeInfo = JSON.parse(sessionStorage.getItem('myNodeInfo'))
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
    .app-header
      background $color-background-sub !important
    .node-details-content
      margin 20px
      margin-top 60px
      background $color-background
      border-radius 20px
      overflow hidden
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
          &>div
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


</style>
