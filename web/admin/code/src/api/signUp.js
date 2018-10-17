import request from '@/utils/request'

// 创建注册通道
export function createSign(meeting_id, name) {
  return request({
    url: '/meeting/create-sign',
    method: 'post',
    data: {
      meeting_id,
      name
    }
  })
}

// 获取注册通道信息
export function getSign(id) {
  return request({
    url: '/sign/detail',
    method: 'post',
    data: {
      id
    }
  })
}

// 提交注册通道数据
export function subSign(passageway_id, data, open_time, close_time, status) {
  return request({
    url: '/sign/save-sign-data',
    method: 'post',
    data: {
      passageway_id,
      data: JSON.stringify(data),
      open_time,
      close_time,
      status
    }
  })
}

// 数据示例
export function example() {
  return request({
    url: '/sign/get-data-json',
    method: 'post'
  })
}
// 获取可选标签
export function getLabel() {
  return request({
    url: '/sign/get-sign-tag',
    method: 'post'
  })
}
// 获取标签可设置属性
export function getLabelAttr(id) {
  return request({
    url: '/sign/get-tag-attr',
    method: 'post',
    data: {
      id
    }
  })
}
