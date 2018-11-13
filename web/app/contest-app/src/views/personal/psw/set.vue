<template>
  <psw-field title="" :label="fieldLabel" :loadingShow="loadingShow" :hiddenHeader="hiddenHeader"
             @iptWord="acceptWord"></psw-field>
</template>

<script>
  import pswField from 'views/personal/psw/field'
  import http from 'js/http'
  import {mapState, mapMutations, mapGetters} from 'vuex'

  export default {
    name: "reset",
    components: {
      pswField
    },
    data() {
      return {
        fieldLabel: '设置支付密码',
        newPsw: '',
        step: 0,
        loadingShow: false,
        hiddenHeader:true
      }
    },
    methods: {
      acceptWord(word) {
        switch (this.step) {
          case 0:
            this.newPsw = word
            this.fieldLabel = '确认支付密码'
            this.step = 1
            break
          case 1:
            this.setNewPsw(word)
            break

        }
      },
      setNewPsw(repass) {
        if (this.newPsw !== repass) {
          this.$vux.toast.show('两次密码输入不一致,请重新设置')
          this.newPsw = ''
          this.fieldLabel = '设置支付密码'
          this.step = 0
          return
        }
        this.loadingShow = true

        http.post('/pay/create-pass', {
          pass: this.newPsw,
          repass: repass,
        }, (res) => {
          this.loadingShow = false
          this.$vux.toast.show(res.msg)
          if (res.code !== 0) {
            this.newPsw = ''
            this.fieldLabel = '设置支付密码'
            this.step = 0
            return
          }
          let n = '1'
          this.setPayPsw(n)
          localStorage.setItem('payPsw', n)
          setTimeout(() => {
            this.$router.back()
            /*this.$router.replace({
              path:'/home'
            })*/
          }, 1000)
        })
      },
      ...mapMutations({
        setPayPsw: 'PAY_PSW'
      })
    },
    created() {
    },
    destroyed() {
    }

  }
</script>

<style scoped>

</style>
