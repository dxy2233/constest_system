<template>
  <slide>
    <div class="psw-field">
      <x-header :left-options="{backText: ''}" class="w-header">
        <div slot="overwrite-left" class="icon-back header-left-icon" @click="closeTem"></div>
      </x-header>
      <div class="psw-field-box">
        <div class="content">
          <div class="psw-label">验证支付密码</div>
          <div class="ipt-box">
            <ul class="psw-show">
              <li v-for="i in 6">
                <span class="dot" v-show="i<=pswIpt.length"></span>
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
  import { Loading} from 'vux'


  export default {
    name: "index",
    components: {
      slide,
      Loading,
    },
    data() {
      return {
        pswIpt: '',
        loadingShow:false,
        fieldDisabled:false,
        keys: [1, 2, 3, 4, 5, 6, 7, 8, 9, '', 0],
      }
    },
    computed:{

    },
    watch: {
      /*pswIpt(v) {
        this.pswIpt = v.replace(/[^\d]/g, '')
        if (this.pswIpt.length === 6) {
          this.validPsw()
        }
      }*/
    },
    methods:{
      keyUpHandle(v) {
        if (v === '' || this.pswIpt.length === 6) return
        this.pswIpt = this.pswIpt + v
        if (this.pswIpt.length >= 6) {
          this.validPsw()
        }
      },
      delHandle () {
        if (this.pswIpt.length <= 0) return false
        this.pswIpt=this.pswIpt.slice(0,-1)
      },
      closeTem(){
        this.$emit('close', true)
      },
      validPsw(){
        this.fieldDisabled = true
        this.loadingShow = true
        http.post('/pay/validate-pass', {
          pass: this.pswIpt
        }, (res) => {
          this.fieldDisabled = false
          this.loadingShow = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          if (!res.content){
            this.$vux.toast.show('校验失败')
            this.pswIpt = ''
            return
          }
          this.$emit('validSuccess', this.pswIpt)
        })
      }
    }

  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .psw-field
    fixed-full-screen()
    overflow hidden
    .m-header
      padding 0 $space-box
      line-height 50px
      height 50px
      span
        font-size 18px
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
          left 0
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
