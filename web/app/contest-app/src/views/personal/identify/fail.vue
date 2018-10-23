<template>
  <slide>
    <div class="identify-fail">
      <!--<x-header :left-options="{backText: ''}">实名认证
      </x-header>-->
      <app-header>
        实名认证
      </app-header>
      <div class="h-main">
        <div class="top">
          <img src="/static/images/state-fail.png" alt="" class="icon">
          <span>认证失败</span>
        </div>
        <div class="bottom">
          <p>节点申请失败原因：</p>
          <p v-html="identifyMsg.remark"></p>
          <div class="again">
            <router-link to="/personal/identify/submit">
              <x-button type="warn" class="again-btn">重新认证</x-button>
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
  .identify-fail
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
