import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

let baseRouter = [
  {
    path: '/',
    redirect: '/home'
  },
  {
    path: '/login',
    component: () => import('views/login/index'),
  },
  {
    path: '/setpsw',
    component: () => import('views/personal/psw/set'),
  },
]
export let mainRouter = [
  {
    path: '/home',
    name: '首页',
    icon: 'icon-home',
    component: () => import('views/home/index'),
    children: [
      {
        path: 'node',
        component: () => import('views/home/nodeRank/index'),
        children: [
          {
            path: 'dts:id',
            component: () => import('views/home/nodeDetails/index'),
            children: [
              {
                path: 'voting',
                component: () => import('views/home/votingDetails/index'),
              },
            ]
          },
          {
            path: 'rule',
            component: () => import('views/home/ruleDescription/index'),
          }
        ]
      },
      {
        path: 'notice',
        component: () => import('views/home/noticeList/index'),
        children: [
          {
            path: 'dts:id',
            component: () => import('views/home/noticeDetails/index'),
          }
        ]
      },
      {
        path: 'contribute',
        component: () => import('views/home/contribute/index'),
        children: [
          {
            path: 'rule',
            component: () => import('views/home/rewardRules/index'),
          }
        ]
      },
      {
        path: 'vote',
        component: () => import('views/home/vote/index'),
      }
    ]
  },
  {
    path: '/assets',
    name: '资产',
    icon: 'icon-money',
    component: () => import('views/assets/index'),
    /*meta: {
      keepAlive: false // 不需要缓存
    },*/
    children:[
      {
        path: 'dts:id',
        component: () => import('views/assets/assetsDetails/index'),
        children:[
          {
            path: 'frozen',
            component: () => import('views/assets/frozen/index'),
          },
          {
            path: 'collect',
            name:'collect',
            component: () => import('views/assets/collect/index'),
          },
          {
            path: 'transfer',
            component: () => import('views/assets/transfer/index'),
          }
        ]
      },
      /*{
        path: 'collect',
        name:'collect',
        component: () => import('views/assets/collect/index'),
      },
      {
        path: 'transfer',
        component: () => import('views/assets/transfer/index'),
      }*/
    ]
  },
  {
    path: '/personal',
    name: '我的',
    icon: 'icon-my',
    component: () => import('views/personal/index'),
    children: [
      {
        path: 'node',
        // component: () => import('views/personal/node/index'),
        component: () => import('components/emptyRouter/index'),
        hidden: true,
        children: [
          {
            path: 'index',
            component: () => import('views/personal/node/index'),
            children:[
              {
                path: 'interests',
                component: () => import('views/personal/node/interests'),
              },
              {
                path: 'invite',
                component: () => import('views/personal/node/invite'),
              },
              {
                path: 'edit',
                component: () => import('views/personal/node/edit'),
              },
              {
                path: 'upgrade',
                component: () => import('views/personal/node/upgrade'),
              },
              {
                path: 'record',
                component: () => import('views/personal/node/record'),
              },
              {
                path: 'fail',
                component: () => import('views/personal/node/fail'),
              },
              {
                path: 'wait',
                component: () => import('views/personal/node/wait'),
              },
            ]
          },
          {
            path: 'fail',
            component: () => import('views/personal/node/fail'),
          },
          {
            path: 'wait',
            component: () => import('views/personal/node/wait'),
          },
        ]
      },
      {
        path:'applynode',
        component: () => import('views/personal/node/applynode'),
        children:[
          {
            path: 'rules',
            component: () => import('views/personal/node/rules'),
          },
          {
            path: 'agreement',
            component: () => import('views/personal/node/agreement'),
          },
          {
            path: 'submit',
            component: () => import('views/personal/node/submit'),
          },
          {
            path: 'address',
            component: () => import('views/assets/collect/index'),
          },
        ]
      },
      {
        path: 'identify',
        component: () => import('components/emptyRouter/index'),
        children: [
          {
            path: 'fail',
            component: () => import('views/personal/identify/fail'),
          },
          {
            path: 'wait',
            component: () => import('views/personal/identify/wait'),
          },
          {
            path: 'success',
            component: () => import('views/personal/identify/success'),
          },
          {
            path: 'submit',
            component: () => import('views/personal/identify/submit'),
          }
        ]
      },
      {
        path: 'psw',
        component: () => import('components/emptyRouter/index'),
        children: [
          {
            path: 'index',
            component: () => import('views/personal/psw/index'),
            children:[
              {
                path: 'reset',
                component: () => import('views/personal/psw/reset'),
              },
              {
                path: 'revise',
                component: () => import('views/personal/psw/revise'),
              },
            ]
          },
          {
            path: 'set',
            component: () => import('views/personal/psw/set'),
          },


        ]
      },
      {
        path:'voucher',
        component: () => import('views/personal/voteVoucher/index'),
      },
      {
        path:'vote',
        component: () => import('views/personal/vote/index'),
        children:[
          {
            path:'redeem',
            component: () => import('views/personal/vote/redeem'),
          }
        ]
      },
      {
        path:'rcmd',
        component: () => import('views/personal/recommend/index'),
        children:[
          {
            path:'record',
            component: () => import('views/personal/recommend/record'),
          }
        ]
      },
      {
        path:'set',
        component: () => import('views/personal/set/index'),
        children:[
          {
            path:'about',
            component: () => import('views/personal/set/about'),
          }
        ]
      },
      {
        path:'service',
        component: () => import('views/personal/service/index'),
      },
      {
        path:'address',
        component: () => import('views/personal/address/index'),
        children:[
          {
            path:'edit:type',
            component: () => import('views/personal/address/submit'),
          }
        ]
      }
    ]
  },

]

export default new Router({
  routes: baseRouter.concat(mainRouter)
})

