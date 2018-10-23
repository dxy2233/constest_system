<template>
  <psw-field title="修改支付密码" :label="fieldLabel" :loadingShow="loadingShow" :fieldDisabled="fieldDisabled"
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
        newPsw: '',
        step: 0,
        loadingShow: false,
        vcode: '',
        oldPsw:'',
        fieldDisabled:false
      }
    },
    methods: {
      acceptWord(word) {
        switch (this.step) {
          case 0:
            this.validatePsw(word)
            break
          case 1:
            this.newPsw = word
            this.step = 2
            break
          case 2:
            this.setNewPsw(word)
            break

        }
      },
      validatePsw(psw) {
        this.loadingShow = true
        this.fieldDisabled = true
        http.post('/pay/validate-pass', {
          pass: psw
        }, (res) => {
          this.fieldDisabled = false
          this.loadingShow = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          if (!res.content){
            this.$vux.toast.show('校验失败')
            return
          }
          this.step = 1
          this.oldPsw = psw
        })
      },
      setNewPsw(repass) {
        this.fieldDisabled = true
        if (this.newPsw !== repass) {
          this.$vux.toast.show('两次密码输入不一致,请重新设置')
          this.newPsw = ''
          this.step = 0
          this.fieldDisabled = false
          return
        }
        this.loadingShow = true
        http.post('/pay/update-pass', {
          pass: this.newPsw,
          repass: repass,
          oldpass: this.oldPsw
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
    created() {
    },
    destroyed() {
    },
    computed:{
      fieldLabel(){
        switch (this.step){
          case 0:
            return '输入原支付密码'
          case 1:
            return '设置新支付密码'
          case 2:
            return '确认新支付密码'
        }
      }
    }

  }
</script>

<style scoped>

</style>
