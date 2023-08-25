<?php

// TODO: tag_name to something else

namespace MarketStorm;

class Updater {
    private $file;
    private $plugin;
    private $basename;
    private $active;
    private $github_username;
    private $github_repository;
    private $github_data;

    public function __construct($file, $username, $repository) {
        $this->file = $file;
        $this->github_username = $username;
        $this->github_repository = $repository;

        add_action('admin_init', [$this, 'getPlugin']);

        return $this;
    }


    public function getPlugin() {
        $this->plugin = get_plugin_data($this->file);
        $this->basename = plugin_basename($this->file);
        $this->active = is_plugin_active($this->basename);
    }


    private function fetchRepository() {
        if (is_null($this->github_data)) {
            $uri = sprintf('https://api.github.com/repos/%s/%s/releases', $this->github_username, $this->github_repository);

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $uri,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "User-Agent: Marketstorm Updater"
                ]
            ]);

            $r = curl_exec($curl);
            $r = json_decode($r, true);

            curl_close($curl);

            if (is_array($r)) {
                $r = current($r);
            }

            $this->github_data = $r;
        }
    }


    public function initialize() {
        add_filter('pre_set_site_transient_update_plugins', [$this, 'addTransient'], 10, 1);
        add_filter('plugins_api', [$this, 'pluginPopup'], 10, 3);
        add_filter('upgrader_post_install', [$this, 'postInstall'], 10, 3);
    }


    public function addTransient($transient) {
        if (property_exists($transient, 'checked')) {
            if ($checked = $transient->checked) {
                $this->fetchRepository();

                $out_of_date = version_compare($this->github_data['tag_name'], $checked[$this->basename], 'gt');

                if ($out_of_date) {
                    $new_files = $this->github_data['zipball_url'];
                    $slug = current(explode('/', $this->basename));

                    $plugin = [
                        'url' => $this->plugin['PluginURI'],
                        'slug' => $slug,
                        'package' => $new_files,
                        'new_version' => $this->github_data['tag_name']
                    ];

                    $transient->response[$this->basename] = (object) $plugin;
                }
            }
        }

        return $transient;
    }


    public function pluginPopup($result, $action, $args) {
        if ($action !== 'plugin_information') {
            return false;
        }

        if (!empty($args->slug)) {
            if ($args->slug == current(explode('/' , $this->basename))) {
                $this->fetchRepository();

                $plugin = [
                    'name' => $this->plugin['Name'],
                    'slug' => $this->basename,
                    'requires' => MARKETSTORM_REQUIRED_WP_VERSION,
                    'tested' => MARKETSTORM_TESTED_WP_VERSION,
                    'version' => $this->github_data['tag_name'],
                    'author' => $this->plugin['AuthorName'],
                    'author_profile' => $this->plugin['AuthorURI'],
                    'last_updated' => $this->github_data['published_at'],
                    'homepage' => $this->plugin['PluginURI'],
                    'short_description' => $this->plugin['Description'],
                    'sections' => [
                        'Description' => $this->plugin['Description'],
                        'Updates' => $this->github_data['body'],
                    ],
                    'download_link' => $this->github_data['zipball_url']
                ];

                return (object) $plugin;
            }
        }

        return $result;
    }

    
    public function postInstall($response, $hook, $result) {
        global $wp_filesystem;

        $directory = plugin_dir_path($this->file);
        $wp_filesystem->move($result['destination'], $directory);
        $result['destination'] = $directory;

        if ($this->active) {
            activate_plugin($this->basename);
        }

        return $result;
    }
}