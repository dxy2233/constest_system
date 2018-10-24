<template>
  <slide>
    <div class="assets-collect">
      <!--<x-header class="w-header">
        <x-icon slot="overwrite-left" type="ios-close-empty" size="35"  @click.native="backPath"></x-icon>
      </x-header>-->
      <app-header>
      </app-header>
      <div class="h-main wrapper">
        <div class="title">
          <h3>收款地址</h3>
          <h5>{{$route.query.name}}</h5>
        </div>
        <div class="qrcode-box">
          <qrcode :value="address" type="img" :size="230"></qrcode>
          <p>{{address}}</p>
        </div>
        <x-button type="warn" id="copy" class="copy-btn" @click.native="" :data-clipboard-text="address">复制钱包地址</x-button>
      </div>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import { Qrcode } from 'vux'
  import Clipboard from 'clipboard';

  export default {
    name: "index",
    components: {
      slide,
      Qrcode
    },
    data() {
      return {
        address: ''
      }
    },
    created() {
      // this.address = this.$route.query.address
      this.getCollectAddress()
    },
    mounted(){
      const clipboard = new Clipboard('#copy');

      clipboard.on('success', (e) => {
        this.$vux.toast.show('复制成功')
      });

      clipboard.on('error', (e) => {
        this.$vux.toast.show('复制失败，你可以选择手动复制')
      });
    },
    methods:{
      backPath(){
        this.$router.back()
      },
      getCollectAddress(){
        http.post('/wallet/recharge-address', {
          id: this.$route.params.id,
        }, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.address = res.content.address
        })
      }
    },
    computed:{
      qrcodeData(){

      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .assets-collect
    fixed-full-screen()
    overflow auto
    &>.wrapper
      padding-left $space-box
      padding-right $space-box
    .title
      padding 35px 0
      h3
        font-size 28px
      h5
        margin-top 12px
        font-size $font-size-medium-x
    .qrcode-box
      text-align center
      p
        margin-top 15px
        font-size $font-size-small-s
        color #939BA4
    .copy-btn
      margin 40px 0


</style>
