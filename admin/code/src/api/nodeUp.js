import request from '@/utils/request'

// 获取节点升级列表
export function getNodeUpList(page, status, searchName, order) {
  return request({
    url: '/node/examine-upgrade',
    method: 'post',
    data: {
      page,
      status,
      searchName,
      order
    }
  })
}

// 审核通过
export function nodeUpPass(id) {
  return request({
    url: '/node/upgrade-examine-on',
    method: 'post',
    data: {
      id
    }
  })
}

// 审核不通过
export function nodeUpFail(id, remark) {
  return request({
    url: '/node/upgrade-examine-off',
    method: 'post',
    data: {
      id,
      remark
    }
  })
}

// 基本信息
export function getBaseInfo(id) {
  return request({
    url: '/node/upgrade-detail',
    method: 'post',
    data: {
      id
    }
  })
}
