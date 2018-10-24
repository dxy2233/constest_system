import axios from 'axios'
import qs from 'qs'
// import {Message} from 'element-ui'
import {base} from 'js/constant'

const service = axios.create({
  baseURL: base.url, // api的base_url
  timeout: 5000 // request timeout
})
service.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=UTF-8';

// request interceptor
service.interceptors.request.use(
  config => {
    let loginMsg = JSON.parse(sessionStorage.getItem('loginMsg'))
    // 判断是否login，
    if (loginMsg) {
      config.headers.Authorization = `Bearer ${loginMsg.accessToken}`
    } else {
      config.headers.Authorization = ''
    }
    // 格式化参数
    if (config.method === 'post') {
      config.data = qs.stringify(config.data);
    }
    return config
  },
  error => {
    // Do something with request error
    console.log(error) // for debug
    Promise.reject(error)
  }
)

// code状态码200判断
axios.interceptors.response.use(function (res) {
  return res;
}, function (err) {
  console.log(err)
  if (err && err.response) {
    switch (err.response.status) {
      case 400:
        err.msg = '请求错误'
        break

      case 401:
        err.msg = '未授权，请登录'
        break

      case 403:
        err.msg = '拒绝访问'
        break

      case 404:
        err.msg = `请求地址出错: ${err.response.config.url}`
        break

      case 408:
        err.msg = '请求超时'
        break

      case 500:
        err.msg = '服务器内部错误'
        break

      case 501:
        err.msg = '服务未实现'
        break

      case 502:
        err.msg = '网关错误'
        break

      case 503:
        err.msg = '服务不可用'
        break

      case 504:
        err.msg = '网关超时'
        break

      case 505:
        err.err = 'HTTP版本不受支持'
        break

      default:
    }

    alert(err.msg)
  }
  console.log(err)
  return Promise.reject(err);
});

export default service
