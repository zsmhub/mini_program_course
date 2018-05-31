<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE>
<html>
<head>
    <?php include_once ADMINVIEWPATH . 'head.php'; ?>
    <style>
        #dlg_fm .fitem label {
            width: 100px;
        }
        #dlg_fm .sm-textbox {
            width: 310px;
        }
    </style>
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
                        <label>课程名</label>
                        <input type="text" class="search-input" name="search_a" id="search_a" />
                    </td>
                    <td>
                        <label>任课老师</label>
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
        <?php if(user_auth('course', 'course_add', 'admin') == 1): ?>
            <a href='javascript:void(0)' class="easyui-linkbutton" iconCls="icon-add" plain="true" id="toolbar_add">添加课程</a>
        <?php endif; ?>
        <?php if(user_auth('course', 'export_course', 'admin') == 1): ?>
            <a href='javascript:void(0)' class="easyui-linkbutton" iconCls="icon-download" plain="true" id="toolbar_download">导出课程</a>
        <?php endif; ?>
    </div>

    <div id="dlg_fm" class="dlg_fitem">
        <form id="fm" method="post">
            <div class="fitem">
                <label>课程名<span class="fitem-star">*</span></label>
                <input type="text" name="course_name" id="course_name" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>上课地点<span class="fitem-star">*</span></label>
                <input type="text" name="place" id="place" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>上课时间<span class="fitem-star">*</span></label>
                <input type="text" name="course_time" id="course_time" class="sm-textbox" />
            </div>
            <div class="fitem">
                <label>课程简介<span class="fitem-star">*</span></label>
                <textarea class="sm-textarea" name="outline" id="outline" style="width: 310px;height: 80px;"></textarea>
            </div>
            <div class="fitem">
                <label>课程学分<span class="fitem-star">*</span></label>
                <input type="text" name="score" id="score" class="sm-textbox" style="width: 310px;height: 30px;"/>
            </div>
            <div class="fitem">
                <label>任课老师<span class="fitem-star">*</span></label>
                <textarea class="sm-textarea" name="teacher_names" id="teacher_names" placeholder="点击选择任课老师" style="width: 310px;height: 80px;" readonly></textarea>

                <div id="dlg-select-user">
                    <div id="cc" class="easyui-layout" fit="true">
                        <div data-options="region:'north'" style="height: 50px; padding: 8px 30px;">
                            <input type="text" id="search-data" class="sm-textbox" style="width: 450px" placeholder="搜索老师姓名或所属学院" />
                            <input type="button" id="search-button" class="sm-button" style="width: 60px; font-size: 12px; height: 30px; padding: 4px;" value="搜索" />
                        </div>
                        <div data-options="region:'center',title:'老师信息'">
                            <table id="dlg_datagrid"></table>
                        </div>
                    </div>
                </div>
                <div id="dlg-select-user-buttons">
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="dlg_save()" style="width:90px" id="course_save">确定</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg-select-user').dialog('close')" style="width:90px">取消</a>
                </div>
                <input type="hidden" name="teacher_ids" id="teacher_ids" value="" />
            </div>
            <div class="fitem">
                <label>报名人数上限<span class="fitem-star">*</span></label>
                <input type="text" name="limit_num" id="limit_num" class="sm-textbox" style="width: 310px;height: 30px;"/>
            </div>
            <div class="fitem">
                <label>报名截止时间<span class="fitem-star">*</span></label>
                <input class="sm_datetimebox" name="close_time" id="close_time" />
            </div>
            <div class="fitem">
                <label>扫码迟到时间<span class="fitem-star">*</span></label>
                <input class="sm_datetimebox" name="late_time" id="late_time" />
            </div>
        </form>
    </div>
    <div id="dlg_buttons">
        <a href="javascript:void(0);" class="easyui-linkbutton easyui-linkbutton-dialog" iconCls="icon-ok" onclick="save_fm()">保存</a>
        <a href="javascript:void(0);" class="easyui-linkbutton easyui-linkbutton-dialog" iconCls="icon-cancel" onclick="javascript:$('#dlg_fm').dialog('close');">取消</a>
    </div>
</div>

