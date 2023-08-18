# MarketStorm Wordpress Plugin

This plugin is designed to replace manual Pixel placement on MarketStorm client's Wordpress sites. The plugin is compatible with both Single and Multisite instances of Wordpress.
## Installation and Usage

1) Download the latest release from Github [here](https://github.com/marketstorm-ai/wordpress-plugin/releases/latest)
2) Upload Plugin to Wordpress
   - Plugins -> Add New -> Upload Plugin

![Wordpress Plugin Upload](/docs/wordpress_upload.png)

**Network Activate should only be used if every site on the Multisite Wordpress instance will be using the MarketStorm plugin**, otherwise, the relevant sites should be activated manually.

3) Activate the plugin for the given website
4) Find the Container ID in Matomo

![Matomo Container ID](/docs/container_id.png)

5) On Wordpress in the Settings -> Marketstorm menu, enter the Matomo Container ID for the website and press Save

![Wordpress Settings](/docs/wordpress_settings.png)

6) If needed, the plugin update check can be triggered using the Update hyperlink in the Wordpress Plugin List.

## Important Notes
- The Container ID stored by the plugin will not persist upon deactivation
- If the Container ID is left blank, no code will be injected.
