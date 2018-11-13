import * as types from './mutation-types'

const mutations = {
  [types.WAIT_CURSOR](state, waitCursor) {
    state.waitCursor = waitCursor
  },
  [types.TOAST_OBJ](state, toastObj) {
    state.toastObj = toastObj
  },
  [types.LOGIN_MSG](state, loginMsg) {
    state.loginMsg = loginMsg
  },
  [types.IDENTIFY_MSG](state,identifyMsg){
    state.identifyMsg = identifyMsg
  },
  [types.PAY_PSW](state,payPsw){
    state.payPsw = payPsw
  },
  [types.MY_NODE_INFO](state,myNodeInfo){
    state.myNodeInfo = myNodeInfo
  }
}
export default mutations
