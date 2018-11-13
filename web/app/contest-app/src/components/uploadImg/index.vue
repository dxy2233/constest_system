<template>
  <div class="upload-img">
    <loading :show="show"></loading>
    <input class="file-ipt" type="file" @change='add_img'accept="image/*" capture="camera">
  </div>
</template>

<script>
  import axios from 'axios'
  import { Loading } from 'vux'
  import {base} from 'js/constant'

  export default {
    name: "index",
    components: {
      Loading
    },
    props: {
      url: {
        type: String,
        default: base.url+'/upload/upload/image'
      },
      fileName: {
        type: String,
        default: 'image_file'
      },
    },
    data(){
      return{
        show:false,
        accept: 'image/gif, image/jpeg, image/png, image/jpg',
      }
    },
    methods: {
      getFileUrl(obj) {
        let url;
        url = window.URL.createObjectURL(obj.files.item(0));
        return window.URL.createObjectURL(obj.files.item(0));
      },
      add_img(event) {
        let file = event.target.files[0];
        if(this.accept.indexOf(file.type) === -1){
          this.$vux.toast.show('请选择我们支持的图片格式')
          return
        }
        if(file.size>3145728){
          this.$vux.toast.show('请选择3M以内的图片')
          return
        }
        this.show = true
        let param = new FormData();
        param.append(this.fileName, file, file.name);
        param.append('type','identify');
        // console.log(param.get('image_file')); //FormData私有类对象，访问不到，可以通过get判断值是否传进去
        let config = {
          headers: {'Content-Type': 'multipart/form-data'}
        };  //添加请求头
        axios.post(this.url, param, config)
          .then(response => {
            this.show = false
            // console.log(response.data);
            if (response.data.code !== 0) {
              this.$vux.toast.show(response.data.msg)
              return
            }
            this.$emit('success', response.data.content, this.getFileUrl(event.srcElement))
          })
      }
    }
  }
</script>

<style lang="stylus" rel="stylesheet/stylus">
  @import "~stylus/variable"
  @import "~stylus/mixin"
  .upload-img
    width 100%
    height 100%
    .file-ipt
      position absolute
      top 0
      bottom 0
      width 100%
      opacity:0
</style>
