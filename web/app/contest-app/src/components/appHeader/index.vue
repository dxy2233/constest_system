<template>
  <div class="app-header">
    <div class="center">
      <slot></slot>
    </div>
    <div class="left">
      <slot name="left">
        <span class="icon-back" @click="backPage"></span>
      </slot>
    </div>
    <div class="right">
      <slot name="right"></slot>
    </div>
  </div>
  <!--<div class="header">
    <div>
      <slot>我小标题</slot>
    </div>
    &lt;!&ndash;<div class="test-two">
      <slot name="two"></slot>
    </div>
    <div class="test-one">
      <slot name="one"></slot>
    </div>&ndash;&gt;
  </div>-->
</template>

<script>
  export default {
    name: "index",
    data() {
      return {}
    },
    methods: {
      backPage() {
        // console.log(window.history)
        // console.log(document.referrer)
        // console.log(window.history.length)
        this.$router.back()
      }
    },

  }
</script>

<style scoped lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"

  .app-header
    padding 0 $space-box
    display flex
    justify-content space-between
    height 50px
    line-height 50px
    box-sizing border-box
    align-items center
    position fixed
    top 0
    left 0
    right 0
    z-index 5
    background $color-background
    .left
      float left
    .icon-back
      font-size $font-size-large
    .center
      position absolute
      text-align center
      top 0
      left 0
      right 0
      bottom 0
      font-size $font-size-medium
      z-index -1
    .right
      float right
      font-size $font-size-small

</style>
