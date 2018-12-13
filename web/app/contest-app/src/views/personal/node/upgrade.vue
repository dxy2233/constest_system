<template>
  <slide>
    <div class="upgrade">
      <app-header>
        节点升级
      </app-header>
      <div class="h-main">
        <div class="upgrade-top">
          <h1>共生态节点升级申请</h1>
          <h6>请确认转入积分后再提交申请，确保信息准确无误</h6>
          <p>提示：尊敬的客户您好！请您在转积分的时候确认扫描二 维码出来的钱包地址是否与我们标注的钱包地址一致。</p>
          <router-link tag="button" :to="{path:'/personal/applynode/address',query:{name:'GRT'}}">
            官方GRT钱包
            <br>
            收款地址
          </router-link>
        </div>
        <div class="upgrade-table">
          <p>升级所需转入积分</p>
          <table>
            <tr v-for="tr in table">
              <td v-for="td in tr">
                {{td}}
              </td>
            </tr>
          </table>
        </div>
        <div class="upgrade-form">
          <div class="form-item">
            <div class="label">
              升级节点类型
              <span class="must">*</span>
            </div>
            <sel :dataList="nodeSelData" placeholder="请选择节点类型" :select="form.type_id"
                 value="id" label="name" @changeSel="changeNode"></sel>
          </div>
          <div class="form-item" v-show="!hasRecommend">
            <div class="label">
              推荐人手机号
              <span>{{recommend_name}}</span>
            </div>
            <input onkeyup="(this.v=function(){this.value=this.value.replace(/[^0-9-]+/,'');}).call(this)"
                   @blur="getRecommendMsg" maxlength="11"
                   type="text" v-model="form.recommend_mobile" placeholder="输入推荐人手机号">
          </div>
          <div class="form-item">
            <div class="label">
              确认已转入贵人通数量
              <span class="must">*</span>
            </div>
            <input type="text" v-model="form.grt_num" placeholder="输入数量">
          </div>

          <div class="form-item">
            <div class="label">
              申请贵人通钱包地址
              <span class="must">*</span>
            </div>
            <input type="text" v-model="form.grt_address" placeholder="输入或粘贴钱包地址">
          </div>

          <div class="sbm-btn-box">
            <x-button type="warn" class="base-btn" @click.native="submitFrom" :disabled="btnLoading"
                      :show-loading="btnLoading">确定
            </x-button>
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
  import {limitFloating} from 'js/mixin'
  import {mapGetters} from 'vuex'

  export default {
    name: "upgrade",
    components: {
      slide,
      sel
    },
    data() {
      return {
        table: [
          [
            '升级节点', '升级节点', '动力节点', '中级节点', '高级节点'
          ],
          [
            '动力节点', '2000GRT', '', '', ''
          ],
          [
            '中级节点', '8000GRT', '6000GRT', '', ''
          ],
          [
            '高级节点', '20000GRT', '18000GRT', '12000GRT', ''
          ],
          [
            '超级节点', '50000GRT', '48000GRT', '42000GRT', '30000GRT'
          ],
        ],
        form: {
          type_id: '',
          grt_address: '',
          grt_num: '',
          recommend_mobile: ''
        },
        nodeSelData: [],
        btnLoading: false,
        recommend_name: '',
        hasRecommend: true,
      }
    },
    methods: {
      submitFrom() {
        if (this.myNodeInfo.isTenure){
          this.$vux.toast.show('任职状态下不能升级')
          return
        }
        if (this.form.type_id >= this.myNodeInfo.typeId) {
          this.$vux.toast.show('只能升级到更高的节点')
          return
        }
        let grt = Number(this.form.grt_num)
        if (!grt) {
          this.$vux.toast.show('请输入有效的贵人通数量')
          return
        }
        if (!this.form.grt_address) {
          this.$vux.toast.show('请输入贵人通钱包地址')
          return
        }
        this.upgradeNode()
      },
      upgradeNode() {
        this.btnLoading = true
        http.post('/node/upgrade', this.form, (res) => {
          this.btnLoading = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.$vux.toast.show({
            text: res.msg,
            type: 'success'
          })
          setTimeout(() => {
            this.$router.push({
              path: '/personal'
            })
          }, 1500)
        })
      },
      getRecommendMsg() {
        http.post('/node/recommend-mobile', {mobile: this.form.recommend_mobile}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.recommend_name = res.content.realname
        })
      },
      getNodeSel() {
        http.post('/node/type-list', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.nodeSelData = res.content
          let nodeInfo = this.nodeSelData[0]
          this.form.type_id = nodeInfo.id
          // this.form.grt_num = nodeInfo.grt
        })
      },
      changeNode(item) {
        this.form.type_id = item.id
        // this.form.grt_num = item.grt
      },
      getHasRecommend() {
        http.post('/node/has-recommend', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.hasRecommend = res.content
        })
      }
    },
    computed: {
      ...mapGetters([
        'myNodeInfo'
      ]),
    },
    created() {
      this.getNodeSel()
      this.getHasRecommend()
    },
    watch: {
      'form.grt_num'(v) {
        let n = limitFloating(v)
        this.form.grt_num = n
      },
    },
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .upgrade
    fixed-full-screen()
    overflow auto
    .app-header
      border-bottom 1px solid $color-border
      background $color-background-sub
    .h-main
      padding-left $space-box
      padding-right $space-box
    .upgrade-top
      padding-top 35px
      h1
        text-align center
        font-size $font-size-large-x
      h6
        margin-top 5px
        text-align center
        font-size $font-size-small-s
      p
        line-height 1.25em
        font-size $font-size-small-s
        margin-top 30px
        color $color-theme
      button
        margin 25px 0
        background #FF9E45
        padding 10px
        border-radius 10px
        width 100%
        border 0
        color white
        box-shadow: 0px 6px 8px 0px rgba(255, 158, 69, 0.29);

    .upgrade-table
      p
        color $color-text-minor
        margin-bottom 5px
      table
        width 100%
        background #fffaf7
        border-collapse: collapse;
        border-spacing: 0;
        overflow hidden
      td
        border 1px solid #ECE5E1
        text-align center
        font-size $font-size-small-s
        &:first-of-type
          color #FF7F32
    .upgrade-form
      margin-top 20px
      .form-item
        margin-bottom 15px
        ipt-pr(#959da6)
        .label
          margin-bottom 2px
          color $color-text-minor
          font-size $font-size-small-s
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
