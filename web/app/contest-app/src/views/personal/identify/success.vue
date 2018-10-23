<template>
  <slide>
    <div class="identify-success">
      <app-header>
        实名认证
      </app-header>
      <div class="h-main">
        <div class="top">
          <img src="/static/images/default-avatar.png" alt="" class="icon">
          <!--<span>认证成功</span>-->
        </div>
        <div class="bottom">
          <p>
            <span>姓名</span><span v-html="identifyMsg.realname"></span>
          </p>
          <p>
            <span>身份证</span><span v-html="identifyMsg.number"></span>
          </p>
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
    computed: {
      ...mapGetters([
        "identifyMsg"
      ]),
    },
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .identify-success
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
        display flex
        justify-content space-between
        margin-bottom 25px


</style>
