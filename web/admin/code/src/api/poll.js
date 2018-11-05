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

// 获取投票周期历史记录
export function getCampHistory(page) {
  return request({
    url: '/cycle/history',
    method: 'post',
    data: {
      page
    }
  })
}
// 获取投票周期列表
export function getCamp() {
  return request({
    url: '/cycle/index',
    method: 'post'
  })
}
// 添加投票周期
export function addCamp(cycleStartTime, cycleEndTime, tenureStartTime, tenureEndTime) {
  return request({
    url: '/cycle/create-cycle',
    method: 'post',
    data: {
      cycleStartTime,
      cycleEndTime,
      tenureStartTime,
      tenureEndTime
    }
  })
}
// 删除投票周期
export function deleteCamp(id) {
  return request({
    url: '/cycle/del',
    method: 'post',
    data: {
      id
    }
  })
}
// 修改投票周期
export function editCamp(id, cycleStartTime, cycleEndTime, tenureStartTime, tenureEndTime) {
  return request({
    url: '/cycle/update-cycle',
    method: 'post',
    data: {
      id,
      cycleStartTime,
      cycleEndTime,
      tenureStartTime,
      tenureEndTime
    }
  })
}
