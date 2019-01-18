<template>
  <slide>
    <div class="recode-details">
      <app-header></app-header>
      <div class="h-main">
        <div class="recode-details-content">
          <div class="top">
            <h4>
              {{info.currencyName}}
            </h4>
            <h2>
              {{info.amount}}
            </h2>
          </div>
          <ul class="bottom">
            <li>
              <div class="label">当前状态</div>
              <div class="value">{{info.statusStr}}</div>
            </li>
            <li>
              <div class="label">IET账号</div>
              <div class="value">{{info.tag}}</div>
            </li>
            <li>
              <div class="label">IET地址</div>
              <div class="value">
                {{info.destinationAddress}}
              </div>
            </li>
            <li>
              <div class="label">备注</div>
              <div class="value">{{info.remark}}</div>
            </li>
            <li>
              <div class="label">提交时间</div>
              <div class="value">{{info.createTime}}</div>
            </li>
          </ul>
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
    },
    data() {
      return {
        info: {}
      }
    },
    methods: {
      getInfo() {
        http.post('/wallet/auditing-info', {
          id: this.$route.params.recodeId,
        }, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.info = res.content
        })
      }
    },
    created() {
      this.getInfo()
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .recode-details
    fixed-full-screen()
    overflow auto

    .recode-details-content
      padding 0 $space-box

      .top
        height 120px
        display flex
        flex-direction column
        align-items center
        justify-content center
        border-bottom 1px solid $color-border

        h4
          font-size $font-size-medium
          margin-bottom 10px
          font-weight bold

        h2
          font-size $font-size-large-x
      .bottom
        padding 20px 0
        li
          margin-bottom 20px
          overflow hidden
          .label
            width 70px
            float left
            color $color-text-minor
          .value
            word-wrap break-word
            float left
            width calc(100% - 70px)


</style>
