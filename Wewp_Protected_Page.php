<?php
if (!defined('ABSPATH')) {
    exit;
}

class Wewp_Protected_Page
{
    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * The current plugin name.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name .
     */
    protected $plugin_name;

    /**
     * Guard content instance.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wewp_Guard_Content $guard_content Instance of guard content for protect $content with password.
     */
    protected $guard_content;

    /**
     * WewpPassword_Manager instance.
     *
     * @since    1.0.0
     * @access   protected
     * @var      WewpPassword_Manager $password_manager .
     */
    protected $password_manager;

    private $parent_menu_slug = 'protected-page';
    protected $analytics_slug = null;
    protected $setting_slug = null;
    protected $generate_password_slug = null;


    public function __construct()
    {
        $this->version = '1.0.9';
        $this->plugin_name = 'protected-page';

        add_action('plugins_loaded', array($this, 'init'));
    }

    public function init()
    {
        require_once 'Wewp_Guard_Content.php';
        $this->guard_content = new Wewp_Guard_Content();

        require_once 'Wewp_Password_Manager.php';
        $this->password_manager = new WewpPassword_Manager();

        $this->addPostTypesSupport();
        $this->addFilters();
        $this->addActions();

        if (is_admin()) {
            add_action('admin_menu', array($this, 'setAdminMenu'));
            add_filter('plugins_loaded', array($this, 'load_plugin_text_domain'));
        }
        require_once 'Wewp_Customize.php';
    }

    private function addFilters()
    {
    }

