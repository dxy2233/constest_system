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
  { path: '/404', component: () => import('@/views/404'), hidden: true }

  // {
  //   path: '/',
  //   component: Layout,
  //   redirect: '/admin',
  //   name: '统计报表',
  //   children: [{
  //     path: 'admin',
  //     name: 'Admin',
  //     component: () => import('@/views/admin/index'),
  //     meta: { title: '统计报表', icon: '数据' }
  //   }]
  // },
  //
  // {
  //   path: '/user',
  //   component: Layout,
  //   children: [
  //     {
  //       path: '',
  //       name: 'UserManagement',
  //       component: () => import('@/views/userManagement/index'),
  //       meta: { title: '用户管理', icon: '用户管理' }
  //     }
  //   ]
  // },
  //
  // {
  //   path: '/nodemangement',
  //   component: Layout,
  //   children: [
  //     {
  //       path: '',
  //       name: 'NodeManagement',
  //       component: () => import('@/views/NodeManagement/index'),
  //       meta: { title: '节点管理', icon: '节点-子流程' }
  //     }
  //   ]
  // },
  //
  // {
  //   path: '/poll',
  //   component: Layout,
  //   children: [
  //     {
  //       path: '',
  //       name: 'PollManagement',
  //       component: () => import('@/views/PollManagement/index'),
  //       meta: { title: '投票管理', icon: '投票' }
  //     }
  //   ]
  // },
  //
  // {
  //   path: '/assets',
  //   component: Layout,
  //   children: [
  //     {
  //       path: '',
  //       name: 'AssetsManagement',
  //       component: () => import('@/views/assetsManagement/index'),
  //       meta: { title: '资产管理', icon: '我的资产' }
  //     }
  //   ]
  // },
  //
  // {
  //   path: '/finance',
  //   component: Layout,
  //   children: [
  //     {
  //       path: '',
  //       name: 'Finance',
  //       component: () => import('@/views/finance/index'),
  //       meta: { title: '财务流水', icon: '财务' }
  //     }
  //   ]
  // },
  //
  // {
  //   path: '/notice',
  //   component: Layout,
  //   children: [
  //     {
  //       path: '',
  //       name: 'NoticeManagement',
  //       component: () => import('@/views/noticeManagement/index'),
  //       meta: { title: '公告管理', icon: '公告' }
  //     }
  //   ]
  // },
  //
  // {
  //   path: '/nodecheck',
  //   component: Layout,
  //   children: [
  //     {
  //       path: '',
  //       name: 'NodeCheck',
  //       component: () => import('@/views/nodeCheck/index'),
  //       meta: { title: '节点审核', icon: 'form' }
  //     }
  //   ]
  // },
  //
  // {
  //   path: '/verified',
  //   component: Layout,
  //   children: [
  //     {
  //       path: '',
  //       name: 'Verified',
  //       component: () => import('@/views/verified/index'),
  //       meta: { title: '实名认证', icon: '实名认证' }
  //     }
  //   ]
  // },
  //
  // {
  //   path: '/transfer',
  //   component: Layout,
  //   children: [
  //     {
  //       path: '',
  //       name: 'Transfer',
  //       component: () => import('@/views/transfer/index'),
  //       meta: { title: '转账审核', icon: '单笔转账' }
  //     }
  //   ]
  // },
  //
  // {
  //   path: '/member',
  //   component: Layout,
  //   children: [
  //     {
  //       path: '',
  //       name: 'Member',
  //       component: () => import('@/views/member/index'),
  //       meta: { title: '成员列表', icon: '群组' }
  //     }
  //   ]
  // },
  //
  // {
  //   path: '/purview',
  //   component: Layout,
  //   children: [
  //     {
  //       path: '',
  //       name: 'Purview',
  //       component: () => import('@/views/purview/index'),
  //       meta: { title: '权限管理', icon: '权限管理' }
  //     }
  //   ]
  // },
  //
  // {
  //   path: '/log',
  //   component: Layout,
  //   children: [
  //     {
  //       path: '',
  //       name: 'Log',
  //       component: () => import('@/views/log/index'),
  //       meta: { title: '操作日志', icon: '用户日志' }
  //     }
  //   ]
  // }

  // { path: '*', redirect: '/404', hidden: true }
]

