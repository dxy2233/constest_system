import request from '@/utils/request'

// 获取下载excel的验证码
export function getVerifiCode() {
  return request({
    url: '/download/get-download-code',
    method: 'post'
  })
}
