<?php

/**
 * Class Woocommerce_Setting
 */

if (!class_exists('Woocommerce_Setting_Page')) {
	class Woocommerce_Setting_Page
	{
		private $page_id = 'protected-page_woocommerce_setting_page';

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
		}


		public function render()
		{
		?>
			<?php if (DISPLAY_BANNER) : ?>
				<div style="background: #2e89fc;<?php if (is_rtl()) : ?>margin-right<?php else : ?>margin-left<?php endif; ?>: -20px;padding: 10px 21px;">
					<img src="<?php echo plugin_dir_url(__FILE__); ?>../assets/images/protected-page-logo-white.png" style="max-height: 50px;" />
				</div>
			<?php endif; ?>
			<div class="wrap">
				<h1 class="wp-heading-inline"><?php echo __('Woocommerce connector settings', 'protected-page'); ?></h1>
				<hr>
				asdas
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
