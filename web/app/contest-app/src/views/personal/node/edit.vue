<template>
  <slide>
    <div class="node-edit">
      <app-header>
        <span slot="left" class="icon-back" @click="backPage"></span>
        节点编辑
        <div slot="right">
          <inline-loading v-show="saveLoad"></inline-loading>
          <span @click="vaildFrom">保存</span>
        </div>
      </app-header>
      <div class="h-main">
        <div class="top">
          <div class="img-box">
            LOGO
            <img v-if="logoSrc" :src="logoSrc" alt="">
          </div>
          <p class="upload-btn">上传宣传LOGO</p>
          <upload-img @success="handleSuccessLogo"></upload-img>
        </div>
        <div class="bottom">
          <div class="form">
            <div class="form-item">
              <div class="label">
                节点名称
                <span class="must">*</span>
              </div>
              <div class="form-item-content">
                <input class="node-name" type="text" v-model="form.name">
              </div>
            </div>
            <div class="form-item">
              <div class="label">
                节点简介
                <span class="must">*</span>
              </div>
              <div class="form-item-content">
                <textarea class="node-desc" v-model="form.desc"></textarea>
                <p class="length-hint" :class="{'error':form.desc.length>1000}">{{form.desc.length}}/1000</p>
              </div>
            </div>
            <div class="form-item">
              <div class="label">
                建设方案
                <span class="must">*</span>
              </div>
              <div class="form-item-content">
                <textarea class="node-scheme" v-model="form.scheme"></textarea>
                <p class="length-hint" :class="{'error':form.scheme.length>1000}">{{form.scheme.length}}/1000</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-transfer-dom>
        <confirm v-model="show"
                 title="确认离开此页面？"
                 cancel-text="暂不保存"
                 confirm-text="保存"
                 @on-cancel="noSave"
                 @on-confirm="vaildFrom">
          <p style="text-align:center;">您修改的资料还未保存</p>
        </confirm>
      </div>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import uploadImg from 'components/uploadImg/index'
  import {Confirm, TransferDomDirective as TransferDom,InlineLoading} from 'vux'
  import {mapState, mapMutations, mapGetters} from 'vuex'

  export default {
    name: "index",
    directives: {
      TransferDom
    },
    components: {
      slide,
      uploadImg,
      Confirm,
      InlineLoading
    },
    data() {
      return {
        logoSrc: '',
        form: {
          logo: '',
          name: '',
          desc: '',
          scheme: ''
        },
        show:false,
        saveLoad:false
      }
    },
    computed: {
      ...mapGetters([
        'myNodeInfo'
      ]),
    },
    methods: {
      noSave(){
        this.$router.back()
      },
      handleSuccessLogo(res, src) {
        this.logoSrc = src
        this.form.logo = res
      },
      backPage() {
        for (let i in this.form) {
          // this.form[i] = this.nodeInfo[i]||''
          if (this.form[i] !== this.myNodeInfo[i]) {
            this.show = true
            return
          }
        }
        this.$router.back()
      },
      handleSave(){
        this.saveLoad = true
        http.post('/node/edit',this.form,(res)=>{
          this.saveLoad=false
          if (res.code!==0){
            this.$vux.toast.show(res.msg)
            return
          }
          let newData = Object.assign({},this.myNodeInfo,this.form)
          sessionStorage.setItem('myNodeInfo',JSON.stringify(newData))
          this.setMyNodeInfo(newData)
          this.$vux.toast.show({
            text: res.msg,
            type: 'success'
          })
          setTimeout(() => {
            this.$router.back()
          }, 1500)
        })

      },
      vaildFrom(){
        if (!this.form.name){
          this.$vux.toast.show('节点名称不能为空')
          return
        }
        if (!this.form.desc){
          this.$vux.toast.show('节点简介不能为空')
          return
        }
        if (this.form.desc.length>1000){
          this.$vux.toast.show('节点简介内容超出限制')
          return
        }
        if (!this.form.scheme){
          this.$vux.toast.show('建设方案不能为空')
          return
        }
        if (this.form.scheme.length>1000){
          this.$vux.toast.show('建设方案内容超出限制')
          return
        }
        this.handleSave()
      },
      ...mapMutations({
        setMyNodeInfo: 'MY_NODE_INFO'
      })
    },
    created() {
      for (let i in this.form) {
        this.form[i] = this.myNodeInfo[i] || ''
      }
      this.logoSrc = this.myNodeInfo.logo||'/static/images/node-avatar-default.jpg'
    }
  }
</script>


<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .node-edit
    fixed-full-screen()
    overflow auto
    .app-header
      border-bottom 1px solid $color-border
      background $color-background-sub
    .top
      width 100px
      margin 20px auto
      position relative
      .img-box
        position relative
        height 100px
        background #FFFCF9
        text-align center
        line-height 100px
        color #959DA6
        border 1px solid #DBDBDB
        border-radius 10px
        overflow hidden
        img
          position absolute
          top 0
          left 0
          width 100%
          height 100%
      .upload-btn
        font-size $font-size-small-s
        background #FFF8F2
        line-height 25px
        border 1px solid #FFA344
        margin-top 10px
        border-radius 5px
        color #6C747A
        text-align center
    .bottom
      padding 0 $space-box
      .form-item
        margin-bottom 20px
        .must
          color $color-theme
        .form-item-content
          border-radius 10px
          border 1px solid $color-border-sub
          box-sizing border-box
          width 100%
          padding 5px
        .length-hint
          font-size $font-size-small-s
          height $font-size-small-s
          line-height $font-size-small-s
          color $color-text-sub
          text-align right
        .length-hint.error
          color $color-theme
      .label
        margin-bottom 10px
      textarea, input
        width 100%
        resize none
        border 0
      input
        line-height 30px
      .node-desc
        height 65px
      .node-scheme
        height 100px
</style>
