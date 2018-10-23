<template>
  <ul class="rank-list">
    <router-link tag="li" v-for="(item,index) in list"
                 :to="'/home/node/dts'+item.id" :key="item.id+item.name+index">
      <div class="rank">
        <span :class="{'sign':index<3}">{{++index}}</span>
      </div>
      <div class="content">
        <div class="left">
          <!--<div class="avatar">
            <img src="" alt="">
          </div>-->
          <img :src="item.logo" alt="" class="avatar">
          <div class="text">
            <h4>{{item.name}}</h4>
            <h6>{{item.countNumber}}人支持</h6>
          </div>
        </div>
        <div class="right">
          {{item.voteNumber}}票
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
        replaceImg:'https://ww1.sinaimg.cn/large/663d3650gy1fq66vvsr72j20p00gogo2.jpg'
      }
    },
    watch: {

    },
    methods:{
      clickItem(id){
        this.$emit('selectItem', id)
      }
    }
  }
</script>

<style scoped lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .rank-list
    padding $space-box
    li
      position relative
      display flex
      align-items center
      padding 17px 15px
      box-shadow 0 0 20px 2px rgba(90, 90, 90, 0.18)
      border-radius 10px
      margin-bottom 30px
      overflow hidden
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
        flex 0 0 30px
        span
          text-align center
          background-color #DBDBDB
          padding 3px
          display inline-block
          min-width 10px
          color #fff
          font-size $font-size-small-s
          &.sign
            background-color #FFB24E
      .content
        flex 1
        display flex
        justify-content space-between
        align-items flex-end
        .left
          display flex
          align-items center
          h4
            font-size $font-size-small
            font-weight bold
          h6
            margin-top 10px
            font-size $font-size-small-s
            color #B4B5BC
        .avatar
          width 40px
          height 40px
          margin-right 10px
          /*border 1px solid #AFAFAF*/
          border-radius 50%

</style>
