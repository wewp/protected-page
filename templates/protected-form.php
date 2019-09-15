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
        background-color: #000000;
        border: 1px solid transparent;
        color: white;
        line-height: 44px;
        font-size: 20px;
        padding: 0 22px;
    }

    .insert-password-form input[type=submit]:hover {
        color: black;
        background: white;
        border-color: #0F0F0F
    }
    .error-message{color:red}
</style>
<section class="page-protected">
    <header class="entry-header single-heading">
        <h1 class="entry-title"><?php echo __('Content protected', 'protected-page'); ?></h1>
    </header>
    <div class="entry-content">
        <div class="insert-password-form">
            <form method="post" action="" class="">
                <?php wp_nonce_field('protected-form' . $post->ID); ?>
                <div>
                    <label for="name"><?php echo __('Name', 'protected-page'); ?></label>
                    <div class="input-wrapper">
                        <input type="text" name="protect_page_name" id="name"
                               placeholder="<?php echo __('Name', 'protected-page'); ?>"/>
                    </div>
                </div>
                <div>
                    <label for="email"><?php echo __('Email', 'protected-page'); ?></label>
                    <div class="input-wrapper">
                        <input type="email" name="protect_page_email" id="email"
                               placeholder="<?php echo __('Email', 'protected-page'); ?>"/>
                    </div>
                </div>
                <div>
                    <label for="sssd"><?php echo __('Password *', 'protected-page'); ?></label>
                    <div class="input-wrapper">
                        <input type="text" name="protect_page_password" id="sssd"
                               placeholder="<?php echo __('Password', 'protected-page'); ?>"/>
                    </div>
                </div>

                <?php if (isset($_POST['protect_page_name']) && isset($displayForm) && !empty($displayForm->error)): ?>
                    <div class="error-message">
                        <label> </label><span><?php echo esc_html($displayForm->error); ?></span>
                    </div>
                <?php endif; ?>
                <input type="submit" value="<?php echo __('Send', 'protected-page'); ?>"/>
            </form>
        </div><!-- .entry-content -->
    </div>
</section><!-- #post -->
