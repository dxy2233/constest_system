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
          <ul class="address-btn">
            <router-link tag="li" :to="{path:'/personal/applynode/address',query:{name:'GRT'}}" class="grt">
              官方GRT钱包
              <br>
              收款地址
            </router-link>
            <router-link tag="li" :to="{path:'/personal/applynode/address',query:{name:'TT'}}" class="tt">
              官方TT钱包
              <br>
              收款地址
            </router-link>
          </ul>
          <dl class="condition">
            <dt>
              报名节点条件：
              <br>
              节点竞选以“贵人通+茶通”总个数为准，成为各节点条件分别为：
            </dt>
            <dd class="normal" v-for="item in conditionDts">{{item}}</dd>
            <dd class="added">
              （如您现在有美食通可按照原条件要求的三通数量转入）
            </dd>
          </dl>
          <div class="form">
            <div class="form-item">
              <div class="label">
                您的微信
                <span class="must">*</span>
              </div>
              <input type="text" v-model="form.weixin" placeholder="输入您的微信">
            </div>
            <div class="form-item" v-show="isShowRecommend">
              <div class="label">
                推荐人手机号
                <span>{{recommend_name}}</span>
              </div>
              <input onkeyup="(this.v=function(){this.value=this.value.replace(/[^0-9-]+/,'');}).call(this)"
                     @blur="getRecommendMsg" maxlength="11"
                     type="text" v-model="form.recommend_mobile" placeholder="输入推荐人手机号">
            </div>
            <!--<div class="form-item">
              <div class="label">
                推荐人姓名
              </div>
              <input type="text" v-model="form.recommend_name" placeholder="输入推荐人姓名">
            </div>
            <div class="form-item">
              <div class="label">
                推荐人手机号
                <br>
                <span>推荐人将获得投票券</span>
              </div>
              <input onkeyup="(this.v=function(){this.value=this.value.replace(/[^0-9-]+/,'');}).call(this)"
                     onblur="this.v()" maxlength="11"
                     type="text" v-model="form.recommend_mobile" placeholder="输入推荐人手机号">
            </div>-->
            <div class="form-item">
              <div class="label">
                节点类型
                <span class="must">*</span>
              </div>
              <sel :dataList="nodeSelData" placeholder="请选择节点类型" :select="form.type_id"
                   value="id" label="name" @changeSel="changeNode"></sel>
            </div>
            <div class="form-item">
              <div class="label">
                确认已转入贵人通数量
                <!--<span class="must">*</span>-->
              </div>
              <input type="text" v-model="form.grt_num" placeholder="输入数量">
            </div>

            <div class="form-item">
              <div class="label">
                申请贵人通钱包地址
                <!--<span class="must">*</span>-->
              </div>
              <input type="text" v-model="form.grt_address" placeholder="输入或粘贴钱包地址">
            </div>
            <div class="form-item">
              <div class="label">
                确认已转入茶通数量
              </div>
              <input type="text" v-model="form.tt_num" placeholder="输入数量">
            </div>
            <div class="form-item">
              <div class="label">
                申请茶通钱包地址
              </div>
              <input type="text" v-model="form.tt_address" placeholder="输入或粘贴钱包地址">
            </div>
            <div class="form-item">
              <div class="label">
                确认已转入美食通数量
              </div>
              <input type="text" v-model="form.bpt_num" placeholder="输入数量">
            </div>
            <div class="form-item">
              <div class="label">
                申请美食通钱包地址
              </div>
              <input type="text" v-model="form.bpt_address" placeholder="输入或粘贴钱包地址">
            </div>
          </div>
          <div class="sbm-btn-box">
            <x-button type="warn" class="base-btn" @click.native="submitFrom" :disabled="btnLoading"
                      :show-loading="btnLoading">下一步
            </x-button>
          </div>
        </div>
      </div>
      <router-view></router-view>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import sel from 'components/sel/index'
  import {limitFloating} from 'js/mixin'

  export default {
    name: "index",
    components: {
      slide,
      sel
    },
    data() {
      return {
        conditionDts: [
          '超级节点：48250GRT+8750TT',
          '高级节点：19300GRT+3500TT',
          '中级节点：7720GRT+1400TT',
          '动力节点：1930GRT+350TT'
        ],
        form: {
          type_id: '',
          weixin: '',
          /*recommend_name: '',
          recommend_mobile: '',*/
          grt_address: '',
          grt_num: '',
          tt_address: '',
          tt_num: '',
          bpt_address: '',
          bpt_num: '',
          recommend_mobile: ''
        },
        nodeSelData: [],
        btnLoading: false,
        recommend_name: ''
      }
    },
    methods: {
      getNodeSel() {
        http.post('/node/type-list', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.nodeSelData = res.content
          let nodeInfo = this.nodeSelData[0]
          this.form.type_id = nodeInfo.id
          this.form.grt_num = nodeInfo.grt
          this.form.tt_num = nodeInfo.tt
          this.form.bpt_num = nodeInfo.bpt
        })
      },
      changeNode(item) {
        this.form.type_id = item.id
        this.form.grt_num = item.grt
        this.form.tt_num = item.tt
        this.form.bpt_num = item.bpt
      },
      submitFrom() {
        if (!this.form.weixin) {
          this.$vux.toast.show('微信号必填')
          return
        }
        let grt = Number(this.form.grt_num)
        let tt = Number(this.form.tt_num)
        let bpt = Number(this.form.bpt_num)
        if (grt + tt + bpt <= 0) {
          this.$vux.toast.show('请输入数量')
          return
        }
        if (grt && !this.form.grt_address) {
          this.$vux.toast.show('请输入贵人通钱包地址')
          return
        }
        if (tt && !this.form.tt_address) {
          this.$vux.toast.show('请输入茶通钱包地址')
          return
        }
        if (bpt && !this.form.bpt_address) {
          this.$vux.toast.show('请输入美食通钱包地址')
          return
        }
        this.applyNode()
      },
      applyNode() {
        this.btnLoading = true
        http.post('/node/apply', this.form, (res) => {
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
      }
    },
    created() {
      this.getNodeSel()
    },
    watch: {
      'form.grt_num'(v) {
        let n = limitFloating(v)
        this.form.grt_num = n
      },
      'form.tt_num'(v) {
        let n = limitFloating(v)
        this.form.tt_num = n
      },
      'form.bpt_num'(v) {
        let n = limitFloating(v)
        this.form.bpt_num = n
      },
    },
    computed: {
      isShowRecommend() {
        return this.form.type_id !== '1'
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
      .address-btn
        display flex
        justify-content space-between
        margin-top 5px
        margin-bottom 15px
        li
          width 48%
          padding 10px 0
          border-radius 10px
          font-size $font-size-medium
          text-align center
          color white
          line-height 1.25rem
          box-shadow 0px 2px 6px 0px rgba(255, 181, 67, .5)
        .grt
          background #ff4800
        .tt
          background #ffb543
      .condition
        font-size $font-size-small-s
        margin-bottom 20px
        .normal
          &:before
            content ''
            margin-right 5px
            display inline-block
            width 8px
            height 8px
            background $color-text-minor
        .added
          color $color-theme
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
