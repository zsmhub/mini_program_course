<!DOCTYPE>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="style/forgame.ico">
    <title><?php echo $title; ?></title>
    <style>
        body {
            background-color: #e1f0d7;
            text-align: center;
        }
        #content {
            font-size: 18px;
            font-weight: bold;
            margin: 50px;
        }
        #content .div_text {
            margin-bottom: 30px;
            color: #000033;
            font-size: 15px;
        }
        /** 自定义按钮输入框样式 **/
        .sm-button {
            height: auto;
            width: auto;
            background-color: #428bca;
            border-color: #357ebd;
            color: #fff;
            -moz-user-select: none;
            background-image: none;
            border: 1px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: normal;
            line-height: 1.42857;
            margin-bottom: 0;
            padding: 6px 12px;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }
        .sm-button:hover, .sm-button:focus, .sm-button.focus, .sm-button:active, .sm-button.active {
            background-color: #3071a9;
            border-color: #285e8e;
            color: #fff;
        }
    </style>
    <style type="text/css" media=print>
        /*不打印的内容*/
        .no_print {
            display: none
        }
    </style>
</head>

<body>
<div id='loading' style="position: absolute; z-index: 1000; top: 0px; left: 0px;
    width: 100%; height: 100%; background: #fff; text-align: center;">
    <h1 style="top: 48%; position: relative;">
        <font color="#15428B">加载中···</font>
    </h1>
</div>
<div id="content">
    <div class="div_text">
        <p><?php echo $content; ?></p>
    </div>
    <div>
        <img src="<?php echo $img; ?>" alt="加载中。。。" />
    </div>
    <div class="no_print" style="margin-top: 30px;">
        <input class="sm-button" type="button" value="打印" onclick="javascript:window.print();" />
    </div>
</div>

<script type="text/javascript" src="style/default/js/jquery.min.js"></script>
<script>
    //二维码图片加载完成
    // $('img').load(function(){
    //     $('#loading').fadeOut("normal", function() {
    //         $(this).remove();
    //     });
    // });
    $('#loading').fadeOut("normal", function() {
        $(this).remove();
    });
</script>
</body>
</html>