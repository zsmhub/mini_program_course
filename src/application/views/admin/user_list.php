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
        <form id="search_fm">
            <table>
                <tr>
                    <td>
                        <label>姓名</label>
                        <input type="text" class="search-input" name="search_a" id="search_a" />
                    </td>
                    <td>
                        <label>邮箱</label>
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
        <?php if(user_auth('user', 'adduser', 'admin') == 1): ?>
            <a href='javascript:void(0)' class="easyui-linkbutton" iconCls="icon-add" plain="true" id="toolbar_add">添加用户</a>
        <?php endif; ?>
    </div>

    <div id="dlg_fm" class="dlg_fitem">
        <form id="fm" method="post">
            <div class="fitem">
                <label>Username<span class="fitem-star">*</span></label>
                <input type="text" name="UserName" id="UserName" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>Password<span class="fitem-star">*</span></label>
                <input type="password" name="Password" id="Password" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>姓名<span class="fitem-star">*</span></label>
                <input type="text" name="NickName" id="NickName" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>邮箱<span class="fitem-star">*</span></label>
                <input type="text" name="Email" id="Email" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>角色<span class="fitem-star">*</span></label>
                <select class="sm-select" name="RoleId" id="RoleId">
                    <?php foreach($role_list as $role): ?>
                        <option value="<?php echo $role['Id']; ?>"><?php echo $role['Name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="fitem">
                <label>状态<span class="fitem-star">*</span></label>
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
</div>

<?php include_once ADMINVIEWPATH . 'foot.php'; ?>
<script>
    var url;
    var url_del = '<?php echo geturl('user', 'delete', 'admin'); ?>';
    $(function() {
        var opts = {
            url: "<?php echo geturl('user', 'userlist', 'admin', 'action=getData'); ?>",
            columns: [[
                {field: 'UserName', title: 'Username', width: 100, align: 'center', sortable: 'true'},
                {field: 'NickName', title: '姓名', width: 100, align: 'center', sortable: 'true'},
                {field: 'Email', title: '邮箱', width: 100, align: 'center', sortable: 'true'},
                {field: 'RoleId', title: '角色', width: 100, align: 'center', sortable: 'true', formatter: function(value, row, index) {
                    return row.RoleName;
                }},
                {field: 'Status', title: '状态', width: 100, align: 'center', sortable: 'true', formatter: function(value, row, index) {
                    return row.StatusTrans;
                }},
                {field: 'Operation', title: '操作', width: 150, align: 'center', formatter: function(value, row, index) {
                    var del_param = [row.Id, url_del];
                    var btn = '';
                    btn += '<?php if(user_auth('user', 'edit_user', 'admin') == 1): ?>' + easyui.button('编辑', 'edit', index, 'edit') + '<?php endif; ?>';
                    btn += '<?php if(user_auth('user', 'delete', 'admin') == 1): ?>' + easyui.button('删除', 'del', del_param, 'remove') + '<?php endif; ?>';
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
            $('#UserName').removeAttr('onfocus').removeClass('border_zero');
            $('#Password').attr('placeholder', '');
            easyui.form('fm', 'reset');  //重置表单
            url = '<?php echo geturl('user', 'adduser', 'admin'); ?>';
            easyui.dialog_open('dlg_fm', '添加用户');
        });
    });
    //编辑
    function edit(index) {
        var row = $('#datagrid').datagrid('selectRow', index).datagrid('getSelected');
        $('#fm').form('load', {
            'UserName': row.UserName,
            'NickName': row.NickName,
            'Email': row.Email,
            'RoleId': row.RoleId,
            'Status': row.Status
        });
        $('#UserName').attr('onfocus', 'this.blur()').addClass('border_zero');
        $('#Password').attr('placeholder', '选填');
        easyui.dialog_open('dlg_fm', '编辑用户');
        url = '<?php echo geturl('user', 'edit_user', 'admin'); ?>' + '&Id=' + row.Id;
    }
    //保存增改结果
    function save_fm() {
        easyui.messager('progress_open');

        //前台参数判断
        var param_a = $('#UserName').val();
        var param_b = $('#NickName').val();
        var param_c = $('#Email').val();
        var param_d = $('#Status').val();
        var param_e = $('#RoleId').val();
        var param_f = $('#Password').val();
        if(param_a == '' || param_b == '' || param_c == '' || param_d == '' || param_e == '') {
            easyui.messager('progress_close');
            easyui.messager('warning', '带*的选项不能为空!');
            return false;
        }

        //添加新用户时，密码验证
        if(url.indexOf('adduser') != -1 && param_f == '') {
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