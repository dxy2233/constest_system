<template>
  <div class="pay-tool">
    <div class="pay-tool-content">
      <div class="pay-tool-inputs">
        <div class="item" v-for="i in 6"><span class="icon_dot" v-if="password[++i]"></span></div>
      </div>
    </div>
    <div class="pay-tool-keyboard">
      <ul>
        <li v-for="val in keys" @click="">
          {{ val }}
        </li>
        <li class="del" @click=""><span class="icon-del"><</span></li>
      </ul>
    </div>
  </div>
</template>

<script>
  export default {
    name: "index",
    data() {
      return {
        keys: [1, 2, 3, 4, 5, 6, 7, 8, 9, '', 0],
        password: []
      }
    },
    methods: {
      backHandle () {
        this.clearPasswordHandle()  // 返回时清除password
        this.$emit('backFnc') // 返回上级
      },
      keyUpHandle (e) {
        console.log(e)
        let text = e.currentTarget.innerText
        let len = this.password.length
        if (!text || len >= 6) return
        this.password.push(text)
        this.ajaxData()
      },
      delHandle () {
        if (this.password.length <= 0) return false
        this.password.shift()
      },
      ajaxData () {
        if (this.password.length >= 6) {
          console.log(parseInt(this.password.join(' ').replace(/\s/g, '')))
        }
        return false
      },
      clearPasswordHandle: function () {
        this.password = []
      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .pay-tool
    fixed-full-screen()
    overflow hidden
    .pay-tool-inputs {
      width: 14.46666666rem;
      height: 2.31111111rem;
      margin: 1.28888888rem auto 0;
      border: 1px solid #b9b9b9;
      border-radius: 0.26666666rem;
      box-shadow: 0 0 1px #e6e6e6;
      display: flex;
      .item {
        width: 16.66666666%;
        height: 2.31111111rem;
        border-right: 1px solid #b9b9b9;
        line-height: 2.31111111rem;
        text-align: center;
        &:last-child {
          border-right: none;
        }
        .icon_dot {
          display: inline-block;
          width: 0.51111111rem;
          height: 0.51111111rem;
          background-size: cover;
        }
      }
    }
    .pay-tool-keyboard
      position absolute
      left 0
      right 0
      bottom 0
      ul {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        li {
          width: 33.3333%;
          height: 60px
          line-height: 60px
          text-align: center;
          border-right: 1px solid #aeaeae;
          border-bottom: 1px solid #aeaeae;
          font-size: 18px
          font-weight: bold;
          box-sizing border-box
          &:nth-child(1), &:nth-child(2), &:nth-child(3) {
            border-top: 1px solid #eee;
          }
          &:nth-child(3), &:nth-child(6), &:nth-child(9), &:nth-child(12) {
            border-right: none;
          }
          &:nth-child(10), &:nth-child(11), &:nth-child(12) {
            border-bottom: none;
          }
          &:nth-child(10), &:nth-child(12), &:active {
            background-color: #d1d4dd;
          }
          &:nth-child(12):active {
            background-color: #fff;
          }
        }
      }


</style>
