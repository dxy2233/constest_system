<template>
  <div id="app">
    <!--<keep-alive>
      <router-view v-if="$route.meta.keepAlive"></router-view>
    </keep-alive>
    <router-view v-if="!$route.meta.keepAlive"></router-view>-->
    <!--<router-view></router-view>-->
    <keep-alive>
      <router-view></router-view>
    </keep-alive>
    <app-footer></app-footer>
  </div>
</template>

<script>
  import {Toast} from 'vux'
  import appFooter from 'components/appFooter'
  import {mapState, mapMutations, mapGetters} from 'vuex'
  import http from 'js/http'

  export default {
    components: {
      Toast,
      appFooter
    },
    methods:{

    },
    name: 'app',
    watch: {
      '$route': function (router) {
        if (this.loginMsg&&!parseInt(this.payPsw)){
          this.$router.push({
            path:'/setpsw'
          })
        }
      }
    },
    computed: {
      ...mapGetters([
        "loginMsg",
        "identifyMsg",
        "payPsw"
      ]),
    },
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  #app
    width 100%
    height 100%
    position fixed
/*
  .vux-header
    background red
*/

</style>
