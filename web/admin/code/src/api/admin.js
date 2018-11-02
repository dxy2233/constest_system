import request from '@/utils/request'

// 用户列表
export function getUserList(searchName, str_time, end_time, page, order) {
  return request({
    url: '/user/index',
    method: 'post',
    data: {
      searchName,
      str_time,
      end_time,
      page,
      order
    }
  })
}
// export function getUserListExcel(download_code, url) {
//   return request({
//     // url: '/user/download',
//     url: url,
//     // url: 'http://localhost:3000/index',
//     // url: 'http://admin.contest_system.local/index/test',
//     // url: 'http://admin.contest_system.local/index/index',
//     method: 'get',
//     responseType: 'blob',
//     // responseType: 'arraybuffer',
//     data: {
//       download_code
//     }
//   })
// }

// tabs基础信息
export function getUserBase(userId) {
  return request({
    url: '/user/get-user-info',
    method: 'post',
    data: {
      userId
    }
  })
}
// tabs实名信息
export function getUserIdentify(userId) {
  return request({
    url: '/user/get-user-identify',
    method: 'post',
    data: {
      userId
    }
  })
}
// tabs投票信息
export function getUserVote(userId) {
  return request({
    url: '/user/get-user-vote',
    method: 'post',
    data: {
      userId
    }
  })
}
// tabs资产信息
export function getUserWallet(userId) {
  return request({
    url: '/user/get-currency',
    method: 'post',
    data: {
      userId
    }
  })
}
// tabs投票券信息
export function getUserVoucher(userId) {
  return request({
    url: '/user/get-user-voucher',
    method: 'post',
    data: {
      userId
    }
  })
}
// tabs推荐信息
export function getUserRecommend(userId) {
  return request({
    url: '/user/get-user-recommend',
    method: 'post',
    data: {
      userId
    }
  })
}

// 冻结用户
export function freezeUser(userId) {
  return request({
    url: '/user/stop-user',
    method: 'post',
    data: {
      userId
    }
  })
}
// 解冻用户
export function thawUser(userId) {
  return request({
    url: '/user/open-user',
    method: 'post',
    data: {
      userId
    }
  })
}

// 编辑用户名
export function editUser(userId, name, code) {
  return request({
    url: '/user/edit-user',
    method: 'post',
    data: {
      userId,
      name,
      code
    }
  })
}

// 新增用户
export function addUser(mobile, code) {
  return request({
    url: '/user/create-user',
    method: 'post',
    data: {
      mobile,
      code
    }
  })
}

// 派发查询
export function giveInfo(mobile, type, userId) {
  return request({
    url: '/user/get-give-info',
    method: 'post',
    data: {
      mobile,
      type,
      userId
    }
  })
}
// 确认派发
export function saveGive({ mobile, type, userId, voucherNum, remark }) {
  return request({
    url: '/user/give',
    method: 'post',
    data: {
      mobile,
      type,
      userId,
      voucherNum,
      remark
    }
  })
}
