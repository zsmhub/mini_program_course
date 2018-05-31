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
<div data-options="region: 'north', title: '搜索条件', border: false" class="sm-layout-search">
    <div id="search">
        <form id="search_fm" method="post">
            <table>
                <tr>
                    <td>
                        <label>账号</label>
                        <input type="text" class="search-input" name="search_a" id="search_a" />
                    </td>
                    <td>
                        <label>姓名</label>
                        <input type="text" class="search-input" name="search_b" id="search_b" />
                    </td>
                    <td>
                        <a href='javascript:void(0)' class="easyui-linkbutton easyui-linkbutton-search" iconCls="icon-search" onclick="search_sm()">搜索</a>
                        <a href='javascript:void(0)' class="easyui-linkbutton easyui-linkbutton-search" iconCls="icon-reload" onclick="search_reset()">重置</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<div data-options="region: 'center', border: false">
    <table id="datagrid"></table>
    <div id="toolbar">
        <?php if(user_auth('contacts', 'add_user', 'admin') == 1): ?>
            <a href='javascript:void(0)' class="easyui-linkbutton" iconCls="icon-add" plain="true" id="toolbar_add">添加账号</a>
        <?php endif; ?>
        <?php if(user_auth('contacts', 'export_user', 'admin') == 1): ?>
            <a href='javascript:void(0)' class="easyui-linkbutton" iconCls="icon-download" plain="true" id="toolbar_download">导出账号</a>
        <?php endif; ?>
        <?php if(user_auth('contacts', 'import_user', 'admin') == 1): ?>
            <a href='<?php echo $url_upload; ?>' class="easyui-linkbutton" iconCls="icon-upload" plain="true" id="toolbar_upload">导入账号</a>
        <?php endif; ?>
    </div>

    <div id="dlg_fm" class="dlg_fitem">
        <form id="fm" method="post">
            <div class="fitem">
                <label>登陆账号<span class="fitem-star">*</span></label>
                <input type="text" name="username" id="username" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>登陆密码<span class="fitem-star">*</span></label>
                <input type="password" name="password" id="password" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>姓名<span class="fitem-star">*</span></label>
                <input type="text" name="nickname" id="nickname" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>所属院系<span class="fitem-star">*</span></label>
                <input type="text" name="college" id="college" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>邮箱<span class="fitem-star">*</span></label>
                <input type="text" name="email" id="email" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>状态<span class="fitem-star">*</span></label>
                <select class="sm-select" name="status" id="status">
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
</div>

<iframe id="down_file" name="down_file" style="display:none"></iframe>

<?php include_once ADMINVIEWPATH . 'foot.php'; ?>
<script>
    var url;
    var url_del = '<?php echo $url_del; ?>';
    $(function() {
        var opts = {
            url: "<?php echo $url . '&action=ajax_data'; ?>",
            columns: [[
                {field: 'username', title: '登陆账号', width: 100, align: 'center', sortable: 'true'},
                {field: 'nickname', title: '姓名', width: 100, align: 'center', sortable: 'true'},
                {field: 'college', title: '所属院系', width: 100, align: 'center', sortable: 'true'},
                {field: 'email', title: '邮箱', width: 100, align: 'center', sortable: 'true'},
                {field: 'status', title: '状态', width: 100, align: 'center', sortable: 'true', formatter: function(value, row, index) {
                    return row.status_trans;
                }},
                {field: 'operation', title: '操作', width: 150, align: 'center', formatter: function(value, row, index) {
                    var del_param = [row.id, url_del];
                    var btn = '';
                    btn += '<?php if(user_auth('contacts', 'edit_user', 'admin') == 1): ?>' + easyui.button('编辑', 'edit', index, 'edit') + '<?php endif; ?>';
                    btn += '<?php if(user_auth('contacts', 'delete', 'admin') == 1): ?>' + easyui.button('删除', 'del', del_param, 'remove') + '<?php endif; ?>';
                    return btn;
                }}
            ]],
            toolbar: '#toolbar'
        };
        easyui.datagrid('datagrid', opts);

        easyui.dialog('dlg_fm', {
            buttons: '#dlg_buttons'
        });

        //添加
        $('#toolbar_add').on('click', function() {
            $('#username').removeAttr('onfocus').removeClass('border_zero');
            $('#password').attr('placeholder', '');
            easyui.form('fm', 'reset');  //重置表单
            url = '<?php echo $url_add; ?>';
            easyui.dialog_open('dlg_fm', '添加账号');
        });
        //导出
        $('#toolbar_download').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            easyui.messager('info', '正在导出Excel，请稍后...');
            $('#search_fm').attr('target', 'down_file').attr('action', "<?php echo $url_export; ?>").submit();

        });
    });
    //编辑
    function edit(index) {
        var row = $('#datagrid').datagrid('selectRow', index).datagrid('getSelected');
        $('#fm').form('load', {
            'username': row.username,
            'nickname': row.nickname,
            'college': row.college,
            'email': row.email,
            'status': row.status
        });
        $('#username').attr('onfocus', 'this.blur()').addClass('border_zero');
        $('#password').attr('placeholder', '选填');
        easyui.dialog_open('dlg_fm', '编辑账号');
        url = '<?php echo $url_edit; ?>' + '&id=' + row.id;
    }
    //保存增改结果
    function save_fm() {
        easyui.messager('progress_open');

        //前台参数判断
        var param_a = $('#username').val();
        var param_b = $('#nickname').val();
        var param_c = $('#email').val();
        var param_d = $('#status').val();
        var param_e = $('#college').val();
        var param_f = $('#password').val();
        if(param_a == '' || param_b == '' || param_c == '' || param_d == '' || param_e == '') {
            easyui.messager('progress_close');
            easyui.messager('warning', '带*的选项不能为空!');
            return false;
        }

        //添加新用户时，密码验证
        if(url.indexOf('add_user') != -1 && param_f == '') {
            easyui.messager('progress_close');
            easyui.messager('warning', '请输入初始登录密码!');
            return false;
        }

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
    //模糊搜索
    function search_sm() {
        easyui.search('datagrid', {
            search_a: $('#search_a').val(),
            search_b: $('#search_b').val()
        });
    }
    //重置搜索
    function search_reset() {
        $('#search_a').val('');
        $('#search_b').val('');
        search_sm();
    }
</script>
</body>
</html>