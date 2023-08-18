<?php

namespace MarketStorm;

if (!class_exists('Controller')) {
	class Controller {
		private array $injectors;
        private static $instance;


		protected function __construct() {
			$this->hooks();
			$this->injectors = array();
			array_push($this->injectors, MatomoInjector::get());
		}


        public static function getInstance() {
            if(is_null(self::$instance)) {
                self::$instance = new Controller();
            }

            return self::$instance;
        }


		public static function loadResources() {
			$url = plugin_dir_url(__FILE__) . '/css/marketstorm.css';

			wp_register_style(
				PREFIX . '_styles',
				$url,
			);

			wp_enqueue_style(PREFIX . '_styles');
		}
		

		protected static function clear_caching_plugins() {
			if ( function_exists( 'wp_cache_clear_cache' ) ) {
				wp_cache_clear_cache();
			}

			if ( class_exists( 'W3_Plugin_TotalCacheAdmin' ) ) {
				$w3_total_cache = w3_instance( 'W3_Plugin_TotalCacheAdmin' );

				if ( method_exists( $w3_total_cache, 'flush_all' ) ) {
					$w3_total_cache->flush_all();
				}
			}
		}


		public function activate( $network_wide ) {
			$this->clear_caching_plugins();
		}


		public function activate_new_site( $blog_id ) {
			switch_to_blog( $blog_id );
			$this->single_activate( true );
			restore_current_blog();
		}


		protected function single_activate( $network_wide ) {
			foreach ( $this->modules as $module ) {
				$module->activate( $network_wide );
			}

			flush_rewrite_rules();
		}


		public function deactivate() {
			// if(is_multisite()) {
			// 	$count = get_sites( ['count' => true] );	

			// 	$sites = get_sites([
			// 		'number' => $count,
			// 		'fields' => 'ids'
			// 	]);
	
			// 	foreach($sites as $site) {
			// 		switch_to_blog($site);
			// 		delete_option(OPTION_NAME);
			// 	}
			// }
			// else {
			// 	delete_option(OPTION_NAME);
			// }

			delete_option(OPTION_NAME);
		}

		public function update() {}


		public function markupSettingPage() {
			if(!current_user_can(REQUIRED_PERMISSIONS)) {
				return;
			}

			if(isset($_GET['settings-updated'])) {
				add_settings_error(
					PREFIX . '_messages', 
					PREFIX . '_message', 
					__('Settings Saved', OPTION_GROUP), 
					'updated'
				);
			}

			?>
				<div class="marketstorm">
					<div class="heading">
						<img class="logo" src='<?php echo plugin_dir_url(__FILE__) . 'img/logo_with_text.svg'; ?>'>
					</div>
					<hr>
					<form action="options.php" method="post">
					<?php
						settings_fields(OPTION_GROUP);
						do_settings_sections(OPTION_GROUP);
						submit_button('Save');
					?>
					</form>
				</div>
			<?php
		}


		public function registerSettingMenu() {
			add_submenu_page(
				'options-general.php',
				DISPLAY_NAME,
				DISPLAY_NAME,
				REQUIRED_PERMISSIONS,
				OPTION_GROUP,
				array($this, 'markupSettingPage')

			);
		}


		public function registerSettingPage() {
			register_setting(
				OPTION_GROUP, 
				OPTION_NAME,
				array(__CLASS__ . '::settingsValidate')
			);

			foreach($this->injectors as $injector) {
				$injector->settingRegister(OPTION_GROUP);
			}
		}


		public static function settingsValidate($settings_new) {
			$settings = get_option(OPTION_NAME, array());

			return shortcode_atts($settings, $settings_new);
		}


		public function deleteUpdateTransients() {
			delete_transient('update_plugins');
			delete_site_transient('update_plugins');
		}


		public function actionLinks($actions) {
			$links = array(
				'<a href="' . admin_url( 'options-general.php?page=' . PREFIX . '_settings' ) . '">Settings</a>',
			);


			if(!is_multisite()) {
				array_push($links, '<a href="" onclick="fetch(\'/wp-admin/admin-post.php?action=delete_update_transients\', { method: \'post\' });">Update</a>');
			}

			$actions = array_merge($actions, $links);

			return $actions;
		}


		public function actionLinksNetwork($actions) {
			$links = array(
				'<a href="" onclick="fetch(\'/wp-admin/admin-post.php?action=delete_update_transients\', { method: \'post\' });">Update</a>'
			);

			$actions = array_merge($actions, $links);
			return $actions;
		}


		public function hooks() {
			add_action('wp_enqueue_scripts',    __CLASS__ . '::loadResources', 50);
			add_action('admin_enqueue_scripts', __CLASS__ . '::loadResources', 50);
			// add_action('admin_init', [$this, 'update']);
			add_action('admin_post_delete_update_transients', [$this, 'deleteUpdateTransients']);
			add_action('admin_init', [$this, 'registerSettingPage']);
			add_action('admin_menu', [$this, 'registerSettingMenu']);
			add_filter('plugin_action_links_' . PLUGIN_BASENAME, [$this, 'actionLinks'], 10, 1);
			add_filter('network_admin_plugin_action_links_' . PLUGIN_BASENAME, [$this, 'actionLinksNetwork'], 10, 1);
		}
	}
}