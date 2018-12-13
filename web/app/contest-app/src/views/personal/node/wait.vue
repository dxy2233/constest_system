<template>
  <slide>
    <div class="node-wait">
      <app-header>
        {{headerStr}}
      </app-header>
      <div class="h-main ">
        <div class="node-wait-content">
          <img src="/static/images/state-wait.png" alt="" class="icon">
          <span>{{detailsStr}}</span>
          <!--<x-button type="warn" class="revoke-btn">撤回申请</x-button>-->
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
        headerStr: '',
        detailsStr: ''
      }
    },
    computed: {
    },
    created() {
      let isUpgrade = this.$route.path.includes('node/index')
      if (isUpgrade) {
        this.headerStr = '节点升级'
        this.detailsStr = '您的节点升级信息已提交，等待审核中... ...'
      } else {
        this.headerStr = '我的节点'
        this.detailsStr = '您的节点信息已提交，等待审核中... ...'
      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .node-wait
    fixed-full-screen()
    overflow auto
    .node-wait-content
      display flex
      flex-direction column
      align-items center
      padding-top 85px
      img
        width 115px
      span
        margin-top 25px
        font-size $font-size-small-s
      .revoke-btn
        margin-top 70px
        line-height 45px
        width 180px
        font-size $font-size-small


</style>
