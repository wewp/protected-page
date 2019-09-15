<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WewpPassword_Manager
{
    public function update()
    {
        global $wpdb;

        $code = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : '';
        $uses = isset($_POST['times']) ? sanitize_text_field($_POST['times']) : 0;
        $post_id = sanitize_text_field($_POST['theid']);
        $query = "UPDATE {$this->getTableName()} SET `code`=%s ,`uses`=%s WHERE `id`=%s";
        $query = $wpdb->prepare($query, $code, $uses, $post_id);
        $result = $wpdb->query($query);
        if ($result) {
            return __("Successfully updated password", 'protected-page') . '.';
        }

        return __("Error when you try to update password", 'protected-page') . '.';
    }

    public function add()
    {
        global $wpdb;
        require_once 'Wewp_Utils.php';

        $data = array();
        $usages = isset($_POST['usages']) && !empty($_POST['usages']) ? sanitize_text_field($_POST['usages']) : 500000;
        $usages_left = isset($_POST['usages_left']) && !empty($_POST['usages_left']) ? sanitize_text_field($_POST['usages_left']) : $usages;
        $page_id = isset($_GET['page_id']) && !empty($_GET['page_id']) ? sanitize_text_field($_GET['page_id']) : -1;
        $prefix = isset($_POST['prefix']) && !empty($_POST['prefix']) ? sanitize_text_field($_POST['prefix']) : '';
        $code = isset($_POST['code']) && !empty($_POST['code']) ? sanitize_text_field($_POST['code']) : null;
        $many = isset($_POST['many']) && !empty($_POST['many']) ? $_POST['many'] : 1;
        $now = date('Y-m-d H:i:s');
        $all_pass = '';

        for ($i = 1; $i <= $many; $i++) {
            $code = $code ? $code : self::randomPassword($prefix);
            $all_pass .= "{$code} \n";
            $data[] = array(
                'usages' => $usages,
                'usages_left' => $usages_left,
                'code' => $code,
                'page_id' => $page_id,
                'created_date' => $now,
                'updated_date' => $now,
            );

            $code = null;
        }

        if ($many < 2) {
            $all_pass = null;
        }

        $result = Wewp_Utils::insertRows($this->getTableName(), $data);

        if ($result) {
            $success_message = $many > 1 ? __("Entered passwords successfully", 'protected-page') : __("Entered password successfully", 'protected-page');
            return array(
                'message' => $success_message . '.',
                'codes' => $all_pass,
            );
        }

        return array(
            'message' => __("Error when you try to add multiple password", 'protected-page') . " ->" . $wpdb->last_error . '.',
            'codes' => null,
        );
    }

    public function remove()
    {
        global $wpdb;
        $query = "DELETE FROM {$this->getTableName()} WHERE id=%s;";
        $query = $wpdb->prepare($query, sanitize_text_field($_POST['theid']));
        $result = $wpdb->query($query);
        if ($result) {
            return __('Removed password successfully', 'protected-page') . '.';
        }
    }

    public function getTableName()
    {
        global $wpdb;
        return "{$wpdb->prefix}pp_passwords";
    }

    public function randomPassword($prefix = '', $length = 10)
    {
        $alphabet = PASSWORD_CHARACTERS;
        $pass = '';
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, strlen($alphabet) - 1);
            $pass .= $alphabet[$n];
        }
        return $prefix . $pass;
    }

    public function updatePassword()
    {
        global $wpdb;

        $response = new stdClass();
        $response->errors = array();
        $data = array();
        $data['start_time'] = null;
        $data['end_time'] = null;
        $data['updated_date'] = date('Y-m-d H:i:s', current_time('timestamp'));

        if (!isset($_POST['theid']) || empty($_POST['theid'])) {
            $response->errors['id'] = 'Missing row id';
            $response->success = false;
            $this->ajaxResponse($response);
        }
        $id = sanitize_text_field($_POST['theid']);

        $code = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : null;
        if ($code) {
            $data['code'] = $code;
        }

        $usages_left = isset($_POST['usages_left']) ? sanitize_text_field($_POST['usages_left']) : null;
        if ($usages_left) {
            $data['usages_left'] = $usages_left;
        }
        $user_role = isset($_POST['user_role']) && $_POST['user_role'] !== '-' ? sanitize_text_field($_POST['user_role']) : null;
        $data['user_role'] = $user_role;

        $user_id = isset($_POST['user_id']) && $_POST['user_id'] !== '-' ? sanitize_text_field($_POST['user_id']) : null;
        $data['user_id'] = $user_id;

        $expiration_date = isset($_POST['expiration_date']) && $_POST['expiration_date'] === '1' ? $_POST['expiration_date'] : null;
        if ($expiration_date) {
            $start_time = isset($_POST['start_time']) && $_POST['start_time'] !== '-' ? sanitize_text_field($_POST['start_time']) : null;
            $end_time = isset($_POST['end_time']) && $_POST['end_time'] !== '-' ? sanitize_text_field($_POST['end_time']) : null;
            $data['start_time'] = $start_time;
            $data['end_time'] = $end_time;
        }

        $terms = array(
            'id' => $id
        );
        $result = $wpdb->update($this->getTableName(), $data, $terms);
        $response->success = empty($response->errors);
        $response->update_row = $result;
        $this->ajaxResponse($response);
    }

    private function ajaxResponse($response = [])
    {
        wp_die(json_encode($response));
    }

    public function deletePassword()
    {
        global $wpdb;
        $response = new stdClass();
        $response->errors = array();
        $response->success = true;

        $query = "DELETE FROM {$this->getTableName()} WHERE id=%s;";
        if (!isset($_POST['theid'])) {
            $response->success = false;
            $response->errors[] = 'Missing field';
            wp_die(json_encode($response));
        }
        $id = sanitize_text_field($_POST['theid']);
        $query = $wpdb->prepare($query, $id);
        $result = $wpdb->query($query);
        if (!$result) {
            $response->errors[] = __('Error when try to delete password', 'protected-page') . '.';
            $response->success = false;
        }
        $response->result = $result;

        wp_die(json_encode($response));
    }

}
