//获取应用实例
var app = getApp()

//配置文件
var config = require('../../config.js');

//通用js
var extend = require("../../style/js/extend.js")

var userinfo = ''

Page({

  /**
   * 页面的初始数据
   */
  data: {
    flag_teacher: false
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this
    // 登陆权限验证
    if (!app.globalData.userinfo) {
      extend.redirectTo({ url: '/pages/login/login' })
      return false
    }
    
    userinfo = app.globalData.userinfo
    // 设置老师不能点击扫一扫按钮
    if(userinfo.type == '1') that.setData({ flag_teacher: true })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
  
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
  
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
    return {
      title: config.mini_apps_title,
      path: '/pages/scan/scan'
    }
  },

  /**
   * 扫一扫
   */
  scan: function () {
    extend.showLoading()
    extend.scanCode({
      success: function(ret) {
        if (ret == undefined || ret.errMsg != 'scanCode:ok' || ret.scanType != 'QR_CODE') {
          extend.showModal({ content: '请扫描老师给予的二维码！'})
          extend.hideLoading()
          return false
        }

        var url = ret.result  // 需要进行url正则匹配
        extend.request({
          url: url,
          method: 'POST',
          header: { 'content-type': 'application/x-www-form-urlencoded' },
          data: { param2: userinfo.username },
          success: function (ret) {
            var data = ret.data

            extend.showModal({ content: data.msg })
            extend.hideLoading()
          }
        })
      },
      fail: function(ret) {
        extend.hideLoading()
      }
    })
  }
})