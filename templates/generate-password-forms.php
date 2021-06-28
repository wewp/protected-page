<script type="text/javascript">
    function generatePassword() {
        var text = "";
        var possible = "<?php echo PASSWORD_CHARACTERS; ?>";

        for (var i = 0; i < 10; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        document.getElementById('pass-code').value = text;
    }
</script>

<style>
    hr {
        margin: 30px 0;
    }

    h1 a {
        text-decoration: none
    }

    h1 a:hover {
        text-decoration: underline
    }
</style>
<?php if (DISPLAY_BANNER) : ?>
    <div style="background: #2e89fc;<?php if (is_rtl()) : ?>margin-right<?php else : ?>margin-left<?php endif; ?>: -20px;padding: 10px 21px;">
        <img src="<?php echo plugin_dir_url(__FILE__); ?>../assets/images/protected-page-logo-white.png" style="max-height: 50px;" />
    </div>
<?php endif; ?>
<div class="wrap">
    <h1 style="position: sticky;top: 31px;background: #f1f1f1;padding: 13px 0;">
        <a href="<?php echo admin_url(); ?>admin.php?page=protected-page" title="Back to choose page" style="margin-right: 15px;"><?php if (is_rtl()) : ?>
                <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                    <path fill="#000000" d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
                </svg>
            <?php else : ?>
                <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                    <path fill="#000000" d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z" />
                </svg>
            <?php endif; ?>
        </a>
        <?php echo __('Generate password for:', 'protected-page'); ?>
        <a target="_blank" href="/?p=<?php echo esc_html($_GET['page_id']); ?>">
            <?php echo esc_html(get_the_title($_GET['page_id'])); ?>
            <?php if (false) : ?>
                <?php if (is_rtl()) : ?>&#8592;<?php else : ?>&#8594;<?php endif; ?>
            <?php endif; ?>
        </a>
    </h1>

    <hr>

    <?php if (isset($display_page_not_protected) && $display_page_not_protected) : ?>
        <div class="notice notice-warning inline">
            <p><?php echo __('This page not set to protected yet, please set him to protected content', 'protected-page'); ?>
                .</p>
        </div>
        <hr>
    <?php endif; ?>

    <h2><?php echo __('Generate one password', 'protected-page'); ?></h2>
    <form method="post" action="<?php echo admin_url(); ?>admin.php?page=protected-page&page_id=<?php echo $page_id; ?>">
        <table class="form-table">
            <tbody>
                <tr>
                    <th><?php echo __('Password code:', 'protected-page'); ?></th>
                    <td>
                        <input type="text" name="code" id="pass-code" required maxlength="10" pattern="<?php echo PASSWORD_PATTERN ?>" />
                        <a href="#" onClick="javascript: generatePassword();" class="button delete"><?php echo __('Generate', 'protected-page'); ?></a>
                    </td>

                </tr>

                <tr>
                    <th><?php echo __('Number uses:', 'protected-page'); ?></th>
                    <td><input type="number" name="usages" /><?php echo __('(Leave blank for unlimited)', 'protected-page'); ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="submit" value="<?php echo __('Submit', 'protected-page'); ?>" class="button button-primary" />
    </form>
    <br>

    <hr>

    <?php do_action('render_generate_multiple_password_form', $page_id, (isset($add_success_message) ? $add_success_message : null)); ?>

    <?php
    //    if (pc_fs()->is_not_paying()) {
    //        echo '<section><h1>' . esc_html__('For generate multiple password per time', 'protected-page') . '</h1>';
    //        echo '<a href="' . pc_fs()->get_upgrade_url() . '">' .
    //            esc_html__('Upgrade Now!', 'my-plugin-slug') .
    //            '</a>';
    //        echo '</section>';
    //        echo '<hr>';
    //    }
    ?>

    <?php if (isset($add_success_message) && $add_success_message['message']) : ?>
        <div class="notice notice-success inline">
            <p><?php echo esc_html($add_success_message['message']); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($mess)) : ?>
        <div class="notice notice-success inline">
            <p><?php echo esc_html($mess); ?></p>
        </div>
    <?php endif; ?>
    <h2><?php echo __('Passwords', 'protected-page'); ?></h2>
    <style>
        thead th {
            position: sticky;
            top: 84px;
            background: white;
            z-index: 100;
        }

        tbody input[type=text]:after {
            content: '/ 50'
        }

        .reportrange {
            display: none
        }

        input[type=checkbox]:checked~.reportrange {
            display: inline-block
        }
    </style>
    <table id='passwords-table' class="wp-list-table widefat fixed striped posts password-list" style="width: 1650px;">
        <thead>
            <tr>
                <th style="width: 30px;">
                    #
                </th>
                <th style="width: 184px;">
                    <label for="date"><?php echo __('Password Code', 'protected-page'); ?></label>
                </th>

                <th>
                    <label for="city"><?php echo __('Usages left', 'protected-page'); ?></label>
                </th>
                <?php do_action('render_column_custom_restrict_condition'); ?>
                <th style="width:230px;"></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($rows as $key => $row) : ?>

                <tr>
                    <form method="post" action="<?php echo admin_url(); ?>admin.php?page=protected-page&page_id=<?php echo $page_id; ?>">
                        <td><?php echo $key + 1 ?>.</td>
                        <td class="code">
                            <input type="hidden" name="action" value="update_password" />
                            <input type="hidden" name="theid" value="<?php echo $row['id']; ?>" />
                            <input type="text" name="code" id="code_<?php echo esc_html($key) ?>" pattern="<?php echo PASSWORD_PATTERN ?>" value="<?php echo $row['code']; ?>" style="max-width: 121px;margin-right: 10px;" />
                            <svg style="width:18px;height:19px;cursor: pointer" viewBox="0 0 24 24" data-clipboard-target="#code_<?php echo esc_html($key) ?>" class="clipboard-btn">
                                <path fill="#000000" d="M19,21H8V7H19M19,5H8A2,2 0 0,0 6,7V21A2,2 0 0,0 8,23H19A2,2 0 0,0 21,21V7A2,2 0 0,0 19,5M16,1H4A2,2 0 0,0 2,3V17H4V3H16V1Z" />
                            </svg>
                        </td>
                        <td class="times">
                            <input type="text" name="usages_left" value="<?php echo esc_html($row['usages_left']) ?>" />
                            / <?php echo esc_html($row['usages']) ?>
                        </td>
                        <?php do_action('render_column_data_custom_restrict_condition', $row); ?>
                        <td class="acction-buttons">

                            <button type="submit" name="action" value="<?php echo __('update', 'protected-page'); ?>" class="button update button-primary">
                                <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                    <path fill="#fff" d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z" />
                                </svg>
                                <?php echo __('update', 'protected-page'); ?>
                            </button>
                            <button type="submit" name="action" value="<?php echo __('remove', 'protected-page'); ?>" class="button delete" style="margin-left: 10px;">
                                <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                    <path fill="#000000" d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9M7,6H17V19H7V6M9,8V17H11V8H9M13,8V17H15V8H13Z" />
                                </svg>
                                <?php echo __('remove', 'protected-page'); ?>
                            </button>
                            <div class="spinner"></div>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <hr>


</div>
<?php
