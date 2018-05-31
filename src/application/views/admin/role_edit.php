<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE>
<html>
<head>
    <?php include_once ADMINVIEWPATH . 'head.php'; ?>
</head>
<body class="easyui-layout">
<div data-options="region:'center', border: false, title: '角色修改'" style="padding: 10px 20px;">
    <form id="fm" method="post">
        <div class="fitem">
            <label>角色名称<span class="fitem-star">*</span></label>
            <input type="text" name="Name" id="Name" class="sm-textbox" value="<?php echo isset($info['Name'])?$info['Name']:''; ?>" />
        </div>
        <div class="fitem">
            <label>角色描述<span class="fitem-star">*</span></label>
            <textarea name="Intro" id="Intro" style="width: 300px; height: 80px;"><?php echo isset($info['Intro'])?$info['Intro']:''; ?></textarea>
        </div>
        <div>
            <div style="float: left; padding-left: 5px;">角色授权<span class="fitem-star">*</span></div>
            <div style="float: left; margin-left: 5px;">
                <?php foreach($ctrl_list as $_contrl): ?>
                    <?php if( !empty($_contrl['Methods'])): ?>
                        <div style="display:block;">
                            <strong style="display:block;width:100%;clear:both"><?php echo $_contrl['Name']; ?>(<?php echo $_contrl['Dir']; ?>/<?php echo $_contrl['IdKey']; ?>)</strong>
                            <?php foreach($_contrl['Methods'] as $_action): ?>
                                <?php if($_action['REV_AUTH'] != 0): ?>
                                    <label style="margin:0 3px;float:left;width:100px;">
                                        <input type="checkbox" name="Permissions[]" value="<?php echo $_contrl['IdKey']; ?>:<?php echo $_action['IdKey']; ?>" <?php if(isset($info['Id']) && role_auth($info['Id'], $_contrl['IdKey'], $_action['IdKey'])): ?>checked="checked"<?php endif; ?>/><?php echo $_action['Name']; ?>
                                    </label>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <p style="width:100%;clear:both;padding:0;margin:0"></p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="clear"></div>
        <div class="fitem" style="margin-top: 20px;">
            <label>使用状态<span class="fitem-star">*</span></label>
            <select class="sm-select" name="Status" id="Status">
                <option value="1" <?php if(isset($info['Status']) && $info['Status'] == 1): ?>selected="selected"<?php endif; ?>>正常</option>
                <option value="0" <?php if(isset($info['Status']) && $info['Status'] == 0): ?>selected="selected"<?php endif; ?>>禁用</option>
            </select>
        </div>
        <input type="hidden" name="action" value="<?php echo $action; ?>" />
        <input type="hidden" name="Id" value="<?php echo isset($info['Id']) ? $info['Id'] : ''; ?>" />
    </form>
</div>
<div data-options="region:'south'" style="height:50px; padding: 7px 20px;">
    <input type="button" id="submit" class="sm-button sm_foot_button" value="提交" />
    <input type="button" class="sm-button sm_foot_button" value="返回" onclick="back()" />
</div>
<?php include_once ADMINVIEWPATH . 'foot.php'; ?>
<script>
    $('#submit').click(function() {
        var param_a = $('#Name').val();
        var param_b = $('#Intro').val();
        var param_c = $('#Status').val();
        if(param_a == '' || param_b == '' || param_c == '' ) {
            easyui.messager('warning', '带*的选项不能为空!');
            return false;
        }
        $('#submit').prop('disabled', 'true');
        $('#fm').submit();
    });
</script>
</body>
</html>