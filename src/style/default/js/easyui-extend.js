/**
 * easyui js扩展
 *
 * @author zhangshimian<张仕勉>
 */
//在layout的panle全局配置中,增加一个onCollapse处理title
if(typeof $.fn.layout !== 'undefined') {  //没有加载easyui框架，则不执行以下扩展代码
    $.extend($.fn.layout.paneldefaults, {
        onCollapse : function () {
            //获取layout容器
            var layout = $(this).parents(".layout");
            //获取当前region的配置属性
            var opts = $(this).panel("options");
            //获取key
            var expandKey = "expand" + opts.region.substring(0, 1).toUpperCase() + opts.region.substring(1);
            //从layout的缓存对象中取得对应的收缩对象
            var expandPanel = layout.data("layout").panels[expandKey];
            //针对横向和竖向的不同处理方式
            if (opts.region == "west" || opts.region == "east") {
                //竖向的文字打竖,其实就是切割文字加br
                var split = [];
                for (var i = 0; i < opts.title.length; i++) {
                    split.push(opts.title.substring(i, i + 1));
                }
                expandPanel.panel("body").addClass("panel-title").css("text-align", "center").html(split.join("<br>"));
            } else {
                expandPanel.panel("setTitle", opts.title);
            }
        }
    });
}

//延迟加载显示: 解决一开始样式未加载完，页面混乱问题
function close() {
    $('#loading').fadeOut("normal", function() {
        $(this).remove();
    });
}
var delayTime;
$.parser.onComplete = function() {
    if(delayTime) {
        clearTimeout(delayTime);
    }
    delayTime = setTimeout(close, 500);
};

//删除
function del(id, url) {
    easyui.messager('confirm', '确定要删除这条记录？', function(r) {
        if(r) {
            easyui.messager('progress_open');
            $.post(url, {id: id}, function(result) {
                easyui.messager('progress_close');
                if(result.success) {
                    easyui.messager('success', result.msg);
                    $('#datagrid').datagrid('reload');
                } else {
                    easyui.messager('error', result.msg);
                }
            }, 'json');
        }
    });
}

var easyui = {};

/**
 * 弹出messager
 *
 * @param type
 * @param message
 * @param callback
 * @returns {null}
 */
easyui.messager = function(type, message, callback) {
    switch (type) {
        case "success":
            $.messager.show({
                title: '提示',
                msg: message,
                timeout: 2000,
                showType: 'slide'
            });
            break;
        case "error":
            $.messager.alert('错误', message, 'error');
            break;
        case "warning":
            $.messager.alert('警告', message, 'warning');
            break;
        case "confirm":
            $.messager.confirm('确认', message, callback);
            break;
        case "info":
            $.messager.alert('温馨提醒', message, 'info');
            break;
        case "progress_open":
            $.messager.progress({
                title:'提示',
                msg:'正在处理中，请稍后...'
            });
            break;
        case "progress_close":
            $.messager.progress('close');
            break;
        default:
            break;
    }
    return null;
};

/**
 * datagrid表格定义
 *
 * @param grid_id html容器ID
 * @param opts 参数对象
 */
easyui.datagrid = function(grid_id, opts) {
    opts = $.extend({
        url: '',
        title: '',
        columns: '',
        toolbar: '',
        rownumbers: true,
        singleSelect: true,
        pagination: true,
        pageSize: 20,
        pageList: [20, 40, 60, 80, 100],
        fit: true,
        fitColumns: true,
        idField: 'Id',
        nowrap: false,
        border: false,
        onLoadSuccess: function(data){
            $('#' + grid_id).datagrid('clearSelections');
        }
    }, opts);

    $('#' + grid_id).datagrid(opts);
};

/**
 * datagrid模糊搜索
 *
 * @param id
 * @param opts
 */
easyui.search = function(id, opts) {
    $('#' + id).datagrid('load', opts);
};

/**
 * 对话框定义
 * @param id 对话框html容器ID
 * @param opts 参数对象
 */
easyui.dialog = function(id, opts) {
    if(typeof(opts) != 'undefined') {
        opts = $.extend({
            width: 400,
            buttons: '',
            maximizable: true,
            modal: true,
            maximized: false
        }, opts);
    }
    $('#' + id).dialog(opts).dialog('close');
};

/**
 * 打开对话框
 * @param id 对话框html容器ID
 * @param title 标题
 * @param form_id 表单html容器ID
 */
easyui.dialog_open = function(id, title, form_id) {
    $('#' + id).dialog('open').dialog('setTitle', title);

    //清空表单
    if(typeof(form_id) != 'undefined') {
        $('#' + form_id).form('clear');
    }
};

/**
 * 返回一个按钮
 *
 * @param sm_title 文字
 * @param sm_callback 回调函数名
 * @param sm_param 回调函数参数
 * @param sm_icon 按钮图标
 * @return {string}
 */
easyui.button = function(sm_title, sm_callback, sm_param, sm_icon) {
    if(typeof(sm_param) === 'object') {
        sm_param = "'" + sm_param.join("','") + "'";
    }
    var btn = '&nbsp;<a href="#" class="easyui-linkbutton l-btn l-btn-small sm-linkbutton" data-options="iconCls:'+"'"+'icon-'+'x_icon'+"'"+'" onclick="'+sm_callback+'('+sm_param+')" group="" id="">' +
            '<span class="l-btn-left l-btn-icon-left">' +
                '<span class="l-btn-text">'+sm_title+'</span>' +
                '<span class="l-btn-icon icon-'+sm_icon+'">&nbsp;</span>' +
            '</span>' +
        '</a>&nbsp;';

    return btn;
};

/**
* 表单处理
*
* @param id 容器ID
* @param type 类型，如reset
*/
easyui.form = function(id, type) {
    $('#' + id).form(type);
}

/**
* datetimebox
*
* @param id 容器ID
* @param opts 参数
*/
easyui.datetimebox = function(id, opts) {
    opts_init = {
        showSeconds:false,
        editable:false
    };
    if(typeof(opts) != 'undefined') {
        opts_init = $.extend(opts_init, opts);
    }
    $('#' + id).datetimebox(opts_init);
}

/**
* numberbox
*
* @param id 容器ID
* @param opts 参数
*/
easyui.numberbox = function(id, opts) {
    opts_init = {
        precision: 0
    };
    if(typeof(opts) != 'undefined') {
        opts_init = $.extend(opts_init, opts);
    }
    $('#' + id).numberbox(opts_init);
}