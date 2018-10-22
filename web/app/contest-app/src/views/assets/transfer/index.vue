<template>
  <slide>
    <div class="assets-transfer">
      <!--<x-header class="w-header">
        <x-icon slot="overwrite-left" type="ios-close-empty" size="35" @click.native="backPath"></x-icon>
      </x-header>-->
      <app-header>
      </app-header>
      <div class="h-main wrapper">
        <div class="title">转账</div>
        <div class="transfer-form">
          <div class="form-item">
            <label for="">币种</label>
            <div class="ipt-box">
              <sel :dataList="currencyList" placeholder="请选择币种"
                   value="id" label="name" @changeSel="changeCurrent"></sel>
            </div>
          </div>
          <div class="form-item">
            <label for="">数量</label>
            <div class="ipt-box">
              <input type="text" v-model="form.amount" :placeholder="'余额'+balance">
              <span class="all-btn" @click="form.amount = balance" v-if="form.id">全部</span>
            </div>
          </div>
          <div class="form-item">
            <label for="">接收方钱包地址</label>
            <div class="ipt-box">
              <input type="text" v-model="form.address" placeholder="输入或长按黏贴">
            </div>
          </div>
          <div class="form-item">
            <label for="">备注</label>
            <div class="ipt-box">
              <input type="text" v-model="form.remark" placeholder="描述描述描述">
            </div>
          </div>
        </div>
        <x-button type="warn" class="again-btn" @click.native="submitTransfer">确认支付</x-button>
        <valid-pay-psw v-if="validPswShow" @validSuccess="validPswSuccess" @close="validPswShow=false"></valid-pay-psw>
        <valid-vcode v-if="validVcodeShow" @close="validVcodeShow=false" @valid="validAll"></valid-vcode>
        <loading :show="loadingShow" text=""></loading>
      </div>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import {PopupPicker} from 'vux'
  import sel from 'components/sel/index'
  import validPayPsw from 'components/validPayPsw/index'
  import validVcode from 'views/assets/transfer/validVcode'
  import {Loading} from 'vux'

  export default {
    name: "index",
    components: {
      slide,
      PopupPicker,
      sel,
      validPayPsw,
      validVcode,
      Loading
    },
    data() {
      return {
        form: {
          id: '',
          amount: '',
          address: '',
          remark: ''
        },
        currencyList: [],
        currency: '',
        balance: '0',
        validPswShow: false,
        validVcodeShow: false,
        payPsw: '',
        vcode: "",
        loadingShow: false
      }
    },
    methods: {
      backPath() {
        this.$router.back()
      },
      changeCurrent(item) {
        // console.log(item)
        this.form.id = item.id
        this.balance = item.useAmount
      },
      submitTransfer() {
        if (!this.form.id) {
          this.$vux.toast.show('请选择币种')
          return
        }
        if (!this.form.amount) {
          this.$vux.toast.show('请输入数量')
          return
        }
        if (!(/^\d+(\.\d+)?$/.test(this.form.amount))){
          this.$vux.toast.show('请输入有效的数量')
          return
        }
        if (!this.form.address) {
          this.$vux.toast.show('请输入或长按黏贴钱包地址')
          return
        }
        this.validPswShow = true
      },
      validPswSuccess(payPsw) {
        this.validVcodeShow = true
        this.validPswShow = false
        this.payPsw = payPsw
        console.log(this.payPsw)
      },
      validAll(vcode) {
        this.loadingShow = true
        http.post('/wallet/transfer', Object.assign({
          pass: this.payPsw,
          vcode: vcode
        }, this.form), (res) => {
          this.loadingShow = false
          let type = res.code === 0 ? 'success' : 'warn'
          this.$vux.toast.show({
            text: res.msg,
            time: 3000,
            type: type
          })
          this.validVcodeShow = false
        })
      }
    },
    created() {
      let list = JSON.parse(sessionStorage.getItem('currencyList'))
      this.currencyList = list
    },

  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .assets-transfer
    fixed-full-screen()
    overflow auto
    & > .wrapper
      padding-left $space-box
      padding-right $space-box
    .title
      font-size 28px
      padding 35px 0
    .transfer-form
      .form-item
        margin-bottom 30px
      .ipt-box
        position relative
        line-height 30px
        height 30px
        border-bottom 1px solid $color-border
        ipt-pr(#959DA6)
      input
        width 100%
        line-height 30px
        color $color-text-sub
      .all-btn
        position absolute
        right 0
        top 0
        color $color-theme


</style>
