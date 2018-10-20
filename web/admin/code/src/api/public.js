import request from '@/utils/request'

// 获取上传图片的域名
export function upload() {
  return request({
    url: 'http://admin.contest_system.local/site/site/get-site-info',
    method: 'post'
  })
}
