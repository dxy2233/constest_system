<template>
  <slide>
    <div class="interests">
      <!--<x-header :left-options="{backText: ''}">
        当前权益
      </x-header>-->
      <app-header>
        当前权益
      </app-header>
      <div class="interests-content">
        <div class="top" :style="bgStyle">
          <img :src="'/static/images/personal-node/icon_'+nodeInfo.typeId+'.png'" alt="" class="img">
          <p class="name">{{nodeInfo.name}}</p>
          <span class="sign" v-if="nodeInfo.isTenure">任职</span>
        </div>
        <div class="bottom">
          <dl>
            <dt>节点权益</dt>
            <dd v-for="item in interestsList">
              <div class="spot"></div>
              <div class="text">
                <h3>{{item.name}}</h3>
                <p>{{item.content}}</p>
              </div>
            </dd>
          </dl>
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
    components: {
      slide
    },
    data() {
      return {
        nodeInfo: {},
        interestsList: []
      }
    },
    created() {
      this.nodeInfo = JSON.parse(sessionStorage.getItem('myNodeInfo'))
      this.getInterests()
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
      getInterests() {
        http.post('/user/node-rule-info', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.interestsList = res.content.rules
        })
      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .interests
    fixed-full-screen()
    overflow auto
    background $color-background-sub
    & > .vux-header
      background none
      border none
    .interests-content
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
          width 90px
        .sign
          position absolute
          top 20px
          right 20px
          border-radius 10px
          font-size $font-size-small-s
          background rgba(150, 150, 150, .3)
          padding 5px 10px
      .bottom
        padding 30px 25px
        min-height 250px
        dt
          margin-bottom 30px
          font-size 26px
        dd
          display flex
          align-items center
          margin-bottom 25px
          .spot
            width 8px
            height 8px
            margin-right 15px
            background #dfdde0
            border-radius 50%
          h3
            font-size $font-size-medium-x
          p
            margin-top 5px
            color $color-text-minor


</style>