    private function addActions()
    {
        // run only with wp_head
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));

        add_action('wp_ajax_toggle_protected_page', array($this, 'toggleProtectedPage'));
        add_action('wp_ajax_toggle_protected_all_page', array($this, 'toggleProtectedAllPage'));
        add_action('admin_bar_menu', array($this, 'add_toolbar_items'), 100);

        add_action('wp_ajax_update_password', array($this->password_manager, 'updatePassword'));
        add_action('wp_ajax_delete_password', array($this->password_manager, 'deletePassword'));

        add_filter('plugin_row_meta', [$this, 'plugin_row_meta'], 10, 2);
    }

    public function plugin_row_meta($plugin_meta, $plugin_file)
    {

        $plugin_base_file = $this->plugin_name.'/'.$this->plugin_name.'.php';
        if ($plugin_base_file === $plugin_file) {
            $row_meta = [
                'github' => '<a href="https://github.com/wewp/protected-page" aria-label="' . esc_attr(__('Protected page Github', $this->plugin_name)) . '" target="_blank">' . __('Github', $this->plugin_name) . '</a>',
            ];
            $plugin_meta = array_merge($plugin_meta, $row_meta);
        }

        return $plugin_meta;
    }

    public function add_toolbar_items($wp_admin_bar)
    {
        global $post;
        require_once 'Wewp_Utils.php';

        if (!Wewp_Utils::isDisplayAdminTopBarButton() || !isset($post) || !Wewp_Utils::is_post_type_support($post->post_type)) {
            return;
        }

        global $post;

        $args = array(
            'id' => 'protected-page-link',
            'title' => __('Protected Page', 'protected-page'),
            'href' => admin_url() . 'admin.php?page=' . $this->parent_menu_slug . '&page_id=' . esc_html($post->ID),
            'meta' => array(
                'title' => __('Go to protected your page', 'protected-page')
            )
        );
        $wp_admin_bar->add_node($args);
    }

    public function toggleProtectedPage()
    {
        $page_id = isset($_POST['page_id']) && !empty($_POST['page_id']) ? sanitize_text_field($_POST['page_id']) : null;
        $is_checked = isset($_POST['is_page_protected']) ? rest_sanitize_boolean($_POST['is_page_protected']) : null;
        if ($page_id === null || !is_bool($is_checked)) {
            wp_die(json_encode(
                array(
                    'success' => false,
                    'errorMessage' => 'Invalid fields'
                )
            ));
        }

        $success = update_post_meta((int)$page_id, 'is_page_protected', $is_checked ? 1 : 0);

        wp_die(json_encode(
            array('success' => true)
        ));
    }

    public function toggleProtectedAllPage()
    {
        $page_id = isset($_POST['page_id']) && !empty($_POST['page_id']) ? sanitize_text_field($_POST['page_id']) : null;
        $is_checked = isset($_POST['is_all_page_protected']) ? rest_sanitize_boolean($_POST['is_all_page_protected']) : null;
        if ($page_id === null || !is_bool($is_checked)) {
            wp_die(json_encode(
                array(
                    'success' => false,
                    'errorMessage' => 'Invalid fields'
                )
            ));
        }

        $success = update_post_meta((int)$page_id, 'is_all_page_protected', $is_checked ? 1 : 0);

        wp_die(json_encode(
            array('success' => true)
        ));
    }

    public function enqueue_scripts()
    {
        if (is_rtl()) {
            wp_register_script($this->plugin_name . '_rtl.js', plugin_dir_url(__FILE__) . 'assets/js/rtl.js', array('jquery',), $this->version, true);
            wp_enqueue_script($this->plugin_name . '_rtl.js');
        }

        //#TODO load only pro and with page_id
        wp_register_script($this->plugin_name . '_awesome-notifications', plugin_dir_url(__FILE__) . 'libs/awesome-notifications/dist/index.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '_awesome-notifications');

        wp_register_script($this->plugin_name . '_moment', plugin_dir_url(__FILE__) . 'libs/datepicker/moment.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '_moment');

        wp_register_script($this->plugin_name . '_datepicker.js', plugin_dir_url(__FILE__) . 'assets/js/datepicker.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '_datepicker.js');

        wp_register_script($this->plugin_name . '_clipboard.min.js', plugin_dir_url(__FILE__) . 'assets/js/clipboard.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '_clipboard.min.js');

        wp_register_script($this->plugin_name . '_admin.js', plugin_dir_url(__FILE__) . 'assets/js/admin.js', array('jquery', $this->plugin_name . '_clipboard.min.js'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '_admin.js');

        wp_register_script($this->plugin_name . '_daterangepicker.js', plugin_dir_url(__FILE__) . 'libs/datepicker/daterangepicker.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '_daterangepicker.js');


    }

    public function enqueue_styles()
    {
        wp_register_style($this->plugin_name . '_awesome-notifications', plugin_dir_url(__FILE__) . 'libs/awesome-notifications/dist/style.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '_awesome-notifications');

        wp_register_style($this->plugin_name . '_switcher', plugin_dir_url(__FILE__) . 'assets/css/switcher.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '_switcher');

        wp_register_style($this->plugin_name . '_admin', plugin_dir_url(__FILE__) . 'assets/css/admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '_admin');

        wp_register_style($this->plugin_name . '_daterangepicker.css', plugin_dir_url(__FILE__) . 'libs/datepicker/daterangepicker.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '_daterangepicker.css');


    }

    private function addPostTypesSupport()
    {
        $cpt_supported = get_option('wewp_protected_page_cpt_support', ['page' => 'Pages']);

        if (empty($cpt_supported)) {
            $cpt_supported = [];
        }

        foreach ($cpt_supported as $type => $cpt_slug) {
            add_post_type_support($type, 'wewp-protected-page');
        }
    }

    public function getTableName()
    {
        global $wpdb;
        return "{$wpdb->prefix}pp_passwords";
    }


    public function load_plugin_text_domain()
    {
        load_plugin_textdomain(
            'protected-page',
            false,
            (basename(dirname(__FILE__))) . '/languages'
        );
    }


    public function setAdminMenu()
    {
        $this->generate_password_slug = "{$this->parent_menu_slug}_generate_password";
        $this->analytics_slug = "{$this->parent_menu_slug}_analytics";
        $this->setting_slug = "{$this->parent_menu_slug}_setting";

        add_menu_page('Pages', 'Protected page', 'read', $this->parent_menu_slug, array($this, 'renderPostsListPage'), plugins_url('assets/images/protected-page-admin-icon.png', __FILE__));
    }


    public function renderGeneratePasswordPage()
    {
        if (isset($_POST['theid']) && isset($_POST['update'])) {
            $update_success_message = $this->password_manager->update();
        }
        if (isset($_POST['theid']) && isset($_POST['remove'])) {
            $remove_success_message = $this->password_manager->remove();
        }

        if ((!isset($_POST['theid']) && isset($_POST['code'])) || isset($_POST['many'])) {
            $add_success_message = $this->password_manager->add();
        }

        $rows = $this->getPagePasswords();
        $page_id = isset($_GET['page_id']) ? sanitize_text_field($_GET['page_id']) : '';
        $display_page_not_protected = $this->isDisplayPageNotProtected();
        require_once 'templates/generate-password-forms.php';
    }

    /**
     * @return bool
     * @since 1.0.8
     *
     * Check if page set to protected
     * return true if page set to protected by meta field $post->is_page_protected
     */
    private function isDisplayPageNotProtected()
    {
        $page_id = isset($_GET['page_id']) && !empty($_GET['page_id']) ? sanitize_text_field($_GET['page_id']) : null;
        if ($page_id === null) {
            return false;
        }
        $post = get_post($page_id);
        return (isset($post->is_page_protected) && $post->is_page_protected !== '1');
    }


    private function getPagePasswords()
    {
        global $wpdb;
        $page_id = isset($_GET['page_id']) ? sanitize_text_field($_GET['page_id']) : '';
        $sql = 'SELECT * FROM ' . $wpdb->prefix . "pp_passwords WHERE page_id = '$page_id'";
        return $wpdb->get_results($sql, ARRAY_A);
    }

    public function renderPostsListPage()
    {
        if (isset($_GET['page_id'])) {
            $this->renderGeneratePasswordPage();
            return;
        }

        $supported_post_types = array('page' => 'Page');
        $selected_post_type = 'page';

        if (has_filter('get_support_post_types')) {
            $supported_post_types = apply_filters('get_support_post_types', $supported_post_types);
        }

        if (empty($supported_post_types)) {
            require_once 'templates/message_for_checked_support_post_typee.php';
            return;
        }

        if (has_filter('get_select_post_type')) {
            $selected_post_type = apply_filters('get_select_post_type', $selected_post_type);
        }

//        if (pc_fs()->is__premium_only()) {
//            $supported_post_types = $this->setting_page->getSupportedPostTypes();
//            if (empty($supported_post_types)) {
//                require_once 'templates/message_for_checked_support_post_typee.php';
//                return;
//            }
//        }

        require_once 'templates/pages_table.php';
    }

    public function renderGeneratePasswordForms()
    {
        ?>
        <h1 class="wp-heading-inline"><?php echo __('Protected page - Setting', 'protected-page'); ?></h1>
        <?php
    }

    public function renderAnalyticsPage()
    {
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php echo __('Analytics', 'protected-page'); ?></h1>
        </div>
        <?php
//        require_once 'templates/analytics.tpl.php';
    }
}
