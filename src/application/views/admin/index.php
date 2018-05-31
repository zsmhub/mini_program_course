<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE>
<html>
<head>
    <?php include_once ADMINVIEWPATH . 'head.php'; ?>
</head>
<body class="easyui-layout" fit="true">
<div id='loading'>
    <h1>
        <font>加载中···</font>
    </h1>
</div>
<div data-options="region:'north',border:false" class="head-north">
    <div class="head head-right">
        <div class='text'>
            <span>欢迎您！</span><span style="font-weight: bold;"><?php echo $top['NICKNAME']; ?></span>&nbsp;&nbsp;<span>[<?php echo $top['ROLENAME']; ?>]</span>
            <br/>
            <span>上次登录&nbsp;|&nbsp;时间:<?php echo empty($top['LASTLOGTIME']) ? '您是第一次登录' : $top['LASTLOGTIME']; ?>&nbsp;|&nbsp;IP:<a href="javascript:void(0)" title="查看IP所在地" class="underline" onClick="window.open('http://www.ip138.com/ips138.asp?ip=<?php echo $top['LASTLOGIP']; ?>')" style="font-weight:bold"><?php echo $top['LASTLOGIP']; ?></a></span>
        </div>
        <div class='img'>
            <a href="<?php echo geturl('user', 'logout', 'admin'); ?>" onclick="return confirm('您确认要退出吗?');"><img src="style/default/images/logout.jpg" width="67" alt="您确认要退出吗"></a>
        </div>
    </div>
    <div class="head-left">
        <span><?php echo get_system_name(); ?></span>
    </div>
    <div class="clear"></div>
</div>


<div data-options="region:'west',split:true" title="导航菜单" style="width: 180px; overflow-x: hidden;">
    <?php include_once ADMINVIEWPATH . 'menu_left.php' ?>
</div>

<div data-options="region:'center', border: false">
    <div id="tt" class="easyui-tabs" fit="true">
        <div title="首页">
            <div class="easyui-layout" fit="true">
                <div data-options="region: 'center', border: false">
                    <div style="margin: 20px; text-align: center;">
                        <div style="font-size: 40px;">
                            <p><?php echo get_system_name(); ?><p>
                            <p>欢迎您！</p>
                        </div>
                        <!-- <div style="margin-top: 20px;">
                            <h3>简介</h3>
                            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;本系统是一个管理系统，主要用于集团的资产管理，实现资产管理信息化、自动化，减少资产管理的工作量。</p>
                        </div>
                        <div style="margin-top: 20px;">
                            <h3>基本功能</h3>
                            <ul style="margin-left: 20px;">
                                <li>用户管理</li>
                                <li>角色管理</li>
                                <li>菜单管理</li>
                            </ul>
                        </div>
                        <div style="margin-top: 20px;">
                            <h3>技术交流</h3>
                            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;本系统由Forgame集团信息管理部开发,为大家提供良好的服务。</p>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div data-options="region: 'south'" class="footer">
    Copyright © 公选课后台管理系统
</div>

<?php include_once ADMINVIEWPATH . 'foot.php'; ?>
<script>
    //添加一个新的tabs
    function addPanel(URL, title, id) {
        var tab = $('#tt');
        var content = '<iframe scrolling="auto" frameborder="0" src="'+URL+'" style="width:100%;height:100%;"></iframe>';
        if (tab.tabs('exists', title)) {
            tab.tabs('select', title);
            var currentTab = tab.tabs('getSelected');
            tab.tabs('update', { tab: currentTab, options: { content: content } });
        } else {
            tab.tabs('add',{
                title:title,
                content:content,
                closable:true
            });
        }
        $('.menu_li').removeClass('on');
        $('#menu_li_' + id).addClass('on');
    }
</script>
</body>
</html>
