<?php
require 'head.php';

$post_state = '';
$post_title = '';
$post_content = '';
$post_date = '';
$post_time = '';
$post_can_comment = '';
$error_msg = '';
$succeed = false;

$page_type = 'post';
$tip = '文章';

$page_path = '';
$post_tags = array();
$post_id = '';

if ($page_file == 'page-edit.php') {
    $page_type = 'page';
    $tip = '页面';
}

if (isset($_POST['_IS_POST_BACK_'])) {
    $post_id = isset($_POST['id']) ? $_POST['id'] : '';
    if ($page_type == 'post') {
        $post_tags = explode(',', isset($_POST['tags']) ? trim($_POST['tags']) : '');
        $post_tags_count = count($post_tags);

        for ($i = 0; $i < $post_tags_count; $i++) {
            $trim = trim($post_tags[$i]);
            if ($trim == '') {
                unset($post_tags[$i]);
            } else {
                $post_tags[$i] = $trim;
            }
        }

        reset($post_tags);
    } else {
        $page_path = isset($_POST['path']) ? $_POST['path'] : '';
        $page_path_part = explode('/', $page_path);
        $page_path_count = count($page_path_part);

        for ($i = 0; $i < $page_path_count; $i++) {
            $trim = trim($page_path_part[$i]);
            if ($trim == '') {
                unset($page_path_part[$i]);
            } else {
                $page_path_part[$i] = $trim;
            }
        }

        reset($page_path_part);

        $page_path = implode('/', $page_path_part);
        if ($page_path == '') $error_msg = '页面路径不能为空';
    }

    $post_state = isset($_POST['state']) ? $_POST['state'] : '';
    $post_title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $post_content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $post_content = get_magic_quotes_gpc() ? stripslashes($post_content) : $post_content;
    $post_date = date("Y-m-d");
    $post_time = date("H:i:s");
    $post_can_comment = isset($_POST['can_comment']) ? $_POST['can_comment'] : '';

    if (isset($_POST['date']) && $_POST['date'] != '')
        $post_date = $_POST['date'];

    if (isset($_POST['time']) && $_POST['time'] != '')
        $post_time = $_POST['time'];


    if ($post_title == '') {
        $error_msg = $tip . '标题不能为空';
    } else {
        if ($post_id == '') {
            $file_names = shorturl($post_title);

            foreach ($file_names as $file_name) {
                $file_path = __COMMON_PATH__ . "/{$page_type}s/data/{$file_name}.dat";

                if (!is_file($file_path)) {
                    $post_id = $file_name;
                    break;
                }
            }
        } else {
            $file_path = __COMMON_PATH__ . "/{$page_type}s/data/{$post_id}.dat";
            $data = unserialize(file_get_contents($file_path));
            $post_old_state = $data['state'];
            if ($page_type == 'page') {
                $page_old_path = $data['path'];
                if ($post_old_state != $post_state || $page_old_path != $page_path) {
                    $index_file = __COMMON_PATH__ . '/pages/index/' . $post_old_state . '.php';

                    require $index_file;

                    unset($mc_posts[$page_old_path]);

                    file_put_contents($index_file,
                        "<?php\n\$mc_posts=" . var_export($mc_posts, true) . "\n?>"
                    );
                }
            } else {
                if ($post_old_state != $post_state) {
                    $index_file = __COMMON_PATH__ . "/{$page_type}s/index/{$post_old_state}.php";

                    require $index_file;

                    unset($mc_posts[$post_id]);

                    file_put_contents($index_file,
                        "<?php\n\$mc_posts=" . var_export($mc_posts, true) . "\n?>"
                    );
                }
            }
        }

        $data = array(
            'id' => $post_id,
            'state' => $post_state,
            'title' => $post_title,
            'date' => $post_date,
            'time' => $post_time,
            'can_comment' => $post_can_comment,
        );
        if ($page_type == 'post') {
            $data['tags'] = $post_tags;
        } else {
            $data['path'] = $page_path;
        }


        $index_file = __COMMON_PATH__ . "/{$page_type}s/index/{$post_state}.php";

        require $index_file;

        if ($page_type == 'post') {
            $mc_posts[$post_id] = $data;
            uasort($mc_posts, "post_sort");
        } else {
            $mc_posts[$page_path] = $data;
            ksort($mc_posts);
        }

        file_put_contents($index_file,
            "<?php\n\$mc_posts=" . var_export($mc_posts, true) . "\n?>"
        );

        $data['content'] = $post_content;

        file_put_contents($file_path, serialize($data));

        $succeed = true;
    }
} else if (isset($_GET['id'])) {
    $file_path = __COMMON_PATH__ . "/{$page_type}s/data/{$_GET['id']}.dat";

    $data = unserialize(file_get_contents($file_path));

    $post_id = $data['id'];
    $post_state = $data['state'];
    $post_title = $data['title'];
    $post_content = $data['content'];
    $post_date = $data['date'];
    $post_time = $data['time'];
    $post_can_comment = isset($data['can_comment']) ? $data['can_comment'] : '1';


    if ($page_type == 'post') {
        $post_tags = $data['tags'];
    } else {
        $page_path = $data['path'];
    }
}
?>
<script type="text/javascript">

