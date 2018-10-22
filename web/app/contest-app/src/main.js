// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import FastClick from 'fastclick'
import router from './router'
import App from './App'
import store from './store'
import {ToastPlugin} from 'vux'
import {Icon, XHeader, XButton, LoadMore} from 'vux'
import VueQuickLoadmore from 'vue-quick-loadmore';
import appHeader from 'components/appHeader/index'
import loadMore from 'components/loadmore/index'

Vue.use(VueQuickLoadmore)

Vue.component('icon', Icon)
Vue.component('x-header', XHeader)
Vue.component('x-button', XButton)
Vue.component('load-more', loadMore)
Vue.component('app-header', appHeader)
import 'stylus/index.styl'

Vue.use(ToastPlugin, {time: 1500, type: 'text', width: '12em'})

FastClick.attach(document.body)

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app-box')
