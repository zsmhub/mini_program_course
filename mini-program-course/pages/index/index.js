//获取应用实例
var app = getApp();

//配置文件
var config = require('../../config.js');

//通用js
var extend = require("../../style/js/extend.js")

Page({
  data: {
    nickname: '',
    userinfo: null
  },
  onLoad: function (options) {

  },
  onShareAppMessage: function () {
    return {
      title: config.mini_apps_title,
      path: '/pages/index/index'
    }
  },
  login_navigate: function () {
    extend.navigateTo({ url: '/pages/login/login' })
  }
})

