import request from 'js/request'
import {cancelLogin} from 'js/mixin'
import router from '@/router'
// import { Message } from 'element-ui'
import store from '@/store/index'
import {ToastPlugin} from 'vux'


let http = {
  get(url, callback) {
    request({
      method: 'get',
      url: url,
    }).then((response) => {
      callback(response.data);
    }).catch((response) => {
      if (response.msg) {
        console.log(response.msg)
      }
    });
  },

  post(url, data, callback) {
    checkToken(() => {
      request({
        method: 'post',
        url: url,
        data: data
      }).then((response) => {
        if (response.data.code === -1) {
          cancelLogin()
          router.push({
            path: '/login',
          })
        }
        callback(response.data);
      }).catch((response) => {
        if (response.msg) {
          // console.log(response.msg)
          alert(response.msg)
        }
      });
    })

  },

}

window.refreshLock = false

let checkToken = function (callback) {
  if (window.refreshLock) {
    setTimeout(() => {
      checkToken(callback)
    }, 500)
    return
  }
  let loginMsg = JSON.parse(localStorage.getItem('loginMsg'))
  console.log(loginMsg)
  if (!!loginMsg) {
    let timestamp = parseInt(Date.parse(new Date()) / 1000)
    // loginMsg.expireTime = timestamp + 1000
    if (loginMsg.expireTime - timestamp > 0) {
      if (loginMsg.expireTime - timestamp > 1800) {
        callback()
      } else {
        window.refreshLock = true
        request({
          method: 'post',
          url: '/login/refresh-token',
          data: {refreshToken: loginMsg.refreshToken}
        }).then((response) => {
          response = response.data
          if (response.code === 0) {
            localStorage.setItem("loginMsg", JSON.stringify(response.content));
            store.commit('LOGIN_MSG', response.content)
          } else {
            cancelLogin()
          }
          window.refreshLock = false
          callback()
        }).catch((response) => {
          // window.refreshLock = false
          if (response.msg) {
            console.log(response.msg)
          }
        });
      }
    } else {
      cancelLogin()
      callback()
    }
  } else {
    callback()
  }
}

let refreshToken = (callback) => {
  console.log('rf')
  request({
    method: 'post',
    url: '/login/refresh-token',
    data: {refreshToken: loginMsg.refreshToken}
  }).then((response) => {
    response = response.data
    if (response.code === 0) {
      localStorage.setItem("loginMsg", JSON.stringify(response.content));
    } else {
      cancelLogin()
    }
    window.refreshLock = false
    callback()
  }).catch((response) => {
    window.refreshLock = false
    if (response.msg) {
      console.log(response.msg)
    }
  });
}

export default http
