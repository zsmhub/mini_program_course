// pages/course/course.js
//获取应用实例
var app = getApp();

//配置文件
var config = require('../../config.js');

//通用js代码
var extend = require("../../style/js/extend.js")

var course_id = ''

var userinfo = ''

Page({

  /**
   * 页面的初始数据
   */
  data: {
    flag: false,  //内容显示标志
    flag_apply: true,  //按钮显示标志
    course_detail: '',  //课程明细信息
    apply_text: '我要报名'  //按钮文字信息
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    // 登陆权限验证
    if (!app.globalData.userinfo) {
      extend.redirectTo({ url: '/pages/login/login' })
      return false
    }
    
    userinfo = app.globalData.userinfo
    course_id = options.course_id
    this.course_detail()
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
    this.course_detail()
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
      path: '/pages/detail/detail'
    }
  },

  /**
   * 获取课程明细
   */
  course_detail: function (e) {
    extend.showLoading()
    var that = this

    extend.request({
      url: config.url_course_detail,
      method: 'POST',
      header: { 'content-type': 'application/x-www-form-urlencoded' },
      data: { course_id: course_id, username: userinfo.username },
      success: function (ret) {
        if (ret.statusCode != 200) {
          extend.showModal({ content: '网络请求出错,请稍后重试!' })
          return false
        }

        var data = ret.data
        if (!data.success) {
          extend.showModal({ content: data.msg })
          extend.hideLoading()
          return false
        }

        //报名状态判断
        var flag_apply = true
        var apply_text = ''
        if (data.data.status != 0) {
          flag_apply = false
          apply_text = '报名已结束'
        }
        if (data.apply_status) {
          flag_apply = false
          apply_text = '您已报名成功'
        }
        if ( userinfo.type == 1 ) {
          flag_apply = false
          apply_text = '学生才能报名哦'
        }

        that.setData({
          flag: true,
          flag_apply: flag_apply,
          course_detail: data.data,
          apply_text: apply_text
        })
        extend.hideLoading()
      }
    })
  },

  /**
   * 报名按钮
   */
  apply: function (e) {
    extend.showLoading()
    var that = this

    extend.request({
      url: config.url_course_apply,
      method: 'POST',
      header: { 'content-type': 'application/x-www-form-urlencoded' },
      data: { course_id: course_id, username: userinfo['username'] },
      success: function (ret) {
        if (ret.statusCode != 200) {
          extend.showModal({ content: '网络请求出错,请稍后重试!' })
          extend.hideLoading()
          return false
        }

        var data = ret.data
        if (!data.success) {
          extend.showModal({ content: data.msg })
          extend.hideLoading()
          return false
        }

        that.setData({
          flag_apply: false,
          apply_text: '您已报名成功',
          course_detail: data.course_detail
        })
        extend.hideLoading()
        extend.showModal({ content: data.msg })
      }
    })
  }
})