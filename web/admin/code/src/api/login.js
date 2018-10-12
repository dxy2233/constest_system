import request from '@/utils/request'

export function login(username, password) {
  return request({
    url: '/login/index',
    method: 'post',
    data: {
      username,
      password
    }
  })
}

export function getInfo() {
  return request({
    url: '/manager/detail',
    method: 'post'
  })
}

export function logout() {
  return request({
    url: '/login/logout',
    method: 'post'
  })
}
