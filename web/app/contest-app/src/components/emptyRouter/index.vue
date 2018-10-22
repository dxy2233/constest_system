<template>
  <slide>
    <router-view></router-view>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  export default {
    name: "index",
    components: {
      slide,
    },
  }
</script>
<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
/*  .empty-router
    fixed-full-screen()*/

</style>
