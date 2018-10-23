<template>
  <slide>
    <div class="identify-submit">
      <app-header>
        实名认证
      </app-header>
      <div class="h-main">
        <div class="identify-submit-content">
          <div class="form">
            <div class="form-item">
              <div class="label">姓名</div>
              <input class="text-ipt" type="text" v-model="identifyForm.realname">
            </div>
            <div class="form-item">
              <div class="label">身份证号</div>
              <input class="text-ipt" type="text" v-model="identifyForm.number">
            </div>
            <div class="form-item">
              <div class="label">请上传手持身份证照片</div>
              <div class="upload-box upload-front">
                <img class="default-img" src="/static/images/identify/zj_front.png" alt="">
                <div class="show-img" v-show="!!frontImg" :style='{ backgroundImage: "url(" + frontImg + ")"}'></div>
                <upload-img @success="handleSuccessFront"></upload-img>
              </div>
            </div>
            <div class="form-item">
              <div class="upload-box">
                <img class="default-img" src="/static/images/identify/zj_back.png" alt="">
                <div class="show-img" v-show="!!backImg" :style='{ backgroundImage: "url(" + backImg + ")"}'></div>
                <upload-img @success="handleSuccessBack"></upload-img>
              </div>
            </div>
          </div>
          <div class="claim">
            <divider>照片拍摄要求</divider>
            <ul class="claim-list">
              <li v-for="(item,index) in claimList" :key="index">
                <img :src="'/static/images/identify/claim_'+index+'.png'" alt="">
                <!--<img src="/static/images/identify/claim_0.png" alt="">-->
                <span>{{item}}</span>
              </li>
            </ul>
          </div>
          <div class="btn-box">
            <x-button type="warn" class="again-btn" @click.native="submitIdentify">下一步</x-button>
          </div>
        </div>
      </div>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import uploadImg from 'components/uploadImg/index'
  import {mapState, mapMutations, mapGetters} from 'vuex'
  import {Divider} from 'vux'

  const claimList = [
    '√标准',
    '×五官遮挡',
    '×未手持',
    '×人物模糊'
  ]

  export default {
    name: "index",
    components: {
      slide,
      uploadImg,
      Divider
    },
    data() {
      return {
        identifyForm: {
          realname: '',
          number:''
        },
        frontImg: '',
        backImg: '',
        claimList: claimList
      }
    },
    methods: {
      getIdentifyMsg() {
        http.post('/identify', {}, (res) => {
          console.log(res)
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          let identify = {}
          if (res.content === '') {
            identify.isIdentify = false
          } else {
            identify = res.content
            identify.isIdentify = true
          }
          sessionStorage.setItem("identifyMsg", JSON.stringify(identify));
          this.setIdentifyMsg(identify)
          let identifyPath = this.switchPath(identify)
          this.$router.replace({
            path: '/personal/identify/'+identifyPath
          })
        })
      },
      switchPath(identify){
        if (!identify.isIdentify) return 'submit'
        switch (identify.status) {
          case 0:
            return 'wait'
          case 1:
            return 'success'
          case 2:
            return 'fail'
        }
      },
      handleSuccessFront(res, src) {
        this.frontImg = src
        this.identifyForm.pic_front = res
      },
      handleSuccessBack(res, src) {
        this.backImg = src
        this.identifyForm.pic_back = res
      },
      submitIdentify(){
        let callback = this.clickFrom()
        if (callback) {
          this.$vux.toast.show(callback)
          return
        }
        http.post('/identify/submit', this.identifyForm, (res) => {
          this.$vux.toast.show(res.msg)
          if (res.code === 0) {
            this.getIdentifyMsg()
          }
        })
      },
      clickFrom(){
        if (!this.identifyForm.realname) {
          return '请输入姓名'
        }
        if (!this.identifyForm.number) {
          return '请输入身份证号'
        }
        if (!this.identifyForm.pic_front) {
          return '请上传身份证人像页'
        }
        if (!this.identifyForm.pic_back) {
          return '请上传身份证国徽页'
        }
        /*if (!(/^1\d{10}$/.test(mobile))) {
          return '请输入有效的手机号'
        }*/
        return ''
      },
      ...mapMutations({
        setIdentifyMsg: 'IDENTIFY_MSG',
      })
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .identify-submit
    fixed-full-screen()
    overflow auto
    .identify-submit-content
      padding 30px 20px
      .form
        padding 0 35px
      .form-item
        margin-bottom 20px
      .label
        margin-bottom 8px
      .text-ipt
        width 100%
        height 35px
        line-height 35px
        box-sizing border-box
        padding 5px
        border 1px solid $color-border
      .upload-box
        position relative
        overflow hidden
        border-radius 3px
        .default-img
          width 100%
        .show-img
          background-size cover
          background-position center
          position absolute
          top 0
          bottom 0
          width 100%
      .upload-front
        top 5px
      .claim-list
        display flex
        justify-content space-between
        margin 20px 0
        li
          width 20%
          display flex
          flex-direction column
          align-items center
          img
            width 100%
          span
            margin-top 10px
            margin-right 5px
            font-size $font-size-small-s
      .btn-box
        padding-top 10px





</style>
