<template>
  <slide>
    <div class="apply-node">
      <app-header>
        申请成为节点
        <router-link tag="span" to="/personal/applynode/rules" slot="right">规则说明</router-link>
      </app-header>
      <div class="h-main">
        <img src="/static/images/submit-node-img.png" alt="" class="info-img">
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
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .apply-node
    fixed-full-screen()
    overflow hidden
    .h-main
      width 100%
      height 100%
      overflow auto
    .info-img
      width 100%

</style>
