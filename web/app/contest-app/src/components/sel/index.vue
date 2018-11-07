<template>
  <div class="sel">
    <div class="sel-box" @click="showPop=true">
      <input type="text" :value="selectedLabel" :placeholder="placeholder">
    </div>
    <div class="popup">
      <transition name="fade">
        <div class="mask" v-if="showPop" @click="showPop=false"></div>
      </transition>
      <slide>
        <ul class="popup-content" v-if="showPop">
          <li v-for="(item,index) in dataList" :class="{'act':item[value]===selected}" @click="selectedItem(item)">
            {{item[label]}}
          </li>
        </ul>
      </slide>
    </div>
  </div>
</template>

<script>
  import slide from 'components/slide/bottom'

  export default {
    name: "index",
    components: {
      slide,
    },
    props: {
      dataList: {
        type: Array,
        default: function () {
          // return [];//或者是return {}
          return []
        }
      },
      label: {
        type: String,
        default: 'label'
      },
      value: {
        type: String,
        default: 'value'
      },
      placeholder:{
        type:String,
        default:''
      },
      select:{
        type:String,
        default:''
      }
    },
    data() {
      return {
        selected: '',
        selectedLabel: '',
        showPop: false
      }
    },
    methods: {
      openPop(){
        this.showPop = true
      },
      selectedItem(item) {
        // console.log(item)
        if (item.selected === this.selected) return
        this.selected = item[this.value]
        this.selectedLabel = item[this.label]
        this.showPop = false
        this.$emit('changeSel', item)
      }
    },
    computed: {
    },
    created(){
      if (this.select){
        let idx = this.dataList.findIndex((item)=>{
          return this.select===item[this.value]
        })
        this.selected = this.select
        this.selectedLabel = this.dataList[idx][this.label]
      }
    }
  }
</script>

<style scoped lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .fade-enter-active, .fade-leave-active {
    transition: opacity .3s;
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
  }
  .sel
    position relative
    .sel-box
      /*height 30px*/
      width 100%
    .popup
      .mask
        position fixed
        top 0
        bottom 0
        left 0
        right 0
        z-index 100
        background rgba(0, 0, 0, 0.5)
        transition-compatible(.3s)
      .popup-content
        position fixed
        bottom 0
        left 0
        right 0
        z-index 101
        background white
        margin-bottom -1px
        overflow hidden
        li
          text-align center
          line-height 40px
          margin-left $space-box
          margin-right $space-box
          border-bottom 1px solid $color-border
          &.act
            color $color-theme

</style>
