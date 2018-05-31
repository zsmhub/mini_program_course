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
<div data-options="region:'center', border: false" style="padding: 10px;">
    <div class="sub-link">
        <h1>
            <span class="action-span-right">
                <?php foreach($tablink as $row): ?>
                    <a href='<?php echo $row['Url']; ?>'><?php echo $row['Title']; ?></a>
                <?php endforeach; ?>
            </span>
            <span class="action-span-left cur">当前操作 </span><span class="action-span-left" id="search_id"> - 添加菜单 </span>
            <div class="clear"></div>
        </h1>
    </div>
    <div class="list-div">
        <form name="form1" method="post" action="">
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <th colspan="3">添加菜单</th>
                </tr>
                <tr>
                    <td style="width:20%;text-align:right">所在分类：</td>
                    <td style="width:40%;padding-left:10px;">
                        <label>
                            <select name="ParentId" id="ParentId">
                                <?php foreach($catlist as $row): ?>
                                    <option value="<?php echo $row['Id']; ?>"<?php if(isset($info['ParentId']) && $info['ParentId'] == $row['Id']): ?> selected="selected"<?php endif; ?>><?php echo $row['Title']; ?></option>
                                 <?php endforeach; ?>
                            </select>
                        </label></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align:right">菜单名称：</td>
                    <td style="padding-left:10px;"><label>
                            <input name="Title" type="text" id="Title" value="<?php echo isset($info['Title']) ? $info['Title'] : ''; ?>">
                        </label></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align:right" valign="top">菜单链接：</td>
                    <td style="padding-left:10px;" id="evenodd">
                        <?php foreach($contrllist as $_contrl): ?>
                            <?php if(!empty($_contrl['Methods']) && $_contrl['Status'] == 1): ?>
                                <div style="display:block;margin:7px 0;clear:both">
                                    <strong style="display:block;width:100%;clear:both"><?php echo $_contrl['Name']; ?>(<?php echo $_contrl['Dir']; ?>/<?php echo $_contrl['IdKey']; ?>)</strong>
                                    <?php foreach($_contrl['Methods'] as $_action): ?>
                                        <label style="margin:0 3px;float:left">
                                            <input name="LinkInfo" type="radio" value="<?php echo $_contrl['IdKey']; ?>:<?php echo $_action['IdKey']; ?>" <?php if(isset($info['LinkInfo']) && $info['LinkInfo']['c'] == $_contrl['IdKey'] && $info['LinkInfo']['a'] == $_action['IdKey']): ?> checked="checked"<?php endif; ?>><?php echo $_action['Name']; ?>
                                        </label>
                                    <?php endforeach; ?>
                                    <p style="width:100%;clear:both;padding:0;margin:0"></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:20%;text-align:right">菜单排序：</td>
                    <td style="width:40%;padding-left:10px;"><label>
                            <input type="text" name="Sort" id="Sort" value="<?php echo isset($info['Sort']) ? $info['Sort'] : 0; ?>" />
                        </label></td>
                    <td>&nbsp; (分类排序,数值越大越往前) </td>
                </tr>
                <tr>
                    <td style="text-align:right">使用状态：</td>
                    <td style="padding-left:10px;">
                        <label>
                            <input name="Status" type="radio" value="1" <?php if(isset($info['Status']) && $info['Status'] == 1): ?>checked="checked"<?php endif; ?> />
                                    正常</label>
                        <label>
                            <input type="radio" name="Status" value="0" <?php if(isset($info['Status']) && $info['Status'] == 0): ?>checked="checked"<?php endif; ?> />
                                    禁用</label>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td style="padding-left:10px;"><label>
                            <input type="hidden" name="action" value="<?php echo $action; ?>" />
                            <input type="hidden" name="Id" value="<?php echo isset($info['Id']) ? $info['Id'] : ''; ?>" />
                            <input type="submit" name="button" id="button" value="提交">
                        </label></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php include_once ADMINVIEWPATH . 'foot.php'; ?>
</body>
</html>