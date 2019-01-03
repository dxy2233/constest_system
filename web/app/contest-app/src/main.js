// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import FastClick from 'fastclick'
import router from './router'
import App from './App'
import store from './store'
import {ToastPlugin} from 'vux'
import {Icon, XHeader, XButton, LoadMore} from 'vux'
// import VueQuickLoadmore from 'vue-quick-loadmore';
import appHeader from 'components/appHeader/index'
import loadMore from 'components/loadmore/index'
import {cancelLogin} from 'js/mixin'
import Mint from 'mint-ui';
import 'mint-ui/lib/style.css'
Vue.use(Mint);

import VueScroller from 'vue-scroller'
Vue.use(VueScroller)
// Vue.use(VueQuickLoadmore)

Vue.component('icon', Icon)
Vue.component('x-header', XHeader)
Vue.component('x-button', XButton)
Vue.component('load-more', loadMore)
Vue.component('app-header', appHeader)
import 'stylus/index.styl'

Vue.use(ToastPlugin, {time: 1500, type: 'text', width: '10em'})

FastClick.attach(document.body)

Vue.config.productionTip = false

router.beforeEach((to, from, next) => {
  if (store.state.loginMsg) {
    if (to.path === '/login'){
      cancelLogin()
    }
  }
  if (!store.state.loginMsg && (to.path.includes('assets')||to.path.includes('personal')||to.path.includes('home/vote'))) {
    next({ path: '/login' })
  } else {
    next()
  }
});

/* eslint-disable no-new */
new Vue({
  router,
  store,
  render: h => h(App),
  /*activated: function () {
    this.$setgoindex()
  },*/
}).$mount('#app-box')


/*

Vue.prototype.$setgoindex = function () {
  if (window.history.length <= 1) {
    if (location.href.indexOf('?') === -1) {
      window.location.href = location.href + '?goindex=true'
    } else if (location.href.indexOf('?') !== -1 && location.href.indexOf('goindex') === -1) {
      window.location.href = location.href + '&goindex=true'
    }
  }
}
*/
