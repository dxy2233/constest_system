import request from '@/utils/request'

// 上传
export function getVoteList(searchName, page, str_time, end_time) {
  return request({
    url: '/vote/index',
    method: 'post',
    data: {
      searchName,
      page,
      str_time,
      end_time
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
