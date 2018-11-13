<template>
  <slide>
    <div class="notice-details">
      <app-header>
        <span v-if="noticeInfo.type==1">{{noticeInfo.title}}</span>
      </app-header>
      <div class="h-main" v-if="noticeInfo.type==1">
        <div class="content-box">
          <!--<h2 class="title">{{noticeInfo.desc}}</h2>-->
          <div class="detail" v-html="noticeInfo.detail">egwrgwgvgwegr</div>
        </div>
      </div>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'

  export default {
    name: "index",
    components:{
      slide
    },
    data(){
      return{
        noticeInfo:{}
      }
    },
    methods:{
      getData(){
        http.post('/notice/info', {
          id: this.$route.params.id,
        }, (res) => {
          // console.log(res)
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          if (res.content.type == 0){
            // window.open(res.content.url)
            this.$router.back()
            window.location.href = res.content.url
          }else {
            this.noticeInfo = res.content
          }

        })
      }
    },
    created(){
      // this.getData()
      this.noticeInfo = JSON.parse(localStorage.getItem('noticeInfo'))
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .notice-details
    fixed-full-screen()
    overflow auto
    .content-box
      padding 20px $space-box
      .title
        margin-bottom 20px
        font-weight bold
        font-size $font-size-large
      .detail
        line-height 1.5em
      img
        max-width 100%

</style>
