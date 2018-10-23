<template>
  <slide>
    <div class="reward-rules">
      <!--<x-header :left-options="{backText: ''}">奖励规则</x-header>-->
      <app-header>
        奖励规则
      </app-header>
      <div class="h-main">
        <div class="content">规则规则。</div>
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
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .reward-rules
    fixed-full-screen()
    overflow auto
    .content
      height 1000px
      padding 20px $space-box
      font-size $font-size-small
      line-height 18px

</style>