export default new Router({
  // mode: 'history', //后端支持可开
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
})

export const asyncRouterMap = {
  0: {
    path: '/',
    redirect: '/admin'
  },

  1: {
    path: '/admin',
    component: Layout,
    children: [{
      path: '',
      name: 'Admin',
      component: () => import('@/views/admin/index'),
      meta: { title: '统计报表', icon: '数据' }
    }]
  },

  2: {
    path: '/user',
    component: Layout,
    children: [
      {
        path: '',
        name: 'UserManagement',
        component: () => import('@/views/userManagement/index'),
        meta: { title: '用户管理', icon: '用户管理' }
      }
    ]
  },

  3: {
    path: '/nodemangement',
    component: Layout,
    children: [
      {
        path: '',
        name: 'NodeManagement',
        component: () => import('@/views/NodeManagement/index'),
        meta: { title: '节点管理', icon: '节点-子流程' }
      }
    ]
  },

  4: {
    path: '/poll',
    component: Layout,
    children: [
      {
        path: '',
        name: 'PollManagement',
        component: () => import('@/views/PollManagement/index'),
        meta: { title: '投票管理', icon: '投票' }
      }
    ]
  },

  5: {
    path: '/assets',
    component: Layout,
    children: [
      {
        path: '',
        name: 'AssetsManagement',
        component: () => import('@/views/assetsManagement/index'),
        meta: { title: '资产管理', icon: '我的资产' }
      }
    ]
  },

  6: {
    path: '/finance',
    component: Layout,
    children: [
      {
        path: '',
        name: 'Finance',
        component: () => import('@/views/finance/index'),
        meta: { title: '财务流水', icon: '财务' }
      }
    ]
  },

  7: {
    path: '/notice',
    component: Layout,
    children: [
      {
        path: '',
        name: 'NoticeManagement',
        component: () => import('@/views/noticeManagement/index'),
        meta: { title: '公告管理', icon: '公告' }
      }
    ]
  },

  8: {
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

  9: {
    path: '/verified',
    component: Layout,
    children: [
      {
        path: '',
        name: 'Verified',
        component: () => import('@/views/verified/index'),
        meta: { title: '实名认证', icon: '实名认证' }
      }
    ]
  },

  10: {
    path: '/transfer',
    component: Layout,
    children: [
      {
        path: '',
        name: 'Transfer',
        component: () => import('@/views/transfer/index'),
        meta: { title: '转账审核', icon: '单笔转账' }
      }
    ]
  },

  11: {
    path: '/member',
    component: Layout,
    children: [
      {
        path: '',
        name: 'Member',
        component: () => import('@/views/member/index'),
        meta: { title: '成员列表', icon: '群组' }
      }
    ]
  },

  38: {
    path: '/purview',
    component: Layout,
    children: [
      {
        path: '',
        name: 'Purview',
        component: () => import('@/views/purview/index'),
        meta: { title: '权限管理', icon: '权限管理' }
      }
    ]
  },

  37: {
    path: '/log',
    component: Layout,
    children: [
      {
        path: '',
        name: 'Log',
        component: () => import('@/views/log/index'),
        meta: { title: '操作日志', icon: '用户日志' }
      }
    ]
  },

  40: {
    path: '/nodeup',
    component: Layout,
    children: [
      {
        path: '',
        name: 'NodeUp',
        component: () => import('@/views/nodeUp/index'),
        meta: { title: '节点升级', icon: '用户日志' }
      }
    ]
  },

  404: { path: '*', redirect: '/404', hidden: true }
}
