<template>
  <slide>
    <div class="rule-description">
      <app-header>
        规则说明
      </app-header>
      <div class="h-main">
        <div class="title">节点排名规则</div>
        <div class="content">
          <p>
            目前仅开放超级节点和高级节点的竞选，各节点可通过线下宣传或者分享自己节点链接来寻求支持者为节点投票，通过排名争夺任职节点名额（参与社区治理权及分红，12项超级权益）。排名以票数衡量，如出现票数一致的情况以支持人数多者获胜，两者相同按投票时间最早胜出。
            官方公告会发布每次节点竞选的截止时间和产生的名额，请关注首页公告内容，实时更新动态。
          </p>
          <p>注：超级节点最低支持人数达到3941方可有效。高级节点最低支持人数达到941方可有效。</p>
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
    components: {
      slide
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .rule-description
    fixed-full-screen()
    overflow auto
    .title
      padding 20px $space-box
      font-size 20px
    .content
      padding 20px $space-box
      font-size $font-size-small
      line-height 25px
      p
        margin-bottom 15px

</style>
