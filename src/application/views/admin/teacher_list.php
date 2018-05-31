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
                        <label>教师姓名</label>
                        <input type="text" class="search-input" name="search_b" id="search_b" />
                    </td>
                    <td>
                        <label>所属院系</label>
                        <input type="text" class="search-input" name="search_c" id="search_c" />
                    </td>
                    <td>
                        <label>课程名</label>
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
<div data-options="region: 'center', border: false">
    <table id="datagrid"></table>
    <div id="toolbar">
        <?php if(user_auth('course', 'export_teacher', 'admin') == 1): ?>
            <a href='javascript:void(0)' class="easyui-linkbutton" iconCls="icon-download" plain="true" id="toolbar_download">导出教师</a>
        <?php endif; ?>
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
                {field: 'nickname', title: '教师姓名', width: 100, align: 'center', sortable: 'true'},
                {field: 'college', title: '所属院系', width: 100, align: 'center', sortable: 'true'},
                {field: 'course_name', title: '课程名', width: 100, align: 'center', sortable: 'true'},
                {field: 'course_time', title: '上课时间', width: 100, align: 'center', sortable: 'true'},
                {field: 'email', title: '联系邮箱', width: 100, align: 'center', sortable: 'true'}
            ]],
            toolbar: '#toolbar'
        };
        easyui.datagrid('datagrid', opts);

        //导出
        $('#toolbar_download').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            easyui.messager('info', '正在导出Excel，请稍后...');
            $('#search_fm').attr('target', 'down_file').attr('action', "<?php echo $url_export; ?>").submit();

        });
    });
    //模糊搜索
    function search_sm() {
        easyui.search('datagrid', {
            search_a: $('#search_a').val(),
            search_b: $('#search_b').val(),
            search_c: $('#search_c').val()
        });
    }
    //重置搜索
    function search_reset() {
        $('#search_fm').form('reset');
        search_sm();
    }
</script>
</body>
</html>