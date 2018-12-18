import request from '@/utils/request'

// 节点审核列表
// 1：已通过 2：未审核 4： 未通过
export function getCheckList(status, searchName, page, order) {
  return request({
    url: '/node/examine',
    method: 'post',
    data: {
      status,
      searchName,
      page,
      order
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

// 删除记录
export function deleteNote(nodeId) {
  return request({
    url: '/node/del-old-data',
    method: 'post',
    data: {
      nodeId
    }
  })
}

// 获取节点基本信息
export function getNodeBase(nodeId) {
  return request({
    url: '/node/get-node-examine-detail',
    method: 'post',
    data: {
      nodeId
    }
  })
}
