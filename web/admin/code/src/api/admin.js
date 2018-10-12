import request from '@/utils/request'

// 管理员列表
export function getAdmin() {
  return request({
    url: '/manager/index',
    method: 'post'
  })
}

// 添加管理员
export function addAdmin({ name, password, password2 }) {
  return request({
    url: '/manager/create',
    method: 'post',
    data: {
      name,
      password,
      password2
    }
  })
}

// 修改管理员信息
export function modifyAdmin({ id, real_name, email, department, mobile }) {
  return request({
    url: '/manager/update',
    method: 'post',
    data: {
      id,
      real_name,
      email,
      department,
      mobile
    }
  })
}
