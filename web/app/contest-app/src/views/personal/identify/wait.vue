<template>
  <slide>
    <div class="identify-wait">
      <app-header>
        实名认证
      </app-header>
      <div class="h-main ">
        <div class="identify-wait-content">
          <img src="/static/images/state-wait.png" alt="" class="icon">
          <span>您的实名认证信息已提交，等待审核中... ...</span>
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
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .identify-wait
    fixed-full-screen()
    overflow auto
    .identify-wait-content
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
