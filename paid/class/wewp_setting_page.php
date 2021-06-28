<?php

/**
 * Class Wewp_Setting_Page
 */

if (!class_exists('Wewp_Setting_Page')) {
	class Wewp_Setting_Page
	{
		private $page_id = 'protected-page-settings';
		private $post_type_supports_section_name = 'cpt_supports';
		private $admin_top_bar_button_section_name = 'admin_top_bar_button';

		public function __construct()
		{
			$this->addFilters();
			$this->addAction();
		}

		private function addFilters()
		{
		}

		private function addAction()
		{
			add_action("admin_init", array($this, 'display_theme_panel_fields'));
		}

		public function getSupportedPostTypes()
		{
			$post_types_supported = get_option('wewp_protected_page_cpt_support', ['page' => 'Pages']);
			if (empty($post_types_supported)) {
				$post_types_supported = [];
			}
			return $post_types_supported;
		}

		public function render_support_post_type()
		{
			$post_types_supported = $this->getSupportedPostTypes();

			if (empty($post_types_supported)) {
				$post_types_supported = [];
			}

			$post_types = $this->getPostTypes();
?>
			<?php foreach ($post_types as $post_name => $post_type_options) : ?>
				<div>
					<input id="post_type_<?php esc_attr($post_name); ?>" type="checkbox" name="wewp_protected_page_cpt_support[<?php echo esc_attr($post_name); ?>]" value="<?php echo esc_html($post_type_options->label); ?>" <?php if (isset($post_types_supported[$post_name])) : ?>checked<?php endif; ?> />
					<label for="post_type_<?php esc_attr($post_name); ?>"><?php echo esc_html($post_type_options->label); ?></label>
				</div>
			<?php endforeach; ?>
		<?php
		}

		public function display_theme_panel_fields()
		{
			// support post type
			add_settings_section(
				'cpt_supports',
				"",
				null,
				$this->page_id
			);

			add_settings_field(
				"wewp_protected_page_cpt_support",
				"Support Post Types",
				array($this, "render_support_post_type"),
				$this->page_id,
				'cpt_supports'
			);

			register_setting(
				'protected-page-setting',
				"wewp_protected_page_cpt_support"
			);

			// admin top bar button
			add_settings_section(
				'admin_top_bar_button',
				"",
				null,
				$this->page_id
			);
			add_settings_field(
				"wewp_protected_page_top_bar_button",
				"Display admin top bar button",
				array($this, "render_top_bar_button"),
				$this->page_id,
				'admin_top_bar_button'
			);

			register_setting(
				'protected-page-setting',
				"wewp_protected_page_top_bar_button"
			);

			// admin top bar button
			add_settings_section(
				'redirect_when_user_not_logged_in',
				"",
				null,
				$this->page_id
			);
			add_settings_field(
				"redirect_when_user_not_logged_in_select",
				"Redirect when user not logged in",
				array($this, "render_pages_select"),
				$this->page_id,
				'redirect_when_user_not_logged_in'
			);
			register_setting(
				'protected-page-setting',
				"redirect_when_user_not_logged_in_select"
			);
		}


		public function render_pages_select()
		{
			// require plugin_dir_path(__FILE__) . 'wewp_utils.php';
			// $display_admin_top_bar_button = Wewp_Utils::getOptionAdminTopBar();
			$args = array(
				'sort_order' => 'asc',
				'sort_column' => 'post_title',
				'hierarchical' => 1,
				'exclude' => '',
				'include' => '',
				'meta_key' => '',
				'meta_value' => '',
				'authors' => '',
				'child_of' => 0,
				'parent' => -1,
				'exclude_tree' => '',
				'number' => '',
				'offset' => 0,
				'post_type' => 'page',
				'post_status' => 'publish'
			);
			$pages = get_pages($args); // get all pages based on supplied args
		?>
			<div>

				<select name='pp_redirect_when_user_not_logged_in'>
					<?php foreach ($pages as $page) : ?>
						<option value="<?php echo $page->guid; ?>"> <?php echo $page->post_title; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php
		}

		public function render_top_bar_button()
		{
			require plugin_dir_path(__FILE__) . 'wewp_utils.php';
			$display_admin_top_bar_button = Wewp_Utils::getOptionAdminTopBar();
		?>
			<div>
				<input type="checkbox" name="wewp_protected_page_top_bar_button" id="wewp_protected_page_top_bar_button" value="1" <?php checked(1, $display_admin_top_bar_button, true); ?> />
			</div>
		<?php
		}

		public function render()
		{
			$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
			$roles = get_option('protect_user_roles');
		?>
			<?php if (DISPLAY_BANNER) : ?>
				<div style="background: #2e89fc;<?php if (is_rtl()) : ?>margin-right<?php else : ?>margin-left<?php endif; ?>: -20px;padding: 10px 21px;">
					<img src="<?php echo plugin_dir_url(__FILE__); ?>../assets/images/protected-page-logo-white.png" style="max-height: 50px;" />
				</div>
			<?php endif; ?>

			<div class="wrap">
				<h2 class="nav-tab-wrapper">
					<a href="<?php echo admin_url(); ?>admin.php?page=protected-page_setting&tab=settings" class="nav-tab <?php if ($current_tab === 'settings') : ?>nav-tab-active<?php endif; ?>">Settings</a>
					<a href="<?php echo admin_url(); ?>admin.php?page=protected-page_setting&tab=protect-levels" class="nav-tab <?php if ($current_tab === 'protect-levels') : ?>nav-tab-active<?php endif; ?>">Protect Page User's Levels</a>
				</h2>
				<?php if ($current_tab === 'protect-levels') : ?>
					<div class="wrap">

						<h3>Protect page user's levels</h3>
						<?php if (count($roles)) : ?>
							<ul>
								<?php foreach ($roles as $role) : ?>
									<li><?php echo esc_html($role) ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<h3>Create new protect user role</h3>
						<div class="pp-flex">
							<input name='role_name' />
							<button id='create_new_protect_level' class="button button-primary">&#x2B; create new</button>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($current_tab === 'settings') : ?>
					<div class="wrap">
						<form method="post" action="options.php">
							<?php
							do_settings_sections($this->page_id);
							settings_fields('protected-page-setting');
							//                settings_fields('admin_top_bar_button');
							submit_button();
							?>
						</form>
					</div>
				<?php endif; ?>
			</div>
<?php
		}

		/**
		 * @return array of post types as can we support
		 */
		private function getPostTypes()
		{
			$get_cpt_args = array(
				'public' => true,
			);
			$post_types = get_post_types($get_cpt_args, 'object');
			foreach ($post_types as $post_name => $post_type_options) {
				if ($post_type_options->name === 'elementor_library' || $post_type_options->name === 'attachment') {
					unset($post_types[$post_name]);
				}
			}
			return $post_types;
		}
	}
}
