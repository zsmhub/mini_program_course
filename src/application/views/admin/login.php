<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE>
<html>
<head>
<?php
include_once ADMINVIEWPATH . 'head.php';
?>
<link rel='stylesheet' type='text/css' href="style/default/css/login.css?_=3">
</head>
<body>
<div class="login_header">
    <img src="style/default/images/logo.png" />
    <span><?php echo get_system_name(); ?></span>
</div>
<div class="htmleaf-container">
    <div class="wrapper">
        <div class="container">
            <h1 id="tip">Welcome</h1>

            <form class="form">
                <input type="text" name="username" id="username" placeholder="Username" />
                <input type="password" name="password" id="password" placeholder="Password" />
                <button type="submit" id="login-button">Login</button>
            </form>
        </div>

        <ul class="bg-bubbles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>
</div>

<?php include_once ADMINVIEWPATH . 'foot.php'; ?>
<script>
$('#login-button').on('click', function(event) {
    event.preventDefault();
    $('form').fadeOut(500);
    var username = $('#username').val();
    var pw = $('#password').val();
    if(username == '' || pw == '') {
        alert('账号和密码不能为空！');
        $('form').fadeIn(500);
        return false;
    }

    $('.wrapper').addClass('form-success');
    $('#tip').text('登录中, 请稍后...');

    $.post('<?php echo $url; ?>', $('.form').serialize(), function(r) {
        if( !r.success) {
            alert(r.msg);
            $('form').fadeIn(500);
            $('.wrapper').removeClass('form-success');
            $('#tip').text('Welcome');
            return false;
        }
        window.location.href = '<?php echo $link; ?>';
    }, 'json');
});
</script>
</body>
</html>