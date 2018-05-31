<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="easyui-accordion" data-options="fit:true,border:false">
    <?php foreach($menu['cat'] as $cat): ?>
        <div title="<?php echo $cat['Title']; ?>" class="left_menu">
            <ul>
                <?php foreach($menu['list'][$cat['Id']] as $list): ?>
                    <li>
                        <div>
                            <a href="javascript:void(0);" onclick="addPanel('<?php echo $list['Url']; ?>', '<?php echo $list['Title']; ?>', '<?php echo $list['Id']; ?>')">
                                <span class="icon">&nbsp;</span>
                                <span class="nav"><?php echo $list['Title']; ?></span>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>