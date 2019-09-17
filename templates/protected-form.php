<?php global $post; ?>
<style>
    .page-protected {
        display: block;
        overflow: hidden;
        margin: 100px auto;
        max-width: 600px;
    }

    .page-protected header {
        display: block !important;
        width: 100% !important;
        float: none !important; /* text-align: center; */
    }

    .page-protected .entry-content {
        width: 100% !important;
    }

    .insert-password-form {
        line-height: 27px;
        max-width: 520px; /* margin: 40px auto; */
    }

    .insert-password-form form {
    }

    .insert-password-form label {
        float: left;
        width: 100%;
        max-width: 130px;
    }

    .insert-password-form .input-wrapper {
        overflow: hidden;
        margin: 15px 0;
    }

    .insert-password-form .input-wrapper input {
        width: calc(100% - 2px);
        margin: 0;
        padding: 0;
        border: 1px solid #ccc;
        line-height: 40px;
        text-indent: 15px;
        outline: none;
        height: 40px;
        font-size: 16px;
    }

    .insert-password-form .input-wrapper input:focus {
        border-color: #000000;
    }

    .insert-password-form input[type=submit] {
        border: 1px solid transparent;
        line-height: 44px;
        padding: 0 22px;
    }

    .error-message{color:red}
</style>
<section class="page-protected">
     <div class="protected-page-image"><img src="<?php echo get_option('wewp_page_image'); ?>"></div>
    <header class="entry-header single-heading">
        <h1 class="entry-title"><?php echo get_option('wewp_page_title', 'Content protected'); ?></h1>
    </header>
    <div class="entry-content">
        <div class="insert-password-form">
            <form method="post" action="" class="">
                <?php wp_nonce_field('protected-form' . $post->ID); ?>
                <div>
                    <?php if(get_option('wewp_form_name_field_on', 'show') == 'show') :?>
                       <?php if(get_option('wewp_form_lable_on', 'show') == 'show') :?> 
                    <label for="name"><?php echo get_option('wewp_form_name_field_lable', 'Name'); ?></label>
                <?php endif ?>
                    <div class="input-wrapper">
                        <input type="text" name="protect_page_name" id="name"
                               placeholder="<?php echo get_option('wewp_form_name_field_placeholder', 'Name'); ?>"/>
                    </div>
                <?php endif; ?>
                </div>
                <div>
                    <?php if(get_option('wewp_form_email_field_on', 'show') == 'show') :?>
                        <?php if(get_option('wewp_form_lable_on', 'show') == 'show') :?> 
                    <label for="email"><?php echo get_option('wewp_form_email_field_lable', 'Email'); ?></label>
                    <?php endif; ?>
                    <div class="input-wrapper">
                        <input type="email" name="protect_page_email" id="email"
                               placeholder="<?php echo get_option('wewp_form_email_field_placeholder', 'Email'); ?>"/>
                    </div>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if(get_option('wewp_form_lable_on', 'show') == 'show') :?> 
                    <label for="sssd"><?php echo get_option('wewp_form_password_field_lable', 'Password'); ?></label>
                    <?php endif; ?>
                    <div class="input-wrapper">
                        <input type="text" name="protect_page_password" id="sssd"
                               placeholder="<?php echo get_option('wewp_form_password_field_placeholder', 'Password'); ?>"/>
                    </div>
                </div>

                <?php if (isset($_POST['protect_page_name']) && isset($displayForm) && !empty($displayForm->error)): ?>
                    <div class="error-message">
                        <label> </label><span><?php echo esc_html($displayForm->error); ?></span>
                    </div>
                <?php endif; ?>
                <div class="protected-form-submit">
                <input type="submit" value="<?php echo get_option('wewp_page_submit_text', 'SEND'); ?>"/>
            </div>
            </form>
        </div><!-- .entry-content -->
    </div>
</section><!-- #post -->
