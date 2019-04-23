import request from '@/utils/request'

// 获取列表
export function getCheckList(page, status, searchName, order) {
  return request({
    url: '/transfer/index',
    method: 'post',
    data: {
      page,
      status,
      searchName,
      order
    }
  })
}

// detail
export function getDetail(id) {
  return request({
    url: '/transfer/get-transfer-detail',
    method: 'post',
    data: {
      id
    }
  })
}

// 审核通过
export function checkPass(id) {
  return request({
    url: '/transfer/examine-on',
    method: 'post',
    data: {
      id
    }
  })
}

// 审核不通过
export function checkFail(id, remark) {
  return request({
    url: '/transfer/examine-off',
    method: 'post',
    data: {
      id,
      remark
    }
  })
}
