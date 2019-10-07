<?php
define('PROTECTED_PAGE_PRO_VERSION', '1.0.10');

if (!defined('DISPLAY_BANNER')) {
    define('DISPLAY_BANNER', true);
}

require plugin_dir_path(__FILE__) . 'class/protected-page-pro.php';

function run_protected_page_pro()
{
    $plugin = new Protected_Page_Pro();
}

run_protected_page_pro();
