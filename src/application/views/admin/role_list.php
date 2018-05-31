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
                        <label>角色名称</label>
                        <input type="text" class="search-input" name="search_a" id="search_a" />
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
<div data-options="region:'center', border: false">
    <table id="datagrid"></table>
    <div id="toolbar">
        <?php if(user_auth('role', 'addrole', 'admin') == 1): ?>
            <a href='<?php echo geturl('role', 'addrole', 'admin'); ?>' class="easyui-linkbutton" iconCls="icon-add" plain="true">新增角色</a>
        <?php endif; ?>
    </div>
</div>

<?php include_once ADMINVIEWPATH . 'foot.php'; ?>
<script>
    var url;
    $(function() {
        var opts = {
            url: "<?php echo geturl('role', 'rolelist', 'admin', 'action=getData'); ?>",
            columns: [[
                {field: 'Name', title: '角色名称', width: 100, align: 'center'},
                {field: 'Intro', title: '角色说明', width: 100, align: 'center'},
                {field: 'Status', title: '状态', width: 100, align: 'center', formatter: function(value, row, index) {
                    return row.StatusTrans;
                }},
                {field: 'Operation', title: '操作', width: 100, align: 'center', formatter: function(value, row, index) {
                    var edit_param = index;
                    var del_param = index;
                    var btn = '';
                    btn += '<?php if(user_auth('role', 'editrole', 'admin') == 1): ?>' + easyui.button('编辑', 'edit', edit_param, 'edit') + '<?php endif; ?>';
                    btn += '<?php if(user_auth('role', 'delete', 'admin') == 1): ?>' + easyui.button('删除', 'del', del_param, 'remove') + '<?php endif; ?>';
                    return btn;
                }}
            ]],
            toolbar: '#toolbar',
            onLoadSuccess: function(result) {
                $('#datagrid').datagrid('clearSelections');
            }
        };
        easyui.datagrid('datagrid', opts);
    });
    //编辑
    function edit(index) {
        var row = $('#datagrid').datagrid('selectRow', index).datagrid('getSelected');
        location.href = '<?php echo geturl('role', 'editrole', 'admin'); ?>' + '&id=' + row.Id;
    }
    //删除
    function del(index) {
        var row = $('#datagrid').datagrid('selectRow', index).datagrid('getSelected');
        easyui.messager('confirm', '确定要删除当前角色吗？', function(r) {
            if(r) {
                easyui.messager('progress_open');
                location.href = '<?php echo geturl('role', 'delete', 'admin'); ?>' + '&id=' + row.Id;
            }
        });
    }
    //模糊搜索
    function search_sm() {
        easyui.search('datagrid', {
            search_a: $('#search_a').val()
        });
    }
    //重置搜索
    function search_reset() {
        $('#search_a').val('');
        search_sm();
    }
</script>
</body>
</html>