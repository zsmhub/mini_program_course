/**
 * 通用js
 */

var common = {}

//获取当前日期
common.getDate = function () {
    var myDate = new Date();
    var y = myDate.getFullYear();
    var m = myDate.getMonth() + 1;
    var d = myDate.getDate();

    if (m >= 1 && m <= 9) {
        m = '0' + m;
    }
    if (d >= 1 && d <= 9) {
        d = '0' + d;
    }

    return y + '-' + m + '-' + d;
}

module.exports = common