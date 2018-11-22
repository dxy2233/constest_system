import request from '@/utils/request'

// 基础信息
export function getBaseInfo() {
  return request({
    url: '/statistics/index',
    method: 'post'
  })
}

// 统计图
export function getLineInfo(type, group, str_time, end_time) {
  return request({
    url: '/statistics/get-statistics-data',
    method: 'post',
    data: {
      type,
      group,
      str_time,
      end_time
    }
  })
}

// 地图
export function getMapInfo(type) {
  return request({
    url: '/statistics/get-address-data',
    method: 'post',
    data: {
      type
    }
  })
}
