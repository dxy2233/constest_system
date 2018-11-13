const state={
  waitCursor: false,
  toastObj:{
    show:false,
    time:1000,
    type:'text',
    width:"7.6em"
  },
  loginMsg:JSON.parse(sessionStorage.getItem('loginMsg')) || null,
  identifyMsg:JSON.parse(sessionStorage.getItem("identifyMsg")) || null,
  payPsw:sessionStorage.getItem('payPsw')||'0',
  myNodeInfo:JSON.parse(sessionStorage.getItem('myNodeInfo')) || null,
}

export default state
