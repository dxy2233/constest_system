<template>
  <slide>
    <div class="vote">
      <!--<x-header :left-options="{backText: ''}" class="w-header">
        确认投票
      </x-header>-->
      <app-header>
        确认投票
      </app-header>
      <div class="h-main vote-wrapper">
        <div class="vote-form">
          <div class="form-item">
            <div class="label">所选节点</div>
            <div class="form-item-content">
              <div class="ipt-box node-box" @click="chooseNodeShow=true">
                <!--<input type="text" v-model="nodeName" disabled placeholder="请选择节点">-->
                <input type="text" v-model="nodeName" placeholder="请选择投票节点" disabled>
              </div>
            </div>
          </div>
          <div class="form-item" v-if="typeList.length">
            <div class="label">投票方式</div>
            <ul class="form-item-content type-list">
              <!--<li class="ipt-box" v-for="item in typeList">fa</li>-->
              <li class="ipt-box" v-for="(item,index) in typeList" :class="{'act':item.id ===currentType}"
                  @click="changeType(item.id,index)">
                <h4>{{item.name}}</h4>
                <p v-html="item.scaling">0.1GRT=1票 单次活动截止后可赎回</p>
                <x-icon v-show="item.id ===currentType" type="ios-checkmark-empty" size="40" class="act-icon"></x-icon>
              </li>
            </ul>
          </div>
          <div class="form-item">
            <div class="label">投票数量</div>
            <div class="form-item-content">
              <div class="ipt-box number-box">
                <input type="text" v-model="number" placeholder="请输入投票数量">
                <span class="all" @click="number=typeInfo.maxNumber">最大</span>
              </div>
            </div>
          </div>
          <div class="ps">
            <span v-if="typeInfo.showCurrency">可用GRT数量&nbsp;&nbsp;&nbsp;{{typeInfo.amount}}</span>
            <span v-else></span>
            <span>{{typeInfo.maxNumber+'票'}}</span>
          </div>
          <x-button type="warn" class="sbm-btn" @click.native="sbmVote">确定</x-button>
        </div>
      </div>
      <choose-node v-show="chooseNodeShow" @close="chooseNodeShow=false" :selectId="nodeId" @selectedNode="selectedNode"></choose-node>
      <valid-pay-psw v-if="validPswShow" @validSuccess="validPswSuccess" @close="validPswShow=false"></valid-pay-psw>
    </div>
  </slide>
</template>

<script>
  import slide from 'components/slide/index'
  import http from 'js/http'
  import chooseNode from 'views/home/vote/chooseNode'
  import validPayPsw from 'components/validPayPsw/index'
  import {mapState, mapMutations, mapGetters} from 'vuex'
  import {GetUrlParam} from 'js/mixin'

  export default {
    name: "index",
    components: {
      slide,
      chooseNode,
      validPayPsw
    },
    data() {
      return {
        typeList: [],
        number: '',
        currentType: 1,
        typeInfo: {},
        nodeName: '',
        nodeId: '',
        chooseNodeShow: false,
        validPswShow: false,
      }
    },
    methods: {
      clickAmount(value,cb) {
        if (!value) {
          cb('请输入投票数量')
          return
        }
        if (!(/^[1-9]\d*$/.test(value))) {
          cb('请输入有效的投票数量')
          return
        }
        if (value-this.typeInfo.maxNumber>0){
          cb('可用不足')
          return
        }
        cb('')
      },
      sbmVote() {
        if (!this.nodeId) {
          this.$vux.toast.show('请选择投票节点')
          return
        }
        this.clickAmount(this.number,(res)=>{
          if (res){
            this.$vux.toast.show(res)
            return
          }
          this.validPswShow = true
        })
      },
      validPswSuccess(payPsw) {
        http.post('/vote/submit', {
          node_id: this.nodeId,
          type: this.currentType,
          number: this.number,
          pass: payPsw
        }, (res) => {
          let type = res.code === 0 ? 'success' : 'warn'
          this.$vux.toast.show({
            text: res.msg,
            toast: 3000,
            type: type
          })
          if (res.code===0){
            this.$router.go(-1)
          }
          this.validPswShow = false
        })
      },
      getTypeList() {
        http.post('/vote/types', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.typeList = res.content
          this.currentType = this.typeList[0].id
          this.getTypeInfo()
        })
      },
      selectedNode(item) {
        this.nodeName = item.name
        this.nodeId = item.id
      },
      changeType(id, index) {
        this.currentType = id
        this.getTypeInfo()
      },
      getTypeInfo() {
        http.post('/vote/type-info', {
          type: this.currentType
        }, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.typeInfo = res.content
        })
      },
      pageInt(){
        if (!this.loginMsg){
          this.$router.push({
            path: `/login`
          })
          return
        }

        if (!this.typeList.length){
          this.getTypeList()
        }
        this.nodeId = this.$route.query.nodeId
        this.nodeName = this.$route.query.nodeName

      }
    },
    created() {
      this.pageInt()
    },
    activated() {
      this.pageInt()
    },
    computed: {
      ...mapGetters([
        "loginMsg",
      ]),
    },
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .vote
    fixed-full-screen()
    overflow auto
    & > .vote-wrapper
      padding-left $space-box
      padding-right $space-box
      .vote-form
        padding $space-box 0
      .form-item
        margin-bottom $space-box
        .label
          margin-bottom 8px
          color $color-text-minor
        .form-item-content
          border-radius 10px
          line-height 48px
          min-height 48px
          border 1px solid $color-border-sub
        .ipt-box
          padding 0 $space-box
        .node-box
          input
            line-height 48px
            width 100%
            background none
        .type-list
          overflow hidden
          li
            position relative
            line-height 18px
            border-top 1px solid $color-border-sub
            padding-top 10px
            padding-bottom 10px
            .act-icon
              position absolute
              right $space-box
              top 50%
              margin-top -20px
              color #858F9A
            &.act
              background #EAEAEA
            &:first-child
              border-top 0
            h4
              font-size $font-size-medium
            p
              margin-top 3px
              color $color-text-minor
              font-size $font-size-small-s

        .number-box
          padding-right 0
          display flex
          align-items center
          span
            display inline-block
            line-height 28px
            height 28px
            width 70px
            flex 0 0 70px
            box-sizing border-box
            border-left 1px solid $color-border-sub
            color $color-theme
            text-align center
          input
            line-height 48px
            flex 1
      .ps
        display flex
        justify-content space-between
        color $color-text-minor
        align-items center
      .sbm-btn
        margin-top 45px
        margin-bottom 15px
</style>
