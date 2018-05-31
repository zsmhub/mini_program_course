<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE>
<html>
<head>
    <?php include_once ADMINVIEWPATH . 'head.php'; ?>
</head>
<body class="easyui-layout">
<div data-options="region:'center', border: false, title: '<?php echo $title_msg; ?>'" class='msg'>
    <p><?php echo $msg; ?></p>
</div>
<?php include_once ADMINVIEWPATH . 'foot.php'; ?>
</body>
</html>