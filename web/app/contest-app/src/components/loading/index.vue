<template>
  <div class="loading">
    <p style="text-align:center;">
      <inline-loading></inline-loading>
      <span style="vertical-align:middle;display:inline-block;font-size:14px;">&nbsp;&nbsp;加载中</span>
    </p>
  </div>
</template>

<script>
  import {InlineLoading} from 'vux'

  export default {
    name: "index",
    props:{
      show:{
        type:Boolean,
        default:false
      }
    },
    components: {
      InlineLoading
    }
  }
</script>

<style scoped>

</style>
