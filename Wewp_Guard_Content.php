<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Wewp_Guard_Content
{
    private $displayFormErrorMessage = false;
    private $errorFormMessage = 'Invalid field\s value';
    private $insert_valid_pass_cookie_name = '234kjn23kj4n';
    private $hours_per_usage_password = 5;
    private $current_user = null;

    public function __construct()
    {
        $this->addFilters();
        $this->addAction();
    }

    private function addFilters()
    {
        add_filter('the_content', array($this, 'protectContent'));
        add_action('template_redirect', array($this, 'protectAllPage'), -1);
    }

    private function addAction()
    {
    }

    public function protectAllPage()
    {
        global $post;

        $displayForm = $this->checkDisplayForm();
        if ($displayForm !== false && !empty($displayForm->error)) {

            if (empty($post->is_all_page_protected)) {
                return;
            }

            $default_theme_file = dirname(__FILE__) . '/templates/login-page.php';
            load_template($default_theme_file);
            exit();
        }

        return;
    }

    public function getTableName()
    {
        global $wpdb;
        return "{$wpdb->prefix}pp_passwords";
    }

    public function getAnalyticsTableName()
    {
        global $wpdb;
        return "{$wpdb->prefix}pp_analytics";
    }

    public function protectContent($content)
    {
        if (!in_the_loop()) {
            return $content;
        }

        if (!is_singular()) {
            return $content;
        }

        if (!is_main_query()) {
            return $content;
        }

        //#TODO add check post type supported
        $displayForm = $this->checkDisplayForm();
        if ($displayForm !== false && !empty($displayForm->error)) {
            return $this->getGuardForm($displayForm);
        }
        return $content;
    }

    /**
     * Check for display $content.
     *
     * @since    1.0.0
     * @access   public
     */
    public function checkDisplayForm()
    {
        global $post;
        $page_id = $post->ID;
        $return = new stdClass();
        $return->error = '';

        /**
         * Specific post with specific user level
         */
        $post_protect_by_user_role = get_post_meta($page_id, 'page_protected_user_role', true);


        if ($this->isCPTLimitAuthorization()) {
            if (!$this->validateCPTAuthorization()) {
                $return->error = __('Invalid CPT Authorization', 'protected-page');
                return $return;
            }
            return false;
        }


        if (!isset($post->is_page_protected) || $post->is_page_protected === '0') {
            return false;
        }

        if ($post_protect_by_user_role) {
            if (!$this->validateUserRole($post_protect_by_user_role)) {
                $return->error = __('Invalid user role', 'protected-page');
                return $return;
            }
            return  false;
        }

        $_wpnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : '';
        $verify_nonce = wp_verify_nonce($_wpnonce, 'protected-form' . $post->ID);
        if (false === $verify_nonce) {
            $return->error = __('Invalid fields', 'protected-page');
            return $return;
        }

        $name = isset($_POST['protect_page_name']) && !empty($_POST['protect_page_name']) ? sanitize_text_field($_POST['protect_page_name']) : null;
        $email = isset($_POST['protect_page_email']) && !empty($_POST['protect_page_email']) ? sanitize_text_field($_POST['protect_page_email']) : null;
        $password = isset($_POST['protect_page_password']) && !empty($_POST['protect_page_password']) ? sanitize_text_field($_POST['protect_page_password']) : null;

        if ($post->page_protected_user_role) {
        }
        if (!is_email($email)) {
            $return->error = __('Invalid fields', 'protected-page');
            return $return;
        }

        if (!is_string($name)) {
            $return->error = __('Invalid fields', 'protected-page');
            return $return;
        }

        if (!is_string($password)) {
            $return->error = __('Invalid fields', 'protected-page');
            return $return;
        }


        $password_row = $this->getPasswordRow($page_id, $password);

        if (empty($password_row)) {
            $return->error = __('Invalid fields', 'protected-page');
            return $return;
        }

        if (!$this->validateUser($password_row)) {
            $return->error = __('Invalid permissions', 'protected-page');
            return $return;
        }

        if (!$this->validateUserExpirationDate($password_row)) {
            $return->error = __('Permissions expired', 'protected-page');
            return $return;
        }

        if (!$this->validateUserRole($password_row->user_role)) {
            $return->error = __('Invalid permissions', 'protected-page');
            return $return;
        }

        $usage_number = max(0, $password_row->usages_left - 1);
        $this->updatePasswordUsage($usage_number, $password);
        return false;
    }

    private function validateCPTAuthorization()
    {
        global $post;

        $user = wp_get_current_user();
        if (!$user) {
            return false;
        }

        $cpt_levels = get_option('protect_post_type_by_user_roles');
        if (!isset($cpt_levels[$post->post_type])) {
            return true;
        }

        $current_cpt_levels = $cpt_levels[$post->post_type];

        $logged_user_roles = wp_parse_args(get_the_author_meta('protect_post_type_by_user_roles', $user->ID));
        $hasAuthorization = false;
        foreach ($logged_user_roles as $key => $logged_user_role) {
            $hasAuthorization = in_array($logged_user_role, $current_cpt_levels);
            // var_dump([
            //     '$logged_user_role' => $logged_user_role,
            //     '$current_cpt_levels' => $current_cpt_levels,
            //     '$logged_user_roles' => $logged_user_roles,
            //     '$cpt_levels' => $cpt_levels,
            // ]);
            // die('1');
            if ($hasAuthorization) break;
        }

        return $hasAuthorization;
    }
    private function isCPTLimitAuthorization()
    {
        global $post;
        $option_key = 'cpt_toggle_' . $post->post_type;
        return get_option($option_key);
    }
    /**
     * If $passwordRow set user_id return true if logged user id === $passwordRow['user_id']  else return false
     *
     * @param $passwordRow
     * @return bool
     * @since    1.0.3
     * @access   private
     */
    private function validateUser($passwordRow)
    {
        if ($passwordRow->user_id === null) {
            return true;
        }

        $loggedUser = $this->getCurrentUser();

        // when $loggedUser->ID === 0 it's no logged user
        if ($loggedUser->ID === 0) {
            return false;
        }

        if ($loggedUser->ID !== (int)$passwordRow->user_id) {
            return false;
        }

        return true;
    }

    /**
     * If $passwordRow set user_role return true if logged user role === $passwordRow['user_role'] else return false
     *
     * @param $passwordRow
     * @return bool
     * @since    1.0.3
     * @access   private
     */
    private function validateUserRole($user_role)
    {
        if ($user_role === null || $user_role === 'administrator') {
            return true;
        }

        $loggedUser = $this->getCurrentUser();

        // when $loggedUser->ID === 0 it's no logged user
        if ($loggedUser->ID === 0) {
            return false;
        }

        return in_array($user_role, $loggedUser->roles);
    }

    /**
     * If $passwordRow set start_time & end_time return true if time() >= $passwordRow['start_time'] && time() =< $passwordRow['end_time'] else return false
     *
     * @param $passwordRow
     * @return bool
     * @since    1.0.3
     * @access   private
     */
    private function validateUserExpirationDate($passwordRow)
    {
        if ($passwordRow->start_time === null) {
            return true;
        }
        $now = current_time('timestamp');
        $start_time = strtotime($passwordRow->start_time);
        $end_time = strtotime($passwordRow->end_time);

        if ($start_time <= $now && $now <= $end_time) {
            return true;
        }

        return false;
    }

    private function getCurrentUser()
    {
        if ($this->current_user !== null) {
            return $this->current_user;
        }

        return $this->current_user = wp_get_current_user();
    }

    private function getPasswordRow($page_id = null, $password = null)
    {
        if ($password === null || !is_numeric($page_id)) {
            return null;
        }
        global $wpdb;

        $sql = "SELECT * FROM {$this->getTableName()} WHERE `page_id` = '%s'  AND `usages_left` > 0 AND `code`='%s'";
        $query = $wpdb->prepare($sql, $page_id, $password);
        return $wpdb->get_row($query);
    }

    private function updatePasswordUsage($usage_number, $password)
    {
        global $wpdb;
        global $post;

        $values = array(
            'usages_left' => $usage_number,
        );
        $terms = array(
            'page_id' => $post->ID,
            'code' => $password,
        );

        $table = $this->getTableName();

        $result = $wpdb->update($table, $values, $terms);

        $this->savePasswordUsageAnalytics();

        //#TODO add cookie for 5 hours - each password valid for 5 hours
        //        setcookie($this->insert_valid_pass_cookie_name, rand(), time() + (3600 * $this->hours_per_usage_password));
    }

    private function savePasswordUsageAnalytics()
    {
        global $post;
        require_once 'Wewp_Utils.php';

        try {
            $now = date('Y-m-d H:i:s');
            $name = isset($_POST['protect_page_name']) && !empty($_POST['protect_page_name']) ? sanitize_text_field($_POST['protect_page_name']) : '';
            $email = isset($_POST['protect_page_email']) && !empty($_POST['protect_page_email']) ? sanitize_email($_POST['protect_page_email']) : '';

            $row = array(
                'name' => $name,
                'email' => $email,
                'created_date' => $now,
                'updated_date' => $now,
                'ip' => Wewp_Utils::getIP(),
                'post_id' => (int)$post->ID,
                'post_title' => $post->post_title,
            );

            $result = Wewp_Utils::insertRows($this->getAnalyticsTableName(), array($row));
        } catch (Exception $e) {
            #TODO write to log
        }
    }

    public function getGuardForm($displayForm)
    {
        ob_start();
        require_once 'templates/protected-form.php';
        return ob_get_clean();
    }
}
