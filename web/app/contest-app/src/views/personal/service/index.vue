<template>
  <slide>
    <div class="service">
      <app-header>
        在线客服
      </app-header>
      <iframe id="service-content" src="https://im.7x24cc.com/phone_webChat.html?accountId=N000000010737&chatId=adf12f09-7c82-4519-8042-934394dc2651" frameborder="0"></iframe>
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
  .service
    fixed-full-screen()
    overflow auto
    #service-content
      margin-top 50px
      width 100%
      height calc(100% - 50px)

</style>
