<template>
  <div class="personal">
    <div class="top">
      <div class="user">
        <!--<img src="/static/images/default-avatar.png" alt="" class="icon">-->
        <div class="avatar">
          <span class="icon-my"></span>
        </div>
        <div class="mobile">{{hidMobile(loginMsg?loginMsg.mobile:'')}}</div>
      </div>
      <!--<router-link tag="div" to="/personal/node" class="node"
                   style='background-image: url("/static/images/person-node.png")'>
      </router-link>-->
      <div class="node-brief">
        <div v-if="nodeInfo.status===-1" class="node_0">
          <img src="/static/images/personal-node/bg.png" alt="" class="bg">
          <div class="node-content">
            <div class="left">
              <h2>节点招募火热进行中</h2>
              <p>参与社区治理，享受节点权益与分红</p>
            </div>
            <div class="right">
              <router-link tag="button" to="/personal/applynode">去申请</router-link>
            </div>
          </div>
        </div>
        <div v-else class="node_x">
          <img :src="'/static/images/personal-node/bg'+nodeTypeId+'.png'" alt="" class="bg">
          <router-link tag="div" class="node-content" to="/personal/node/index" v-if="nodeInfo.status===1">
            <div class="img-box">
              <img :src="'/static/images/personal-node/icon_'+nodeTypeId+'.png'" alt="" class="img">
            </div>
            <div class="info">
              <h2>
                <span>{{nodeInfo.name}}</span>
                <span class="sign" v-if="nodeInfo.isTenure">任职</span>
              </h2>
              <p v-if="nodeInfo.typeId===1||nodeInfo.typeId===2">
                <span class="gray">{{nodeInfo.peopleNumber+' 人支持'}}</span>
                <span>{{nodeInfo.voteNumber+' 票'}}</span>
              </p>
            </div>
          </router-link>
          <div class="node-content" v-else>
            <div class="img-box">
              <img :src="'/static/images/personal-node/icon_'+nodeTypeId+'.png'" alt="" class="img">
            </div>
            <div class="info_x">
              <div class="left">
                <h2>{{nodeInfo.name}}</h2>
                <p>{{nodeInfo.statusStr}}</p>
              </div>
              <div class="right">
                <!--<button>查看</button>-->
                <router-link tag="button" :to="'/personal/node/'+nodePath">查看</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="bottom">
      <ul class="link-list">
        <router-link tag="li" to="/personal/vote">
          <span class="text">我的投票</span>
          <x-icon type="ios-arrow-right"></x-icon>
        </router-link>
        <router-link tag="li" to="/personal/voucher">
          <span class="text">投票券</span>
          <x-icon type="ios-arrow-right"></x-icon>
        </router-link>
        <router-link tag="li" to="/personal/rcmd">
          <span class="text">我的推荐</span>
          <x-icon type="ios-arrow-right"></x-icon>
        </router-link>
      </ul>

      <ul class="link-list">
        <router-link tag="li" to="/personal/psw/index">
          <span class="text">支付密码</span>
          <x-icon type="ios-arrow-right"></x-icon>
        </router-link>
        <router-link tag="li" :to="'/personal/identify/'+identifyPath">
          <span class="text">实名认证</span>
          <x-icon type="ios-arrow-right"></x-icon>
        </router-link>
        <router-link tag="li" to="/personal/address">
          <span class="text">收货地址</span>
          <x-icon type="ios-arrow-right"></x-icon>
        </router-link>
        <router-link tag="li" to="/personal/set">
          <span class="text">设置</span>
          <x-icon type="ios-arrow-right"></x-icon>
        </router-link>
      </ul>
      <!--<group>
        <cell-box is-link link="/personal/vote">
          <span class="text">我的投票</span>
        </cell-box>
        <cell-box is-link link="/personal/voucher">
          <span class="text">投票券</span>
        </cell-box>
        <cell-box is-link link="/personal/rcmd">
          <span class="text">我的推荐</span>
        </cell-box>
      </group>
      <group>
        <cell-box is-link link="/personal/psw/index">
          <span class="text">支付密码</span>
        </cell-box>
        <cell-box is-link :link="'/personal/identify/'+identifyPath">
          <span class="text">实名认证</span>
        </cell-box>
        <cell-box is-link link="/personal/set">
          &lt;!&ndash;<span class="icon icon-set"></span>&ndash;&gt;
          <span class="text">设置</span>
        </cell-box>
      </group>-->
    </div>

    <router-view></router-view>
  </div>
</template>

