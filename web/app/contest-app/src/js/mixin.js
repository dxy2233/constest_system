import router from '@/router'
import store from '@/store/index'

export let cancelLogin = () => {
  sessionStorage.clear()
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
