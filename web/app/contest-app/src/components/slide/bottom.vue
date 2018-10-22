<template>
  <transition name="slide">
    <slot></slot>
  </transition>
</template>

<script>
  export default {
    name: "index"
  }
</script>

<style scoped lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"

  .slide-enter-active, .slide-leave-active
    transition: all 0.3s

  .slide-enter, .slide-leave-to
    transform: translate3d(0, 100%, 0)

</style>