<script>
  import {Group, CellBox} from 'vux'
  import {mapState, mapMutations, mapGetters} from 'vuex'
  import {hidMobile} from 'js/mixin'
  import {mainRouter} from '@/router/index'
  import http from 'js/http'
  import psw from 'components/psw/index'

  export default {
    name: "personal",
    components: {
      Group,
      CellBox,
      psw
    },
    computed: {
      identifyPath() {
        if (!this.identifyMsg) return ''
        if (!this.identifyMsg.isIdentify) return 'submit'
        switch (this.identifyMsg.status) {
          case 0:
            return 'wait'
          case 1:
            return 'success'
          case 2:
            return 'fail'
        }

      },
      nodeTypeId() {
        if (!this.nodeInfo.typeId) return 1
        return this.nodeInfo.typeId

      },
      nodePath() {
        //0 停用 1 已生效 2 审核中 3 撤销 4 审核未通过 5 删除'
        if (!this.nodeInfo) return ''
        switch (this.nodeInfo.status) {
          case 2:
            return 'wait'
          case 4:
            return 'fail'
        }

      },
      ...mapGetters([
        "loginMsg",
        'myNodeInfo'
      ]),
    },
    methods: {
      getNodeInfo() {
        http.post('/node/info', {}, (res) => {
          // console.log(res.content)
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.nodeInfo = res.content
          localStorage.setItem("myNodeInfo", JSON.stringify(res.content));
          this.setMyNodeInfo(res.content)
        })
      },
      getIdentifyMsg() {
        http.post('/identify', {}, (res) => {
          // console.log(res)
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          let identify = {}
          if (res.content === '') {
            identify.isIdentify = false
          } else {
            identify = res.content
            identify.isIdentify = true
          }
          this.identifyMsg = identify
          localStorage.setItem("identifyMsg", JSON.stringify(identify));
          this.setIdentifyMsg(identify)
        })
      },
      pageInt() {
        if (!this.loginMsg) {
          this.$router.push({
            path: `/login`
          })
        } else {
          // console.log(this.loginMsg.isNode)
          /*if (!!this.loginMsg.isNode) {
            this.getNodeInfo()
          }*/
          this.getNodeInfo()
          // console.log(this.identifyMsg,JSON.stringify(this.identifyMsg)==='{}')
          /*if (JSON.stringify(identifyMsg)==='{}'){
            this.getIdentifyMsg()
          }*/
          this.identifyMsg = {}
          this.getIdentifyMsg()
        }

      },
      ...mapMutations({
        setIdentifyMsg: 'IDENTIFY_MSG',
        setMyNodeInfo: 'MY_NODE_INFO'
      })
    },
    data() {
      return {
        hidMobile: hidMobile,
        personalRouter: mainRouter[2],
        nodeInfo: {
          typeId:''
        },
        identifyMsg: {}
      }
    },
    created() {
      this.pageInt()
    },
    activated() {

    },
    watch: {
      '$route': function (t, f) {
        if (t.path === '/personal') {
          this.pageInt()
        }
      }
    },
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .personal
    position: absolute
    width: 100%
    top: 0
    bottom: 50px
    background $color-background-sub
    overflow auto
    .link-list
      background white
      margin-top 10px
      border-top 1px solid $color-border
      border-bottom 1px solid $color-border
      li
        display flex
        justify-content space-between
        align-items center
        line-height 55px
        padding-right $space-box
        margin-left $space-box
        border-top 1px solid $color-border
        margin-top -1px
        font-size $font-size-medium
        .vux-x-icon
          fill #e7effa
    & > .top
      padding $space-box
      background $color-background
      /*border-bottom 10px solid $color-background-sub*/
      .user
        margin-bottom 25px
        display flex
        align-items center
        padding-top 10px
        .avatar
          margin-right 15px
          width 60px
          height 60px
          border-radius 50%
          background #fff7ef
          font-size $font-size-large-x
          line-height 60px
          text-align center
          color $color-theme
        .mobile
          font-size $font-size-medium-x
      .node
        width 100%
        height 105px
        border-radius 6px
        background-position center
        background-size cover
    & > .bottom
      .weui-cells
        margin-top 10px
      .weui-cell
        padding-top 20px
        padding-bottom 20px
        .icon
          font-size 22px
          color $color-theme
        .text
          margin-left 10px
          font-size $font-size-small

    .node-brief
      position relative
      .bg
        width 100%
      /*box-shadow 0 4px 15px 4px RGBA(240, 208, 172, 0.5)*/
      .node-content
        position absolute
        top 15%
        bottom 25%
        left 8%
        right 8%
        display flex
        justify-content space-between
        align-items center
        h2
          font-size $font-size-medium-x
        p
          margin-top 10px
          font-size $font-size-small-s
      .node_0
        color #f4db9e
        button
          background none
          border 1px solid color #f4db9e
          color #f4db9e
          font-size $font-size-medium
          padding 5px 10px
          border-radius 20px
      .node_x
        color white
        .node-content
          display flex
          justify-content space-between
        .img-box
          flex 0 0 80px
          .img
            height 60px
        .info
          flex 1
          & > *
            display flex
            justify-content space-between
        .gray
          color bfbfbf
        .sign
          position relative
          top -15px
          border-radius 10px
          font-size $font-size-small-s
          background rgba(150, 150, 150, .3)
          padding 5px 10px
        .info_x
          align-items center
          flex 1
          justify-content space-between
          display flex
          p
            margin-top 2px
            color #bfbfbf
          button
            background none
            border 1px solid color #ddcfac
            color #ddcfac
            font-size $font-size-medium
            padding 5px 15px
            border-radius 20px
</style>


<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  @media only screen and (max-width: 320px)
    .personal
      .node-brief
        .node-content
          left 5%
          right 5%
          h2
            font-size $font-size-medium
          button
            font-size $font-size-small-s
        .node_x
          .sign
            top -10px
          .img-box .img
            height 50px


</style>
