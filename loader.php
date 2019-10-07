<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('DISPLAY_BANNER')) {
    define('DISPLAY_BANNER', true);
}
define('PASSWORD_CHARACTERS', 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789!@#$%_^&*()?{}|;:~');
define('PASSWORD_PATTERN', "[abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789!@#$%^&_*()?{}|;:~]+");

function updateDatabase()
{
    global $wpdb;

    $table_name_pp_passwords = $wpdb->prefix . "pp_passwords";
    $sql_pp_passwords = "CREATE TABLE {$table_name_pp_passwords} (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `code` varchar(75) NOT NULL,
  `usages` int(11) NOT NULL,
  `usages_left` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_role` varchar(45) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `code_UNIQUE` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_name_password_protected_analytics = $wpdb->prefix . "pp_analytics";
    $sql_password_protected_analytics = "CREATE TABLE {$table_name_password_protected_analytics} (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL,
  `email` varchar(75) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `post_id` int(11) NOT NULL,
  `post_title` varchar(250) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_password_protected_analytics);
    dbDelta($sql_pp_passwords);
}


function init_plugin_protected_page()
{
    register_activation_hook(__FILE__, 'updateDatabase');
    require_once 'Wewp_Protected_Page.php';
    $plugin = new Wewp_Protected_Page();
}

init_plugin_protected_page();
