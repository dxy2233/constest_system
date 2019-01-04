<template>
  <slide>
    <div class="assets-transfer">
      <!--<x-header class="w-header">
        <x-icon slot="overwrite-left" type="ios-close-empty" size="35" @click.native="backPath"></x-icon>
      </x-header>-->
      <app-header>
      </app-header>
      <div class="h-main wrapper">
        <div class="title">{{htmlString.title}}</div>
        <div class="transfer-form">
          <div class="form-item">
            <label for="">积分</label>
            <div class="ipt-box">
              <input v-if="this.$route.query.name==='gdt'" type="text" readonly value="GDT">
              <sel v-else :dataList="currencyList" placeholder="请选择积分"
                   value="id" label="name" @changeSel="changeCurrent" :select="this.form.id"></sel>
            </div>
          </div>
          <div class="form-item">
            <label for="">数量</label>
            <div class="ipt-box">
              <input type="text" v-model="form.amount" :placeholder="htmlString.amountPlaceholder+balance">
              <span class="all-btn" @click="form.amount = balance" v-if="form.id">全部</span>
            </div>
          </div>
          <div class="form-item" v-if="!isTransfer">
            <label for="">您的IET账号</label>
            <div class="ipt-box">
              <input type="text" v-model="form.tag" placeholder="手机号/邮箱">
            </div>
          </div>
          <div class="form-item">
            <label for="">{{htmlString.addressLabel}}</label>
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
        <x-button type="warn" class="again-btn" @click.native="submitTransfer" :disabled="btnDisabled">
          {{htmlString.btn}}
        </x-button>
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
  import {limitIpt} from 'js/mixin'

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
          remark: '',
          tag: ''
        },
        currencyList: [],
        currency: '',
        balance: '0',
        validPswShow: false,
        validVcodeShow: false,
        payPsw: '',
        vcode: "",
        loadingShow: false,
        hasIdentify: true,
        btnDisabled: false,
        htmlString: {
          title: '转出',
          amountPlaceholder: '余额',
          addressLabel: '接收方钱包地址',
          btn: '确认支付'
        },
        isTransfer: true
      }
    },
    methods: {
      vaildAmount() {
        let vaild = this.clickAmount(this.form.amount)
        if (vaild) {
          this.$vux.toast.show(vaild)
        }
      },
      vaildAdress() {
        this.clickAdress(this.form.address, (res) => {
          if (res) {
            this.$vux.toast.show(res)
          }
        })
      },
      clickAdress(value, cb) {
        if (!value) {
          cb('转账地址不能为空')
          return
        }
        http.post('/wallet/address-check', {
          id: this.form.id,
          address: this.form.address
        }, (res) => {
          if (res.code !== 0) {
            cb(res.msg)
          } else {
            cb('')
          }
        })
      },
      backPath() {
        this.$router.back()
      },
      changeCurrent(item) {
        console.log(item)
        this.form.id = item.id
        this.balance = item.useAmount
        this.hasIdentify = item.hasIdentify
      },
      clickAmount(value) {
        if (!value) {
          return '请输入数量'
        }
        if (!(/^\d+(\.\d+)?$/.test(value)) || value * 1 === 0) {
          return '请输入有效的数量'
        }
        if (value - this.balance > 0) {
          return '可用不足'
        }
        return ''
      },
      submitTransfer() {
        if (!this.form.id) {
          this.$vux.toast.show('请选择积分')
          return
        }
        let vaild = this.clickAmount(this.form.amount)
        if (vaild) {
          this.$vux.toast.show(vaild)
          return
        }
        if (!this.isTransfer){
          if (!this.form.tag) {
            this.$vux.toast.show('请输入IET账号')
            return
          }else {
            if (this.form.tag.includes('@')) {//邮箱
              if (!(/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(this.form.tag))) {
                this.$vux.toast.show('请输入有效的IET账号')
                return
              }
            }else {
              if (!(/^1\d{10}$/.test(this.form.tag))) {
                this.$vux.toast.show('请输入有效的IET账号')
                return
              }
            }
          }
        }
        this.clickAdress(this.form.address, (res) => {
          if (res) {
            this.$vux.toast.show(res)
            return
          }
          if (!this.hasIdentify) {
            this.$vux.toast.show('请先通过实名认证')
            return
          }
          this.btnDisabled = true
          this.validPswShow = true
        })

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
          this.btnDisabled = false
          this.loadingShow = false
          let type = res.code === 0 ? 'success' : 'warn'
          this.$vux.toast.show({
            text: res.msg,
            time: 3000,
            type: type
          })
          if (res.code === 0) {
            this.$router.go(-1)
          }
          this.validVcodeShow = false
        })
      }
    },
    created() {
      if (this.$route.query.name === 'gdt') {
        this.htmlString = {
          title: '领取',
          amountPlaceholder: '可领取',
          addressLabel: '您的IET地址',
          btn: '确认领取'
        }
        this.isTransfer = false
        this.form.id = this.$route.params.id
        let gdt = JSON.parse(localStorage.getItem('gdtInfo'))
        this.balance = gdt.useAmount
        this.hasIdentify = gdt.hasIdentify
        return
      }
      let list = JSON.parse(localStorage.getItem('currencyList'))
      let nL = []
      for (let item of  list) {
        if (parseInt(item.withdrawStatus)) {
          nL.push(item)
          if (item.id === this.$route.params.id) {
            // console.log(item)
            this.form.id = item.id
            this.balance = item.useAmount
            this.hasIdentify = item.hasIdentify
          }
        }
      }
      this.currencyList = nL
    },
    watch: {
      'form.amount': function () {
        let p = limitIpt(this.form.amount, 2)
        this.form.amount = p
      }
    }
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
      .vux-x-icon
        display none

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
