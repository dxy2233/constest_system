import router from '@/router'
import store from '@/store/index'

export let cancelLogin = () => {
  localStorage.clear()
  store.commit('LOGIN_MSG', null)
  store.commit('IDENTIFY_MSG', null)
}

export let hidMobile = function (originalNum) {
  if (!!originalNum) {
    return originalNum.substr(0, 4) + "****" + originalNum.substr(-4);
  } else {
    return ''
  }
}
export let GetUrlParam = (paraName) => {
  let url = document.location.toString();
  let arrObj = url.split("?");

  if (arrObj.length > 1) {
    let arrPara = arrObj[1].split("&");
    let arr;

    for (let i = 0; i < arrPara.length; i++) {
      arr = arrPara[i].split("=");

      if (arr != null && arr[0] == paraName) {
        return arr[1];
      }
    }
    return "";
  }
  else {
    return "";
  }
}

export let limitFloating = (string)=>{
  if (!string) return 0
  string = string.toString()
  string = string.replace(/[^\d\.]/g,'');
//必须保证第一个为数字而不是.
  string = string.replace(/^\./g,'');
//保证只有出现一个.而没有多个.
  string = string.replace(/\.{2,}/g,'.');
//保证.只出现一次，而不能出现两次以上
  string = string.replace('.','$#$').replace(/\./g,'').replace('$#$','.');
  return string
}


export let limitIpt = (string, num) => {
  num = Number(num)
  string = string.toString()
  if (num === 0) {
    return string.replace(/[^\d]/g, '')
  }
  let findex = string.indexOf('.')
  if (findex >= 0) {
    let prefix = string.substring(0, findex);
    let suffix = string.substring(findex, findex + num + 1);
    prefix = prefix.replace(/[^\d]/g, '')
    // prefix = prefix.replace(/[^0-9]+/, '');
    suffix = suffix.replace(/[^\d]/g, '')
    // suffix = suffix.replace(/[^0-9]+/, '');
    string = prefix + '.' + suffix;
  } else {
    string = string.replace(/[^\d]/g, '')
  }
  if (string.substring(0, 1) === '0') {
    if (string.substring(1, 2) !== '.') {
      string = '0'
    }
  }
  // console.log(string,num)
  return string


}
