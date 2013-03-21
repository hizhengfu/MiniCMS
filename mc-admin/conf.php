<?php require 'head.php' ?>
<?php
$display_info = false;

if (isset($_POST['save'])) {
    $mc_config['site_name'] = isset($_POST['site_name']) ? $_POST['site_name'] : '';
    $mc_config['site_desc'] = isset($_POST['site_desc']) ? $_POST['site_desc'] : '';
    $mc_config['site_link'] = isset($_POST['site_link']) ? trim($_POST['site_link'], '/') . '/' : '';
    $mc_config['user_nick'] = isset($_POST['user_nick']) ? $_POST['user_nick'] : '';
    $mc_config['user_name'] = isset($_POST['user_name']) ? $_POST['user_name'] : '';
    $mc_config['comment_code'] = isset($_POST['comment_code']) ? (get_magic_quotes_gpc() ? stripslashes(trim($_POST['comment_code'])) : trim($_POST['comment_code'])) : '';

    $user_name_changed = $_POST['user_name'] != $mc_config['user_name'];

    $msg = '设置保存成功！';

    if ($_POST['user_pass'] != '') {
        if ($_POST['user_pass'] == $_POST['verify_user_pass']) {
            $mc_config['user_pass'] = encode_pass($_POST['user_pass']);
        } else {
            $msg = '密码不一致，请确认后输入！';
        }
    }


    $code = "<?php\n\$mc_config = " . var_export($mc_config, true) . "\n?>";

    file_put_contents('../' . __COMMON_DIR__ . '/mc-conf.php', $code);

    if ($_POST['user_pass'] != '' || $user_name_changed) {
        set_auth($mc_config['user_name'], $mc_config['user_pass']);
    }

    $display_info = true;
} else {

}

$site_name = $mc_config['site_name'];
$site_desc = $mc_config['site_desc'];
$site_link = $mc_config['site_link'];
$user_nick = $mc_config['user_nick'];
$user_name = $mc_config['user_name'];
$comment_code = isset($mc_config['comment_code']) ? $mc_config['comment_code'] : '';
?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="form-horizontal">
    <?php if ($display_info) { ?>
        <div class="alert alert-info"><?php echo $msg; ?>！</div>
    <?php } ?>
    <div class="admin_page_name">站点设置</div>
    <div>
        <div class="control-group">
            <label class="control-label" for="site_name">网站标题</label>

            <div class="controls">
                <input type="text" class="input-xxlarge" name="site_name" id="site_name" placeholder="请输入网站标题"
                       value="<?php echo htmlspecialchars($site_name); ?>">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="site_desc">网站描述</label>

            <div class="controls">
                <textarea rows="2" class="input-xxlarge" id="site_desc" placeholder="用简洁的文字没描述本站点。"
                          name="site_desc"><?php echo htmlspecialchars($site_desc); ?></textarea>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="site_link">网站地址</label>

            <div class="controls">
                <input type="text" class="input-xxlarge" name="site_link" id="site_link" placeholder="请输入网站标题"
                       value="<?php echo htmlspecialchars($site_link); ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="user_nick">站长昵称</label>

            <div class="controls">
                <input type="text" class="input-xxlarge" name="user_nick" id="user_nick" placeholder="请输入站长昵称"
                       value="<?php echo htmlspecialchars($user_nick); ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="user_name">登录帐号</label>

            <div class="controls">
                <input type="text" class="input-xxlarge" name="user_name" id="user_name" placeholder="请输入登录帐号"
                       value="<?php echo htmlspecialchars($user_name); ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="user_pass">登录密码</label>

            <div class="controls">
                <input type="password" class="input-xxlarge" name="user_pass" id="user_pass" placeholder="请输入登录密码">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="verify_user_pass">确认密码</label>

            <div class="controls">
                <input type="password" class="input-xxlarge" name="verify_user_pass" id="verify_user_pass"
                       placeholder="请输入确认密码">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="comment_code">评论代码</label>

            <div class="controls">
                <textarea rows="5" class="input-xxlarge" id="comment_code" placeholder="请输入评论代码。"
                          name="comment_code"><?php echo htmlspecialchars($comment_code); ?></textarea>
                <span class="help-block input-xxlarge">第三方评论服务所提供的评论代码，例如：<a href="http://disqus.com/" target="_blank">Disqus</a>、<a
                        href="http://open.weibo.com/widget/comments.php" target="_blank">新浪微博评论箱</a>
                                            等。设置此选项后，就拥有了评论功能。</span>
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <button type="submit" class="btn btn-large" name="save">保存设置</button>
            </div>
        </div>
    </div>
</form>
<?php require 'foot.php' ?>
