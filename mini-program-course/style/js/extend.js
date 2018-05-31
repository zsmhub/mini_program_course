/**
 * 小程序api调用/扩展
 */

var extend = {};

//微信版本号
extend.version = function (obj) {
  const SDKVersion = wx.getSystemInfoSync().SDKVersion || '1.0.0'
  const [MAJOR, MINOR, PATCH] = SDKVersion.split('.').map(Number)

  //访问用户微信版本号低于6.5.6时，返回false
  if (MAJOR < 6 && MINOR < 5 && PATCH < 6) {
    return false
  }

  return true
}

//显示loading提示框
extend.showLoading = function (obj = {}) {
  if (wx.showLoading) {
    wx.showLoading({
      title: obj.title || '加载中...',
      mask: obj.mask || true,
      success: obj.success || function () { },
      fail: obj.fail || function () { },
      complete: obj.complete || function () { }
    })
  } else {  //微信版本低于6.5.6时的处理方式
    extend.showModal({
      content: obj.title || '加载中，请稍后...'
    })
  }
}

//隐藏Loading提示框
extend.hideLoading = function () {
  if (wx.hideLoading) {
    wx.hideLoading()
  }
}

//显示消息提示框
extend.showToast = function (obj) {
  wx.showToast({
    title: obj.title || '提示内容',
    duration: obj.duration || 1500,
    icon: obj.icon || 'success'
  })
}

//​显示模态弹窗
extend.showModal = function (obj) {
  wx.showModal({
    title: obj.title || '提示',
    content: obj.content || '提示内容',
    showCancel: obj.showCancel || false,
    success: obj.success || function () { },
    fail: obj.fail || function () { },
    complete: obj.complete || function () { }
  })
}

//发起https请求
extend.request = function (obj) {
  wx.request({
    url: obj.url || '必填的url',
    data: obj.data || {},
    method: obj.method || 'GET',
    header: obj.header || { 'content-type': 'application/json' },
    // dataType: obj.dataType || 'json',
    success: obj.success || function () { },
    fail: obj.fail || function () {
      extend.showModal({ content: 'request请求失败，请稍后重试' })
      extend.hideLoading()
    },
    complete: obj.complete || function () { }
  })
}

//设置本地缓存，这是一个同步接口
extend.setStorageSync = function (key, value) {
  wx.setStorageSync(key, value)
}

//从本地缓存中同步获取指定 key 对应的内容。
extend.getStorageSync = function (key) {
  return wx.getStorageSync(key)
}

//从本地缓存中同步移除指定 key 
extend.removeStorageSync = function (key) {
  wx.removeStorageSync(key)
}

//同步清除本地数据缓存
extend.clearStorageSync = function () {
  try {
    wx.clearStorageSync()
  } catch (e) {
    console.log(e)
  }
}

//同步获取当前storage的相关信息
extend.getStorageInfoSync = function () {
  return wx.getStorageInfoSync()
}

//关闭当前页面，跳转到应用内的某个页面
extend.redirectTo = function (obj) {
  wx.redirectTo({
    url: obj.url,
    success: obj.success || function () { },
    fail: obj.fail || function () { },
    complete: obj.complete || function () { }
  })
}

//保留当前页面，跳转到应用内的某个页面，使用wx.navigateBack可以返回到原页面。
extend.navigateTo = function (obj) {
  wx.navigateTo({
    url: obj.url,
    success: obj.success || function () { },
    fail: obj.fail || function () { },
    complete: obj.complete || function () { }
  })
}

//跳转到 tabBar 页面，并关闭其他所有非 tabBar 页面
extend.switchTab = function (obj) {
  wx.switchTab({
    url: obj.url,
    success: obj.success || function () { },
    fail: obj.fail || function () { },
    complete: obj.complete || function () { }
  })
}

//关闭所有页面，打开到应用内的某个页面。
extend.reLaunch = function (obj) {
  wx.reLaunch({
    url: obj.url,
    success: obj.success || function () { },
    fail: obj.fail || function () { },
    complete: obj.complete || function () { }
  })
}

//关闭当前页面，返回上一页面或多级页面
extend.navigateBack = function (delta = 1) {
  wx.navigateBack({
    delta: delta
  })
}

//返回上一页
extend.navigateBack = function () {
  wx.navigateBack()
}

//调起客户端扫码界面，扫码成功后返回对应的结果
extend.scanCode = function (obj) {
  wx.scanCode(obj)
}

module.exports = extend