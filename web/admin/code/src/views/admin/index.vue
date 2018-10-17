<template>
  <div class="dashboard-container">
    <div v-for="(item,index) in menus" :key="index" class="item">
      <img src="" alt="">
      <p>{{ item.name }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Admin',
  data() {
    return {
      menus: [
        { name: '活动管理', img: '' },
        { name: '联系人管理', img: '' },
        { name: '账号设置', img: '' }
      ]
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.dashboard {
  &-container {
    margin: 30px;
    display: flex;
    & .item {
      margin-right: 30px;
    }
    & img {
      display: block;
      width: 100px;
      height: 100px;
      border: 1px solid black;
    }
  }
}
</style>
