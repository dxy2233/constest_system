import request from '@/utils/request'

// 获取投票列表
export function getVoteList(searchName, page, str_time, end_time, order) {
  return request({
    url: '/vote/index',
    method: 'post',
    data: {
      searchName,
      page,
      str_time,
      end_time,
      order
    }
  })
}

// 获取投票设置列表
export function getVoteSet() {
  return request({
    url: '/vote/get-setting-list',
    method: 'post'
  })
}

// 修改投票设置列表
export function pushVoteSet({ ...data }) {
  return request({
    url: '/vote/set-vote',
    method: 'post',
    data: {
      ...data
    }
  })
}

// 投票排名
// 方式 0全部1：普通2支付3券4推荐
export function getVoteRank(end_time, type, page) {
  return request({
    url: '/vote/get-vote-order',
    method: 'post',
    data: {
      end_time,
      type,
      page
    }
  })
}

// 手动刷新
export function refresh() {
  return request({
    url: '/vote/now-reload',
    method: 'post'
  })
}
