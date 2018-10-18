import request from '@/utils/request'

// 获取公告列表
// 0全部 1已上架 2下架
export function getNoticeList(type, page) {
  return request({
    url: '/notice/index',
    method: 'post',
    data: {
      type,
      page
    }
  })
}

// 删除公告
export function deleteNotice(id) {
  return request({
    url: '/notice/del-notice',
    method: 'post',
    data: {
      id
    }
  })
}

// 置顶
export function toTop(id) {
  return request({
    url: '/notice/set-top',
    method: 'post',
    data: {
      id
    }
  })
}
// 取消置顶
export function unTop(id) {
  return request({
    url: '/notice/set-un-top',
    method: 'post',
    data: {
      id
    }
  })
}

// 获取公告详情
export function getNoticeDetails(id) {
  return request({
    url: '/notice/get-detail',
    method: 'post',
    data: {
      id
    }
  })
}
// 上架
export function onShelf(id) {
  return request({
    url: '/notice/on-shelf',
    method: 'post',
    data: {
      id
    }
  })
}
// 下架
export function offShelf(id) {
  return request({
    url: '/notice/off-shelf',
    method: 'post',
    data: {
      id
    }
  })
}

// 获取公告设置
export function getNoticeSetList() {
  return request({
    url: '/notice/get-setting-list',
    method: 'post'
  })
}
// 修改公告设置
export function pushNoticeSet(show_count) {
  return request({
    url: '/notice/set-notice',
    method: 'post',
    data: {
      show_count
    }
  })
}

// 添加公告
export function addNotice({ image, title, date, type, url, detail, status }) {
  return request({
    url: '/notice/create',
    method: 'post',
    data: {
      image,
      title,
      str_time: date[0],
      end_time: date[1],
      type,
      url,
      detail,
      status
    }
  })
}
// 修改公告
export function editNotice({ id, image, title, date, type, url, status }) {
  return request({
    url: '/notice/edit',
    method: 'post',
    data: {
      id,
      image,
      title,
      str_time: date[0],
      end_time: date[1],
      type,
      url,
      status
    }
  })
}
