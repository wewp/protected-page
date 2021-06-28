<?php

if (!defined('ABSPATH')) {
	exit;
}

require_once 'wewp-plugin-base.php';

if (!class_exists('Protected_Page_Pro')) {
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
		protected $woocommerce_setting_page = null;

		private $page_id = 'protected-page-cpt-tabs';

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
		 * Hold cache for user select template
		 * @var string | null
		 */
		private $current_page_cpt_name = 'page';

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
			$this->current_page_cpt_name = isset($_GET['post_type']) ? $_GET['post_type'] : 'page';
			$this->dependency_plugin_name = 'protected-page';
			$this->plugin_name = 'protected-page-pro';

			$this->required_plugins = [$this->dependency_plugin_name];

			//			if (!$this->have_required_plugins()) {
			//				//#TODO send notice protected-page need to be activate or install -> like elementor
			//				return;
			//			}

			require_once 'wewp_setting_page.php';
			require_once 'woocommerce_setting.php';
			$this->setting_page = new Wewp_Setting_Page();
			$this->woocommerce_page = new Woocommerce_Setting_Page();
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
			add_action('admin_menu', array($this, 'add_admin_woocommerce_setting_page'), 11);

			add_action('render_restrict_all_page_column', array($this, 'render_restrict_all_page_column'), 15);
			add_action('render_page_user_role_column', array($this, 'render_page_user_role_column'), 15);
			add_action('render_redirect_when_user_has_no_access_column', array($this, 'render_redirect_when_user_has_no_access_column'), 15);

			/**
			 * Accepted arg $page, $key
			 */
			add_action('render_restrict_all_page_data_column', array($this, 'render_restrict_all_page_data_column'), 15, 2);
			add_action('render_user_role_data_column', array($this, 'render_user_role_data_column'), 15, 2);
			add_action('render_redirect_when_user_has_no_access_data_column', array($this, 'render_redirect_when_user_has_no_access_data_column'), 15, 2);

			/**
			 * Accepted arg $page_id, $add_success_message
			 */
			add_action('render_generate_multiple_password_form', array($this, 'render_generate_multiple_password_form'), 15, 2);

			add_action('render_column_custom_restrict_condition', array($this, 'render_column_custom_restrict_condition'), 15);
			/**
			 * Accepted arg $row
			 */
			add_action('render_column_data_custom_restrict_condition', array($this, 'render_column_data_custom_restrict_condition'), 15, 1);

			add_action('render_protect_post_type_by_user_roles', array($this, 'render_protect_post_type_by_user_roles'), 15, 1);

			add_action("admin_init", array($this, 'set_options_fields_for_protect_all_post_type'));
			add_action("admin_init", array($this, 'set_options_fields_for_toggle_protect_all_post_type'));

			add_action('wp_ajax_select_user_role_for_protect_page', array($this, 'select_user_role_for_protect_page'));

			add_action('wp_ajax_toggle_protect_all_cpt', array($this, 'toggle_protect_all_cpt'));

			add_action('wp_ajax_create_new_protect_level', array($this, 'create_new_protect_level'));

			add_action('show_user_profile', array($this, 'pp_additional_profile_fields'), 1, 1);

			add_action('edit_user_profile', array($this, 'pp_additional_profile_fields'), 1, 1);

			add_action('personal_options_update', array($this, 'pp_save_profile_fields'), 15, 1);

			add_action('edit_user_profile_update', array($this, 'pp_save_profile_fields'), 15, 1);

			add_action('woocommerce_order_status_completed', array($this, 'pp_woocommerce_order_status_completed'), 15, 1);

			add_filter('woocommerce_product_data_tabs',  array($this, 'filter_woocommerce_product_data_tabs'), 10, 1);

			add_filter('woocommerce_admin_process_product_object',  array($this, 'action_woocommerce_admin_process_product_object'), 10, 1);

			add_filter('woocommerce_product_data_panels',  array($this, 'action_woocommerce_product_data_panels'), 10, 0);
		}

		public function action_woocommerce_admin_process_product_object($product)
		{
			$product->update_meta_data('protect_user_role_after_order_complete', $_POST['protect_user_role_after_order_complete']);
		}

		public function action_woocommerce_product_data_panels()
		{
			global $post;

			$roles = get_option('protect_user_roles');
			$selected_roles = (array) get_post_meta($post->ID, 'protect_user_role_after_order_complete', true);

?>
			<div id="wk_custom_tab_data" class="panel woocommerce_options_panel">
				<h3>Roles that the user will have after purchasing a product</h3>
				<?php if (count($roles)) : ?>
					<ul>
						<p class='form-field _redeem_in_stores'>
							<label for='_redeem_in_stores'><?php _e('Protect roles', 'woocommerce'); ?></label>
							<select name='protect_user_role_after_order_complete[]' class='wc-enhanced-select' multiple='multiple' style='width: 80%;'>
								<?php foreach ($roles as $role) : ?>
									<option <?php selected(in_array($role, $selected_roles)); ?> value='<?php echo $role ?>'><?php echo  $role; ?></option>
								<?php endforeach; ?>
							</select>
						</p>
					</ul>
				<?php endif; ?>
			</div>
		<?php
		}
		public function filter_woocommerce_product_data_tabs($default_tabs)
		{
			$default_tabs['custom_tab'] = array(
				'label'   =>  __('Protect  page', 'domain'),
				'target'  =>  'wk_custom_tab_data',
				'priority' => 60,
				'class'   => array()
			);
			return $default_tabs;
		}

		public function pp_woocommerce_order_status_completed($order_id)
		{
			$order = new WC_Order($order_id);
			$products = $order->get_items();

			foreach ($products as $product) {
				// $product_user_roles = $product->get_meta('protect_user_role_after_order_complete');
				$product_user_roles = (array) get_post_meta($product->get_product_id(), 'protect_user_role_after_order_complete', true);
				if (!$product_user_roles) {
					$product_user_roles = [];
				}

				$user = new WP_User($order->user_id);

				$current_user_roles = wp_parse_args(get_the_author_meta('protect_post_type_by_user_roles', $user->ID));
				if (!$current_user_roles) {
					$current_user_roles = [];
				}

				$roles = array_merge($product_user_roles, $current_user_roles);
				// var_dump([
				// 	'$$product_user_roles' => $product_user_roles,
				// 	'$$current_user_roles' => $current_user_roles,
				// ]);
				// die();
				update_user_meta($order->user_id, 'protect_post_type_by_user_roles', array_merge($product_user_roles, $current_user_roles));
			}
		}
		public function pp_save_profile_fields($user_id)
		{
			if (!current_user_can('edit_user', $user_id)) {
				return false;
			}

			if (!isset($_POST['protect_post_type_by_user_roles'])) {
				// return false;
			}

			$succss = update_user_meta($user_id, 'protect_post_type_by_user_roles', $_POST['protect_post_type_by_user_roles']);
		}

		public function pp_additional_profile_fields($user)
		{
			$roles = get_option('protect_user_roles');
			$current_user_roles = wp_parse_args(get_the_author_meta('protect_post_type_by_user_roles', $user->ID));
		?>
			<div class="protect-page-user-profile-section">
				<h3><img src='<?php echo PROTECT_PAGE_PLUGIN_URL . '/assets/images/protected-page-admin-icon.png' ?>' />Protect Page Levels</h3>
				<table class="form-table">
					<tr>
						<th><label for="birth-date-day">Levels</label></th>
						<td>
							<?php foreach ($roles as $role) : ?>
								<div>
									<input id="user_role_<?php echo esc_html($role) ?>" type="checkbox" name="protect_post_type_by_user_roles[]" value="<?php echo esc_html($role) ?>" <?php checked(1, in_array($role, $current_user_roles), true); ?>>
									<label for="user_role_<?php echo esc_html($role) ?>">
										<?php echo esc_html($role) ?>
									</label>
								</div>
							<?php endforeach; ?>
						</td>
					</tr>
				</table>
			</div>

		<?php
		}
		public function create_new_protect_level()
		{
			$protect_level_name = isset($_POST['protect_level_name'])  ? $_POST['protect_level_name'] : null;
			if ($protect_level_name === null) {
				wp_die(json_encode(
					array(
						'success' => false,
						'errorMessage' => 'Invalid fields'
					)
				));
			}

			$roles = get_option('protect_user_roles');

			if (!$roles) {
				$roles = array();
			}

			if (in_array($protect_level_name, $roles)) {
				wp_die(json_encode(
					array(
						'success' => false,
						'errorMessage' => 'Level exist'
					)
				));
			}

			$roles[] = $protect_level_name;

			wp_die(json_encode(
				array('success' => update_option('protect_user_roles', $roles))
			));
		}

		public function toggle_protect_all_cpt()
		{
			$cpt = isset($_POST['cpt_toggle'])  ? ($_POST['cpt_toggle'] === 'false' ? false : true) : null;
			$cpt_name = isset($_POST['cpt_name']) && !empty($_POST['cpt_name']) ? sanitize_text_field($_POST['cpt_name']) : null;
			if ($cpt === null || $cpt_name === null) {
				wp_die(json_encode(
					array(
						'success' => false,
						'errorMessage' => 'Invalid fields'
					)
				));
			}

			$option_key = 'cpt_toggle_' . $cpt_name;
			$option_value = $cpt;

			wp_die(json_encode(
				array('success' => update_option($option_key, $option_value))
			));
		}
		public function select_user_role_for_protect_page()
		{
			$page_id = isset($_POST['page_id']) && !empty($_POST['page_id']) ? sanitize_text_field($_POST['page_id']) : null;
			$role = isset($_POST['role']) && !empty($_POST['role']) ? sanitize_text_field($_POST['role']) : null;
			if ($page_id === null || $role === null) {
				wp_die(json_encode(
					array(
						'success' => false,
						'errorMessage' => 'Invalid fields'
					)
				));
			}

			$success = update_post_meta((int)$page_id, 'page_protected_user_role', $role);

			wp_die(json_encode(
				array('success' => true)
			));
		}

		public function set_options_fields_for_protect_all_post_type()
		{

			// support post type
			add_settings_section(
				'protect_all_cpt',
				"",
				null,
				$this->page_id
			);

			add_settings_field(
				"protect_post_type_by_user_roles",
				"User level as can see the content",
				array($this, "render_user_role_for_cpt"),
				$this->page_id,
				'protect_all_cpt'
			);

			register_setting(
				'protect_all_cpt',
				"protect_post_type_by_user_roles"
			);
		}

		public function set_options_fields_for_toggle_protect_all_post_type()
		{
			// support post type
			add_settings_section(
				'toggle_protect_all_cpt',
				"",
				null,
				'000'
			);

			register_setting(
				'toggle_protect_all_cpt',
				"-"
			);
		}

		public function render_user_role_for_cpt()
		{
		?>
			<?php echo $this->renderUserRolesCheckbox(); ?>
		<?php
		}
		public function add_admin_woocommerce_setting_page()
		{
			$this->woocommerce_setting_page = "{$this->dependency_plugin_name}_woocommerce_setting_page";
			add_submenu_page($this->dependency_plugin_name, 'Woocommerce', 'Woocommerce', 'read', $this->woocommerce_setting_page, array($this->woocommerce_page, 'render'));
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

		public function render_protect_post_type_by_user_roles()
		{
			$cpt_is_protect = get_option('cpt_toggle_' . $this->current_page_cpt_name);
		?>

			<div class="pp-flex pp-flex-center protect-all-cpt-pages">
				<section class="protected-page-switcher cpt-or-single">
					<input type="checkbox" name="protected" id="switcher-" <?php if ($cpt_is_protect) : ?>checked<?php endif; ?> />
					<label for="switcher-"></label>
					<div class="spinner"></div>
				</section>
				<label for="switcher-">Protect all pages in post type (<?php echo esc_html($this->current_page_cpt_name); ?>). </label>


			</div>

			<form method="post" action="options.php" class="toggle-protect-cpt">
				<input type="hidden" name="cpt_name" value="<?php echo esc_html($this->current_page_cpt_name); ?>">
				<?php
				do_settings_sections('000');
				settings_fields('toggle_protect_all_cpt');
				?>
			</form>

			<form method="post" action="options.php" class="select-user-role-section">
				<?php
				do_settings_sections($this->page_id);
				settings_fields('protect_all_cpt');
				//                settings_fields('admin_top_bar_button');
				submit_button();
				?>
			</form>

		<?php
		}



		public function render_redirect_when_user_has_no_access_data_column($page, $key)
		{
			$role = get_post_meta($page->ID, 'page_protected_user_role', true);
		?>

			<td class="td-user-role" data-page_id="<?php echo esc_attr($page->ID); ?>">
				<div class="pp-flex">
					<div class="">
						<?php echo $this->renderProductsSelect($role) ?>

					</div>
					<div>
						<div class="spinner"></div>
					</div>
				</div>
			</td>
		<?php
		}

		private function renderProductsSelect($page_guid = null)
		{
			// if (null === $role && $this->user_name_template !== null) {
			// 	return $this->user_name_template;
			// }

			// $roles = get_option('protect_user_roles', []);
			$products = get_posts(array(
				'post_type' => 'product',
				// 'numberposts' => -1,
			));
			ob_start();
		?>
			<select name="user_role">
				<option>-</option>
				<?php foreach ($products as $key => $product) : ?>
					<option value="<?php echo esc_html($product->guid) ?>" <?php if ($page_guid === $product->guid) : ?>selected<?php endif; ?>><?php echo esc_html($product->post_title); ?></option>
				<?php endforeach; ?>
			</select>
			<?php

			if ($role !== null) {
				return ob_get_clean();
			}

			return $this->user_name_template = ob_get_clean();
		}

		public function render_user_role_data_column($page, $key)
		{
			$role = get_post_meta($page->ID, 'page_protected_user_role', true);
			?>

			<td class="td-user-role" data-page_id="<?php echo esc_attr($page->ID); ?>">
				<div class="pp-flex">
					<div class="">
						<?php echo $this->renderUserRoles($role) ?>

					</div>
					<div>
						<div class="spinner"></div>
					</div>
				</div>
			</td>
		<?php
		}

		public function render_restrict_all_page_data_column($page, $key)
		{
		?>
			<td>
				<section class="protected-page-switcher is-protected-all-page">
					<input type="checkbox" name="protected" id="switcher-<?php echo $key; ?>-all-page" data-page_id="<?php echo esc_attr($page->ID); ?>-all-page" data-page_title="<?php echo esc_attr($page->post_title); ?>" <?php if ($page->is_all_page_protected) : ?>checked<?php endif; ?> />
					<label for="switcher-<?php echo $key; ?>-all-page"></label>
					<div class="spinner"></div>
				</section>
			</td>
		<?php
		}

		public function render_redirect_when_user_has_no_access_column()
		{
		?>
			<th class="row-title" style="width: 150px;">
				<?php echo __('Redirect when user has no access', 'protected-page'); ?>
			</th>
		<?php
		}

		public function render_page_user_role_column()
		{
		?>
			<th class="row-title" style="width: 150px;">
				<?php echo __('User\'s levels', 'protected-page'); ?>
			</th>
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
			<form method="post" action="<?php echo admin_url(); ?>admin.php?page=protected-page&page_id=<?php echo $page_id; ?>">
				<table class="form-table">
					<tbody>
						<tr>
							<th>
								<?php echo __('Prefix', 'protected-page'); ?>
							</th>
							<td><input type="text" name="prefix" id="pass-prefix" /></td>
						</tr>
						<tr>
							<th>
								<?php echo __('How many to generate', 'protected-page'); ?>?
							</th>
							<td><input type="number" name="many" /></td>
						</tr>
						<tr>
							<th>
								<?php echo __('Number uses:', 'protected-page'); ?>
							</th>
							<td><input type="number" name="usages" /> <?php echo __('(Leave blank for unlimited)', 'protected-page'); ?>
							</td>
						</tr>
					</tbody>
				</table>
				<input type="submit" value="<?php echo __('Submit', 'protected-page'); ?>" class="button button-primary" />
			</form>
			<br>
			<?php if (isset($add_success_message)) : ?>

				<br>
				<?php if (!empty($add_success_message['codes'])) : ?>
					<textarea style="width:400px;clear:both; height:300px; overflow-y: scroll"><?php echo $add_success_message['codes'] ?></textarea>
				<?php endif; ?>
			<?php endif; ?>
			<hr>
		<?php
		}

		public function render_column_custom_restrict_condition()
		{
		?>
			<th class="author column-author" style="width: 200px;"><?php echo __('User', 'protected-page'); ?></th>
			<th class="author column-author" style="width: 200px;"><?php echo __('User role', 'protected-page'); ?></th>
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
				<input type="hidden" name="start_time" />
				<input type="hidden" name="end_time" />

				<input type="checkbox" name="expiration_date" value="1" <?php if (isset($row['start_time'])) : ?>checked<?php endif; ?>>
				<div class="reportrange" style="background: #fff; cursor: pointer; padding: 2px 10px; border: 1px solid #ccc; max-width:367px; width: 100%;">
					<svg style="width:18px;height:18px;    position: relative;
    top: 4px;" viewBox="0 0 24 24">
						<path fill="#000000" d="M15,13H16.5V15.82L18.94,17.23L18.19,18.53L15,16.69V13M19,8H5V19H9.67C9.24,18.09 9,17.07 9,16A7,7 0 0,1 16,9C17.07,9 18.09,9.24 19,9.67V8M5,21C3.89,21 3,20.1 3,19V5C3,3.89 3.89,3 5,3H6V1H8V3H16V1H18V3H19A2,2 0 0,1 21,5V11.1C22.24,12.36 23,14.09 23,16A7,7 0 0,1 16,23C14.09,23 12.36,22.24 11.1,21H5M16,11.15A4.85,4.85 0 0,0 11.15,16C11.15,18.68 13.32,20.85 16,20.85A4.85,4.85 0 0,0 20.85,16C20.85,13.32 18.68,11.15 16,11.15Z" />
					</svg>&nbsp;
					<input type="text" name="datetimes" style="width: 317px;border: none;box-shadow: none;margin: 0" />
					<svg style="width:18px;height:18px;    position: relative;
    top: 4px;" viewBox="0 0 24 24">
						<path fill="#000000" d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
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
				<?php foreach ($users as $user) : ?>
					<option value="<?php echo esc_html($user->ID) ?>" <?php if ($user_id == $user->ID) : ?>selected<?php endif; ?>><?php echo esc_html($user->data->display_name) ?>
					</option>
				<?php endforeach; ?>
			</select>
			<?php

			if ($user_id !== null) {
				return ob_get_clean();
			}

			return $this->user_role_template = ob_get_clean();
		}

		private function renderUserRolesCheckbox($role = null)
		{
			global $wp_roles;
			$roles = get_option('protect_user_roles', []);
			$posts_types = get_option('protect_post_type_by_user_roles', []);

			ob_start();
			?>
			<?php foreach ($posts_types as $post_type_name => $_roles) : ?>
				<?php if ($post_type_name === $this->current_page_cpt_name) : continue;
				endif;
				?>
				<?php foreach ($_roles as $role) : ?>
					<input type="hidden" name="protect_post_type_by_user_roles[<?php echo esc_html($post_type_name) ?>][]" value="<?php echo esc_html($role) ?>" checked="">
				<?php endforeach; ?>
			<?php endforeach; ?>

			<?php foreach ($roles as $key => $display) : ?>
				<div>
					<input id="user_role_<?php echo esc_html($key) ?>" type="checkbox" name="protect_post_type_by_user_roles[<?php echo esc_html($this->current_page_cpt_name) ?>][]" value="<?php echo esc_html($display) ?>" <?php checked(1, in_array($display, $posts_types[$this->current_page_cpt_name]), true); ?>>
					<label for="user_role_<?php echo esc_html($key) ?>">
						<?php echo esc_html($display) ?>
					</label>
				</div>
			<?php endforeach; ?>
		<?php

			return  ob_get_clean();
		}

		private function renderUserRoles($role = null)
		{
			if (null === $role && $this->user_name_template !== null) {
				return $this->user_name_template;
			}

			$roles = get_option('protect_user_roles', []);
			ob_start();
		?>
			<select name="user_role">
				<option>-</option>
				<?php foreach ($roles as $key => $display) : ?>
					<option value="<?php echo esc_html($display) ?>" <?php if ($display === $role) : ?>selected<?php endif; ?>><?php echo esc_html($display); ?></option>
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
