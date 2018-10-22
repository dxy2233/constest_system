import request from '@/utils/request'

// 节点审核列表
// 0待审核 1已通过 3 未通过
export function getList(status, currency_id, searchName, page, str_time, end_time) {
  return request({
    url: '/withdraw/index',
    method: 'post',
    data: {
      status,
      currency_id,
      searchName,
      page,
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
export function passTrial(user_id) {
  return request({
    url: '/withdraw/examine-on',
    method: 'post',
    data: {
      user_id
    }
  })
}
// 审核不通过
export function failTrial(user_id, remark) {
  return request({
    url: '/withdraw/examine-off',
    method: 'post',
    data: {
      user_id,
      remark
    }
  })
}
