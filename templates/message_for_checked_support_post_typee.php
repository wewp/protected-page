<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo __('Protected Page', 'protected-page'); ?></h1>
    <h3 class="wp-heading-inline"><?php echo __('Pick a page to manage passwords', 'protected-page'); ?></h3>

    <div class="notice notice-warning is-dismissible inline">
        <p>
            <?php echo __('You need to choose supported post type.', 'protected-page'); ?>
            <a href="<?php echo admin_url(); ?>admin.php?page=protected-page_setting"><?php echo __('Click here to start','wewp-protected-page');?> <?php if (is_rtl()): ?>&#8592;<?php else: ?>&#8594;<?php endif; ?></a>
        </p>
    </div>
</div>
