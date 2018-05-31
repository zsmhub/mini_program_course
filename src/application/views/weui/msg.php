<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
    <?php include_once '_head.php'; ?>
</head>
<body ontouchstart>

<div class="weui_msg">
    <div class="weui_icon_area">
        <?php if($type == 1): ?>
            <i class="weui_icon_success weui_icon_msg"></i>
        <?php else: ?>
            <i class="weui_icon_warn weui_icon_msg"></i>
        <?php endif; ?>
    </div>
    <div class="weui_text_area">
        <h2 class="weui_msg_title"><?php echo $msg; ?></h2>
    </div>
</div>

<?php include_once '_foot.php'; ?>
</body>
</html>