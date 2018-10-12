import request from '@/utils/request'

// 上传
export function upload(image_file, type) {
  return request({
    url: 'http://team.meeting_system.local/upload/upload/image',
    method: 'post',
    data: {
      image_file,
      type
    }
  })
}

// 地址信息
export function getAddress(parentid) {
  return request({
    url: 'http://team.meeting_system.local/area/area/get-area-list',
    method: 'post',
    data: {
      parentid
    }
  })
}
