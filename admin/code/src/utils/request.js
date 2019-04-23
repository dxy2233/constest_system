import axios from 'axios'
// import { Message, MessageBox } from 'element-ui'
import { Message } from 'element-ui'
import store from '../store'
import { getToken } from '@/utils/auth'
import qs from 'qs'

// axios.defaults.headers['Content-Type'] = 'application/x-www-form-urlencoded'

// 创建axios实例
const service = axios.create({
  baseURL: process.env.BASE_API, // api 的 base_url
  timeout: 5000, // 请求超时时间
  transformRequest: [function(data, headers) {
    data = qs.stringify(data)
    return data
  }] // QS.stringify
})

// request拦截器
service.interceptors.request.use(
  config => {
    if (store.getters.token) {
      config.headers['Authorization'] = `Bearer ${getToken()}` // 让每个请求携带自定义token 请根据实际情况自行修改
    }
    return config
  },
  error => {
    // Do something with request error
    console.log(error) // for debug
    Promise.reject(error)
  }
)

// response 拦截器
service.interceptors.response.use(
  response => {
    if (response.data.type === 'application/octet-stream') {
      return response
    }
    const res = response.data
    // 0:正常 -1:么有登录 1:有错 2:么有权限
    if (res.code === -1) {
      Message({
        message: res.msg,
        type: 'error',
        duration: 1000
      })
      setTimeout(() => {
        store.dispatch('FedLogOut').then(() => {
          location.reload()
        })
      }, 1000)
      return Promise.reject('error')
    }
    if (res.code !== 0 && res.code !== -1) {
      Message({
        message: res.msg,
        type: 'error',
        duration: 5 * 1000
      })
    } else {
      return response.data
    }
  },
  error => {
    console.log('err' + error) // for debug
    Message({
      message: error.msg,
      type: 'error',
      duration: 5 * 1000
    })
    return Promise.reject(error)
  }
)

export default service
