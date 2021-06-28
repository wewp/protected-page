<?php

global $wpdb;

//$sql = "SELECT count(id) as count,`page_id` FROM {$this->guard_content->getTableName()}  group by `page_id`";
//$rows = $wpdb->get_results($sql, ARRAY_A);
//
//$sql = "SELECT `page_id` FROM {$this->guard_content->getTableName()}  WHERE `uses`>0 group by `page_id`";
//$protected_pages = $wpdb->get_results($sql);

$pages = get_posts(array(
    'post_type' => $selected_post_type,
    'numberposts' => -1,
));

$current_page_cpt_name = isset($_GET['post_type']) ? $_GET['post_type'] : 'page';
$cpt_is_protect = (bool)get_option('cpt_toggle_' . $current_page_cpt_name, true);
//if (!empty($pages)) {
//    foreach ($pages as $key => $page) {
//        foreach ($rows as $row) {
//            if ($row['page_id'] == $page->ID) {
//                $page->uses = $row['count'];
//            }
//        }
//
//        foreach ($protected_pages as $protected_page) {
//            if ($protected_page->page_id == $page->ID) {
//                $page->is_protected_page = true;
//            }
//        }
//    }
//
//}
?>


<style>
    .widefat td a:not(.button button-primary):hover {
        text-decoration: underline;
    }
</style>
<?php if (DISPLAY_BANNER) : ?>
    <div style="background: #2e89fc;<?php if (is_rtl()) : ?>margin-right<?php else : ?>margin-left<?php endif; ?>: -20px;padding: 10px 21px;">
        <img src="<?php echo plugin_dir_url(__FILE__); ?>../assets/images/protected-page-logo-white.png" style="max-height: 50px;" />
    </div>
<?php endif; ?>
<div class="wrap">
    <h3 class="wp-heading-inline"><?php echo __('Select page to generate passwords', 'protected-page'); ?></h3>



    <h2 class="nav-tab-wrapper">
        <?php foreach ($supported_post_types as $post_type => $post_label) : ?>
            <a href="<?php echo admin_url(); ?>admin.php?page=protected-page&post_type=<?php echo esc_html($post_type) ?>" class="nav-tab <?php if ($post_type === $selected_post_type) : ?>nav-tab-active<?php endif; ?>"><?php echo esc_html($post_label) ?></a>
        <?php endforeach; ?>
        <!--            --><?php //if (pc_fs()->is_not_paying()) : 
                            ?>
        <!--                <a href="--><?php //echo pc_fs()->get_upgrade_url() 
                                        ?>
        <!--"-->
        <!--                   target="_blank"-->
        <!--                   class="nav-tab ">Upgrade Now! For more CPT</a>-->
        <!--            --><?php //endif; 
                            ?>
    </h2>

    <?php if (empty($pages)) : ?>
        <br>
        <br>
        <div class="notice notice-info inline">
            <p><?php echo __('No posts found in the requested type', 'wewp-protected-page'); ?>.</p>
        </div>
    <?php else : ?>
        <style>
            thead th {
                position: sticky;
                top: 32px;
                background: white;
                z-index: 100
            }
        </style>


        <div class="wrapper <?php if ($cpt_is_protect) {
                                echo 'only-cpt';
                            } ?>">
            <?php do_action('render_protect_post_type_by_user_roles'); ?>

            <table style="margin-top: 2em;" class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th style="width: 30px">#</th>
                        <th class="row-title">
                            <a style="color: #32373c;"><span><?php echo __('Post title', 'protected-page'); ?></span></a>
                        </th>
                        <th class="row-title" style="width: 150px;">
                            <?php echo __('Protected', 'protected-page'); ?>
                        </th>
                        <?php do_action('render_restrict_all_page_column'); ?>
                        <?php do_action('render_page_user_role_column'); ?>
                        <?php do_action('render_redirect_when_user_has_no_access_column'); ?>
                        <th class="row-title">
                            <?php echo __('Action', 'protected-page'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody id="the-list">
                    <?php foreach ($pages as $key => $page) : ?>
                        <tr class="<?php if ($page->is_page_protected) : ?>is-protected<?php endif; ?>">
                            <td style="text-align: center;"><?php echo $key + 1 ?>.</td>
                            <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                                <a style="color: #32373c;" target="_blank" href="<?php echo esc_url(get_permalink($page->ID)); ?>">
                                    <?php echo esc_html($page->post_title); ?> <?php if (is_rtl()) : ?>&#8592;<?php else : ?>&#8594;<?php endif; ?>
                                </a>
                            </td>
                            <td>
                                <section class="protected-page-switcher is-page-protected">
                                    <input type="checkbox" name="protected" id="switcher-<?php echo $key; ?>" data-page_id="<?php echo esc_attr($page->ID); ?>" data-page_title="<?php echo esc_attr($page->post_title); ?>" <?php if ($page->is_page_protected) : ?>checked<?php endif; ?> />
                                    <label for="switcher-<?php echo $key; ?>"></label>
                                    <div class="spinner"></div>
                                </section>
                            </td>
                            <?php do_action('render_restrict_all_page_data_column', $page, $key); ?>
                            <?php do_action('render_user_role_data_column', $page, $key); ?>
                            <?php do_action('render_redirect_when_user_has_no_access_data_column', $page, $key); ?>
                            <td>
                                <a href="<?php echo admin_url(); ?>admin.php?page=<?php echo $this->parent_menu_slug ?>&page_id=<?php echo esc_html($page->ID); ?>" class="button button-primary generate-password-link"><?php echo __('Generate password', 'protected-page'); ?></a>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php
