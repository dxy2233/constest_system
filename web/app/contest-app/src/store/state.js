const state={
  waitCursor: false,
  toastObj:{
    show:false,
    time:1000,
    type:'text',
    width:"7.6em"
  },
  loginMsg:JSON.parse(localStorage.getItem('loginMsg')) || null,
  identifyMsg:JSON.parse(localStorage.getItem("identifyMsg")) || null,
  payPsw:localStorage.getItem('payPsw')||'0',
  myNodeInfo:JSON.parse(localStorage.getItem('myNodeInfo')) || null,
}

export default state
