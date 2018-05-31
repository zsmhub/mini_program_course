<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE>
<html>
<head>
    <?php include_once ADMINVIEWPATH . 'head.php'; ?>
</head>
<body class="easyui-layout">
<div id='loading'>
    <h1>
        <font>加载中···</font>
    </h1>
</div>
<div data-options="region:'center'">
    <table id="datagrid"></table>
    <div id="toolbar">
        <?php if(user_auth('ctrl', 'addcontroller', 'admin') == 1): ?>
            <a href='javascript:void(0)' class="easyui-linkbutton" iconCls="icon-add" plain="true" id="toolbar_add">添加控制器</a>
        <?php endif; ?>
    </div>

    <div id="dlg_fm" class="dlg_fitem">
        <form id="fm" method="post">
            <div class="fitem">
                <label>英文标识<span class="fitem-star">*</span></label>
                <input type="text" name="IdKey" id="IdKey" class="sm-textbox"  />
            </div>
            <div class="fitem">
                <label>中文描述<span class="fitem-star">*</span></label>
                <input type="text" name="Name" id="Name" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>访问控制<span class="fitem-star">*</span></label>
                <select class="sm-select" name="REV_AUTH" id="REV_AUTH">
                    <option value="1">需要验证</option>
                    <option value="0">不需要验证</option>
                </select>
            </div>
            <div class="fitem">
                <label>使用状态<span class="fitem-star">*</span></label>
                <select class="sm-select" name="Status" id="Status">
                    <option value="1">正常</option>
                    <option value="0">禁用</option>
                </select>
            </div>
        </form>
    </div>
    <div id="dlg_buttons">
        <a href="javascript:void(0);" class="easyui-linkbutton easyui-linkbutton-dialog" iconCls="icon-ok" onclick="save_fm()">保存</a>
        <a href="javascript:void(0);" class="easyui-linkbutton easyui-linkbutton-dialog" iconCls="icon-cancel" onclick="javascript:$('#dlg_fm').dialog('close');">取消</a>
    </div>

    <div id="dlg_fun">
        <table id="datagrid_fun"></table>
        <div id="toolbar_fun">
            <?php if(user_auth('ctrl', 'addaction', 'admin') == 1): ?>
                <a href='javascript:void(0)' class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="action_add()">添加功能</a>
            <?php endif; ?>
        </div>
    </div>

    <div id="dlg_fm_fun" class="dlg_fitem">
        <form id="fm_fun" method="post">
            <div class="fitem">
                <label>控制器<span class="fitem-star">*</span></label>
                <select class="sm-select" name="CtrlEname" id="CtrlEname">
                </select>
            </div>
            <div class="fitem">
                <label>功能标识<span class="fitem-star">*</span></label>
                <input type="text" name="ActionEname" id="ActionEname" class="sm-textbox"  />
            </div>
            <div class="fitem">
                <label>功能描述<span class="fitem-star">*</span></label>
                <input type="text" name="ActionCname" id="ActionCname" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>访问控制<span class="fitem-star">*</span></label>
                <select class="sm-select" name="Auth" id="Auth">
                    <option value="2">继承</option>
                    <option value="1">需要验证</option>
                    <option value="0">不需要验证</option>
                </select>
            </div>
            <div class="fitem">
                <label>使用状态<span class="fitem-star">*</span></label>
                <select class="sm-select" name="Status" id="Status">
                    <option value="1">正常</option>
                    <option value="0">禁用</option>
                </select>
            </div>
        </form>
    </div>
    <div id="dlg_buttons_fun">
        <a href="javascript:void(0);" class="easyui-linkbutton easyui-linkbutton-dialog" iconCls="icon-ok" onclick="save_fm_fun()">保存</a>
        <a href="javascript:void(0);" class="easyui-linkbutton easyui-linkbutton-dialog" iconCls="icon-cancel" onclick="javascript:$('#dlg_fm_fun').dialog('close');">取消</a>
    </div>
