<template>
  <psw-field title="重置支付密码" :label="fieldLabel" :loadingShow="loadingShow"
             @iptWord="acceptWord"></psw-field>
</template>

<script>
  import pswField from 'views/personal/psw/field'
  import http from 'js/http'

  export default {
    name: "reset",
    components: {
      pswField
    },
    data() {
      return {
        fieldLabel: '设置新支付密码',
        newPsw: '',
        step: 0,
        loadingShow: false,
        vcode:''
      }
    },
    methods: {
      acceptWord(word) {
        switch (this.step) {
          case 0:
            this.newPsw = word
            this.fieldLabel = '确认新支付密码'
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
          this.fieldLabel = '设置新支付密码'
          this.step = 0
          return
        }
        this.loadingShow = true
        let vcode = sessionStorage.getItem("resetVcode")
        http.post('/pay/reset-pass', {
          pass: this.newPsw,
          repass: repass,
          vcode: vcode
        }, (res) => {
          this.loadingShow = false
          this.$vux.toast.show(res.msg)
          if (res.code !== 0) {
            return
          }
          setTimeout(() => {
            this.$router.back()
          }, 1000)
        })
      }
    },
    created(){
      let code = sessionStorage.getItem("resetVcode")
      if (!code){
        this.$router.back()
        return
      }
      this.vcode = code
    },
    destroyed(){
      sessionStorage.removeItem('resetVcode')
    }

  }
</script>

<style scoped>

</style>
