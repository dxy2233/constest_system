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
    url: '/manager/index',
    method: 'post'
  })
}

export function logout() {
  return request({
    url: '/login/logout',
    method: 'post'
  })
}

// 修改用户密码
export function resetPW({ ...data }) {
  return request({
    url: '/manager/change-password',
    method: 'post',
    data: {
      ...data
    }
  })
}
