import request from '@/utils/request'

// 节点审核列表
// 0待审核 1已通过 3 未通过
export function getList(status, currency_id, searchName, page, type, str_time, end_time) {
  return request({
    url: '/withdraw/index',
    method: 'post',
    data: {
      status,
      currency_id,
      searchName,
      page,
      type,
      str_time,
      end_time
    }
  })
}

// 获取转账设置初始值
export function getSetValue(currency_id) {
  return request({
    url: '/withdraw/get-setting-list',
    method: 'post',
    data: {
      currency_id
    }
  })
}

// 修改转账设置
export function editSet({ ...data }) {
  return request({
    url: '/withdraw/set-vote',
    method: 'post',
    data: {
      ...data
    }
  })
}

// 审核通过
export function passTrial(id) {
  return request({
    url: '/withdraw/examine-on',
    method: 'post',
    data: {
      id
    }
  })
}
// 审核不通过
export function failTrial(id, remark) {
  return request({
    url: '/withdraw/examine-off',
    method: 'post',
    data: {
      id,
      remark
    }
  })
}

// 钱包资产信息
export function walletInfo(type, currency_code) {
  return request({
    url: '/withdraw/wallet-info',
    method: 'post',
    data: {
      type,
      currency_code
    }
  })
}
