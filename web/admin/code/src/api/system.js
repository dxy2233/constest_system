import request from '@/utils/request'

// 基础信息
export function getBaseInfo() {
  return request({
    url: '/statistics/index',
    method: 'post'
  })
}
