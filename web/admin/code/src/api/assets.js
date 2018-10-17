import request from '@/utils/request'

// 获取资产列表
export function getFinanceList(searchName, currency_id, type, min, max, order, page) {
  return request({
    url: '/finance/index',
    method: 'post',
    data: {
      searchName,
      currency_id,
      type,
      min,
      max,
      order,
      page
    }
  })
}

// 获取币种
export function getMoneyType() {
  return request({
    url: '/finance/get-currency-list',
    method: 'post'
  })
}

// 锁仓记录列表
export function getLockList(searchName, currency_id, str_time, end_time, page) {
  return request({
    url: '/finance/get-frozen-list',
    method: 'post',
    data: {
      searchName,
      currency_id,
      str_time,
      end_time,
      page
    }
  })
}
