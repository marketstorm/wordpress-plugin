<?php

namespace MarketStorm;

if (!class_exists('MarketStorm\AbstractInjector')) {
    abstract class AbstractInjector {
        const HEAD = 'wp_head';
        const BODY = 'wp_body';
        const FOOTER = 'wp_footer';

        
        protected function __construct($target = self::HEAD) {
            add_action($target, array($this, 'inject'), 100);
        }


        public function __get($variable) {
            $values = get_option(MARKETSTORM_OPTION_NAME, null);
            $key = $this->getId() . "_{$variable}";

            if(is_null($values)) {
                return null;
            }

            if(!array_key_exists($key, $values)) {
                return null;
            }

            return $values[$key];
        }


        public function __set($variable, $value) {
            $key = $this->getId() . "_{$variable}";
            $settings = Controller::settingsValidate(array($key => $value));

			update_option(MARKETSTORM_OPTION_NAME, $settings);
        }


        abstract public static function get() : AbstractInjector;
        abstract public function getId() : string;
        abstract public function inject();
        abstract public function settingSectionMarkup($args) : void;
        abstract public function settingFieldMarkup($args) : void;
        abstract public function settingRegister(string $settingPage) : void;
    }
}