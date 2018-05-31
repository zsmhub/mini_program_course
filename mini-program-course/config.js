/**
 * 小程序配置文件
 */

// 此处是小程序后台服务器的域名
var host = "http://website/mini_program_course/src";

var config = {
    //小程序的标题
    mini_apps_title: '学校公选课小助手',

    //登录接口，用于获取用户信息
    url_login: host + '/index.php?d=api&c=course_mini&a=login',

    //获取课程列表接口
    url_course: host + '/index.php?d=api&c=course_mini&a=course_list',

    //获取课程详细信息接口
    url_course_detail: host + '/index.php?d=api&c=course_mini&a=course_detail',

    //获取我参与的课程接口
    url_course_self: host + '/index.php?d=api&c=course_mini&a=course_self',

    //课程报名接口
    url_course_apply: host + '/index.php?d=api&c=course_mini&a=course_apply'
};

module.exports = config;
