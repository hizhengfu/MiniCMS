<?php
ini_set("display_errors", "On"); error_reporting(E_ALL);

require_once __COMMON_PATH__ . '/mc-tags.php';
require_once __COMMON_PATH__ . '/markdown.php';

function mc_404()
{
    header('HTTP/1.0 404 Not Found');
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
}
?>
