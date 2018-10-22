<template>
  <slide>
    <div class="about">
      <!--<x-header :left-options="{backText: ''}" class="w-header">
        关于
      </x-header>-->
      <app-header>
        关于
      </app-header>
      <div class="h-main">
        <div class="about-content">
          <img src="/static/images/gst-logo.png" alt="">
          <p class="gray">v1.0.0.0</p>
          <ul class="contact">
            <li>
              <div class="">联系邮箱</div>
              <div class="gray">4832184@163.com</div>
            </li>
            <li>
              <div class="">客服电话</div>
              <div class="gray">400-096-0909</div>
            </li>
          </ul>
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
  .about
    fixed-full-screen()
    .about-content
      padding 45px 40px
      display flex
      align-items center
      flex-direction column
      img
        margin-bottom 15px
        width 80px
      ul
        margin-top 80px
        border-top 1px solid $color-border
        width 100%
        li
          padding 20px 0
          display flex
          justify-content space-between
          align-items center
          border-bottom 1px solid $color-border
      .gray
        color $color-text-minor
</style>
