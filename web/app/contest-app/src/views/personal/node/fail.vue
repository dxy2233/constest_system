<template>
  <slide>
    <div class="node-fail">
      <app-header>
        {{headerStr}}
      </app-header>
      <div class="h-main">
        <div class="top">
          <img src="/static/images/state-fail.png" alt="" class="icon">
          <span>申请失败</span>
        </div>
        <div class="bottom">
          <p>{{detailsStr}}</p>
          <p>{{myNodeInfo.statusRemark}}</p>
          <div class="again">
            <router-link :to="againPath">
              <x-button type="warn" class="again-btn">重新申请</x-button>
            </router-link>
          </div>
        </div>
      </div>
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
    data(){
      return {
        headerStr: '',
        detailsStr: '',
        againPath:''
      }
    },
    created(){
      let isUpgrade = this.$route.path.includes('node/index')
      if (isUpgrade) {
        this.headerStr = '节点升级'
        this.detailsStr = '节点申请失败原因：'
        this.againPath = '/personal/node/index/upgrade'
      } else {
        this.headerStr = '我的节点'
        this.detailsStr = '节点升级失败原因：'
        this.againPath = '/personal/applynode/submit'
      }
    },
    computed: {
      ...mapGetters([
        'myNodeInfo'
      ]),
    },
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .node-fail
    fixed-full-screen()
    overflow auto
    .top
      padding 55px 0
      display flex
      flex-direction column
      align-items center
      border-bottom 1px solid $color-border
      img
        width 96px
      span
        margin-top 20px
        font-size 16px
    .bottom
      padding 20px 30px
      &>p
        margin-bottom 25px
      .again
        padding-top 50px
        padding-bottom 20px
      .again-btn
        line-height 45px
        width 180px
        font-size $font-size-small


</style>
