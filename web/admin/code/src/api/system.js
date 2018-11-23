import request from '@/utils/request'

// 获取成员列表
export function getMemList(page, searchName) {
  return request({
    url: '/manager/get-admin-list',
    method: 'post',
    data: {
      page,
      searchName
    }
  })
}
// 添加成员
export function addMen({ ...data }) {
  return request({
    url: '/manager/create-admin',
    method: 'post',
    data: {
      ...data
    }
  })
}
// 编辑成员信息
export function editMen({ ...data }) {
  return request({
    url: '/manager/edit-admin',
    method: 'post',
    data: {
      ...data
    }
  })
}
// 成员启用
export function onMen(id) {
  return request({
    url: '/manager/admin-on',
    method: 'post',
    data: {
      id
    }
  })
}
// 成员停用
export function offMen(id) {
  return request({
    url: '/manager/admin-off',
    method: 'post',
    data: {
      id
    }
  })
}

// 获取管理员类型列表
export function getRoleList() {
  return request({
    url: '/manager/get-role-list',
    method: 'post'
  })
}

// 日志列表
export function getLogList(page, userName, strTime, endTime) {
  return request({
    url: '/log/index',
    method: 'post',
    data: {
      page,
      userName,
      strTime,
      endTime
    }
  })
}
