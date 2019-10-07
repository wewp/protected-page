<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once 'wewp-plugin-base.php';

if(!class_exists('Protected_Page_Pro')){
	class Protected_Page_Pro extends Wewp_Plugin_Base
	{
		/**
		 * The unique identifier of this plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string $plugin_name The string used to uniquely identify this plugin.
		 */
		protected $plugin_name;

		/**
		 * The unique identifier of this plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string $plugin_name The string used to uniquely identify this plugin.
		 */
		protected $dependency_plugin_name;

		/**
		 * The current version of the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string $version The current version of the plugin.
		 */
		protected $version;

		/**
		 * Wewp_Setting_Page instance.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      Wewp_Setting_Page $setting_page .
		 */
		protected $setting_page;
		protected $setting_slug = null;

		/**
		 * Hold cache for user role select template
		 * @var string | null
		 */
		private $user_role_template = null;

		/**
		 * Hold cache for user select template
		 * @var string | null
		 */
		private $user_name_template = null;

		/**
		 * Define the core functionality of the plugin.
		 *
		 * Set the plugin name and the plugin version that can be used throughout the plugin.
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function __construct()
		{

			if (defined('PROTECTED_PAGE_PRO_VERSION')) {
				$this->version = PROTECTED_PAGE_PRO_VERSION;
			} else {
				$this->version = '1.0.0';
			}
			$this->dependency_plugin_name = 'protected-page';
			$this->plugin_name = 'protected-page-pro';

			$this->required_plugins = [$this->dependency_plugin_name];

			if (!$this->have_required_plugins()) {
				//#TODO send notice protected-page need to be activate or install -> like elementor
				return;
			}

			require_once 'Wewp_Setting_Page.php';
			$this->setting_page = new Wewp_Setting_Page();
			$this->addActions();
			$this->addFilters();
		}

		private function addFilters()
		{
			add_filter('get_support_post_types', array($this, 'get_support_post_types'));
			add_filter('get_select_post_type', array($this, 'get_select_post_type'));
		}


		private function addActions()
		{
			add_action('admin_menu', array($this, 'add_admin_setting_page'), 11);
			add_action('render_restrict_all_page_column', array($this, 'render_restrict_all_page_column'), 15);

			/**
			 * Accepted arg $page, $key
			 */
			add_action('render_restrict_all_page_data_column', array($this, 'render_restrict_all_page_data_column'), 15, 2);

			/**
			 * Accepted arg $page_id, $add_success_message
			 */
			add_action('render_generate_multiple_password_form', array($this, 'render_generate_multiple_password_form'), 15, 2);

			add_action('render_column_custom_restrict_condition', array($this, 'render_column_custom_restrict_condition'), 15);
			/**
			 * Accepted arg $row
			 */
			add_action('render_column_data_custom_restrict_condition', array($this, 'render_column_data_custom_restrict_condition'), 15, 1);
		}

		public function add_admin_setting_page()
		{
			$this->setting_slug = "{$this->dependency_plugin_name}_setting";
			add_submenu_page($this->dependency_plugin_name, 'Settings', 'Settings', 'read', "{$this->dependency_plugin_name}_setting", array($this->setting_page, 'render'));
		}

		public function get_support_post_types($supported_post_types)
		{
			return $this->setting_page->getSupportedPostTypes();
		}

		public function get_select_post_type($selected_post_type)
		{
			$supported_post_types = $this->setting_page->getSupportedPostTypes();
			$selected_post_type = isset($_GET['post_type']) && !empty($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : null;
			if ($selected_post_type === null) {
				foreach ($supported_post_types as $post_type => $post_label) {
					$selected_post_type = $post_type;
					break;
				}
			}

			return $selected_post_type;
		}

		public function render_restrict_all_page_data_column($page, $key)
		{
			?>
            <td>
                <section class="protected-page-switcher is-protected-all-page">
                    <input type="checkbox" name="protected" id="switcher-<?php echo $key; ?>-all-page"
                           data-page_id="<?php echo esc_attr($page->ID); ?>-all-page"
                           data-page_title="<?php echo esc_attr($page->post_title); ?>"
					       <?php if ($page->is_all_page_protected): ?>checked<?php endif; ?>/>
                    <label for="switcher-<?php echo $key; ?>-all-page"></label>
                    <div class="spinner"></div>
                </section>
            </td>
			<?php
		}

		public function render_restrict_all_page_column()
		{
			?>
            <th class="row-title" style="width: 150px;">
				<?php echo __('Restrict all page', 'protected-page'); ?>
            </th>
			<?php
		}

		public function render_generate_multiple_password_form($page_id, $add_success_message)
		{
			?>
            <h2><?php echo __('Generate multiple passwords', 'protected-page'); ?></h2>
            <form method="post"
                  action="<?php echo admin_url(); ?>admin.php?page=protected-page&page_id=<?php echo $page_id; ?>">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th>
							<?php echo __('Prefix', 'protected-page'); ?>
                        </th>
                        <td><input type="text" name="prefix" id="pass-prefix"/></td>
                    </tr>
                    <tr>
                        <th>
							<?php echo __('How many to generate', 'protected-page'); ?>?
                        </th>
                        <td><input type="number"
                                   name="many"/></td>
                    </tr>
                    <tr>
                        <th>
							<?php echo __('Number uses:', 'protected-page'); ?>
                        </th>
                        <td><input type="number"
                                   name="usages"/> <?php echo __('(Leave blank for unlimited)', 'protected-page'); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <input type="submit" value="<?php echo __('Submit', 'protected-page'); ?>" class="button button-primary"/>
            </form>
            <br>
			<?php if (isset($add_success_message)): ?>

            <br>
			<?php if (!empty($add_success_message['codes'])): ?>
                <textarea
                        style="width:400px;clear:both; height:300px; overflow-y: scroll"><?php echo $add_success_message['codes'] ?></textarea>
			<?php endif; ?>
		<?php endif; ?>
            <hr>
			<?php
		}

		public function render_column_custom_restrict_condition()
		{
			?>
            <th class="author column-author" style="width: 200px;"><?php echo __('User', 'protected-page'); ?></th>
            <th class="author column-author"
                style="width: 200px;"><?php echo __('User role', 'protected-page'); ?></th>
            <th style="width:415px;"><?php echo __('Expiration date', 'protected-page'); ?></th>
			<?php
		}

		public function render_column_data_custom_restrict_condition($row)
		{
			?>
            <td>
				<?php echo $this->renderUsers($row['user_id']) ?>
            </td>
            <td>
				<?php echo $this->renderUserRoles($row['user_role']) ?>
            </td>
            <td class="expiration_date">
                <input type="hidden" name="start_time"/>
                <input type="hidden" name="end_time"/>

                <input type="checkbox"
                       name="expiration_date"
                       value="1"
				       <?php if (isset($row['start_time'])): ?>checked<?php endif; ?>>
                <div class="reportrange"
                     style="background: #fff; cursor: pointer; padding: 2px 10px; border: 1px solid #ccc; max-width:367px; width: 100%;">
                    <svg style="width:18px;height:18px;    position: relative;
    top: 4px;" viewBox="0 0 24 24">
                        <path fill="#000000"
                              d="M15,13H16.5V15.82L18.94,17.23L18.19,18.53L15,16.69V13M19,8H5V19H9.67C9.24,18.09 9,17.07 9,16A7,7 0 0,1 16,9C17.07,9 18.09,9.24 19,9.67V8M5,21C3.89,21 3,20.1 3,19V5C3,3.89 3.89,3 5,3H6V1H8V3H16V1H18V3H19A2,2 0 0,1 21,5V11.1C22.24,12.36 23,14.09 23,16A7,7 0 0,1 16,23C14.09,23 12.36,22.24 11.1,21H5M16,11.15A4.85,4.85 0 0,0 11.15,16C11.15,18.68 13.32,20.85 16,20.85A4.85,4.85 0 0,0 20.85,16C20.85,13.32 18.68,11.15 16,11.15Z"/>
                    </svg>&nbsp;
                    <input type="text" name="datetimes"
                           style="width: 317px;border: none;box-shadow: none;margin: 0"/>
                    <svg style="width:18px;height:18px;    position: relative;
    top: 4px;" viewBox="0 0 24 24">
                        <path fill="#000000" d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z"/>
                    </svg>
                </div>

            </td>
			<?php
		}

		private function renderUsers($user_id = null)
		{
			if (null === $user_id && $this->user_role_template !== null) {
				return $this->user_role_template;
			}

			global $wp_roles;
			$users = get_users();
			ob_start();
			?>
            <select name="user_id">
                <option>-</option>
				<?php foreach ($users as $user): ?>
                    <option value="<?php echo esc_html($user->ID) ?>"
					        <?php if ($user_id == $user->ID): ?>selected<?php endif; ?>><?php echo esc_html($user->data->display_name) ?>
                    </option>
				<?php endforeach; ?>
            </select>
			<?php

			if ($user_id !== null) {
				return ob_get_clean();
			}

			return $this->user_role_template = ob_get_clean();
		}

		private function renderUserRoles($role = null)
		{
			if (null === $role && $this->user_name_template !== null) {
				return $this->user_name_template;
			}

			global $wp_roles;
			$roles = $wp_roles->get_names();
			ob_start();
			?>
            <select name="user_role">
                <option>-</option>
				<?php foreach ($roles as $key => $display): ?>
                    <option value="<?php echo esc_html($key) ?>"
					        <?php if ($key === $role): ?>selected<?php endif; ?>><?php echo esc_html($display); ?></option>
				<?php endforeach; ?>
            </select>
			<?php

			if ($role !== null) {
				return ob_get_clean();
			}

			return $this->user_name_template = ob_get_clean();

		}
	}
}


