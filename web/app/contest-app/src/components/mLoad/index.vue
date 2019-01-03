<template>
  <div class="load-more">
    <div v-infinite-scroll="loadMore"
         infinite-scroll-disabled="scrollDisabled"
         infinite-scroll-distance="10">
      <slot></slot>
    </div>
    <!--显示加载中-->
    <div class="loading-box" v-if="isLoading">
      <mt-spinner type="fading-circle" :size="20" class="loading-more"></mt-spinner>
      <span class="loading-more-txt">加载中...</span>
    </div>
    <div class="no-more loading-box" v-if="noMore">
      <p v-if="!err">没有更多了~</p>
      <p v-else>出错了~</p>
    </div>
  </div>
</template>

<script>
  export default {
    name: "index",
    data() {
      return {
        isLoading: false, // 加载中转菊花
        noMore: false, // 是否还有更多
        err:false
      }
    },
    methods: {
      loadMore() {
        this.isMoreLoading = true // 设置加载更多中
        this.isLoading = true // 加载中
        this.$emit('loadMore')
      },
    },
    computed: {
      scrollDisabled() {
        if (this.noMore) {
          return true
        } else {
          return this.isLoading
        }
      }
    },
    mounted() {
      this.$on('finishInfinite', (val) => {
        // console.log(val)
        this.isLoading = false
        if (val) this.noMore = val
      });
      this.$on('errInfinite', () => {
        this.isLoading = false
        this.noMore = true
        this.err = true
      });
    },
  }
</script>

<style scoped lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .loading-box
    padding 5px 0
    display flex
    justify-content center
    align-items center
    span
      /*color $color-theme*/
      margin-left 10px
  .no-more
    color $color-text-sub

</style>
