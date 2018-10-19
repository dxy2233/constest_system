import request from '@/utils/request'

// 实名认证列表
// 0 待审核 1 已审核 2审核不通过
export function getList(status, searchName, page) {
  return request({
    url: '/identify/index',
    method: 'post',
    data: {
      status,
      searchName,
      page
    }
  })
}

// 详细信息
export function getDetail(user_id) {
  return request({
    url: '/identify/detail',
    method: 'post',
    data: {
      user_id
    }
  })
}

// 审核通过
export function passVerified(user_id) {
  return request({
    url: '/identify/examine-on',
    method: 'post',
    data: {
      user_id
    }
  })
}

// 审核不通过
export function failVerified(user_id, remark) {
  return request({
    url: '/identify/examine-off',
    method: 'post',
    data: {
      user_id,
      remark
    }
  })
}
