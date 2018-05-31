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
<div data-options="region: 'center', border: false, title: '导入excel-<?php echo $title; ?>'" style="padding: 20px;">
    <form id="fm" method="post" enctype="multipart/form-data">
        <div id="excel_import">
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <td style="width:20%; text-align:right">选择excel文件：</td>
                    <td style="width:30%; padding-left:10px;">
                        <input type="file" name="readExcel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .csv"/>
                    </td>
                    <td style="width: 50%;" class="sm-red"><?php echo isset($tip) ? $tip : ''; ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td><a href="<?php echo $template; ?>" class="a_style" id="down_excel_template">下载导入excel模板</a></td>
                    <td></td>
                </tr>
            </table>

            <input type="hidden" name="action" value="<?php echo $action; ?>" />
        </div>
    </form>

    <?php if(isset($msg)): ?>
        <div class="list-div" style="margin-top: 40px; overflow-y: auto;">
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <th colspan="<?php echo $totalField; ?>" align="left">excel上传结果</th>
                </tr>
                <tr>
                    <th colspan="<?php echo $totalField; ?>" align="left">上&nbsp;&nbsp;传&nbsp;&nbsp;记&nbsp;&nbsp;录：<?php echo $totalRecord; ?>&nbsp;&nbsp;条</th>
                </tr>
                <tr>
                    <th colspan="<?php echo $totalField; ?>" align="left">上传成功记录：<?php echo $successRecord; ?>&nbsp;&nbsp;条</th>
                </tr>
                <tr>
                    <th colspan="<?php echo $totalField; ?>" align="left">上传失败记录：<?php echo $failureRecord; ?>&nbsp;&nbsp;条</th>
                </tr>
                <?php foreach($msg as $row): ?>
                    <tr>
                        <?php foreach($row as $i => $key): ?>
                            <td style="text-align:left"><?php echo $row[$i]; ?></td>
                         <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>

    <iframe id="down_file" name="down_file" style="display:none"></iframe>
</div>
<div data-options="region:'south'" class="layout_south">
    <div>
        <a href="javascript:void(0);" class="easyui-linkbutton easyui-linkbutton-dialog" iconCls="icon-ok" id="submit">提交</a>
        <a href="<?php echo $jump_url; ?>" class="easyui-linkbutton easyui-linkbutton-dialog" iconCls="icon-back">返回</a
    </div>
</div>

<?php include_once ADMINVIEWPATH . 'foot.php'; ?>
<script>
    $(function() {
        $('#submit').on('click', function() {
            $.messager.confirm('提示', '确定要提交？', function(r) {
                if(r) {
                    easyui.messager('progress_open');
                    $('#fm').submit();
                }
            });
        });

        $('#down_excel_template').on('click', function() {
            easyui.messager('info', '下载中，请稍后...');
        });
    });
</script>
</body>
</html>