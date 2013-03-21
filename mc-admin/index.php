<?php
require_once '../conf-inc.php';
require_once __COMMON_PATH__ . '/functions.php';
if ($token = get_auth()) {
    if (check_token($mc_config['user_name'], $mc_config['user_pass'],$token)) {
        Header('Location:post.php');
        exit();
    }
}

if (isset($_POST['login'])) {
    if ($_POST['user'] == $mc_config['user_name']
        && $_POST['pass'] == decode_pass($mc_config['user_pass'])
    ) {
        $expire = (isset($_POST['rememberme']) ? $_POST['rememberme'] : null) == 'forever' ? 365 * 86400 : 0;
        empty($expire) ? $expire = null : $expire += time();
        set_auth($mc_config['user_name'], $mc_config['user_pass'],$expire);
        Header('Location:post.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>内容管理</title>
    <style type="text/css">
        *{font-family:"Microsoft YaHei", Segoe UI, Tahoma, Arial, Verdana, sans-serif;}
        body{background:#f9f9f9;font-size:14px;}
        #login_title{text-align:center;width:360px;margin:60px auto;margin-bottom:0;font-size:32px;color:#333;text-shadow:0 2px 0 #FFFFFF;}
        #login_form{width:360px;margin:0 auto;margin-top:20px;border:solid 1px #e0e0e0;background:#fff;border-radius:3px 3px 3px 3px;}
        #login_form_box{padding:16px;}
        #login_form label{font-weight:bold;padding-bottom:6px;color:#333;display: block;}
        #login_form .textbox input{border:none;padding:0;font-size:24px;width:312px;color:#333;}
        #login_form .textbox{border:1px solid #e0e0e0;padding:6px;margin-bottom:20px;border-radius:3px 3px 3px 3px;}
        #login_form .bottom{text-align:right;position:relative;}
        #login_form .button{padding:8px 16px;font-size:14px;}
        #login_form .bottom label{display:inline-block;position:absolute;left:0;}
    </style>
</head>
<body>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <div id="login_title">登录后台</div>
    <div id="login_form">
        <div id="login_form_box">
            <label for="user">帐号:</label>
            <div class="textbox"><input name="user" id="user" type="text" tabindex="1" placeholder="用户名..."/></div>
            <label for="password">密码:</label>
            <div class="textbox"><input name="pass" id="password" type="password" tabindex="2" placeholder="密码..."/></div>
            <div class="bottom"><label><input type="checkbox" name="rememberme" tabindex="3" value="forever">记住我</label>
                <input name="login" type="submit" tabindex="4" value="登录" class="button"/></div>
        </div>
    </div>
</form>
</body>
</html>
