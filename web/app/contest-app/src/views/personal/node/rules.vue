<template>
  <slide>
    <div class="app-node-rules">
      <app-header>
        规则说明
      </app-header>
      <div class="h-main">
        <div class="title">节点申请规则说明</div>
        <div class="content">
          <p>用户申请节点时需完成实名认证，签署《联盟共识》，提交个人基本信息，质押数字资产及节点基本信息。</p>
          <p>用户可以申请四类节点，动力节点，中级节点，高级节点，超级节点。根据级别不同所享受的权益也不同，级别越高享受的权益越高，同时也要履行相应的义务，共同建设联盟。</p>
          <p>动力节点质押数字资产GRT1800，BPT260，TT350</p>
          <p>中级节点质押数字资产GRT7200，BPT1040，TT1400</p>
          <p>高级节点质押数字资产GRT18000，BPT2600，TT3500</p>
          <p>超级节点质押数字资产GRT45000，BPT6500，TT8750</p>
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
  .app-node-rules
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