</script>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="_IS_POST_BACK_" value=""/>
    <?php if ($succeed) { ?>
        <?php if ($post_state == 'publish') { ?>
            <div class="alert alert-success"><?php echo $tip;?>已发布。 <a
                    href="../?<?php echo $page_type == 'post' ? "{$page_type}/{$post_id}" : "$page_path/"; ?>"
                    target="_blank">查看<?php echo $tip;?></a></div>
        <?php } else { ?>
            <div class="alert alert-success"><?php echo $tip;?>已保存到“草稿箱”。 <a href="post.php?state=draft">打开草稿箱</a></div>
        <?php } ?>
    <?php } ?>
    <div class="admin_page_name">
        <?php if ($post_id == '') echo "撰写" . $tip; else echo "编辑" . $tip; ?>
    </div>
    <div style="margin-bottom:20px;">
        <input name="title" type="text" class="input-block-level edit_textbox" placeholder="在此输入标题" value="<?php
        echo htmlspecialchars($post_title);
        ?>"/>
    </div>
    <div style="margin-bottom:20px;">
        <textarea name="content" placeholder="在此输入内容，支持Markdown格式。"
                  class="input-block-level edit_textarea"><?php echo htmlspecialchars($post_content); ?></textarea>
    </div>
    <div style="margin-bottom:20px;">
        <input name="<?php echo $page_type == 'post' ? 'tags' : 'path' ?>" type="text"
               class="input-block-level edit_textbox"
               placeholder="<?php echo $page_type == 'post' ? '在此输入标签，多个标签用英语逗号(,)分隔' : '在此输入页面路径，多级路径用英语斜杠(/)分割'; ?>"
               value="<?php
               if ($page_type == 'post') {
                   if (count($post_tags) > 0) {
                       echo htmlspecialchars(implode(',', $post_tags));
                   }
               } else {
                   if (!empty($page_path)) {
                       echo htmlspecialchars($page_path);
                   }
               }
               ?>"/>
    </div>
    <div class="input-prepend input-append">
        <span class="add-on">日期:</span>
        <input class="span2" id="date" name="date" placeholder="yyyy-MM-dd" type="text"
               value="<?php echo $post_date ?>">
        <span class="add-on">时间:</span>
        <input class="span2" id="time" name="time" placeholder="hh-mm-ss" type="text" value="<?php echo $post_time ?>">
        <span class="add-on">可以为空，默认为当前时间。</span>
    </div>
    <div class="input-prepend f-right">
        <span class="add-on">状态:</span>
        <select name="state" style="width:100px;">
            <option value="publish" <?php if ($post_state == 'publish') echo 'selected="selected"'; ?>>发布</option>
            <option value="draft" <?php if ($post_state == 'draft') echo 'selected="selected"'; ?>>草稿</option>
        </select>
    </div>
    <?php if (!empty($mc_config['comment_code'])) { ?>
        <div class="input-prepend f-right" style="margin-right: 6px;">
            <span class="add-on">评论:</span>
            <select name="can_comment" style="width:100px;">
                <option value="1" <?php if ($post_can_comment == '1') echo 'selected="selected";'; ?>>允许</option>
                <option value="0" <?php if ($post_can_comment == '0') echo 'selected="selected";'; ?>>禁用</option>
            </select>
        </div>
    <?php }?>
    <input type="hidden" name="id" value="<?php echo $post_id; ?>"/>

    <div class="t-r">
        <input type="submit" class="btn btn-large btn-primary" name="save" value="保存"/>
    </div>
</form>
<?php require 'foot.php' ?>
