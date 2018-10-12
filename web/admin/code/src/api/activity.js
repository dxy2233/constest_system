import request from '@/utils/request'

// 获取列表
export function getMeetList(status, page, title) {
  return request({
    url: '/meeting/get-meeting-list',
    method: 'post',
    data: {
      status,
      page,
      title
    }
  })
}

// 删除活动
export function deleteMeet(id) {
  return request({
    url: '/meeting/delete-meeting',
    method: 'post',
    data: {
      id
    }
  })
}

// 恢复活动
export function undeleteMeet(id) {
  return request({
    url: '/meeting/un-delete-meeting',
    method: 'post',
    data: {
      id
    }
  })
}

// (创建||修改)活动
export function createMeet({ id, title, people_num, begin_time, sponsor, mobile, style_id,
  background, line, button, image, area_id, address, contacts, email, money }) {
  return request({
    url: '/meeting/create',
    method: 'post',
    data: {
      id,
      title,
      people_num,
      begin_time,
      sponsor,
      mobile,
      style_id,
      background,
      line,
      button,
      image,
      area_id,
      address,
      contacts,
      email,
      money
    }
  })
}

// 活动详情
export function getMeetDetail(id) {
  return request({
    url: '/meeting/meeting-detail',
    method: 'post',
    data: {
      id
    }
  })
}

// 开启团队注册
export function openTeam(id) {
  return request({
    url: '/meeting/open-team-reg',
    method: 'post',
    data: {
      id
    }
  })
}
// 关闭团队注册
export function closeTeam(id) {
  return request({
    url: '/meeting/close-team-reg',
    method: 'post',
    data: {
      id
    }
  })
}
// 获取领队管理列表
export function getTeamList(meeting_id, page, page_size) {
  return request({
    url: '/team/index',
    method: 'post',
    data: {
      meeting_id,
      page,
      page_size
    }
  })
}
// 获取团队基本信息
export function getTeamInfo(meeting_id) {
  return request({
    url: '/team/info',
    method: 'post',
    data: {
      meeting_id
    }
  })
}

// 开启pc签到
export function openPC(id) {
  return request({
    url: '/meeting/open-pc-sign',
    method: 'post',
    data: {
      id
    }
  })
}
// 关闭pc签到
export function closePC(id) {
  return request({
    url: '/meeting/close-pc-sign',
    method: 'post',
    data: {
      id
    }
  })
}

// 开启微信签到
export function openWX(id) {
  return request({
    url: '/meeting/open-weixin-sign',
    method: 'post',
    data: {
      id
    }
  })
}
// 关闭微信签到
export function closeWX(id) {
  return request({
    url: '/meeting/close-weixin-sign',
    method: 'post',
    data: {
      id
    }
  })
}
