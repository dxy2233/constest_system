<template>
  <ul class="app-footer">
    <router-link class="footer-item" v-for="(item,idx) in tabList" :class="{'act':currentPath===item.path}"
                 :to="item.path" tag="li" :key="item.path">
      <!--<icon type="circle" class="icon"></icon>-->
      <span class="icon" :class="item.icon"></span>
      <p>{{item.name}}</p>
    </router-link>
  </ul>
</template>

<script>
  import {Tabbar, TabbarItem, Group, Cell} from 'vux'
  import {mainRouter} from '@/router/index'

  export default {
    name: "index",
    components: {
      Tabbar,
      TabbarItem,
      Group,
      Cell
    },
    data() {
      return {
        tabList: mainRouter,
        currentPath: ''
      }
    },
    methods: {
      obtainPath(path) {
        let routerPathArr = path.split('/')
        this.currentPath = `/${routerPathArr[1]}`
      }
    },
    created() {
      this.obtainPath(this.$route.path)
    },
    watch: {
      $route(v) {
        this.obtainPath(v.path)
      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .app-footer
    position absolute
    bottom 0
    /*bottom 0
    !*z-index 300*!*/
    width 100%
    display flex
    justify-content space-around
    background-color #FBFBFB
    border-top 1px solid $color-border
    box-sizing border-box
    height 50px
    align-items center
    font-size $font-size-small-s
    color #B4B5BC
    .footer-item
      display flex
      flex-direction column
      align-items center
      .icon
        font-size 22px
    .act
      color $color-theme
</style>
