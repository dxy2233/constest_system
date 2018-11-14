<template>
  <slide>
    <div class="address-submit">
      <loading :show="show" text=""></loading>
      <app-header>
        <!--<span slot="left" class="icon-back" @click="backPage"></span>-->
        收货地址
      </app-header>
      <div class="h-main">
        <div class="form">
          <div class="form-item">
            <div class="label">
              收货人姓名
              <span class="must">*</span>
              <br>
              <span>为您邮寄产品</span>
            </div>
            <input type="text" v-model="form.consignee" placeholder="输入收货人姓名">
          </div>
          <div class="form-item">
            <div class="label">
              收货人电话
              <span class="must">*</span>
            </div>
            <input type="text" onkeyup="(this.v=function(){this.value=this.value.replace(/[^0-9-]+/,'');}).call(this)"
                   onblur="this.v()"
                   v-model="form.consignee_mobile" placeholder="输入收货人电话" maxlength="11">
          </div>
          <div class="form-item">
            <div class="label">
              收货人地址
              <span class="must">*</span>
            </div>
            <sel :dataList="provinceList" placeholder="请选择"
                 value="id" label="areaname" @changeSel="changeProvince" :select="this.form.area_province_id"></sel>
          </div>
          <div class="form-item">
            <sel :dataList="cityList" placeholder="请选择" @changeSel="changeCity"
                 value="id" label="areaname" :select="this.form.area_city_id"></sel>
          </div>
          <div class="form-item">
            <input type="text" v-model="form.address" placeholder="详细地址">
          </div>
          <div class="form-item">
            <input type="text" v-model="form.zip_code" placeholder="邮编">
          </div>

          <div class="sbm-btn-box">
            <!--<button class="base-btn" @click="submitAddressFrom">下一步</button>-->
            <x-button type="warn" class="base-btn" @click.native="submitAddressFrom" :show-loading="btnLoading">下一步
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
  import {Loading} from 'vux'

  export default {
    name: "index",
    components: {
      slide,
      sel,
      Loading
    },
    data() {
      return {
        form: {
          consignee: '',
          consignee_mobile: '',
          area_province_id: '',
          area_city_id: '',
          address: '',
          zip_code: ''
        },
        // addressInfo: {},
        provinceList: [],
        cityList: [],
        btnLoading: false,
        show: true

      }
    },
    methods: {
      changeProvince(item) {
        this.form.area_province_id = item.id
      },
      changeCity(item){
        this.form.area_city_id = item.id
      },
      backPage() {
        this.$emit('close', true)
      },
      getAddressInfo() {
        http.post('/user/address-info', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.form.consignee = res.content.consignee
          this.form.consignee_mobile = res.content.consigneeMobile
          this.form.area_province_id = res.content.areaProvinceId
          this.form.area_city_id = res.content.areaCityId
          this.form.address = res.content.address
          this.form.zip_code = res.content.zipCode
        })
      },
      getProvinceList() {
        http.post('/area/area/get-city-list', {}, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.provinceList = res.content
          if (this.$route.params.type === '0') {//添加
            this.form.area_province_id = this.provinceList[0].id
          } else {
            this.getAddressInfo()
          }
        })
      },
      getCityList() {
        http.post('/area/area/get-city-list', {
          id: this.form.area_province_id
        }, (res) => {
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.cityList = res.content
          this.form.area_city_id = this.cityList[0].id
        })
      },
      submitAddressFrom() {
        if (!this.form.consignee) {
          this.$vux.toast.show('请输入收货人姓名')
          return
        }
        if (!this.form.consignee_mobile || !(/^1\d{10}$/.test(this.form.consignee_mobile))) {
          this.$vux.toast.show('请输入有效的电话号码')
          return
        }
        if (!this.form.area_province_id || !this.form.area_city_id || !this.form.address || !this.form.zip_code) {
          this.$vux.toast.show('请完善收货人地址')
          return
        }
        this.btnLoading = true
        this.saveAddress()
      },
      saveAddress() {
        http.post('/user/address-save', this.form, (res) => {
          this.btnLoading = false
          if (res.code !== 0) {
            this.$vux.toast.show(res.msg)
            return
          }
          this.$vux.toast.show({
            text: res.msg,
            type: 'success'
          })
          setTimeout(()=>{
            this.$router.back()
          },1500)

        })
      }
    },
    created() {
      this.getProvinceList()
    },
    watch: {
      'form.area_province_id'(v) {
        this.getCityList()
      },
      'form.area_city_id'(c, o) {
        if (c&&!o){
          this.show = false
        }
      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .address-submit
    fixed-full-screen()
    overflow auto
    .app-header
      border-bottom 1px solid $color-border
      background $color-background-sub
    .form
      padding 20px $space-box
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
