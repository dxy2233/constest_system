<template>
  <div class="load-more">
    <inline-loading></inline-loading><span style="vertical-align:middle;display:inline-block;font-size:14px;">&nbsp;&nbsp;加载中</span>
  </div>
</template>

<script>
  import { InlineLoading } from 'vux'
  export default {
    name: "index",
    components: {
      InlineLoading
    }
  }
</script>

<style scoped lang="stylus" rel="stylesheet/stylus">
  .load-more
    text-align center


</style>
