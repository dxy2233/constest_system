<template>
  <slide>
    <div class="notice-details">
      <!--<x-header :left-options="{backText: ''}">{{noticeInfo.title}}</x-header>-->
      <app-header>
        {{noticeInfo.title}}
      </app-header>
      <div class="h-main">
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

          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.noticeInfo = res.content
          // console.log(this.noticeInfo)
        })
      }
    },
    created(){
      this.getData()
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

</style>
