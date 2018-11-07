<template>
  <slide>
    <div class="receive-address">
      <div v-if="have" class="edit">
        <app-header>
          收获地址
          <router-link tag="span" to="/personal/applynode/rules" slot="right">编辑</router-link>
        </app-header>
        <div class="h-main">
          <ul class="address-list">
            <li>
              <p>
                郭德纲
                <span>13100056895</span>
              </p>
              <h4>重庆市 南岸区 铜元局街道亚太一路郭德纲4栋</h4>
            </li>
          </ul>
        </div>
      </div>
      <div v-else class="add">
        <app-header>
          收获地址
        </app-header>
        <div class="h-main">
          <div class="add-content">
            <img src="/static/images/add-address.png" alt="">
            <button class="base-btn">添加收获地址</button>
          </div>
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
    data(){
      return{
        have:false
      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .receive-address
    fixed-full-screen()
    .edit
      .app-header
        border-bottom 1px solid $color-border
        background $color-background-sub
      .address-list
        li
          margin-left $space-box
          padding $space-box
          padding-left 0
          border-bottom 1px solid $color-border
          font-size $font-size-small
          p
            color $color-text-minor
            span
              margin-left 30px
          h4
            margin-top 5px
            font-weight bold
    .add
      .add-content
        display flex
        flex-direction column
        align-items center
        img
          margin 85px 0
          width 154px
        button
          width 290px

</style>
