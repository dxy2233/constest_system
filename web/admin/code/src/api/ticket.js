import request from '@/utils/request'

// 获取可添加标签
export function getTicketTips() {
  return request({
    url: '/ticket/get-ticket-tag',
    method: 'post'
  })
}