<div id="dlg-result" class="easyui-dialog" data-options="title: '查看报名结果', maximizable: true, modal: true" style="width:540px;height:300px;">
    <table id="result-datagrid"></table>
    <div id="result-toolbar">
        <?php if(user_auth('course', 'export_student_apply', 'admin') == 1): ?>
            <a href='javascript:void(0)' class="easyui-linkbutton" iconCls="icon-download" plain="true" onclick="export_ret()">导出EXCEL</a>
        <?php endif; ?>
        <input type="hidden" name="resultId" id="resultId" />
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
                {field: 'course_name', title: '课程名', width: 100, align: 'center', sortable: 'true'},
                {field: 'place', title: '上课地点', width: 100, align: 'center', sortable: 'true'},
                {field: 'course_time', title: '上课时间', width: 100, align: 'center', sortable: 'true'},
                {field: 'score', title: '课程学分', width: 60, align: 'center', sortable: 'true'},
                {field: 'teacher_names', title: '任课老师', width: 100, align: 'center', sortable: 'true'},
                {field: 'limit_num', title: '人数上限', width: 60, align: 'center', sortable: 'true', formatter: function(value, row, index) {
                    return row.limit_trans;
                }},
                {field: 'activity_num', title: '已报人数', width: 60, align: 'center', sortable: 'true'},
                {field: 'status', title: '报名状态', width: 80, align: 'center', sortable: 'true', formatter: function(value, row, index) {
                    return row.status_trans;
                }},
                {field: 'operator', title: '创建者', width: 80, align: 'center', sortable: 'true'},
                {field: 'operate_time', title: '创建时间', width: 100, align: 'center', sortable: 'true'},
                {field: 'operation', title: '操作', width: 200, align: 'center', formatter: function(value, row, index) {
                    var del_param = [row.id, url_del];
                    var btn = '';
                    btn += '<?php if(user_auth('course', 'course_edit', 'admin') == 1): ?>' + easyui.button('编辑', 'edit', index, 'edit') + '<?php endif; ?>';
                    btn += '<?php if(user_auth('course', 'course_del', 'admin') == 1): ?>' + easyui.button('删除', 'del', del_param, 'remove') + '<?php endif; ?>';
                    btn += '<?php if(user_auth('course', 'course_apply_result', 'admin') == 1): ?>' + easyui.button('报名管理', 'openResult', row.id, 'setting') + '<?php endif; ?>';
                    btn += '<?php if(user_auth('course', 'course_sign', 'admin') == 1): ?>' + easyui.button('二维码签到', 'openSign', row.id, 'redo') + '<?php endif; ?>';
                    return btn;
                }}
            ]],
            toolbar: '#toolbar'
        };
        easyui.datagrid('datagrid', opts);

        easyui.dialog('dlg_fm', {
            width: 560,
            buttons: '#dlg_buttons',
            maximized: true
        });

        $('#dlg-result').dialog('close');

        //添加
        $('#toolbar_add').on('click', function() {
            easyui.form('fm', 'reset');  //重置表单
            url = '<?php echo $url_add; ?>';
            easyui.dialog_open('dlg_fm', '添加课程');
        });
        //导出
        $('#toolbar_download').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            easyui.messager('info', '正在导出Excel，请稍后...');
            $('#search_fm').attr('target', 'down_file').attr('action', "<?php echo $url_export; ?>").submit();

        });

        //任课老师
        easyui.datagrid('dlg_datagrid', {
            url: '<?php echo $url_get_teacher; ?>',
            rownumbers: false,
            singleSelect: false,
            pagination: false,
            fit: true,
            fitColumns: true,
            idField: 'username',
            nowrap: false,
            border: false,
            columns: [[
                {field: 'ck', checkbox: 'true'},
                {field: 'nickname', title: '姓名', width: 100, align: 'center', sortable: 'true' },
                {field: 'college', title: '所属学院', width: 100, align: 'center', sortable: 'true'}
            ]]
        });
        easyui.dialog('dlg-select-user', {
            title: '选择任课老师',
            width: 600,
            height: 400,
            maximizable: true,
            modal: true,
            buttons: '#dlg-select-user-buttons'
        });
        $('#teacher_names').on('click', function() {
            clearSelectedUser();
            $( "#dlg-select-user" ).dialog('open');
        });
        $('#search-button').on('click', function() {
            var search = $('#search-data').val();
            $('#dlg_datagrid').datagrid('load', {
                search: search
            });
        });

        easyui.datetimebox('close_time');
        easyui.datetimebox('late_time');
        easyui.numberbox('score', {max: 99999999, precision:0, buttonText: '分(请输入正整数)'});
        easyui.numberbox('limit_num', {max: 99999999, precision:0, buttonText: '请输入人数(默认0为无限制)'});
    });
    //编辑
    function edit(index) {
        var row = $('#datagrid').datagrid('selectRow', index).datagrid('getSelected');
        $('#fm').form('load', {
            'course_name': row.course_name,
            'place': row.place,
            'course_time': row.course_time,
            'outline': row.outline,
            'score': row.score,
            'teacher_names': row.teacher_names,
            'teacher_ids': row.teacher_ids,
            'limit_num': row.limit_num,
            'close_time': row.close_time,
            'late_time': row.late_time
        });
        easyui.dialog_open('dlg_fm', '编辑课程');
        url = '<?php echo $url_edit; ?>' + '&id=' + row.id;
    }
    //保存增改结果
    function save_fm() {
        easyui.messager('progress_open');

        //前台参数判断
        var param_a = $('#course_name').val();
        var param_b = $('#place').val();
        var param_c = $('#course_time').val();
        var param_d = $('#outline').val();
        var param_e = $('#score').numberbox('getValue');
        var param_f = $('#teacher_names').val();
        var param_g = $('#limit_num').numberbox('getValue');
        var param_h = $('#close_time').datetimebox('getValue');
        var param_i = $('#late_time').datetimebox('getValue');
        if(param_a == '' || param_b == '' || param_c == '' || param_d == '' || param_e == '' || param_f == '' || param_g == '' || param_h == '' || param_i == '') {
            easyui.messager('progress_close');
            easyui.messager('warning', '带*的选项不能为空!');
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
    //二维码签到页面
    function openSign(id) {
        var url = "<?php echo $url_sign; ?>" + '&id=' + id;
        window.open(url);
    }
    //选择任课老师对话框，选中已选记录
    function clearSelectedUser() {
        //clear表格行选中
        $('#dlg_datagrid').datagrid('clearSelections');

        //选中已选择的记录
        var user_id = $('#teacher_ids').val();
        if(user_id != '') {
            var user_id_arr = user_id.split('|');
            for(var i in user_id_arr) {
                $('#dlg_datagrid').datagrid('selectRecord', user_id_arr[i]);
            }
        }
    }
    //保存选中的任课老师
    function dlg_save() {
        var rows = $('#dlg_datagrid').datagrid('getSelections');
        var nickname = '';
        var ids = '';
        if(rows.length) {
            for(var i = 0; i < rows.length; i++) {
                if(nickname == '') {
                    nickname += rows[i].nickname;
                } else {
                    nickname += ', ' + rows[i].nickname;
                }
                if(ids == '') {
                    ids += rows[i].username;
                } else {
                    ids += '|' + rows[i].username;
                }
            }
            $('#teacher_names').val(nickname);
            $('#teacher_ids').val(ids);
        }
        $('#dlg-select-user').dialog('close');
    }
    //查看报名结果
    function openResult(id) {
        $('#datagrid').datagrid('reload');  //刷新数据
        $('#result-datagrid').datagrid({
            url: '<?php echo $url_apply_ret; ?>&id=' + id,
            rownumbers: true,
            singleSelect: true,
            fit: true,
            fitColumns: true,
            nowrap: false,
            border: true,
            columns: [[
                {field: 'nickname', title: '姓名', width: 100, align: 'center'},
                {field: 'college', title: '学院', width: 150, align: 'center'},
                {field: 'email', title: '邮箱', width: 150, align: 'center'},
                {field: 'sign_time', title: '签到结果', width: 100, align: 'center'}
            ]],
            toolbar: '#result-toolbar',
            onLoadSuccess: function(data) {
                $('#resultId').val(id);
            }
        });
        $('#dlg-result').dialog('open');
    }
    //导出报名数据
    function export_ret() {
        var resultId = $('#resultId').val();
        easyui.messager('info', '正在导出Excel，请稍后...');
        $('#search_fm').attr('target', 'down_file').attr('action', "<?php echo $url_export_ret; ?>&id=" + resultId).submit();
    }
</script>
</body>
</html>