/**
 * app.js
 */

//配置文件
var config = require('./config.js');

//通用js代码
var extend = require("./style/js/extend.js")

App({
  onLaunch: function () {
    // Do something when launch.
    var that = this
    var storage_userinfo = wx.getStorageSync('UserInfo')

    //用户已登陆，页面跳转至课程列表
    if (storage_userinfo) {
      that.globalData.userinfo = storage_userinfo
      extend.reLaunch({ url: '/pages/course/course' })
    }
  },
  onShow: function () {
    // Do something when show.
  },
  onHide: function () {
    // Do something when hide.
  },
  onError: function (msg) {
    // console.log(msg)
  },
  globalData: {
    userinfo: ''
  }
})