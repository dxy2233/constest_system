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
          <h3>{{title}}</h3>
          <h5>{{littleTitle}}</h5>
        </div>
        <div class="qrcode-box">
          <qrcode :value="address" type="img" :size="230"></qrcode>
          <p>{{address}}</p>
        </div>
        <ul class="tip" v-if="isCollect">
          <li>• 请勿向上述地址转入非{{littleTitle}}积分，否则积分将不可找回</li>
          <li>• 最小转入金额：0.01{{littleTitle}}，小于最小金额的充值将不会上账且无法退回。</li>
          <li>• 请务必确认浏览器安全，防止信息被篡改或泄露。</li>
        </ul>
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
        address: '',
        title:'',
        littleTitle:'',
        isCollect:false
      }
    },
    created() {
      this.isCollect = this.$route.path.includes('collect')
      if (this.isCollect){//资产转入
        this.getCollectAddress()
        this.title = '转入地址'
        this.littleTitle = this.$route.query.name
      }else {//申请节点地址
        if (this.$route.query.name==='GRT'){//grt
          this.title = '官方GRT收款地址'
          this.littleTitle = '适用于GRT/BPT'
          this.address = 'jKmCLKm3Qw21WGZoBjxShiB5wRjSrVKT4m'
        }else {//tt
          this.title = '官方TT收款地址'
          this.littleTitle = '适用于TT'
          this.address = '0xd42622bDCe756B3FEe61a4fDC2adE7ada8A1FD62'
        }

      }
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
      margin-top 40px
      margin-bottom 10px
    .tip
      padding 20px
      padding-bottom 0
      font-size $font-size-small-s


</style>
