import Vue from 'vue'
import Router from 'vue-router'

// in development-env not use lazy-loading, because lazy-loading too many pages will cause webpack hot update too slow. so only in production use lazy-loading;
// detail: https://panjiachen.github.io/vue-element-admin-site/#/lazy-loading

Vue.use(Router)

/* Layout */
import Layout from '../views/layout/Layout'

/**
* hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
* alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
*                                if not set alwaysShow, only more than one route under the children
*                                it will becomes nested mode, otherwise not show the root menu
* redirect: noredirect           if `redirect:noredirect` will no redirect in the breadcrumb
* name:'router-name'             the name is used by <keep-alive> (must set!!!)
* meta : {
    title: 'title'               the name show in submenu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar,
  }
**/
export const constantRouterMap = [
  { path: '/login', component: () => import('@/views/login/index'), hidden: true },
  { path: '/404', component: () => import('@/views/404'), hidden: true },

  {
    path: '/',
    component: Layout,
    redirect: '/user',
    name: '用户管理',
    // hidden: true,
    children: [{
      path: 'user',
      name: 'UserManagement',
      component: () => import('@/views/userManagement/index'),
      meta: { title: '用户管理', icon: 'menu' }
    }]
  },

  {
    path: '/nodemangement',
    component: Layout,
    children: [
      {
        path: '',
        name: 'NodeManagement',
        component: () => import('@/views/NodeManagement/index'),
        meta: { title: '节点管理', icon: 'menu' }
      }
    ]
  },

  {
    path: '/poll',
    component: Layout,
    children: [
      {
        path: '',
        name: 'PollManagement',
        component: () => import('@/views/PollManagement/index'),
        meta: { title: '投票管理', icon: 'form' }
      }
    ]
  },

  {
    path: '/assets',
    component: Layout,
    children: [
      {
        path: '',
        name: 'AssetsManagement',
        component: () => import('@/views/assetsManagement/index'),
        meta: { title: '资产管理', icon: 'form' }
      }
    ]
  },

  {
    path: '/finance',
    component: Layout,
    children: [
      {
        path: '',
        name: 'Finance',
        component: () => import('@/views/finance/index'),
        meta: { title: '财务流水', icon: 'form' }
      }
    ]
  },

  {
    path: '/notice',
    component: Layout,
    children: [
      {
        path: '',
        name: 'NoticeManagement',
        component: () => import('@/views/noticeManagement/index'),
        meta: { title: '公告管理', icon: 'form' }
      }
    ]
  },

  {
    path: '/nodecheck',
    component: Layout,
    children: [
      {
        path: '',
        name: 'NodeCheck',
        component: () => import('@/views/nodeCheck/index'),
        meta: { title: '节点审核', icon: 'form' }
      }
    ]
  },

  {
    path: '/verified',
    component: Layout,
    children: [
      {
        path: '',
        name: 'Verified',
        component: () => import('@/views/verified/index'),
        meta: { title: '实名认证', icon: 'form' }
      }
    ]
  },

  {
    path: '/transfer',
    component: Layout,
    children: [
      {
        path: '',
        name: 'Transfer',
        component: () => import('@/views/transfer/index'),
        meta: { title: '转账审核', icon: 'form' }
      }
    ]
  },

  { path: '*', redirect: '/404', hidden: true }
]

export default new Router({
  // mode: 'history', //后端支持可开
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
})