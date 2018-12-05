<template>
  <ul class="rank-list">
    <router-link tag="li" v-for="(item,index) in list"
                 :to="'/home/node/dts'+item.id" :key="item.id+item.name+index">
      <div class="rank">
        <img v-if="index<3" :src="'/static/images/rank_'+(++index)+'.png'" alt="">
        <span v-else>{{++index}}</span>
      </div>
      <div class="content">
        <div class="left">
          <img :src="item.logo||'/static/images/node-avatar-default.jpg'" alt="" class="avatar">
        </div>
        <div class="right">
          <h4>{{item.name}}</h4>
          <h6>
            <span>{{item.peopleNumber+'人支持'}}</span>
            <span>{{item.voteNumber+'票'}}</span>
          </h6>
        </div>
      </div>
      <div class="tenure" v-if="item.isTenure">
        <span>任职</span>
      </div>
    </router-link>
  </ul>
</template>

<script>
  export default {
    name: "index",
    props: {
      list: {
        type: Array,
        default: []
      }
    },
    data() {
      return {
        replaceImg: 'https://ww1.sinaimg.cn/large/663d3650gy1fq66vvsr72j20p00gogo2.jpg',
      }
    },
    watch: {},
    methods: {
      clickItem(id) {
        this.$emit('selectItem', id)
      }
    }
  }
</script>

<style scoped lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .rank-list
    min-height 30px
    li
      position relative
      display flex
      align-items center
      overflow hidden
      width 100%
      .tenure
        position absolute
        right -30px
        top -30px
        background $color-theme
        color #fff
        width 60px
        height 60px
        text-align center
        font-size $font-size-small-s
        transform-compatible(rotate(45deg))
        span
          position relative
          top 43px
      .rank
        padding-left $space-box
        padding-right 10px
        /*width 30px*/
        img
          width 20px
        span
          text-align center
          padding 3px
          display inline-block
          min-width 10px
          font-size $font-size-medium
          &.sign
            background-color #FFB24E
      .content
        width calc(100% - 45px)
        padding $space-box
        padding-left 0
        border-bottom 1px solid $color-border
        box-sizing border-box
        h4
          font-size $font-size-small
          color $color-text-minor
          width 100%
          no-wrap()
        h6
          display flex
          justify-content space-between
          margin-top 10px
          font-size $font-size-small-s
        .left
          float left
        .right
          margin-left 50px
        .avatar
          width 40px
          height 40px
          margin-right 10px
          margin-top 6px
          border-radius 50%

</style>
