import { login, logout, getInfo } from '@/api/login'
import { getRolePurview } from '@/api/system'
import { getToken, setToken, removeToken } from '@/utils/auth'
import { asyncRouterMap, constantRouterMap } from '@/router/index'

const user = {
  state: {
    token: getToken(),
    name: '',
    avatar: '',
    roles: [],
    routers: constantRouterMap,
    addRouters: [],
    buttons: {}
  },

  mutations: {
    SET_TOKEN: (state, token) => {
      state.token = token
    },
    SET_NAME: (state, name) => {
      state.name = name
    },
    SET_AVATAR: (state, avatar) => {
      state.avatar = avatar
    },
    SET_ROLES: (state, roles) => {
      state.roles = roles
    },
    SET_ROUTERS: (state, routers) => {
      state.addRouters = routers
      state.routers = constantRouterMap.concat(routers)
    },
    SET_BUTTONS: (state, data) => {
      state.buttons = { ...state.buttons, ...data }
    }
  },

  actions: {
    // 登录
    Login({ commit }, userInfo) {
      const username = userInfo.username.trim()
      return new Promise((resolve, reject) => {
        login(username, userInfo.password).then(response => {
          if (response.code !== 0) reject()
          const data = response.content
          setToken(data.accessToken)
          commit('SET_TOKEN', data.accessToken)
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 获取用户信息
    GetInfo({ commit, state }) {
      return new Promise((resolve, reject) => {
        getInfo().then(response => {
          const data = response.content
          if (data.name.length > 0) { // 验证返回的roles是否是一个非空数组
            commit('SET_ROLES', data.roleId)
          } else {
            reject('getInfo: roles must be a non-null array !')
          }
          commit('SET_NAME', data.name)
          commit('SET_AVATAR', 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif?imageView2/1/w/80/h/80')
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 登出
    LogOut({ commit, state }) {
      return new Promise((resolve, reject) => {
        logout().then(() => {
          commit('SET_TOKEN', '')
          commit('SET_ROLES', [])
          removeToken()
          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 前端 登出
    FedLogOut({ commit }) {
      return new Promise(resolve => {
        commit('SET_TOKEN', '')
        removeToken()
        resolve()
      })
    },

    // 动态路由
    GenerateRoutes({ commit }, data) {
      return new Promise(resolve => {
        const { roles } = data
        const accessedRouters = []
        // 超管无视权限
        if (roles === 1) {
          for (var i = 0; i < 12; i++) {
            accessedRouters.push(asyncRouterMap[i])
            if (i === 8) {
              accessedRouters.push(asyncRouterMap[40])
              accessedRouters.push(asyncRouterMap[42])
            }
          }
          accessedRouters.push(asyncRouterMap[38])
          accessedRouters.push(asyncRouterMap[37])
          accessedRouters.push(asyncRouterMap[404])
          getRolePurview(roles).then(res => {
            const routersInfo = res.content
            routersInfo.forEach((item, index, arr) => {
              item.isHave = 1
              for (var i = 0; i < item.child.length; i++) {
                item.child[i].isHave = 1
              }
              commit('SET_BUTTONS', { [item.id]: item })
            })
            commit('SET_ROUTERS', accessedRouters)
            resolve()
          })
          return
        }
        getRolePurview(roles).then(res => {
          const routersInfo = res.content
          routersInfo.forEach((item, index, arr) => {
            if (item.isHave === 1) {
              accessedRouters.push(asyncRouterMap[item.id])
              commit('SET_BUTTONS', { [item.id]: item })
            }
          })
          asyncRouterMap[0].redirect = accessedRouters[0].path
          accessedRouters.push(asyncRouterMap[0])
          accessedRouters.push(asyncRouterMap[404])
          commit('SET_ROUTERS', accessedRouters)
          resolve()
        })
      })
    }
  }
}

export default user
