<template>
  <slide>
    <div class="psw-field">
      <!--<x-header :left-options="{backText: ''}" class="w-header" v-show="!hiddenHeader"></x-header>-->
      <app-header v-show="!hiddenHeader"></app-header>
      <div class="psw-field-box">
        <!--<router-link to="/personal/psw/two">fwemgpowir</router-link>-->
        <h2 class="title">{{title}}</h2>
        <div class="content">
          <div class="psw-label">{{label}}</div>
          <div class="ipt-box">
            <ul class="psw-show">
              <li v-for="i in 6">
                <span class="dot" v-show="i<=password.length"></span>
              </li>
            </ul>
            <!--<input type="tel" class="psw-ipt" maxlength="6" v-model="pswIpt" :disabled="fieldDisabled">-->
          </div>
        </div>
      </div>
      <div class="pay-tool-keyboard">
        <ul>
          <li v-for="val in keys" @click="keyUpHandle(val)">
            {{ val }}
          </li>
          <li class="del" @click="delHandle"><span class="icon-del"><</span></li>
        </ul>
      </div>
      <loading :show="loadingShow" text=""></loading>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import {Loading} from 'vux'


  export default {
    name: "index",
    components: {
      slide,
      Loading,
    },
    // props: ['title', 'label','loadingShow','fieldDisabled','hiddenHeader'],
    props: {
      title: {
        type: String,
        default: '',
      },
      label: {
        type: String,
        default: '',
      },
      loadingShow: {
        type: Boolean,
        default: false,
      },
      fieldDisabled: {
        type: Boolean,
        default: false
      },
      hiddenHeader: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        password: '',
        keys: [1, 2, 3, 4, 5, 6, 7, 8, 9, '', 0],
      }
    },
    methods: {
      keyUpHandle(v) {
        if (v === '' || this.password.length === 6) return
        this.password = this.password.toString() + v.toString()
        if (this.password.length >= 6) {
          this.$emit('iptWord', this.password)
          setTimeout(() => {
            this.password = ''
          }, 500)
        }
      },
      delHandle () {
        if (this.password.length <= 0) return false
        this.password=this.password.slice(0,-1)
      },
    },
    watch: {
      /*pswIpt(v) {
        this.pswIpt = v.replace(/[^\d]/g, '')
        if (this.pswIpt.length === 6) {
          this.$emit('iptWord', v)
          setTimeout(()=>{
            this.pswIpt = ''
          },500)
        }
      }*/
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .psw-field
    fixed-full-screen()
    overflow hidden
    .psw-field-box
      position absolute
      top 50px
      bottom 0
      width 100%
      overflow hidden
      .title
        padding 30px $space-box 0 $space-box
        font-weight bold
        font-size $font-size-large
      .psw-label
        margin 60px 0
        font-size $font-size-medium-x
        text-align center
      .ipt-box
        position relative
        width 280px
        margin 0 auto
        .psw-ipt
          position absolute
          width 100%
          /*border 1px solid red*/
          top 0
          bottom 0
          opacity 0
      .psw-show
        display flex
        justify-content center
        padding 10px 0
        li
          width 33px
          line-height 33px
          height 33px
          margin 0 5px
          text-align center
          border-bottom 1px solid $color-border
          span
            display inline-block
            width 8px
            height 8px
            border-radius 50%
            background $color-text-theme


</style>

