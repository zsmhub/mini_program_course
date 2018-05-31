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
            <span class="action-span-left cur">当前操作 </span><span class="action-span-left" id="search_id"> - 菜单列表 </span>
            <div class="clear"></div>
        </h1>
    </div>
    <div class="list-div">
        <table cellspacing="0" cellpadding="0">
            <?php foreach($catelist as $cat): ?>
                <tr>
                    <td colspan="5" bgcolor="#B4D6E7">
                        <strong<?php if($cat['Status'] == 0): ?> style="color:#666;text-decoration:line-through" title="禁用状态"<?php endif; ?>><?php echo $cat['Title']; ?></strong> [
                        <?php if(user_auth("menu", "add_menu", "admin") == 1): ?>
                            <a href='<?php echo geturl("menu", "add_menu", "admin", "cid=" . $cat['Id']); ?>'>添加菜单</a>
                        <?php endif; ?>
                        <?php if(user_auth("menu", "edit_cat", "admin") == 1): ?>
                            | <a href='<?php echo geturl("menu", "edit_cat", "admin", "cid=" . $cat['Id']); ?>'>修改分类</a>
                        <?php endif; ?>
                        <?php if(user_auth("menu", "delete", "admin") == 1): ?>
                            | <a href='<?php echo geturl("menu", "delete", "admin", "id=" . $cat['Id']); ?>' onClick="if(!confirm('确定要删除当前分类吗？')) return false;">删除分类</a>
                        <?php endif; ?>
                        ]
                    </td>
                </tr>
                <tr>
                    <th align="center" style="width:120px;">菜单名称</th>
                    <th align="center" style="width:120px;">分类名称</th>
                    <th align="center" style="width:400px;">链接信息</th>
                    <th align="center" style="width:60px;">状态</th>
                    <th align="center" style="width:120px;">操作</th>
                </tr>
                <?php if( !empty($menulist[$cat['Id']])): ?>
                    <?php foreach($menulist[$cat['Id']] as $m): ?>
                        <tr>
                            <td align="center"><?php echo $m['Title']; ?></td>
                            <td align="center"><?php echo $cat['Title']; ?></td>
                            <td align="center"><?php echo $m['Url']; ?></td>
                            <td align="center"><?php if($m['Status'] == 1): ?>正常<?php else: ?>禁用<?php endif; ?></td>
                            <td align="center">
                                <?php if(user_auth("menu", "edit_menu", "admin") == 1): ?>
                                    <a href='<?php echo geturl("menu", "edit_menu", "admin", "Id=" . $m['Id']); ?>'>修改</a>
                                <?php endif ?>
                                <?php if(user_auth("menu", "delete", "admin") == 1): ?>
                                    | <a href='<?php echo geturl("menu", "delete", "admin", "id=" . $m['Id']); ?>' onClick="if(!confirm('确定要删除当前菜单吗？')) return false;">删除</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php include_once ADMINVIEWPATH . 'foot.php'; ?>
</body>
</html>