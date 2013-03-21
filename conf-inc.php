<?php
if (defined('__ROOT_DIR__')) return 0;
/** 定义根目录 */
define('__ROOT_DIR__', dirname(__FILE__));
define('__COMMON_DIR__', 'mc-files');
define('__ADMIN_DIR__', 'mc-admin');
define('__COMMON_PATH__', __ROOT_DIR__ . '/' . __COMMON_DIR__);
define('__ADMIN_PATH__', __ROOT_DIR__ . '/' . __ADMIN_DIR__);

require_once __COMMON_PATH__ . '/mc-conf.php';
