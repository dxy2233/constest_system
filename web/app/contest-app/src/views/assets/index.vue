<template>
  <div class="assets">
    <div class="assets-wrapper">
      <div class="assets-content">
        <dl class="currency">
          <dt>资产种类</dt>
          <router-link tag="dd" v-for="item in currencyList" :key="item.code" :to="'/assets/dts'+item.id">
            <div class="left">
              <img :src="'/static/images/assets-icon/'+item.code+'.jpg'" alt="" class="icon">
              <span>{{item.name}}</span>
            </div>
            <div class="right">{{item.positionAmount}}</div>
          </router-link>
        </dl>
        <load-more tip="正在加载" v-if="listShow"></load-more>
      </div>
    </div>
    <router-view></router-view>
  </div>
</template>

<script>
  import http from 'js/http'
  import {Loading} from 'vux'
  import {mapState, mapMutations, mapGetters} from 'vuex'

  export default {
    name: "index",
    components: {
      Loading,
    },
    data() {
      return {
        currencyList: [],
        listShow: true
      }
    },
    methods: {
      openCollect() {
        this.$router.push({name: 'collect', params: {address: this.walletData.address}})
      },
      getCurrencyList() {
        http.post('/wallet/currency', {}, (res) => {
          this.listShow = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.currencyList = res.content
          // console.log(this.currencyList)
          sessionStorage.setItem("currencyList", JSON.stringify(this.currencyList));
        })
      },
      pageInt() {
        if (!this.loginMsg) {
          this.$router.push({
            path: `/login`
          })
          return
        }
        this.currencyList = []
        this.listShow = true
        this.getCurrencyList()
        /*if (!this.currencyList.length){
          this.getCurrencyList()
        }*/

      }
    },
    created() {
      // this.pageInt()
    },
    activated() {
      this.pageInt()
    },
    computed: {
      ...mapGetters([
        "loginMsg",
      ]),
    },
    watch: {
      '$route': function (t, f) {
        if (t.path === '/assets') {
          this.pageInt()
        }
      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .assets
    position: absolute
    width: 100%
    top: 0
    bottom: 50px
    overflow hidden
    .assets-wrapper
      padding 20px $space-box
      .title
        display flex
        justify-content space-between
        align-items center
        font-size $font-size-large-x
        padding-bottom 30px
        img
          width 48px
      .currency
        dt
          font-size $font-size-large
          padding 15px 0
          margin-bottom 30px
          font-weight bold
        dd
          display flex
          justify-content space-between
          font-size $font-size-medium
          align-items center
          padding 25px 13px
          box-shadow 0 0 12px 2px rgba(90, 90, 90, 0.18)
          margin-bottom 20px
          border-radius 5px
          .left
            display flex
            align-items center
          .icon
            width 45px
            height 45px
            border-radius 50%
            margin-right 20px
          .right
            color $color-text-minor
</style>
