<?php

namespace MarketStorm;

if (!class_exists('MatomoInjector')) {
    class MatomoInjector extends AbstractInjector {
        private static $instance;

        
        private function __construct() {
            parent::__construct(parent::HEAD);
        }


        public static function get() : MatomoInjector {
            if(is_null(self::$instance)) 
                self::$instance = new MatomoInjector();

            return self::$instance;
        }
        

        public function getId() : string {
            return 'matomo';
        }


        public function getSectionId() : string {
            return $this->getId() . '_section';
        }
        

        public function inject() {
            if($this->container_id == null) return;

            ob_start();

            ?>
            <!-- Matomo Tag Manager -->
            <script <?php echo $this->excludes(); ?> >
                var _mtm = window._mtm = window._mtm || [];
                _mtm.push({
                    'mtm.startTime': (new Date().getTime()), 
                    'event': 'mtm.Start'
                });
            
                (function() {
                    var d = document, 
                        g = d.createElement('script'), 
                        s = d.currentScript;
            
                    g.async = true; 
                    g.src = "<?php echo esc_attr(MATOMO_URI) ?>/container_<?php echo $this->container_id ?>.js";
                    for(const a of s.attributes) { g.setAttribute(a.name, a.value); }
                    s.parentNode.insertBefore(g,s);
                })();
            </script>
            <!-- End Matomo Tag Manager -->
            <?php

            echo ob_get_clean();     
        }


        public function settingRegister(string $settingPage) : void {
            add_settings_section(
				$this->getSectionId(),
				'Matomo', 
				array($this, 'settingSectionMarkup'),
				$settingPage
			);

            add_settings_field(
                $this->getId() . '_container_id',
                'Container ID',
                array($this, 'settingFieldMarkup'),
                $settingPage,
                $this->getSectionId()
            );
        }
   
        
        public function settingSectionMarkup($args) : void {
            ?>
            <p>
                This is the section for the Matomo Injector.
            </p>
            <?php
        }


        public function settingFieldMarkup($args) : void {
            ?>
			<input 
				type='text'
                name='<?php echo esc_attr_e(OPTION_NAME . "[{$this->getId()}_container_id]"); ?>'
				value='<?php echo esc_attr_e($this->container_id); ?>'
			>
			<?php
        }


        private static function excludes() {
			if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$excludes = '';

			if(is_plugin_active('nitropack/main.php')) $excludes .= esc_attr(' nitro-exclude');
			if(is_plugin_active('jetpack/jetpack.php')) $excludes .= esc_attr(' data-jetpack-boost=ignore');
            
            $excludes .= esc_attr( ' data-cfasync=false data-pagespeed-no-defer' );

			return $excludes;
		}
    }
}