import request from '@/utils/request'

// 获取节点列表
export function getNodeList(searchName, str_time, end_time, type) {
  return request({
    url: '/node/index',
    method: 'post',
    data: {
      searchName,
      str_time,
      end_time,
      type
    }
  })
}

// 获取节点类型列表
export function getNodeType() {
  return request({
    url: '/node/get-type-list',
    method: 'post'
  })
}

// 获取节点基本信息
export function getNodeBase(nodeId) {
  return request({
    url: '/node/get-node-data',
    method: 'post',
    data: {
      nodeId
    }
  })
}
// 获取节点实名信息
export function getNodeIdentify(nodeId) {
  return request({
    url: '/node/get-node-identify',
    method: 'post',
    data: {
      nodeId
    }
  })
}
// 获取节点投票信息
export function getNodeVote(nodeId) {
  return request({
    url: '/node/get-vote-list',
    method: 'post',
    data: {
      nodeId
    }
  })
}
// 获取节点权限信息
export function getNodeRule(nodeId) {
  return request({
    url: '/node/get-rule',
    method: 'post',
    data: {
      nodeId
    }
  })
}

// 停用
export function stopNode(nodeId) {
  return request({
    url: '/node/stop-node',
    method: 'post',
    data: {
      nodeId
    }
  })
}
// 启用
export function onNode(nodeId) {
  return request({
    url: '/node/open-node',
    method: 'post',
    data: {
      nodeId
    }
  })
}

// 任职
export function onTenure(nodeId) {
  return request({
    url: '/node/tenure',
    method: 'post',
    data: {
      nodeId
    }
  })
}
// 卸任
export function offTenure(nodeId) {
  return request({
    url: '/node/un-tenure',
    method: 'post',
    data: {
      nodeId
    }
  })
}

// 编辑节点基本信息
export function updataBase(nodeId, logo, name, desc, scheme) {
  return request({
    url: '/node/update-node',
    method: 'post',
    data: {
      nodeId,
      logo,
      name,
      desc,
      scheme
    }
  })
}

// 获取节点设置数据
export function getNodeSet(type_id) {
  return request({
    url: '/node/get-node-setting',
    method: 'post',
    data: {
      type_id
    }
  })
}

// 获取权限列表
export function getRuleList() {
  return request({
    url: '/node/get-rule-list',
    method: 'post'
  })
}

// 添加、修改节点权益
export function pushRuleList(data) {
  return request({
    url: '/node/set-rule',
    method: 'post',
    data: {
      data: JSON.stringify(data)
    }
  })
}

// 修改节点总设置
export function pushNodeSet({ id, name, isExamine, isCandidate, isVote, isOrder,
  tenureNum, maxCandidate, grt, tt, bpt, ruleList }) {
  return request({
    url: '/node/update',
    method: 'post',
    data: {
      id,
      name,
      isExamine,
      isCandidate,
      isVote,
      isOrder,
      tenureNum,
      maxCandidate,
      grt,
      tt,
      bpt,
      rule: JSON.stringify(ruleList)
    }
  })
}

// 获取历史排名
export function getHistory(type, endTime, page) {
  return request({
    url: '/node/get-history-order',
    method: 'post',
    data: {
      type,
      endTime,
      page
    }
  })
}

// 添加节点第一步
export function addNodeOne({ ...data }) {
  return request({
    url: '/node/create-user',
    method: 'post',
    data: {
      ...data
    }
  })
}
// 添加节点第二步
export function addNodeTwo({ ...data }) {
  return request({
    url: '/node/set-identify',
    method: 'post',
    data: {
      ...data
    }
  })
}
// 添加节点第三步
export function addNodeThree({ ...data }) {
  return request({
    url: '/node/create-node',
    method: 'post',
    data: {
      ...data
    }
  })
}
