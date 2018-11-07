<template>
  <slide>
    <div class="node-submit">
      <app-header>
        申请节点
      </app-header>
      <div class="h-main">
        <img src="/static/images/sbm-node-top.png" alt="" class="title-img">
        <div class="wrapper">
          <div class="tip">
            提示：尊敬的客户您好！转积分时请认准公司公众号中唯一指定的钱包地址，并且请您在转积分的时候确认扫描二维码出来的钱包地址是否与我们标注的钱包地址一致。
          </div>
          <dl class="condition">
            <dt>
              报名节点条件：
              <br>
              节点竞选以“贵人通+茶通+美食通”即“三通”总个数为准，成为各节点条件分别为：
            </dt>
            <dd v-for="item in conditionDts">{{item}}</dd>
          </dl>
          <div class="form">
            <div class="form-item">
              <div class="label">
                您的微信
              </div>
              <input type="text" v-model="form.wx" placeholder="输入您的微信">
            </div>
            <div class="form-item">
              <div class="label">
                推荐人姓名
              </div>
              <input type="text" v-model="form.wx" placeholder="输入推荐人姓名">
            </div>
            <div class="form-item">
              <div class="label">
                推荐人手机号
                <br>
                <span>推荐人将获得投票券</span>
              </div>
              <input type="text" v-model="form.wx" placeholder="输入推荐人手机号">
            </div>
            <div class="form-item">
              <div class="label">
                节点类型
                <span class="must">*</span>
              </div>
              <sel placeholder="请选择节点类型"></sel>
            </div>
            <div class="form-item">
              <div class="label">
                确认已转入贵人通数量
                <span class="must">*</span>
              </div>
              <input type="text" v-model="form.wx" placeholder="输入数量">
            </div>

            <div class="form-item">
              <div class="label">
                申请贵人通钱包地址
                <span class="must">*</span>
              </div>
              <input type="text" v-model="form.wx" placeholder="输入或粘贴钱包地址">
            </div>
            <div class="form-item">
              <div class="label">
                确认已转入茶通数量
                <span class="must">*</span>
              </div>
              <input type="text" v-model="form.wx" placeholder="输入数量">
            </div>
            <div class="form-item">
              <div class="label">
                申请茶通钱包地址
                <span class="must">*</span>
              </div>
              <input type="text" v-model="form.wx" placeholder="输入或粘贴钱包地址">
            </div>
            <div class="form-item">
              <div class="label">
                确认已转入美食通数量
                <span class="must">*</span>
              </div>
              <input type="text" v-model="form.wx" placeholder="输入数量">
            </div>
            <div class="form-item">
              <div class="label">
                申请美食通钱包地址
                <span class="must">*</span>
              </div>
              <input type="text" v-model="form.wx" placeholder="输入或粘贴钱包地址">
            </div>
          </div>
          <div class="sbm-btn-box">
            <button class="base-btn">下一步</button>
          </div>
        </div>
      </div>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import sel from 'components/sel/index'

  export default {
    name: "index",
    components: {
      slide,
      sel
    },
    data(){
      return{
        conditionDts:[
          '超级节点：45000GRT+8750TT+6500BPT',
          '高级节点：18000GRT+3500TT+2600BPT',
          '中级节点：7200GRT+1400TT+1040BPT',
          '动力节点：1800GRT+350TT+260BPT'
        ],
        form:{
          wx:''
        }
      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .node-submit
    fixed-full-screen()
    overflow auto
    line-height 1.2em
    .title-img
      width 100%
    .wrapper
      padding 0 $space-box
      .tip
        padding 10px 0
        color $color-theme
        font-size $font-size-small-s
      .condition
        font-size $font-size-small-s
        margin-bottom 20px
        dd
          &:before
            content ''
            margin-right 5px
            display inline-block
            width 8px
            height 8px
            background $color-text-minor
      .form-item
        margin-bottom 15px
        ipt-pr(#959da6)
        .label
          margin-bottom 5px
          font-weight bold
          span
            font-weight normal
            margin-top 5px
            color $color-text-minor
          .must
            color #f52e00
        input
          width 100%
          line-height 42px
          border-radius 10px
          box-sizing border-box
          padding 0 10px
          border 1px solid $color-border-sub
      .sbm-btn-box
        padding 25px 0
</style>