</div>

<?php include_once ADMINVIEWPATH . 'foot.php'; ?>
<script>
    var url, url_func;
    $(function() {
        var opts = {
            url: "<?php echo geturl('ctrl', 'ctrllist', 'admin', 'action=getData'); ?>",
            columns: [[
                {field: 'IdKey', title: '控制器标识', width: 100, align: 'center'},
                {field: 'Name', title: '控制器名字', width: 100, align: 'center'},
                {field: 'Dir', title: '所在目录', width: 100, align: 'center', formatter: function(value, row, index) {
                    return './application/controllers/' + value;
                }},
                {field: 'REV_AUTH', title: '验证', width: 100, align: 'center', formatter: function(value, row, index) {
                    return (value == 1) ? '需要验证' : '无需验证';
                }},
                {field: 'Status', title: '状态', width: 100, align: 'center', formatter: function(value, row, index) {
                    return (value == 1) ? '正常' : '禁用';
                }},
                {field: 'Operation', title: '操作', width: 200, align: 'center', formatter: function(value, row, index) {
                    var btn = '';
                    btn += '<?php if(user_auth('ctrl', 'funclist', 'admin') == 1): ?>' + easyui.button('功能列表', 'func_list', index, 'list') + '<?php endif; ?>';
                    btn += '<?php if(user_auth('ctrl', 'addaction', 'admin') == 1): ?>' + easyui.button('添加功能', 'action_add', index, 'add') + '<?php endif; ?>';
                    btn += '<?php if(user_auth('ctrl', 'editctrl', 'admin') == 1): ?>' + easyui.button('编辑', 'ctl_edit', index, 'edit') + '<?php endif; ?>';
                    return btn;
                }}
            ]],
            toolbar: '#toolbar',
            pagination: false,
            onLoadSuccess: function(result) {
                $('#datagrid').datagrid('clearSelections');
                $('#CtrlEname').html(result.ctrl_list);
            }
        };
        easyui.datagrid('datagrid', opts);

        easyui.dialog('dlg_fm', {
            buttons: '#dlg_buttons'
        });

        easyui.dialog('dlg_fun', {
            width: 600,
            height: 400
        });

        var opts_fun = {
            columns: [[
                {field: 'IdKey', title: '功能标识', width: 100, align: 'center'},
                {field: 'Name', title: '功能说明', width: 100, align: 'center'},
                {field: 'REV_AUTH', title: '验证', width: 100, align: 'center', formatter: function(value, row, index) {
                    return (value == 1) ? '需要验证' : ((value == 2) ? '继承' : '无需验证');
                }},
                {field: 'Status', title: '状态', width: 100, align: 'center', formatter: function(value, row, index) {
                    return (value == 1) ? '正常' : '禁用';
                }},
                {field: 'Operation', title: '操作', width: 100, align: 'center', formatter: function(value, row, index) {
                    var btn = '';
                    btn += '<?php if(user_auth('ctrl', 'editfunc', 'admin') == 1): ?>' + easyui.button('编辑', 'action_edit', index, 'edit') + '<?php endif; ?>';
                    return btn;
                }}
            ]],
            toolbar: '#toolbar_fun',
            pagination: false
        };
        easyui.datagrid('datagrid_fun', opts_fun);

        easyui.dialog('dlg_fm_fun', {
            buttons: '#dlg_buttons_fun',
        });

        //打开添加控制器
        $('#toolbar_add').on('click', function() {
            url = '<?php echo geturl('ctrl', 'addcontroller', 'admin'); ?>';
            $('#IdKey, #Name').attr('disabled', false).css('border', '1px solid #ccc');
            easyui.dialog_open('dlg_fm', '添加控制器');
        });
    });
    //编辑控制器
    function ctl_edit(index) {
        var row = $('#datagrid').datagrid('selectRow', index).datagrid('getSelected');
        $('#fm').form('load', {
            'IdKey': row.IdKey,
            'Name': row.Name,
            'REV_AUTH': row.REV_AUTH,
            'Status': row.Status
        });
        $('#IdKey, #Name').attr('disabled', true).css('border', '0');
        easyui.dialog_open('dlg_fm', '编辑控制器');
        url = '<?php echo geturl('ctrl', 'editctrl', 'admin'); ?>';
    }
    //保存增改结果
    function save_fm() {
        easyui.messager('progress_open');

        //前台参数判断
        var param_a = $('#IdKey').val();
        var param_b = $('#Name').val();
        var param_c = $('#REV_AUTH').val();
        var param_d = $('#Status').val();
        if(param_a == '' || param_b == '' || param_c == '' || param_d == '') {
            easyui.messager('progress_close');
            easyui.messager('warning', '带*的选项不能为空!');
            return false;
        }

        $('#IdKey, #Name').removeAttr('disabled');
        $.post(url, $('#fm').serialize(), function(result) {
            easyui.messager('progress_close');
            if(result.success) {
                easyui.messager('success', result.msg);
                $('#dlg_fm').dialog('close');
                $('#datagrid').datagrid('reload');
            } else {
                easyui.messager('error', result.msg);
            }
        }, 'json');
    }
    //功能列表
    function func_list(index) {
        var row = $('#datagrid').datagrid('selectRow', index).datagrid('getSelected');
        $('#datagrid_fun').datagrid({
            url: '<?php echo geturl('ctrl', 'funclist', 'admin', 'ctrl_name='); ?>' + row.IdKey
        });
        easyui.dialog_open('dlg_fun', '功能列表');
    }
    //打开添加功能
    function action_add(index) {
        url_func = '<?php echo geturl('ctrl', 'addaction', 'admin'); ?>';
        $('#CtrlEname, #ActionEname, #ActionCname').attr('disabled', false).css('border', '1px solid #ccc');

        if(typeof(index) != 'undefined') {
            var row = $('#datagrid').datagrid('selectRow', index).datagrid('getSelected');
            $('#fm_fun').form('load', {
                'CtrlEname': row.IdKey
            });
        }

        easyui.dialog_open('dlg_fm_fun', '添加功能');
    }
    //编辑功能
    function action_edit(index) {
        var row_parent = $('#datagrid').datagrid('getSelected');
        var row = $('#datagrid_fun').datagrid('selectRow', index).datagrid('getSelected');
        $('#fm_fun').form('load', {
            'CtrlEname': row_parent.IdKey,
            'ActionEname': row.IdKey,
            'ActionCname': row.Name,
            'AUTH': row.REV_AUTH,
            'Status': row.Status
        });
        $('#CtrlEname, #ActionEname, #ActionCname').attr('disabled', true).css('border', '0');
        easyui.dialog_open('dlg_fm_fun', '编辑功能');
        url_func = '<?php echo geturl('ctrl', 'editfunc', 'admin'); ?>';
    }
    //保存增改功能记录
    function save_fm_fun() {
        easyui.messager('progress_open');

        //前台参数判断
        var param_a = $('#CtrlEname').val();
        var param_b = $('#ActionEname').val();
        var param_c = $('#ActionCname').val();
        var param_d = $('#AUTH').val();
        var param_e = $('#Status').val();
        if(param_a == '' || param_b == '' || param_c == '' || param_d == '' || param_e == '') {
            easyui.messager('progress_close');
            easyui.messager('warning', '带*的选项不能为空!');
            return false;
        }

        $('#CtrlEname, #ActionEname, #ActionCname').removeAttr('disabled');
        $.post(url_func, $('#fm_fun').serialize(), function(result) {
            easyui.messager('progress_close');
            if(result.success) {
                easyui.messager('success', result.msg);
                $('#dlg_fm_fun').dialog('close');
                $('#datagrid_fun').datagrid('reload');
            } else {
                easyui.messager('error', result.msg);
            }
        }, 'json');
    }
</script>
</body>
</html>