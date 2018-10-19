import request from '@/utils/request'

// 用户列表
export function getUserList(searchName, str_time, end_time, page) {
  return request({
    url: '/user/index',
    method: 'post',
    data: {
      searchName,
      str_time,
      end_time,
      page
    }
  })
}

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
// tabs钱包信息
// export function getUserWallet(userId) {
//   return request({
//     url: '/user/get-user-wallet',
//     method: 'post',
//     data: {
//       userId
//     }
//   })
// }
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
export function addUser(userId, name) {
  return request({
    url: '/user/edit-user',
    method: 'post',
    data: {
      userId,
      name
    }
  })
}

// 编辑钱包地址
export function addWallet(walletId, address) {
  return request({
    url: '/user/edit-wallet',
    method: 'post',
    data: {
      walletId,
      address
    }
  })
}
