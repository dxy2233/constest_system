<template>
  <slide>
    <div class="personal-set">
      <!--<x-header :left-options="{backText: ''}" class="">
        设置
      </x-header>-->
      <app-header>
        设置
      </app-header>
      <div class="h-main">
        <!--<group>
          <cell title="当前账号" :value="hidMobile(loginMsg?loginMsg.mobile:'')"></cell>
          <cell title="关于" is-link link="/personal/set/about"></cell>
        </group>-->
        <ul class="link-list">
          <li>
            <span class="text">当前账号</span>
            <span class="right-text">{{hidMobile(loginMsg?loginMsg.mobile:'')}}</span>
          </li>
          <router-link tag="li" to="/personal/set/about">
            <span class="text">关于</span>
            <x-icon type="ios-arrow-right"></x-icon>
          </router-link>
        </ul>

        <div class="btn-box">
          <x-button type="warn" class="login-out" @click.native="loginOut" :show-loading="btnLoading">退出登录</x-button>
        </div>

      </div>
      <router-view></router-view>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import {Group, Cell, CellBox} from 'vux'
  import {mapState, mapMutations, mapGetters} from 'vuex'
  import {hidMobile, cancelLogin} from 'js/mixin'

  export default {
    name: "index",
    components: {
      slide,
      Group,
      Cell,
      CellBox
    },
    data() {
      return {
        hidMobile: hidMobile,
        btnLoading: false,
      }
    },
    methods: {
      loginOut() {
        this.btnLoading = true
        http.post('/login/logout', {}, (res) => {
          this.btnLoading = false
          this.$vux.toast.show(res.msg)
          if (res.code === 0) {
            cancelLogin()
            this.$router.replace({
              path: '/home',
            })
          }

        })
      }
    },
    computed: {
      ...mapGetters([
        "loginMsg",
        "identifyMsg"
      ]),
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .personal-set
    fixed-full-screen()
    .weui-cells
      margin 0
      .weui-cell
        padding 15px $space-box
    .btn-box
      position absolute
      left $space-box
      right $space-box
      bottom 50px
    .link-list
      margin-top 0
      .right-text
        color $color-text-minor
</style>
