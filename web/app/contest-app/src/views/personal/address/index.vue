<template>
  <slide>
    <div class="receive-address">
      <load-more tip="正在加载" v-show="loading" class="load-box"></load-more>
      <div v-if="!loading">
        <div v-if="addressList.length" class="edit">
          <app-header>
            收获地址
            <router-link tag="span" slot="right" to="/personal/address/edit1">编辑</router-link>
          </app-header>
          <div class="h-main">
            <ul class="address-list">
              <li v-for="item in addressList">
                <p>
                  {{item.consignee}}
                  <span>{{item.consigneeMobile}}</span>
                </p>
                <h4>{{item.address}}</h4>
              </li>
            </ul>
          </div>
        </div>
        <div v-else class="add">
          <app-header>
            收获地址
          </app-header>
          <div class="h-main">
            <div class="add-content">
              <img src="/static/images/add-address.png" alt="">
              <!--<button class="base-btn">添加收获地址</button>-->
              <router-link tag="button" class="base-btn" to="/personal/address/edit0">添加收获地址</router-link>
            </div>
          </div>
        </div>
      </div>
      <router-view></router-view>
      <!--<submit-temp v-if="submitShow" @close="submitShow=false"></submit-temp>-->
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import submitTemp from './submit'

  export default {
    name: "index",
    components: {
      slide,
      submitTemp
    },
    data() {
      return {
        loading: true,
        submitShow: false,
        addressList:[]
      }
    },
    methods:{
      getAddressList(){
        http.post('/user/address-list',{},(res)=>{
          if (res.code!==0){
            this.loading = false
            this.$vux.toast.show(res.msg)
            return
          }
          this.addressList = res.content.list
          this.loading = false
        })
      }
    },
    created(){
      this.getAddressList()
    },
    activated() {
    },
    watch: {
      '$route': function (t, f) {
        if (t.path === '/personal/address') {
          this.getAddressList()
        }
      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .receive-address
    fixed-full-screen()
    .load-box
      margin-top 100px
    .edit
      .app-header
        border-bottom 1px solid $color-border
        background $color-background-sub
      .address-list
        li
          margin-left $space-box
          padding $space-box
          padding-left 0
          border-bottom 1px solid $color-border
          font-size $font-size-small
          p
            color $color-text-minor
            span
              margin-left 30px
          h4
            margin-top 5px
            font-weight bold
    .add
      .add-content
        display flex
        flex-direction column
        align-items center
        img
          margin 85px 0
          width 154px
        button
          width 290px

</style>
