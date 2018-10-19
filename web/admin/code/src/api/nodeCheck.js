import request from '@/utils/request'

// 节点审核列表
// 1：已通过 2：未审核 4： 未通过
export function getCheckList(status, searchName, page) {
  return request({
    url: '/node/examine',
    method: 'post',
    data: {
      status,
      searchName,
      page
    }
  })
}

// 审核通过
export function checkPass(nodeId) {
  return request({
    url: '/node/examine-on',
    method: 'post',
    data: {
      nodeId
    }
  })
}

// 审核不通过
export function checkFail(nodeId, remark) {
  return request({
    url: '/node/examine-off',
    method: 'post',
    data: {
      nodeId,
      remark
    }
  })
}
